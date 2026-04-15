<?php

namespace App\Services;

use App\Models\Customer;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class CustomerService
{
    private ActivityLogService $activityLogService;
    /**
     * Create a new class instance.
     */
    public function __construct(
        ActivityLogService $activityLogService
    )
    {
        $this->activityLogService = $activityLogService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('customer.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            Customer::select(['id', 'name'])
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
        $customer = new Customer();
        
        $uuid =  Uuid::uuid4()->toString();
        $customer->id = $uuid;
        $customer->name = $data->name;
        $customer->save();

        $this->activityLogService->create((object) [
            'action' => 'CREATE',
            'module' => 'Customer',
            'description' => 'Create Customer',
            'subject_id' => $uuid
        ]);

        return $customer;
    }

    public function edit($data) {
        $customer = Customer::find($data->id);

        if (!$customer) {
            return null;
        }

        $customer->name = $data->name;
        $customer->save();

        $this->activityLogService->create((object) [
            'action' => 'UPDATE',
            'module' => 'Customer',
            'description' => 'Update Customer',
            'subject_id' => $data->id
        ]);

        return $customer;
    }

    public function remove($id) {
        $customer = Customer::find($id);

        if (!$customer) {
            return null;
        }

        $customer->delete();

        $this->activityLogService->create((object) [
            'action' => 'DELETE',
            'module' => 'Customer',
            'description' => 'Delete Customer',
            'subject_id' => $id
        ]);

        return $customer;
    }
}
