<?php

namespace App\Console\Commands;

use App\Models\DrawingTransaction;
use App\Models\PrereleaseSoTransaction;
use Illuminate\Console\Command;

class UpdateReleasedPOTanpaKacaPreleaseSoTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-released-po-tanpa-kaca-prerelease-so-transaction-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update released po tanpa kaca prerelease so transaction status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PrereleaseSoTransaction::where('status', 'Released, PO Tanpa Kaca')->update([
            'status' => 'Released, Tanpa PO Kaca'
        ]);
    }
}
