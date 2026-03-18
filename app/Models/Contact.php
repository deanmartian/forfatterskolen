<?php

namespace App\Models;

use App\CoursesTaken;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'user_id', 'source', 'status',
        'unsubscribed_at', 'bounced_at', 'ac_id',
    ];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    // --- Relasjoner ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(ContactTag::class);
    }

    public function automationQueue(): HasMany
    {
        return $this->hasMany(EmailAutomationQueue::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(EmailAutomationExclusion::class);
    }

    public function newsletterSends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    // --- Scopes ---

    public function scopeSubscribed($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithTag($query, string $tag)
    {
        return $query->whereHas('tags', fn ($q) => $q->where('tag', $tag));
    }

    public function scopeWithoutTag($query, string $tag)
    {
        return $query->whereDoesntHave('tags', fn ($q) => $q->where('tag', $tag));
    }

    // --- Hjelpemetoder ---

    public function hasTag(string $tag): bool
    {
        return $this->tags()->where('tag', $tag)->exists();
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Sjekk om kontakten har aktivt kurs (via user_id → CoursesTaken)
     */
    public function hasActiveCourse(): bool
    {
        if (! $this->user_id) {
            return false;
        }

        return CoursesTaken::where('user_id', $this->user_id)
            ->where('is_active', 1)
            ->exists();
    }

    /**
     * Sjekk om kontakten har kurs 17 (mentormøter) — permanent salgsmail-ekskludering
     */
    public function hasCourse17(): bool
    {
        if (! $this->user_id) {
            return false;
        }

        return CoursesTaken::where('user_id', $this->user_id)
            ->where('package_id', 17)
            ->exists();
    }

    /**
     * Er kontakten ekskludert fra salgsmail?
     */
    public function isExcludedFromSales(): bool
    {
        if ($this->status !== 'active') {
            return true;
        }

        if ($this->exclusions()->exists()) {
            return true;
        }

        if ($this->hasActiveCourse()) {
            return true;
        }

        return false;
    }
}
