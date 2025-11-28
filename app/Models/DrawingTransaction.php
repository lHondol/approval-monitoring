<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DrawingTransaction extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    public function steps() {
        return $this->hasMany(DrawingTransactionStep::class);
    }
}
