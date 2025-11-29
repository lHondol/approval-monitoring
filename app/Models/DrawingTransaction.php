<?php

namespace App\Models;

use App\Enums\StatusDrawingTransaction;
use App\Interfaces\DrawingTransactionState;
use App\States\DrawingTransaction\DistributedState;
use App\States\DrawingTransaction\ReviseNeededState;
use App\States\DrawingTransaction\WaitingFor1stApprovalState;
use App\States\DrawingTransaction\WaitingFor2ndApprovalState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DrawingTransaction extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    public function steps() {
        return $this->hasMany(DrawingTransactionStep::class);
    }

    private DrawingTransactionState $state;

    public function getStateAttribute()
    {
        return match ($this->status) {
          StatusDrawingTransaction::WAITING_1ST_APPROVAL->value  => new WaitingFor1stApprovalState($this),
          StatusDrawingTransaction::WAITING_2ND_APPROVAL->value   => new WaitingFor2ndApprovalState($this),
          StatusDrawingTransaction::REVISE_NEEDED->value   => new ReviseNeededState($this),
          StatusDrawingTransaction::DISTRIBUTED->value   => new DistributedState($this),
        };
    }
}
