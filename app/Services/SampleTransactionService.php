<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SampleTransaction;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class SampleTransactionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('sample-transaction.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            SampleTransaction::select([
                'id', 
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
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->filter(function($query) {
            if ($search = request('search.value')) {
                $query->leftJoin('customers', 'customers.id', '=', 'sample.customer_id');
        
                $query->where(function ($q) use ($search) {
                    $q->where('sample.so_number', 'LIKE', "%{$search}%")
                        ->orWhere('customers.name', 'LIKE', "%{$search}%")
                        ->orWhereRaw(
                            "DATE_FORMAT(sample.so_created_at, '%d %b %Y %H:%i:%s') LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhereRaw(
                            "DATE_FORMAT(sample.shipment_request, '%d %b %Y %H:%i:%s') LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhereRaw(
                            "DATE_FORMAT(sample.picture_received_at, '%d %b %Y %H:%i:%s') LIKE ?",
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
        ->rawColumns(['customer_name', 'actions'])
        ->make(true);
    }

    public function getDetail($id) {
        $sample = SampleTransaction::with(['customer', 'processes'])->where('id', $id)->first();
        return $sample;
    }

    public function create($data) {
        $sample = new SampleTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $sample->id =$uuid;
        $sample->so_number = $data->so_number;
        $sample->customer_id = $data->customer;
        $sample->so_created_at = $data->so_created_at;
        $sample->shipment_request = $data->shipment_request;
        $sample->picture_receive_at = $data->picture_receiev_at;
        $sample->save();

        return $sample;
    }

    public function edit($data) {
        $sample = SampleTransaction::find($data->id);

        if (!$sample) {
            return null;
        }

        $sample->so_number = $data->so_number;
        $sample->customer_id = $data->customer;
        $sample->so_created_at = $data->so_created_at;
        $sample->shipment_request = $data->shipment_request;
        $sample->picture_receiev_at = $data->picture_receiev_at;
        $sample->save();

        return $sample;
    }

    public function remove($id) {
        $sample = SampleTransaction::find($id);

        if (!$sample) {
            return null;
        }

        $sample->delete();

        return $sample;
    }

    public function getCustomers() {
        return Customer::select(['id', 'name'])->get();   
    }
}
