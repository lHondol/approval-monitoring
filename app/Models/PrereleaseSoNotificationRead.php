<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PrereleaseSoNotificationRead extends Model
{
    protected $table = 'prerelease_so_notification_reads';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $keyType = 'string';

    protected $fillable = [
        'prerelease_so_transaction_id',
        'user_id',
        'read_at'
    ];

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'read_at' => 'datetime'
    ];
}
