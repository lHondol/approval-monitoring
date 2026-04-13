<?php

namespace App\Models;

use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\States\DrawingTransaction\FinalState;
use App\States\DrawingTransaction\ReviseNeededState;
use App\States\DrawingTransaction\WaitingFor1stApprovalState;
use App\States\DrawingTransaction\WaitingFor2ndApprovalState;
use App\States\DrawingTransaction\WaitingForBomApprovalState;
use App\States\DrawingTransaction\WaitingForCostingApprovalState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SampleTransactionProcess extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    protected $casts = [
        'start_at'     => 'datetime',
        'finish_at'     => 'datetime'
    ];
}
