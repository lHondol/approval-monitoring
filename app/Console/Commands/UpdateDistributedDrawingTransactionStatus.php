<?php

namespace App\Console\Commands;

use App\Models\DrawingTransaction;
use Illuminate\Console\Command;

class UpdateDistributedDrawingTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-distributed-drawing-transaction-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update distributed drawing transaction status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DrawingTransaction::where('status', 'Distributed')->update([
            'status' => 'Distributed, Waiting for BOM Approval'
        ]);
    }
}
