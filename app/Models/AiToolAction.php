<?php

namespace App\Models;

use App\Enums\AiToolActionStatus;
use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiToolAction extends Model
{
    protected $fillable = [
        'conversation_id',
        'inbox_message_id',
        'tool_name',
        'parameters',
        'ui_label',
        'status',
        'suggested_at',
        'executed_at',
        'executed_by_user_id',
        'expires_at',
        'result',
        'error_message',
    ];

    protected $casts = [
        'parameters' => 'array',
        'result' => 'array',
        'status' => AiToolActionStatus::class,
        'suggested_at' => 'datetime',
        'executed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(InboxConversation::class, 'conversation_id');
    }

    public function inboxMessage(): BelongsTo
    {
        return $this->belongsTo(InboxMessage::class, 'inbox_message_id');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by_user_id');
    }

    public function isClickable(): bool
    {
        if (!$this->status->isClickable()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }

    public function scopeSuggested($query)
    {
        return $query->where('status', AiToolActionStatus::Suggested->value);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
