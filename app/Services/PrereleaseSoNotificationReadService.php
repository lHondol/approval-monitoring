<?php

namespace App\Services;

use App\Models\PrereleaseSoNotificationRead;
use Exception;

class PrereleaseSoNotificationReadService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function markAsRead($transactionId) {
        PrereleaseSoNotificationRead::updateOrCreate(
            [
                'prerelease_so_transaction_id' => $transactionId,
                'user_id' => auth()->user()->id,
            ],
            [
                'read_at' => now(),
            ]
        );
    }
}
