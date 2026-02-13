<?php

namespace App\Console\Commands;

use App\Models\PrereleaseSoTransaction;
use Illuminate\Console\Command;

class UpdateFinalizedPrereleaseSoTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-finalized-prerelease-so-transaction-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update finalized prerelease so transaction status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PrereleaseSoTransaction::where('status', 'Finalized')->update([
            'status' => 'Released'
        ]);
    }
}
