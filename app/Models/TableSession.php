<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    protected $table = 'meja_sessions';

    protected $fillable = [
        'meja_id',
        'session_uuid',
        'customer_name',
        'status',
        'started_at',
        'ended_at'
    ];
}