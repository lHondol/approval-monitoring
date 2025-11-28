<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DrawingTransactionRejectedFile extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    public function step() {
        return $this->belongsTo(DrawingTransactionStep::class);
    }
}
