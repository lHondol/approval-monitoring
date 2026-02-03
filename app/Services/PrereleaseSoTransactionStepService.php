<?php

namespace App\Services;

use App\Models\PrereleaseSoTransactionStep;

class PrereleaseSoTransactionStepService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createStep($prereleaseSoTransaction, $action, $reason = null) {
        $prereleaseSoTransactionStep = new PrereleaseSoTransactionStep();
        $prereleaseSoTransactionStep->prerelease_so_transaction_id = $prereleaseSoTransaction->id;
        $prereleaseSoTransactionStep->done_by_user = auth()->user()->id;
        $prereleaseSoTransactionStep->done_at = $prereleaseSoTransaction->updated_at;
        $prereleaseSoTransactionStep->action_done = $action;
        $prereleaseSoTransactionStep->reason = $reason;
        $prereleaseSoTransactionStep->save();

        return $prereleaseSoTransactionStep;
    }
}
