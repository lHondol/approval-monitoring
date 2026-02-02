<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    public function users() {
        return $this->belongsToMany(User::class , 'area_user');
    }
}
