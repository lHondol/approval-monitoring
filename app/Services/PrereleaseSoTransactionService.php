<?php

namespace App\Services;

use App\Enums\ActionPrereleaseSoTransactionStep;
use App\Enums\StatusPrereleaseSoTransaction;
use App\Models\Area;
use App\Models\Customer;
use App\Models\PrereleaseSoTransaction;
use App\Models\PrereleaseSoTransactionStep;
use App\States\PrereleaseSoTransaction\WaitingForAccountingApprovalState;
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
            StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value => "pink",
            StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value => "blue",
            StatusPrereleaseSoTransaction::WAITING_IT_APPROVAL->value => "violet",
            StatusPrereleaseSoTransaction::WAITING_MKT_MGR_CONFIRM_MARGIN->value => "violet",
            StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_RELEASE->value => "purple",
            StatusPrereleaseSoTransaction::RELEASED_WAITING_PO_KACA_APPROVAL->value => "grey",
            StatusPrereleaseSoTransaction::RELEASED_PO_KACA_DONE->value => "green",
            StatusPrereleaseSoTransaction::REVISE_NEEDED->value => "yellow",
        };
    }

    public function getData() {
        
        $status = [];

        // $nonAreaPermissions = [
        //     'rnd_drawing_approve_prerelease_so_transaction', 
        //     'rnd_bom_approve_prerelease_so_transaction', 
        //     'accounting_approve_prerelease_so_transaction', 
        //     'it_approve_prerelease_so_transaction',
        //     'mkt_staff_release_prerelease_so_transaction' 
        // ];

        // $filterByArea = null;
        // if (auth()->user()->hasPermissionTo('sales_area_approve_prerelease_so_transaction') &&
        //     !auth()->user()->hasAnyPermission($nonAreaPermissions)) {
        //     $filterByArea = true;
        // }

        return DataTables::of(PrereleaseSoTransaction::select([
            'prerelease_so_transactions.id',
            'prerelease_so_transactions.so_number',
            'prerelease_so_transactions.po_number',
            'prerelease_so_transactions.description',
            'prerelease_so_transactions.created_at',
            'prerelease_so_transactions.released_at',
            'prerelease_so_transactions.status',
            'prerelease_so_transactions.as_revision_data',
            'prerelease_so_transactions.as_additional_data',
            'prerelease_so_transactions.done_revised',
            'prerelease_so_transactions.customer_id',
            'prerelease_so_transactions.is_urgent',
            'prerelease_so_transactions.target_shipment_month',
            'prerelease_so_transactions.target_shipment_year'
            // 'prerelease_so_transactions.area_id',
        ])->with(['customer', 'area'])
        ->orderBy('is_urgent', 'desc')
        ->when(count($status) > 0, function ($query) use ($status) {
            $query->whereIn('status', $status);
        })
        // ->when($filterByArea, function ($query) {
        //     auth()->user()Id = auth()->user()->id;
        
        //     $query->whereHas('area.users', function ($q) use ($userId) {
        //         $q->where('users.id', $userId);
        //     });
        // })
        )
        ->addColumn('customer_name', function($row) {
            return $row->customer?->name ?? '';
        })
        // ->addColumn('area', function($row) {
        //     return $row->area?->name ?? '';
        // })
        ->orderColumn('customer_name', function($query, $order) {
            $query->leftJoin('customers', 'customers.id', '=', 'prerelease_so_transactions.customer_id')
                  ->orderBy('customers.name', $order);
        })
        // ->orderColumn('area', function($query, $order) {
        //     $query->leftJoin('areas', 'areas.id', '=', 'prerelease_so_transactions.area_id')
        //           ->orderBy('areas.name', $order);
        // })
        ->addColumn('leadtime', function($row) {
            $days = (string) Carbon::parse($row->created_at)
            ->startOfDay()
            ->diffInDays(Carbon::now()->startOfDay());
            return "$days day(s)"; 
        })
        ->addColumn('target_shipment', function ($row) {
            if (!$row->target_shipment_month || !$row->target_shipment_year) {
                return '';
            }

            $date = Carbon::createFromDate(
                $row->target_shipment_year,
                $row->target_shipment_month,
                1
            );

            return $date->format('F Y'); // March 2026
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
                // $query->leftJoin('areas', 'areas.id', '=', 'prerelease_so_transactions.area_id');
        
                $query->where(function ($q) use ($search) {
                    if (str_contains($search, ';')) {
                        $soNumbers = collect(explode(';', $search))
                            ->map(fn ($v) => trim($v))
                            ->filter()
                            ->values()
                            ->toArray();

                        $q->whereIn('prerelease_so_transactions.so_number', $soNumbers);
                    } else {
                        $q->where('prerelease_so_transactions.so_number', 'LIKE', "%{$search}%");
                    }

                    $q->orWhere('prerelease_so_transactions.po_number', 'LIKE', "%{$search}%")
                      ->orWhere('prerelease_so_transactions.description', 'LIKE', "%{$search}%")
                      ->orWhere('customers.name', 'LIKE', "%{$search}%")
                    //   ->orWhere('areas.name', 'LIKE', "%{$search}%")
                      ->orWhereRaw(
                          "DATE_FORMAT(prerelease_so_transactions.created_at, '%d %b %Y %H:%i:%s') LIKE ?",
                          ["%{$search}%"]
                      )
                      ->orWhereRaw(
                          "DATE_FORMAT(prerelease_so_transactions.released_at, '%d %b %Y %H:%i:%s') LIKE ?",
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
        ->editColumn('released_at', function($row) {
            if ($row->released_at)
                return Carbon::parse($row->released_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns([
            'customer_name', 
            // 'area', 
            'status', 
            'actions'
        ])
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
        // $prereleaseSoTransaction->area_id = $data->area;
        $prereleaseSoTransaction->so_number = $data->so_number;
        $prereleaseSoTransaction->po_number = $data->po_number;

        $target = Carbon::createFromFormat('Y-m', $data->target_shipment);

        $prereleaseSoTransaction->target_shipment_year = $target->year;
        $prereleaseSoTransaction->target_shipment_month = $target->month;

        $prereleaseSoTransaction->is_urgent = $data->is_urgent ?? 0;

        $status = StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL;
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

        $createdAt = Carbon::now();

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
                $mergedFilePath = $this->mergePdfWithCreatedAt(
                    $data->files, 
                    $uuid,
                    $createdAt
                );
            // }
        } catch (Exception $execption) {
            return null;
        }

        $prereleaseSoTransaction->filepath = $mergedFilePath;
        $prereleaseSoTransaction->created_at = $createdAt;
        $prereleaseSoTransaction->updated_at = $createdAt;
        $prereleaseSoTransaction->save();

        $this->preleaseSoTransactionStepService->createStep($prereleaseSoTransaction, ActionPrereleaseSoTransactionStep::UPLOAD);

        return $prereleaseSoTransaction;
    }

    public function mergePdf($files, $prereleaseSoTransactionId) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$prereleaseSoTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergePdf($files, $newFileName, "prerelease-so-pdfs");
    }

    public function mergePdfWithCreatedAt($files, $prereleaseSoTransactionId, $createdAt) {
        $timestamp = now()->format('Ymd_His');
        $createdAt = $createdAt->format('d M Y H:i:s');

        $note = "Created At: {$createdAt}";

        $newFileName = "{$prereleaseSoTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergePdfWithNote($files, $newFileName, 10, 10, $note, "prerelease-so-pdfs");
    }

    public function getCustomers() {
        return Customer::select(['id', 'name'])->get();   
    }

    public function getAreas() {
        return Area::select(['id', 'name'])->get();   
    }

    public function getMonths() {
        $months = [];

        $start = now()->copy()->addMonth()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);

            $months[] = [
                'year'  => $date->year,
                'month' => $date->month,
                'label' => $date->format('F Y'),
                'value' => $date->format('Y-m'),
            ];
        }

        return $months;
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

    public function requestConfirmMargin($data) {
        $prereleaseSoTransaction = PrereleaseSoTransaction::where('id', $data->id)->first();
        if ($prereleaseSoTransaction->state instanceof WaitingForAccountingApprovalState) {
            $prereleaseSoTransaction->state->requestConfirmMargin($data);
        } else {
            throw new Exception('Only accounting can request margin confirmation');
        }

        return $prereleaseSoTransaction;
    }

    public function getBadgeCount() {
        $statuses = [];

        if (auth()->user()->hasPermissionTo('rnd_drawing_approve_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value;
        }
        
        if (auth()->user()->hasPermissionTo('rnd_bom_approve_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value;
        }

        if (auth()->user()->hasPermissionTo('accounting_approve_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;
        }

        if (auth()->user()->hasPermissionTo('accounting_request_confirm_margin_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;
        }
        
        if (auth()->user()->hasPermissionTo('mkt_manager_confirm_margin_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_MKT_MGR_CONFIRM_MARGIN->value;
        }

        if (auth()->user()->hasPermissionTo('mkt_staff_release_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_RELEASE->value;
        }

        if (auth()->user()->hasPermissionTo('po_kaca_released_approve_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::RELEASED_WAITING_PO_KACA_APPROVAL->value;
        }

        if (auth()->user()->hasPermissionTo('revise_prerelease_so_transaction')) {
            $statuses[] = StatusPrereleaseSoTransaction::REVISE_NEEDED->value;
        }
    
        $count = PrereleaseSoTransaction::whereIn('status', $statuses)
            ->whereDoesntHave('notificationReads', function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereNotNull('read_at');
            })
            ->count();

        return $count;
    }
}
