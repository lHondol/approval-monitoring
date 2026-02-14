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

class WaitingForMKTMgrConfirmMarginState implements PrereleaseSoTransactionState
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
        $this->prereleaseSoTransaction->status = StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;
        $this->prereleaseSoTransaction->is_margin_confirmed = true;
        $this->prereleaseSoTransaction->save();

        $this->prereleaseSoTransactionStepService->createStep(
            $this->prereleaseSoTransaction, 
            ActionPrereleaseSoTransactionStep::CONFIRM_MARGIN_MKT_MGR,
            $data->reason ?? "Ok, Margin Confirmed"
        );

        $transactionId = $this->prereleaseSoTransaction->id;
        $soNumber = $this->prereleaseSoTransaction->so_number;

        dispatch(function () use ($transactionId, $soNumber) {
            app(EmailService::class)->sendRequestPrereleaseSoApprovalGeneral(
                $transactionId, 
                $soNumber,
                ['accounting_approve_prerelease_so_transaction']
            );
        })->afterResponse();

        return $this->prereleaseSoTransaction;
    }

    public function reject(object $data = null) {
        
    }
}
