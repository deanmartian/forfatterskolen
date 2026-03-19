<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdAccount extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'sync_state' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function strategyProfile()
    {
        return $this->belongsTo(AdStrategyProfile::class, 'strategy_profile_id');
    }

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class, 'account_id');
    }

    public function syncRuns()
    {
        return $this->hasMany(AdSyncRun::class, 'account_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
