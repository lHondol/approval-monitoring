<?php

namespace App\States\DrawingTransaction;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;
use App\Services\DrawingTransactionService;

class WaitingFor1stApprovalState implements DrawingTransactionState
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
        $this->drawingTransaction->status = StatusDrawingTransaction::WAITING_2ND_APPROVAL->value;
        $this->drawingTransaction->save();

        $this->drawingTransactionService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::APPROVE1,
            $data->reason ?? "Ok, Approved"
        );
    }

    public function reject(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::REVISE_NEEDED->value;
        $this->drawingTransactionneed_revise_note = $data->reason;
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
