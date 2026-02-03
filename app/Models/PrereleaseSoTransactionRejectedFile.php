<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PrereleaseSoTransactionRejectedFile extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
}
