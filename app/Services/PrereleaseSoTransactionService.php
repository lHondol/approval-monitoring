<?php

namespace App\Services;

use App\Enums\ActionPrereleaseSoTransactionStep;
use App\Enums\StatusPrereleaseSoTransaction;
use App\Models\Area;
use App\Models\Customer;
use App\Models\PrereleaseSoTransaction;
use App\Models\PrereleaseSoTransactionStep;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Request;
use Ramsey\Uuid\Uuid;
use Str;
use Yajra\DataTables\DataTables;

class PrereleaseSoTransactionService
{
    private PDFService $pdfService;
    private PrereleaseSoTransactionStepService $preleaseSoTransactionStepService;
    private PrereleaseSoTransactionRejectedImageService $preleaseSoTransactionRejectedImageService;
    /**
     * Create a new class instance.
     */
    public function __construct(
        PDFService $pdfService, 
        PrereleaseSoTransactionStepService $preleaseSoTransactionStepService,
        PrereleaseSoTransactionRejectedImageService $preleaseSoTransactionRejectedImageService
    )
    {
        $this->pdfService = $pdfService;
        $this->preleaseSoTransactionStepService = $preleaseSoTransactionStepService;
        $this->preleaseSoTransactionRejectedImageService = $preleaseSoTransactionRejectedImageService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('prerelease-so-transaction.datatables.actions', compact('data'));
    }

    public function renderStatusColor($status) {
        return match ($status) {
            StatusPrereleaseSoTransaction::WAITING_SALES_AREA_APPROVAL->value => "teal",
            StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value => "orange",
            StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value => "cyan",
            StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value => "blue",
            StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value => "purple",
            StatusPrereleaseSoTransaction::FINALIZED->value => "green",
            StatusPrereleaseSoTransaction::REVISE_NEEDED->value => "yellow",
        };
    }

