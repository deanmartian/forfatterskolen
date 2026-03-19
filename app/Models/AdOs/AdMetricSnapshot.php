<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdMetricSnapshot extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'spend' => 'decimal:2',
        'cpa' => 'decimal:2',
        'roas' => 'decimal:2',
        'ctr' => 'decimal:4',
        'cpc' => 'decimal:2',
        'cpm' => 'decimal:2',
        'revenue' => 'decimal:2',
        'platform_data' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }
}
