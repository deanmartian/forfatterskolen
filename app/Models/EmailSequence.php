<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequence extends Model
{
    protected $fillable = [
        'name', 'trigger_event', 'description',
        'from_type', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(EmailSequenceStep::class, 'sequence_id')->orderBy('step_number');
    }

    public function queueItems(): HasMany
    {
        return $this->hasMany(EmailAutomationQueue::class, 'sequence_id');
    }

    /**
     * Hent riktig fra-adresse basert på from_type
     */
    public function getFromAddress(): string
    {
        return $this->from_type === 'newsletter'
            ? config('mail.newsletter_from.address', 'post@nyhetsbrev.forfatterskolen.no')
            : config('mail.from.address', 'post@forfatterskolen.no');
    }

    /**
     * Hent riktig fra-navn basert på from_type
     */
    public function getFromName(): string
    {
        return $this->from_type === 'newsletter'
            ? config('mail.newsletter_from.name', 'Forfatterskolen')
            : config('mail.from.name', 'Forfatterskolen');
    }
}
