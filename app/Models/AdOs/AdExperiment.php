<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdExperiment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'results_summary' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function variants()
    {
        return $this->hasMany(AdExperimentVariant::class, 'experiment_id');
    }

    public function winner()
    {
        return $this->belongsTo(AdExperimentVariant::class, 'winner_variant_id');
    }
}
