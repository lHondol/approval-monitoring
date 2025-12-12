<?php

namespace App\States\DrawingTransaction;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\Models\DrawingTransaction;
use App\Services\DrawingTransactionStepService;
use App\Services\PDFService;
use Exception;
use Str;

class ReviseNeededState implements DrawingTransactionState
{
    private DrawingTransaction $drawingTransaction;
    private PDFService $pdfService;
    private DrawingTransactionStepService $drawingTransactionStepService;
    /**
     * Create a new class instance.
     */
    public function __construct(DrawingTransaction $drawingTransaction)
    {
        $this->drawingTransaction = $drawingTransaction;
        $this->pdfService = app(PDFService::class);
        $this->drawingTransactionStepService = app(DrawingTransactionStepService::class);
    }

    public function next(object $data = null) {
        $this->drawingTransaction->customer_id = $data->customer;
        $this->drawingTransaction->po_number = $data->po_number;

        $status = StatusDrawingTransaction::WAITING_1ST_APPROVAL->value;
        $this->drawingTransaction->status = $status;
        $this->drawingTransaction->done_revised = true;

        if (isset($data->description))
            $this->drawingTransaction->description = $data->description;


        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$this->drawingTransaction->id}_{$timestamp}.pdf";

        try {
            $mergedFilePath = $this->pdfService->mergeDrawingPdf(
                $data->files, 
                $newFileName
            );
        } catch (Exception $execption) {
            return null;
        }

        $this->drawingTransaction->filepath = $mergedFilePath;

        $this->drawingTransaction->save();

        $this->drawingTransactionStepService->createStep($this->drawingTransaction, ActionDrawingTransactionStep::UPLOAD_REVISED);

        return $this->drawingTransaction;
    }

    public function reject(object $data = null) {

    }
}
