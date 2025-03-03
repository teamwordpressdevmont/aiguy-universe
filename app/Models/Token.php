<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    //
    protected $fillable = [
        'token',
        'expires_at',
        'previous_token',
        'previous_expires_at',
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
