<?php

namespace App\States\DrawingTransaction;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;
use App\Services\DrawingTransactionRejectedImageService;
use App\Services\DrawingTransactionService;
use App\Services\DrawingTransactionStepService;
use App\Services\EmailService;
use App\Services\PDFService;

class WaitingForCostingApprovalState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    private DrawingTransactionStepService $drawingTransactionStepService;
    private DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService;
    private PDFService $pdfService;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
        $this->drawingTransactionStepService = app(DrawingTransactionStepService::class);
        $this->drawingTransactionRejectedImageService = app(DrawingTransactionRejectedImageService::class);
        $this->pdfService = app(PDFService::class);
    }

    public function next(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::DISTRIBUTED_COSTING_DONE->value;
        $this->drawingTransaction->save();

        $this->drawingTransactionStepService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::APPROVE_COSTING,
            $data->reason ?? "Ok, Approved"
        );

        return $this->drawingTransaction;
    }

    public function reject(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::REVISE_NEEDED->value;
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

        $transactionId = $this->drawingTransaction->id;
        $soNumber = $this->drawingTransaction->so_number;
        
        dispatch(function () use ($transactionId, $soNumber) {
            app(EmailService::class)->sendRejectNoticeDrawingTransaction(
                $transactionId,
                $soNumber
            );
        })->afterResponse();

        return $this->drawingTransaction;
    }
}
