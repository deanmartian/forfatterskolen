<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'daily_budget' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'spent_total' => 'decimal:2',
        'targeting' => 'array',
        'tracking' => 'array',
        'platform_meta' => 'array',
        'published_at' => 'datetime',
        'paused_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(AdAccount::class, 'account_id');
    }

    public function adSets()
    {
        return $this->hasMany(AdAdSet::class, 'campaign_id');
    }

    public function metrics()
    {
        return $this->hasMany(AdMetricSnapshot::class, 'campaign_id');
    }

    public function decisions()
    {
        return $this->hasMany(AdAiDecision::class, 'campaign_id');
    }

    public function experiments()
    {
        return $this->hasMany(AdExperiment::class, 'campaign_id');
    }

    public function latestMetrics()
    {
        return $this->hasOne(AdMetricSnapshot::class, 'campaign_id')
            ->where('level', 'campaign')
            ->latest('date');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canBePublished(): bool
    {
        return in_array($this->status, ['draft', 'approved']);
    }

    public function getHealthAttribute(): string
    {
        $latest = $this->latestMetrics;
        if (!$latest) return 'unknown';

        $strategy = $this->account?->strategyProfile;
        if (!$strategy) return 'unknown';

        if ($strategy->target_cpa && $latest->cpa && $latest->cpa > $strategy->target_cpa * 1.5) return 'critical';
        if ($strategy->target_cpa && $latest->cpa && $latest->cpa > $strategy->target_cpa) return 'warning';
        if ($latest->spend > 0 && $latest->conversions == 0) return 'warning';

        return 'healthy';
    }
}
