<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DrawingTransactionStep extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    public function transaction() {
        return $this->belongsTo(DrawingTransaction::class);
    }

    public function rejectedFile() {
        return $this->hasOne(DrawingTransactionRejectedFile::class);
    }
}
