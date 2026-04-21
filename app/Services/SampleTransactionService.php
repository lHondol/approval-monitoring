<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SampleTransaction;
use App\Models\SampleTransactionProcess;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class SampleTransactionService
{
    private PDFService $pdfService;
    private FileService $fileService;
    private ActivityLogService $activityLogService;

    /**
     * Create a new class instance.
     */
    public function __construct(
        PDFService $pdfService,
        FileService $fileService,
        ActivityLogService $activityLogService
    )
    {
        $this->pdfService = $pdfService;
        $this->fileService = $fileService;
        $this->activityLogService = $activityLogService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('sample-transaction.datatables.actions', compact('data'));
    }

    public function getForCalendar($start, $end)
    {
        return SampleTransaction::join('customers', 'customers.id', '=', 'sample_transactions.customer_id')
        ->whereBetween('so_created_at', [$start, $end])
        ->selectRaw("
            sample_transactions.id,
            customers.name as customer_name,
            so_number,
            DATE(shipment_request) as start
        ")
        ->get();
    }

    public function getData() {
        return DataTables::of(
            SampleTransaction::with(['processes' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'latestUnfinishedProcess'])->select([
                'sample_transactions.id', 
                'so_number', 
                'customer_id', 
                'so_created_at',
                'note', 
                'shipment_request', 
                'picture_received_at',
                'picture_received_note'
            ])
        )
        ->addColumn('customer_name', function($row) {
            return $row->customer?->name ?? '';
        })
        ->orderColumn('customer_name', function($query, $order) {
            $query->leftJoin('customers', 'customers.id', '=', 'sample_transactions.customer_id')
                  ->orderBy('customers.name', $order);
        })
        ->addColumn('processes', function($row) {
            return collect($row->processes)->map(function($p) {
                return [
                    'id' => $p->id,
                    'process_name' => $p->process_name,
                    'start_at' => $p->start_at 
                        ? \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i:s') 
                        : '',
                    'finish_at' => $p->finish_at 
                        ? \Carbon\Carbon::parse($p->finish_at)->format('d M Y H:i:s') 
                        : '',
                    'total_day' => ($p->start_at && $p->finish_at)
                        ? \Carbon\Carbon::parse($p->start_at)->startOfDay()
                            ->diffInDays(
                                \Carbon\Carbon::parse($p->finish_at)->startOfDay()
                            )
                        : '',
                    'start_note' => $p->start_note ?? '',
                    'finish_note' => $p->finish_note ?? '',
                    'file_url' => $p->filepath 
                        ? asset('storage/' . $p->filepath)
                        : null,
                ];
            })->values()->all();
        }) 
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->filter(function($query) {
            if ($search = request('search.value')) {
                $query->leftJoin('customers', 'customers.id', '=', 'sample_transactions.customer_id');
        
                $query->where(function ($q) use ($search) {
                    $q->where('sample_transactions.so_number', 'LIKE', "%{$search}%")
                        ->orWhere('customers.name', 'LIKE', "%{$search}%")
                        ->orWhereRaw(
                            "DATE_FORMAT(sample_transactions.so_created_at, '%d %b %Y %H:%i:%s') LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhereRaw(
                            "DATE_FORMAT(sample_transactions.shipment_request, '%d %b %Y %H:%i:%s') LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhereRaw(
                            "DATE_FORMAT(sample_transactions.picture_received_at, '%d %b %Y %H:%i:%s') LIKE ?",
                            ["%{$search}%"]
                        );
                      
                });
            }
        })
        ->editColumn('so_created_at', function($row) {
            if ($row->so_created_at)
                return Carbon::parse($row->so_created_at)->format('d M Y H:i:s');
            return '';
        })
        ->editColumn('shipment_request', function($row) {
            if ($row->shipment_request)
                return Carbon::parse($row->shipment_request)->format('d M Y H:i:s');
            return '';
        })
        ->editColumn('picture_received_at', function($row) {
            if ($row->picture_received_at)
                return Carbon::parse($row->picture_received_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns(['customer_name', 'processes', 'latest_unfinished_process', 'actions'])
        ->make(true);
    }

    public function getDetail($id) {
        $sample = SampleTransaction::with(['customer', 'processes' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->where('id', $id)->first();
        return $sample;
    }

    public function getSimpleTransaction($id) {
        $sample = SampleTransaction::select('id', 'so_number')->where('id', $id)->first();
        return $sample;
    }

    public function mergePdf($files, $sampleTransactionId) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$sampleTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergePdf($files, $newFileName, "sample-pdfs");
    }

    public function create($data) {
        $sample = new SampleTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $sample->id = $uuid;
        $sample->so_number = $data->so_number;
        $sample->customer_id = $data->customer;
        $sample->so_created_at = Carbon::now();
        $sample->shipment_request = Carbon::parse($data->shipment_request);
        
        if (isset($data->note)) {
            $sample->note = $data->note;
        }

        try {
            $mergedFilePath = $this->mergePdf(
                $data->files, 
                $uuid,
            );
        } catch (Exception $execption) {
            return null;
        }

        $sample->filepath = $mergedFilePath;

        $sample->save();

        $this->activityLogService->create((object) [
            'action' => 'CREATE',
            'module' => 'Sample Transaction',
            'description' => 'Create Sample',
            'subject_id' => $uuid
        ]);

        return $sample;
    }

    public function edit($data) {
        $sample = SampleTransaction::with(['customer'])->where('id', $data->id)->first();

        if (!$sample) {
            return null;
        }
        
        $sample->customer_id = $data->customer;
        $sample->so_created_at = Carbon::parse($data->so_created_at);
        $sample->shipment_request = Carbon::parse($data->shipment_request);

        if (isset($data->note)) {
            $sample->note = $data->note;
        }

        if (!empty($data->files)) {
    
            if (!empty($sample->filepath)) {
                $this->fileService->deleteFile($sample->filepath);
            }

            try {
                $mergedFilePath = $this->mergePdf(
                    $data->files, 
                    $sample->id,
                );
            } catch (Exception $execption) {
                return null;
            }

            $sample->filepath = $mergedFilePath;
    
        } elseif (!empty($data->existing_file)) {
    
            $sample->filepath = $data->existing_file;
        }

        $sample->save();

        $this->activityLogService->create((object) [
            'action' => 'UPDATE',
            'module' => 'Sample Transaction',
            'description' => 'Update Sample',
            'subject_id' => $data->id
        ]);

        return $sample;
    }

    public function remove($id) {
        $sample = SampleTransaction::with('processes')->find($id);

        if (!$sample) {
            return null;
        }

        $processes = $sample->processes;
        $filepath = $sample->filepath;

        DB::transaction(function () use ($sample, $processes, $filepath) {
    
            foreach ($processes as $process) {
                $process->delete();
            }
    
            $deleted = $sample->delete();

            foreach ($processes as $process) {
                if (!empty($process->filepath)) {
                    $this->fileService->deleteFile($process->filepath);
                }
            }
    
            if ($deleted && !empty($filepath)) {
                $this->fileService->deleteFile($filepath);
            }
        });

        $this->activityLogService->create((object) [
            'action' => 'DELETE',
            'module' => 'Sample Transaction',
            'description' => 'Delete Sample',
            'subject_id' => $id
        ]);

        return $sample;
    }

    public function approve($data) {
        $sample = SampleTransaction::where('id', $data->id)->first();

        if (!$sample) {
            return null;
        }
        
        $sample->picture_received_at = Carbon::now();
        
        if (isset($data->picture_received_note)) {
            $sample->picture_received_note = $data->picture_received_note;
        }

        $sample->save();

        $this->activityLogService->create((object) [
            'action' => 'APPROVE',
            'module' => 'Sample Transaction',
            'description' => 'APPROVE Sample',
            'subject_id' => $sample->id
        ]);

        return $sample;
    }

    public function getCustomers() {
        return Customer::select(['id', 'name'])->get();   
    }

    
    public function getProcesses() {
        return [
            "Pembahanan",
            "Laminating",
            "Process Center",
            "CNC",
            "Finishing/Cat",
            "Finish Good"
        ];
    }

    public function getProcessDetail($id) {
        $process = SampleTransactionProcess::with(['sample'])->where('id', $id)->first();
        return $process;
    }

    public function createProcess($data) {
        $sampleProcess = new SampleTransactionProcess();
        
        $uuid =  Uuid::uuid4()->toString();
        $sampleProcess->id = $uuid;
        $sampleProcess->sample_transaction_id = $data->sampleTransactionId;
        $sampleProcess->process_name = $data->process;
        $now = Carbon::now();
        $sampleProcess->start_at = $now;

        if (!empty($data->start_note) && $data->process !== 'Finish Good') {
            $sampleProcess->start_note = $data->start_note;
        } else if ($data->process == 'Finish Good') {
            $sampleProcess->finish_at = $now;
            if (!empty($data->start_note)) {
                $sampleProcess->start_note = $data->start_note;
                $sampleProcess->finish_note = $data->start_note;
            }
        }

        // $sampleProcess->finish_at = Carbon::parse($data->finish_at);
        
        $filePaths = $this->fileService->storeFiles($data->file);
        $sampleProcess->filepath = $filePaths[0];

        $sampleProcess->save();

        $this->activityLogService->create((object) [
            'action' => 'START',
            'module' => 'Sample Transaction Process',
            'description' => 'Start Sample Process',
            'subject_id' => $uuid
        ]);

        return $sampleProcess;
    }

    public function editProcess($data)
    {
        $sampleProcess = SampleTransactionProcess::where('id', $data->id)->first();

        if (!$sampleProcess) {
            return null;
        }

        $action = $sampleProcess->finish_at ? 'UPDATE' : 'FINISH';

        // $sampleProcess->process_name = $data->process;
        // $sampleProcess->start_at = Carbon::parse($data->start_at);
        if (empty($sampleProcess->finish_at)) {
            $sampleProcess->finish_at = Carbon::now();
        }

        if (!empty($data->finish_note)) {
            $sampleProcess->finish_note = $data->finish_note;
        }
    
        $sampleProcess->save();

        $this->activityLogService->create((object) [
            'action' => $action,
            'module' => 'Sample Transaction Process',
            'description' => "{ucwords($action)} Sample Process",
            'subject_id' => $data->id
        ]);
    
        return $sampleProcess;
    }

    public function removeProcess($id) {
        $sampleProcess = SampleTransactionProcess::find($id);

        if (!$sampleProcess) {
            return null;
        }

        $filepath = $sampleProcess->filepath;

        $deleted = $sampleProcess->delete();

        if ($deleted && !empty($filepath)) {
            $this->fileService->deleteFile($filepath);
        }

        $this->activityLogService->create((object) [
            'action' => 'DELETE',
            'module' => 'Sample Transaction Process',
            'description' => 'Delete Sample Process',
            'subject_id' => $id
        ]);

        return $sampleProcess;
    }
}
