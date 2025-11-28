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

    public function rejected_file() {
        return $this->hasOne(DrawingTransactionRejectedFile::class);
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'done_by_user');
    }
}
