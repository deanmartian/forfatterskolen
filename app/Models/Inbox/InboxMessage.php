<?php

namespace App\Models\Inbox;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InboxMessage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_ai_draft' => 'boolean',
        'is_draft' => 'boolean',
        'ai_confidence' => 'decimal:2',
        'attachments' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(InboxConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }

    public function getCleanBodyAttribute(): string
    {
        return strip_tags($this->body_plain ?? $this->body ?? '');
    }

    public function aiToolActions()
    {
        return $this->hasMany(\App\Models\AiToolAction::class, 'inbox_message_id');
    }
}
