<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateGroupDiscussionReply extends Model
{
    protected $fillable = ['disc_id', 'user_id', 'message'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(\App\PrivateGroupDiscussion::class, 'disc_id');
    }
}
