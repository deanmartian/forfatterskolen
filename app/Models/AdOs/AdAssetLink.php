<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdAssetLink extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array',
        'performance_score' => 'decimal:2',
        'relevance_score' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function creative()
    {
        return $this->belongsTo(AdCreative::class, 'creative_id');
    }
}
