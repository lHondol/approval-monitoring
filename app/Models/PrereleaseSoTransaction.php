<?php

namespace App\Models;

use App\Enums\StatusPrereleaseSoTransaction;
use App\Interfaces\PrereleaseSoTransactionState;
use App\States\DrawingTransaction\WaitingForBomApprovalState;
use App\States\PrereleaseSoTransaction\FinalState;
use App\States\PrereleaseSoTransaction\ReviseNeededState;
use App\States\PrereleaseSoTransaction\WaitingForAccountingApprovalState;
use App\States\PrereleaseSoTransaction\WaitingForITApprovalState;
use App\States\PrereleaseSoTransaction\WaitingForMKTStaffFinalizeState;
use App\States\PrereleaseSoTransaction\WaitingForRnDBomApprovalState;
use App\States\PrereleaseSoTransaction\WaitingForRnDDrawingApprovalState;
use App\States\PrereleaseSoTransaction\WaitingForSalesAreaApprovalState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PrereleaseSoTransaction extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'finalized_at' => 'datetime'
    ];

    public function steps() {
        return $this->hasMany(PrereleaseSoTransactionStep::class);
    }

    private PrereleaseSoTransactionState $state;

    public function getStateAttribute()
    {
        return match ($this->status) {
            StatusPrereleaseSoTransaction::WAITING_SALES_AREA_APPROVAL->value   => new WaitingForSalesAreaApprovalState($this),
            StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value   => new WaitingForRnDDrawingApprovalState($this),
            StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value   => new WaitingForRnDBomApprovalState($this),
            StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value   => new WaitingForAccountingApprovalState($this),
            StatusPrereleaseSoTransaction::WAITING_IT_APPROVAL->value   => new WaitingForITApprovalState($this),
            StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_FINALIZE->value   => new WaitingForMKTStaffFinalizeState($this),
            StatusPrereleaseSoTransaction::FINALIZED->value   => new FinalState($this),
            StatusPrereleaseSoTransaction::REVISE_NEEDED->value   => new ReviseNeededState($this),
        };
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function area() {
        return $this->belongsTo(Area::class);
    }
}
