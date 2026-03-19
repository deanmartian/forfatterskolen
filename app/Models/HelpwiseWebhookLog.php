<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpwiseWebhookLog extends Model
{
    protected $fillable = [
        'event_id',
        'conversation_id',
        'sender_email',
        'sender_name',
        'event_type',
        'should_reply',
        'confidence',
        'draft_status',
        'error_message',
        'ai_response',
        'payload',
    ];

    protected $casts = [
        'should_reply' => 'boolean',
        'confidence' => 'decimal:2',
        'ai_response' => 'array',
        'payload' => 'array',
    ];
}
