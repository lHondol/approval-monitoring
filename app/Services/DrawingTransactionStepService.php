<?php

namespace App\Services;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Models\DrawingTransaction;
use App\Models\DrawingTransactionRejectedFile;
use App\Models\DrawingTransactionStep;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Str;
use Yajra\DataTables\DataTables;

class DrawingTransactionStepService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createStep($drawingTransaction, $action, $reason = null) {
        $drawingTransactionStep = new DrawingTransactionStep();
        $drawingTransactionStep->drawing_transaction_id = $drawingTransaction->id;
        $drawingTransactionStep->done_by_user = auth()->user()->id;
        $drawingTransactionStep->done_at = $drawingTransaction->updated_at;
        $drawingTransactionStep->action_done = $action;
        $drawingTransactionStep->reason = $reason;
        $drawingTransactionStep->save();

        return $drawingTransactionStep;
    }
}
