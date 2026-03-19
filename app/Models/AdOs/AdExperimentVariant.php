<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdExperimentVariant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metrics' => 'array',
        'is_winner' => 'boolean',
        'is_control' => 'boolean',
    ];

    public function experiment()
    {
        return $this->belongsTo(AdExperiment::class, 'experiment_id');
    }

    public function creative()
    {
        return $this->belongsTo(AdCreative::class, 'creative_id');
    }

    public function ad()
    {
        return $this->belongsTo(AdAd::class, 'ad_id');
    }
}
