<?php

namespace App\States\PrereleaseSoTransaction;

use App\Interfaces\PrereleaseSoTransactionState;
use App\Models\PrereleaseSoTransaction;

class FinalState implements PrereleaseSoTransactionState
{
    private PrereleaseSoTransaction $prereleaseSoTransaction;
    /**
     * Create a new class instance.
     */
    public function __construct(PrereleaseSoTransaction $prereleaseSoTransaction)
    {
        $this->prereleaseSoTransaction = $prereleaseSoTransaction;
    }

    public function next(object $data = null) {

    }

    public function reject(object $data = null) {

    }
}
