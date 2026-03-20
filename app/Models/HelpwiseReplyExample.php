<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpwiseReplyExample extends Model
{
    protected $fillable = [
        'external_message_id',
        'conversation_id',
        'subject',
        'sender_email',
        'reply_body',
        'sent_at',
        'category',
        'body_hash',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
