<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivateGroupDiscussion extends Model
{
    protected $fillable = ['private_group_id', 'user_id', 'subject', 'message', 'is_announcement'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupDiscussionReply::class, 'disc_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }
}
