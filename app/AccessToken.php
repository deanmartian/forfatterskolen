<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'access_tokens';

    protected $fillable = ['jti', 'iat', 'exp'];
}
