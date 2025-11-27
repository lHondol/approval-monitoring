<?php

namespace App\Services;

use App\Models\DrawingTransaction;
use Yajra\DataTables\DataTables;

class DrawingTransactionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }

    public function getData() {
        return DataTables::of(DrawingTransaction::select([
            'customer_name',
            'so_number',
            'po_number',
            'created_at',
            'status',
        ]))->make(true);
    }

    public function create($data) {
        $drawingTransaction = new DrawingTransaction();
        $drawingTransaction->customer_name = $data->customer_name;
        $drawingTransaction->so_number = $data->so_number;
        $drawingTransaction->po_number = $data->po_number;

        if (isset($data->description))
            $drawingTransaction->description = $data->description; 

        $drawingTransaction->save();
    }
}
