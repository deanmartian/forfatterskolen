<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagicLink extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'email', 'token', 'expires_at', 'used_at', 'created_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
