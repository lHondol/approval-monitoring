<?php

namespace App\States\PrereleaseSoTransaction;

use App\Enums\ActionPrereleaseSoTransactionStep;
use App\Enums\StatusPrereleaseSoTransaction;
use App\Interfaces\PrereleaseSoTransactionState;
use App\Models\PrereleaseSoTransaction;
use App\Services\PrereleaseSoTransactionRejectedImageService;
use App\Services\PrereleaseSoTransactionStepService;
use App\Services\EmailService;
use App\Services\PDFService;
use Carbon\Carbon;

class WaitingForMKTStaffFinalizeState implements PrereleaseSoTransactionState
{
    private PrereleaseSoTransaction $prereleaseSoTransaction;
    private PrereleaseSoTransactionStepService $prereleaseSoTransactionStepService;
    private PrereleaseSoTransactionRejectedImageService $prereleaseSoTransactionRejectedImageService;
    private PDFService $pdfService;
    /**
     * Create a new class instance.
     */
    public function __construct(PrereleaseSoTransaction $prereleaseSoTransaction)
    {
        $this->prereleaseSoTransaction = $prereleaseSoTransaction;
        $this->prereleaseSoTransactionStepService = app(PrereleaseSoTransactionStepService::class);
        $this->prereleaseSoTransactionRejectedImageService = app(PrereleaseSoTransactionRejectedImageService::class);
        $this->pdfService = app(PDFService::class);
    }

    public function next(object $data = null) {
        $this->prereleaseSoTransaction->status = StatusPrereleaseSoTransaction::FINALIZED->value;
        $this->prereleaseSoTransaction->finalized_at = Carbon::now();
        $this->prereleaseSoTransaction->save();

        $this->prereleaseSoTransactionStepService->createStep(
            $this->prereleaseSoTransaction, 
            ActionPrereleaseSoTransactionStep::FINALIZE_MKT_STAFF,
            $data->reason ?? "Ok, Finalized"
        );

        return $this->prereleaseSoTransaction;
    }

    public function reject(object $data = null) {
        
    }
}
