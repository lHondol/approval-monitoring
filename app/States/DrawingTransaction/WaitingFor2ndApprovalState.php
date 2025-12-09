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
use Carbon\Carbon;

class WaitingFor2ndApprovalState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    private DrawingTransactionStepService $drawingTransactionStepService;
    private DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService;
    private PDFService $pdfService;
    private EmailService $emailService;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
        $this->drawingTransactionStepService = app(DrawingTransactionStepService::class);
        $this->drawingTransactionRejectedImageService = app(DrawingTransactionRejectedImageService::class);
        $this->pdfService = app(PDFService::class);
        $this->emailService = app(EmailService::class);
    }

    public function next(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::DISTRIBUTED->value;
        if (isset($data->so_number))
            $this->drawingTransaction->so_number = $data->so_number;
        $this->drawingTransaction->distributed_at = Carbon::now();
        $this->drawingTransaction->save();

        $this->drawingTransactionStepService->createStep(
            $this->drawingTransaction, 
            ActionDrawingTransactionStep::APPROVE2,
            $data->reason ?? "Ok, Approved"
        );

        $this->pdfService->signPdf(
            $this->drawingTransaction->filepath, 
            20,
            255,
            "APPROVED 2 by",
            $this->drawingTransaction->updated_at
        );
    }

    public function reject(object $data = null) {
        $this->drawingTransaction->status = StatusDrawingTransaction::REVISE_NEEDED->value;
        if (isset($data->so_number))
            $this->drawingTransaction->so_number = $data->so_number;
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

        $this->pdfService->signPdf(
            $this->drawingTransaction->filepath, 
            20,
            255,
            "REJECTED by",
            $this->drawingTransaction->updated_at
        );

        dispatch(function () {
            $this->emailService->sendRequestReviseDrawingTransaction($this->drawingTransaction->id);
        })->afterResponse();
    }
}
