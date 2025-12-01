<?php

namespace App\States\DrawingTransaction;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;
use App\Services\DrawingTransactionService;
use Carbon\Carbon;

class WaitingFor2ndApprovalState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    private DrawingTransactionService $drawingTransactionService;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
        $this->drawingTransactionService = app(DrawingTransactionService::class);
    }

    public function next(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::DISTRIBUTED->value;
        if (isset($data->so_number))
            $this->drawingTransaction->so_number = $data->so_number;
        $this->drawingTransaction->distributed_at = Carbon::now();
        $this->drawingTransaction->save();

        $this->drawingTransactionService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::APPROVE2,
            $data->reason ?? "Ok, Approved"
        );
    }

    public function reject(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::REVISE_NEEDED->value;
        if (isset($data->so_number))
            $this->drawingTransaction->so_number = $data->so_number;
        $this->drawingTransaction->need_revise_note = $data->reason;
        $this->drawingTransaction->save();

        $drawingTransactionStep = $this->drawingTransactionService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::REJECT,
            $data->reason
        );

        $this->drawingTransactionService->createRejectedImages(
            $this->drawingTransaction->id,
            $drawingTransactionStep->id,
            $this->drawingTransaction->filepath
        );
    }
}
