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
}
