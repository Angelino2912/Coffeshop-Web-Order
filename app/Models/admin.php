<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    // nama tabel di database
    protected $table = 'admin'; 
    protected $fillable = ['name', 'email', 'password'];
}