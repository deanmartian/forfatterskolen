<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    protected $fillable = [
        'subject', 'preview_text', 'body_html',
        'from_address', 'from_name', 'segment',
        'status', 'scheduled_at', 'sent_at',
        'total_recipients', 'total_sent', 'total_failed',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    // --- Scopes ---

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    // --- Hjelpemetoder ---

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function pendingSends(): HasMany
    {
        return $this->sends()->where('status', 'pending');
    }

    public function incrementSent(): void
    {
        $this->increment('total_sent');
    }

    public function incrementFailed(): void
    {
        $this->increment('total_failed');
    }
}
