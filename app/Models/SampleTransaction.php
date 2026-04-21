<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SampleTransaction extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';

    protected $casts = [
        'so_created_at'     => 'datetime',
        'shipment_request'     => 'datetime',
        'picture_receive_at' => 'datetime'
    ];

    public function processes() {
        return $this->hasMany(SampleTransactionProcess::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function latestUnfinishedProcess()
    {
        return $this->hasOne(SampleTransactionProcess::class)
            ->whereNull('finish_at')
            ->latestOfMany('start_at');
    }
}
