<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaignStat extends Model
{
    protected $fillable = [
        'ad_campaign_id', 'date', 'impressions', 'clicks', 'leads', 'spend', 'cpl',
    ];

    protected $casts = [
        'date' => 'date',
        'spend' => 'decimal:2',
        'cpl' => 'decimal:2',
    ];

    public $timestamps = false;

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }
}
