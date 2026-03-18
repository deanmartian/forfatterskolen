<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequenceStep extends Model
{
    protected $fillable = [
        'sequence_id', 'step_number', 'subject', 'body_html',
        'delay_hours', 'send_time', 'from_type', 'only_without_active_course',
    ];

    protected $casts = [
        'only_without_active_course' => 'boolean',
        'delay_hours' => 'integer',
        'step_number' => 'integer',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class, 'sequence_id');
    }

    /**
     * Hent riktig fra-adresse (stegets from_type overskriver sekvensens)
     */
    public function getFromAddress(): string
    {
        return $this->from_type === 'newsletter'
            ? config('mail.newsletter_from.address', 'post@nyhetsbrev.forfatterskolen.no')
            : config('mail.from.address', 'post@forfatterskolen.no');
    }

    public function getFromName(): string
    {
        return $this->from_type === 'newsletter'
            ? config('mail.newsletter_from.name', 'Forfatterskolen')
            : config('mail.from.name', 'Forfatterskolen');
    }
}
