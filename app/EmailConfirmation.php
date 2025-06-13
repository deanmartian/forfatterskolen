<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailConfirmation extends Model
{
    protected $fillable = ['user_id', 'email', 'token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
