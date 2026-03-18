<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'platform', 'type', 'name', 'free_webinar_id', 'course_id',
        'status', 'daily_budget', 'external_campaign_id', 'external_adset_id',
        'external_ad_id', 'external_form_id', 'config', 'started_at', 'stopped_at',
    ];

    protected $casts = [
        'config' => 'array',
        'daily_budget' => 'decimal:2',
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    // Platforms
    const PLATFORM_FACEBOOK = 'facebook';
    const PLATFORM_GOOGLE = 'google';

    // Types
    const TYPE_LEAD = 'lead';
    const TYPE_RETARGETING = 'retargeting';
    const TYPE_SEARCH = 'search';
    const TYPE_DISPLAY = 'display';

    // Statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';

    public function webinar()
    {
        return $this->belongsTo(\App\FreeWebinar::class, 'free_webinar_id');
    }

    public function stats()
    {
        return $this->hasMany(AdCampaignStat::class);
    }

    public function latestStats()
    {
        return $this->hasOne(AdCampaignStat::class)->latestOfMany();
    }

    // Aggregerte tall
    public function getTotalSpendAttribute(): float
    {
        return $this->stats()->sum('spend');
    }

    public function getTotalLeadsAttribute(): int
    {
        return (int) $this->stats()->sum('leads');
    }

    public function getTotalClicksAttribute(): int
    {
        return (int) $this->stats()->sum('clicks');
    }

    public function getTotalImpressionsAttribute(): int
    {
        return (int) $this->stats()->sum('impressions');
    }

    public function getCostPerLeadAttribute(): ?float
    {
        $leads = $this->total_leads;
        return $leads > 0 ? round($this->total_spend / $leads, 2) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeFacebook($query)
    {
        return $query->where('platform', self::PLATFORM_FACEBOOK);
    }

    public function scopeGoogle($query)
    {
        return $query->where('platform', self::PLATFORM_GOOGLE);
    }

    public function getPlatformIconAttribute(): string
    {
        return $this->platform === 'facebook' ? 'fa-facebook-square' : 'fa-google';
    }

    public function getPlatformColorAttribute(): string
    {
        return $this->platform === 'facebook' ? '#1877F2' : '#4285F4';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge badge-success">Aktiv</span>',
            'paused' => '<span class="badge badge-warning">Pauset</span>',
            'completed' => '<span class="badge badge-secondary">Avsluttet</span>',
            default => '<span class="badge badge-info">Utkast</span>',
        };
    }
}
