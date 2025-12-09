<?php

namespace App\Services;

use App\Models\Customer;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class CustomerService
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
        return view('customer.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            Customer::select(['id', 'name'])->orderBy('created_at', 'desc')
        )
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function getDetail($id) {
        $customer = Customer::find($id);
        return $customer;
    }

    public function create($data) {
        $customer = new customer();
        
        $uuid =  Uuid::uuid4()->toString();
        $customer->id =$uuid;
        $customer->name = $data->name;
        $customer->save();

        return $customer;
    }

    public function edit($data) {
        $customer = Customer::find($data->id);

        if (!$customer) {
            return null;
        }

        $customer->name = $data->name;
        $customer->save();

        return $customer;
    }

    public function remove($id) {
        $customer = Customer::find($id);

        if (!$customer) {
            return null;
        }

        $customer->delete();

        return $customer;
    }
}
