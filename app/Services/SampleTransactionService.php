<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SampleTransaction;
use App\Models\SampleTransactionProcess;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class SampleTransactionService
{
    private FileService $fileService;

    /**
     * Create a new class instance.
     */
    public function __construct(
        FileService $fileService, 
    )
    {
        $this->fileService = $fileService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('sample-transaction.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            SampleTransaction::with('processes')->select([
                'sample_transactions.id', 
                'so_number', 
                'customer_id', 
                'so_created_at', 
                'shipment_request', 
                'picture_received_at'
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
        ->rawColumns(['customer_name', 'processes', 'actions'])
        ->make(true);
    }

    public function getDetail($id) {
        $sample = SampleTransaction::with(['customer', 'processes'])->where('id', $id)->first();
        return $sample;
    }

    public function getSimpleTransaction($id) {
        $sample = SampleTransaction::select('id', 'so_number')->where('id', $id)->first();
        return $sample;
    }

    public function create($data) {
        $sample = new SampleTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $sample->id = $uuid;
        $sample->so_number = $data->so_number;
        $sample->customer_id = $data->customer;
        $sample->so_created_at = Carbon::parse($data->so_created_at);
        $sample->shipment_request = Carbon::parse($data->shipment_request);
        $sample->picture_received_at = Carbon::parse($data->picture_received_at);
        $sample->save();

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
        $sample->picture_received_at = Carbon::parse($data->picture_received_at);
        $sample->save();

        return $sample;
    }

    public function remove($id) {
        $sample = SampleTransaction::with('processes')->find($id);

        if (!$sample) {
            return null;
        }

        $processes = $sample->processes;

        DB::transaction(function () use ($sample, $processes) {
    
            foreach ($processes as $process) {
                $process->delete();
            }
    
            $sample->delete();
        });
    
        foreach ($processes as $process) {
            if (!empty($process->filepath)) {
                $this->fileService->deleteFile($process->filepath);
            }
        }

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
        $sampleProcess->start_at = Carbon::parse($data->start_at);
        $sampleProcess->finish_at = Carbon::parse($data->finish_at);
        
        $filePaths = $this->fileService->storeFiles($data->file);
        $sampleProcess->filepath = $filePaths[0];

        $sampleProcess->save();

        return $sampleProcess;
    }

    public function editProcess($data)
    {
        $sampleProcess = SampleTransactionProcess::where('id', $data->id)->first();

        if (!$sampleProcess) {
            return null;
        }

        $sampleProcess->process_name = $data->process;
        $sampleProcess->start_at = Carbon::parse($data->start_at);
        $sampleProcess->finish_at = Carbon::parse($data->finish_at);
    
        if (!empty($data->file)) {
    
            if (!empty($sampleProcess->filepath)) {
                $this->fileService->deleteFile($sampleProcess->filepath);
            }

            $filePaths = $this->fileService->storeFiles($data->file);
            $sampleProcess->filepath = $filePaths[0];
    
        } elseif (!empty($data->existing_file)) {
    
            $sampleProcess->filepath = $data->existing_file;
        }
    
        $sampleProcess->save();
    
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

        return $sampleProcess;
    }
}
