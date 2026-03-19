<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdAdSet extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'daily_budget' => 'decimal:2',
        'targeting' => 'array',
        'platform_meta' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function ads()
    {
        return $this->hasMany(AdAd::class, 'ad_set_id');
    }

    public function metrics()
    {
        return $this->hasMany(AdMetricSnapshot::class, 'reference_id')
            ->where('level', 'ad_set');
    }
}
