<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AreaUser extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    protected $table = 'area_user';
}
