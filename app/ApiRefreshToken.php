<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiRefreshToken extends Model
{
    protected $table = 'api_refresh_tokens';

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
