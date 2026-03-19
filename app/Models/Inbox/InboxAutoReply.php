<?php

namespace App\Models\Inbox;

use Illuminate\Database\Eloquent\Model;

class InboxAutoReply extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
        'use_ai' => 'boolean',
    ];
}
