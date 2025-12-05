<?php

namespace App\States\DrawingTransaction;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;
use App\Services\DrawingTransactionRejectedImageService;
use App\Services\DrawingTransactionService;
use App\Services\DrawingTransactionStepService;

class WaitingFor1stApprovalState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    private DrawingTransactionStepService $drawingTransactionStepService;
    private DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
        $this->drawingTransactionStepService = app(DrawingTransactionStepService::class);
        $this->drawingTransactionRejectedImageService = app(DrawingTransactionRejectedImageService::class);
    }

    public function next(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::WAITING_2ND_APPROVAL->value;
        $this->drawingTransaction->save();

        $this->drawingTransactionStepService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::APPROVE1,
            $data->reason ?? "Ok, Approved"
        );
    }

    public function reject(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::REVISE_NEEDED->value;
        $this->drawingTransaction->need_revise_note = $data->reason;
        $this->drawingTransaction->done_revised = false;
        $this->drawingTransaction->save();

        $drawingTransactionStep = $this->drawingTransactionStepService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::REJECT,
            $data->reason
        );

        $this->drawingTransactionRejectedImageService->createRejectedImages(
            $this->drawingTransaction->id,
            $drawingTransactionStep->id,
            $this->drawingTransaction->filepath
        );
    }
}
