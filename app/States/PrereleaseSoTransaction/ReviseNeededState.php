<?php

namespace App\States\PrereleaseSoTransaction;

use App\Enums\ActionPrereleaseSoTransactionStep;
use App\Enums\StatusPrereleaseSoTransaction;
use App\Interfaces\PrereleaseSoTransactionState;
use App\Models\PrereleaseSoTransaction;
use App\Services\PrereleaseSoTransactionStepService;
use App\Services\PDFService;
use Carbon\Carbon;
use Exception;
use Str;

class ReviseNeededState implements PrereleaseSoTransactionState
{
    private PrereleaseSoTransaction $prereleaseSoTransaction;
    private PDFService $pdfService;
    private PrereleaseSoTransactionStepService $prereleaseSoTransactionStepService;
    /**
     * Create a new class instance.
     */
    public function __construct(PrereleaseSoTransaction $prereleaseSoTransaction)
    {
        $this->prereleaseSoTransaction = $prereleaseSoTransaction;
        $this->pdfService = app(PDFService::class);
        $this->prereleaseSoTransactionStepService = app(PrereleaseSoTransactionStepService::class);
    }

    public function next(object $data = null) {
        $this->prereleaseSoTransaction->customer_id = $data->customer;
        // $this->prereleaseSoTransaction->area_id = $data->area;
        $this->prereleaseSoTransaction->so_number = $data->so_number;
        $this->prereleaseSoTransaction->po_number = $data->po_number;

        $target = Carbon::createFromFormat('Y-m', $data->target_shipment);

        $this->prereleaseSoTransaction->target_shipment_year = $target->year;
        $this->prereleaseSoTransaction->target_shipment_month = $target->month;

        $this->prereleaseSoTransaction->is_urgent = $data->is_urgent ?? 0;

        $status = StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value;
        $this->prereleaseSoTransaction->status = $status;
        $this->prereleaseSoTransaction->done_revised = true;

        if (isset($data->description))
            $this->prereleaseSoTransaction->description = $data->description;
    
        $revisedAt = Carbon::now();

        $timestamp = now()->format('Ymd_His');
        $revisedAt = $revisedAt->format('d M Y H:i:s');

        $newFileName = "{$this->prereleaseSoTransaction->id}_{$timestamp}.pdf";


        $note = "Created At: {$revisedAt}";

        try {
            $mergedFilePath = $this->pdfService->mergePdfWithNote(
                $data->files, 
                $newFileName,
                10, 
                10, 
                $note, 
                "prerelease-so-pdfs"
            );
        } catch (Exception $execption) {
            return null;
        }

        $this->prereleaseSoTransaction->filepath = $mergedFilePath;
        $this->prereleaseSoTransaction->updated_at = $revisedAt;
        $this->prereleaseSoTransaction->save();

        $this->prereleaseSoTransactionStepService->createStep($this->prereleaseSoTransaction, ActionPrereleaseSoTransactionStep::UPLOAD_REVISED);

        return $this->prereleaseSoTransaction;
    }

    public function reject(object $data = null) {

    }
}
