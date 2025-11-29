<?php

namespace App\States\DrawingTransaction;

use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;

class DistributedState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
    }

    public function next(object $data = null) {

    }

    public function reject(object $data = null) {

    }
}
