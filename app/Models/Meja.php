<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $table = 'meja';

    protected $fillable = ['no_meja', 'qr_uuid'];

    public function activeSessions()
    {
        return $this->hasMany(Order::class, 'table_number', 'no_meja')
                    ->whereIn('status', ['pending', 'confirmed']);
    }
}