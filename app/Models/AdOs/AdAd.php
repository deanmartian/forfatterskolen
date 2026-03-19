<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdAd extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'platform_meta' => 'array',
    ];

    public function adSet()
    {
        return $this->belongsTo(AdAdSet::class, 'ad_set_id');
    }

    public function creative()
    {
        return $this->belongsTo(AdCreative::class, 'creative_id');
    }

    public function metrics()
    {
        return $this->hasMany(AdMetricSnapshot::class, 'reference_id')
            ->where('level', 'ad');
    }
}
