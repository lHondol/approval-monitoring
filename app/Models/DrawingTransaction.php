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

class DrawingTransaction extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'distributed_at' => 'datetime'
    ];

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
            StatusDrawingTransaction::DISTRIBUTED_WAITING_BOM_APPROVAL->value   => new WaitingForBomApprovalState($this),
            StatusDrawingTransaction::DISTRIBUTED_WAITING_COSTING_APPROVAL->value   => new WaitingForCostingApprovalState($this),
            StatusDrawingTransaction::DISTRIBUTED_BOM_REJECTED->value   => new FinalState($this),
            StatusDrawingTransaction::DISTRIBUTED_COSTING_REJECTED->value   => new FinalState($this),
            StatusDrawingTransaction::DISTRIBUTED_COSTING_DONE->value   => new FinalState($this),
        };
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}
