<?php

namespace App\Services;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Models\Customer;
use App\Models\DrawingTransaction;
use App\Models\DrawingTransactionStep;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Request;
use Ramsey\Uuid\Uuid;
use Str;
use Yajra\DataTables\DataTables;

class DrawingTransactionService
{
    private PDFService $pdfService;
    private DrawingTransactionStepService $drawingTransactionStepService;
    private DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService;
    /**
     * Create a new class instance.
     */
    public function __construct(
        PDFService $pdfService, 
        DrawingTransactionStepService $drawingTransactionStepService,
        DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService
    )
    {
        $this->pdfService = $pdfService;
        $this->drawingTransactionStepService = $drawingTransactionStepService;
        $this->drawingTransactionRejectedImageService = $drawingTransactionRejectedImageService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('drawing-transaction.datatables.actions', compact('data'));
    }

    public function renderStatusColor($status) {
        return match ($status) {
            StatusDrawingTransaction::WAITING_1ST_APPROVAL->value => "teal",
            StatusDrawingTransaction::WAITING_2ND_APPROVAL->value => "teal",
            StatusDrawingTransaction::REVISE_NEEDED->value => "amber",
            StatusDrawingTransaction::DISTRIBUTED_WAITING_BOM_APPROVAL->value => "blue",
            StatusDrawingTransaction::DISTRIBUTED_WAITING_COSTING_APPROVAL->value => "blue",
            StatusDrawingTransaction::DISTRIBUTED_BOM_REJECTED->value => "red",
            StatusDrawingTransaction::DISTRIBUTED_COSTING_REJECTED->value => "red",
            StatusDrawingTransaction::DISTRIBUTED_COSTING_DONE->value => "green",
        };
    }

    public function getData() {
        
        $status = [];
        if (auth()->user()->hasPermissionTo('view_distributed_drawing_transaction')) {
           $status = array_merge($status, [
                'Distributed, Costing Done',
                'Distributed, Waiting for BOM Approval',
                'Distributed, Waiting for Costing Approval'
            ]);
        }

        return DataTables::of(DrawingTransaction::select([
            'drawing_transactions.id',
            'drawing_transactions.so_number',
            'drawing_transactions.po_number',
            'drawing_transactions.description',
            'drawing_transactions.created_at',
            'drawing_transactions.distributed_at',
            'drawing_transactions.status',
            'drawing_transactions.as_revision_data',
            'drawing_transactions.as_additional_data',
            'drawing_transactions.done_revised',
            'drawing_transactions.customer_id',
        ])->with('customer')->when(count($status) > 0, function ($query) use ($status) {
            $query->whereIn('status', $status);
        }))
        ->addColumn('customer_name', function($row) {
            return $row->customer?->name ?? '';
        })
        ->orderColumn('customer_name', function($query, $order) {
            $query->leftJoin('customers', 'customers.id', '=', 'drawing_transactions.customer_id')
                  ->orderBy('customers.name', $order);
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
                $query->leftJoin('customers', 'customers.id', '=', 'drawing_transactions.customer_id');
        
                $query->where(function ($q) use ($search) {
                    $q->where('drawing_transactions.so_number', 'LIKE', "%{$search}%")
                      ->orWhere('drawing_transactions.po_number', 'LIKE', "%{$search}%")
                      ->orWhere('drawing_transactions.description', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                      ->orWhereRaw(
                          "DATE_FORMAT(drawing_transactions.created_at, '%d %b %Y %H:%i:%s') LIKE ?",
                          ["%{$search}%"]
                      )
                      ->orWhereRaw(
                          "DATE_FORMAT(drawing_transactions.distributed_at, '%d %b %Y %H:%i:%s') LIKE ?",
                          ["%{$search}%"]
                      );
                });
            }

            $revision   = request()->get('revision') == '1';
            $additional = request()->get('additional') == '1';
            $revised    = request()->get('revised') == '1';
            
            $conditions = [];
            
            if ($revision) {
                $conditions[] = ['drawing_transactions.as_revision_data', true];
            }
            
            if ($additional) {
                $conditions[] = ['drawing_transactions.as_additional_data', true];
            }
            
            if ($revised) {
                $conditions[] = ['drawing_transactions.done_revised', true];
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
                $query->where('drawing_transactions.status', 'LIKE', "%{$keyword}%");
            }
        })
        ->editColumn('created_at', function($row) {
            return Carbon::parse($row->created_at)->format('d M Y H:i:s');
        })
        ->editColumn('distributed_at', function($row) {
            if ($row->distributed_at)
                return Carbon::parse($row->distributed_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns(['customer_name', 'status', 'actions'])
        ->make(true);
    }

    public function getSteps($drawingTransactionId) {
        return DrawingTransactionStep::where('drawing_transaction_id', $drawingTransactionId)
            ->with(['user', 'rejected_file'])
            ->get();
    }

    public function getDetail($id) {
        $drawingTransaction = DrawingTransaction::with('customer')->where('id', $id)->first();
        if (auth()->user()->hasPermissionTo('view_distributed_drawing_transaction')) {
            if (!str_contains($drawingTransaction->status, 'Distributed')) {
                return null;
            }
        }
        return $drawingTransaction;
    }

    public function create($data) {
        $drawingTransaction = new DrawingTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $drawingTransaction->id =$uuid;

        $drawingTransaction->customer_id = $data->customer;
        $drawingTransaction->po_number = $data->po_number;

        $status = StatusDrawingTransaction::WAITING_1ST_APPROVAL;
        $drawingTransaction->status = $status->value;

        if (isset($data->description))
            $drawingTransaction->description = $data->description; 

        if (isset($data->as_additional_data)) {
            $drawingTransaction->as_additional_data = !!$data->as_additional_data;
            $drawingTransaction->additional_data_note = $data->additional_data_note;
        }

        if (isset($data->as_revision_data)) {
            $drawingTransaction->as_revision_data = !!$data->as_revision_data;
            $drawingTransaction->revision_data_note = $data->revision_data_note;
        }

        try {
            if (isset($data->as_additional_data) || isset($data->as_revision_data)) {
                $note = ($data->additional_data_note ?? '') . "\n" . ($data->revision_data_note ?? '');
                $note = trim($note);
                $mergedFilePath = $this->mergePdfWithNote(
                    $data->files, 
                    $uuid,
                    $note
                );
            } else {
                $mergedFilePath = $this->mergePdf(
                    $data->files, 
                    $uuid
                );
            }
        } catch (Exception $execption) {
            return null;
        }

        $drawingTransaction->filepath = $mergedFilePath;
        $drawingTransaction->save();

        $this->drawingTransactionStepService->createStep($drawingTransaction, ActionDrawingTransactionStep::UPLOAD);

        return $drawingTransaction;
    }

    public function mergePdf($files, $drawingTransactionId) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$drawingTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergeDrawingPdf($files, $newFileName);
    }

    public function mergePdfWithNote($files, $drawingTransactionId, $note) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$drawingTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergeDrawingPdfWithNote($files, $newFileName, 255, 58, $note);
    }

    public function getCustomers() {
        return Customer::select(['id', 'name'])->get();   
    }

    public function approve($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->next($data);

        return $drawingTransaction;
    }

    public function reject($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->reject($data);

        return $drawingTransaction;
    }

    public function revise($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $isCreated = $drawingTransaction->state->next($data);

        if (!$isCreated)
            return null;

        return $drawingTransaction;
    }
}
