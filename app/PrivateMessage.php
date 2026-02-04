<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessage extends Model
{
    protected $fillable = ['user_id', 'from_user', 'message'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user');
    }
}