    public function getData() {
        
        $status = [];

        return DataTables::of(PrereleaseSoTransaction::select([
            'prerelease_so_transactions.id',
            'prerelease_so_transactions.so_number',
            'prerelease_so_transactions.po_number',
            'prerelease_so_transactions.description',
            'prerelease_so_transactions.created_at',
            'prerelease_so_transactions.finalized_at',
            'prerelease_so_transactions.status',
            'prerelease_so_transactions.as_revision_data',
            'prerelease_so_transactions.as_additional_data',
            'prerelease_so_transactions.done_revised',
            'prerelease_so_transactions.customer_id',
        ])->with('customer')->when(count($status) > 0, function ($query) use ($status) {
            $query->whereIn('status', $status);
        }))
        ->addColumn('customer_name', function($row) {
            return $row->customer?->name ?? '';
        })
        ->addColumn('area', function($row) {
            return $row->area?->name ?? '';
        })
        ->orderColumn('customer_name', function($query, $order) {
            $query->leftJoin('customers', 'customers.id', '=', 'prerelease_so_transactions.customer_id')
                  ->orderBy('customers.name', $order);
        })
        ->orderColumn('area', function($query, $order) {
            $query->leftJoin('areas', 'areas.id', '=', 'prerelease_so_transactions.area_id')
                  ->orderBy('areas.name', $order);
        })       
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->editColumn('status', function($row) {
            $renderStatusColor = $this->renderStatusColor($row->status);
            $statusHtml = "<div class='flex gap-2 flex-wrap'>";

            if ($row->as_revision_data) {
                $statusHtml .= "<span class='ui yellow label'>Revision Data</span>";
            }

            if ($row->as_additional_data) {
                $statusHtml .= "<span class='ui green label'>Additional Data</span>";
            }

            if ($row->done_revised) {
                $statusHtml .= "<span class='ui green label'>Revised</span>";
            }

            $statusHtml .= "<span class='ui label {$renderStatusColor}'>{$row->status}</span>";

            $statusHtml .= "</div>";

            return $statusHtml;
        })
        ->filter(function($query) {
            if ($search = request('search.value')) {
                $query->leftJoin('customers', 'customers.id', '=', 'prerelease_so_transactions.customer_id');
                $query->leftJoin('areas', 'areas.id', '=', 'prerelease_so_transactions.area_id');
        
                $query->where(function ($q) use ($search) {
                    $q->where('prerelease_so_transactions.so_number', 'LIKE', "%{$search}%")
                      ->orWhere('prerelease_so_transactions.po_number', 'LIKE', "%{$search}%")
                      ->orWhere('prerelease_so_transactions.description', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                      ->orWhere('areas.name', 'LIKE', "%{$search}%")
                      ->orWhereRaw(
                          "DATE_FORMAT(prerelease_so_transactions.created_at, '%d %b %Y %H:%i:%s') LIKE ?",
                          ["%{$search}%"]
                      )
                      ->orWhereRaw(
                          "DATE_FORMAT(prerelease_so_transactions.finalized_at, '%d %b %Y %H:%i:%s') LIKE ?",
                          ["%{$search}%"]
                      );
                });
            }

            $revision   = request()->get('revision') == '1';
            $additional = request()->get('additional') == '1';
            $revised    = request()->get('revised') == '1';
            
            $conditions = [];
            
            if ($revision) {
                $conditions[] = ['prerelease_so_transactions.as_revision_data', true];
            }
            
            if ($additional) {
                $conditions[] = ['prerelease_so_transactions.as_additional_data', true];
            }
            
            if ($revised) {
                $conditions[] = ['prerelease_so_transactions.done_revised', true];
            }
            
            // Apply OR only if user checks at least one checkbox
            if (!empty($conditions)) {
                $query->where(function($q) use ($conditions) {
                    foreach ($conditions as $cond) {
                        $q->orWhere($cond[0], $cond[1]);
                    }
                });
            }
        })
        ->filterColumn('status', function($query, $keyword) {
            if ($keyword !== '') {
                $query->where('prerelease_so_transactions.status', 'LIKE', "%{$keyword}%");
            }
        })
        ->editColumn('created_at', function($row) {
            return Carbon::parse($row->created_at)->format('d M Y H:i:s');
        })
        ->editColumn('finalized_at', function($row) {
            if ($row->finalized_at)
                return Carbon::parse($row->finalized_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns(['customer_name', 'area', 'status', 'actions'])
        ->make(true);
    }

    public function getSteps($prereleaseSoTransactionId) {
        return PrereleaseSoTransactionStep::where('prerelease_so_transaction_id', $prereleaseSoTransactionId)
            ->with(['user', 'rejected_file'])
            ->get();
    }

    public function getDetail($id) {
        $prereleaseSoTransaction = PrereleaseSoTransaction::with(['customer', 'area'])->where('id', $id)->first();
        return $prereleaseSoTransaction;
    }

    public function create($data) {
        $prereleaseSoTransaction = new PrereleaseSoTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $prereleaseSoTransaction->id =$uuid;

        $prereleaseSoTransaction->customer_id = $data->customer;
        $prereleaseSoTransaction->area_id = $data->area;
        $prereleaseSoTransaction->so_number = $data->so_number;
        $prereleaseSoTransaction->po_number = $data->po_number;

        $status = StatusPrereleaseSoTransaction::WAITING_SALES_AREA_APPROVAL;
        $prereleaseSoTransaction->status = $status->value;

        if (isset($data->description))
            $prereleaseSoTransaction->description = $data->description; 

        if (isset($data->as_additional_data)) {
            $prereleaseSoTransaction->as_additional_data = !!$data->as_additional_data;
            $prereleaseSoTransaction->additional_data_note = $data->additional_data_note;
        }

        if (isset($data->as_revision_data)) {
            $prereleaseSoTransaction->as_revision_data = !!$data->as_revision_data;
            $prereleaseSoTransaction->revision_data_note = $data->revision_data_note;
        }

        try {
            // if (isset($data->as_additional_data) || isset($data->as_revision_data)) {
            //     $note = ($data->additional_data_note ?? '') . "\n" . ($data->revision_data_note ?? '');
            //     $note = trim($note);
            //     $mergedFilePath = $this->mergePdfWithNote(
            //         $data->files, 
            //         $uuid,
            //         $note
            //     );
            // } else {
                $mergedFilePath = $this->mergePdf(
                    $data->files, 
                    $uuid
                );
            // }
        } catch (Exception $execption) {
            return null;
        }

        $prereleaseSoTransaction->filepath = $mergedFilePath;
        $prereleaseSoTransaction->save();

        $this->preleaseSoTransactionStepService->createStep($prereleaseSoTransaction, ActionPrereleaseSoTransactionStep::UPLOAD);

        return $prereleaseSoTransaction;
    }

    public function mergePdf($files, $prereleaseSoTransactionId) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$prereleaseSoTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergePdf($files, $newFileName, "prerelease-so-pdfs");
    }

    public function mergePdfWithNote($files, $prereleaseSoTransactionId, $note) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$prereleaseSoTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergePdfWithNote($files, $newFileName, 255, 58, $note, "prerelease-so-pdfs");
    }

    public function getCustomers() {
        return Customer::select(['id', 'name'])->get();   
    }

    public function getAreas() {
        return Area::select(['id', 'name'])->get();   
    }

    public function approve($data) {
        $prereleaseSoTransaction = PrereleaseSoTransaction::where('id', $data->id)->first();
        $prereleaseSoTransaction->state->next($data);

        return $prereleaseSoTransaction;
    }

    public function reject($data) {
        $prereleaseSoTransaction = PrereleaseSoTransaction::where('id', $data->id)->first();
        $prereleaseSoTransaction->state->reject($data);

        return $prereleaseSoTransaction;
    }

    public function revise($data) {
        $prereleaseSoTransaction = PrereleaseSoTransaction::where('id', $data->id)->first();
        $isCreated = $prereleaseSoTransaction->state->next($data);

        if (!$isCreated)
            return null;

        return $prereleaseSoTransaction;
    }
}
