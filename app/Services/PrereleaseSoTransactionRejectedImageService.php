<?php

namespace App\Services;

use App\Models\PrereleaseSoTransactionRejectedFile;

class PrereleaseSoTransactionRejectedImageService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createRejectedImages($prereleaseSoTransactionId, $prereleaseSoTransactionStepId, $filepath) {
        $prereleaseSoTransactionRejectedFile = new PrereleaseSoTransactionRejectedFile();
        $prereleaseSoTransactionRejectedFile->prerelease_so_transaction_id = $prereleaseSoTransactionId;
        $prereleaseSoTransactionRejectedFile->prerelease_so_transaction_step_id = $prereleaseSoTransactionStepId;
        $prereleaseSoTransactionRejectedFile->filepath = $filepath;
        $prereleaseSoTransactionRejectedFile->save();

        return $prereleaseSoTransactionRejectedFile;
    }
}
