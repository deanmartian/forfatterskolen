<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdCreative extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'headlines' => 'array',
        'descriptions' => 'array',
        'asset_ids' => 'array',
        'performance_score' => 'decimal:2',
        'ai_metadata' => 'array',
    ];

    public function parentCreative()
    {
        return $this->belongsTo(self::class, 'variant_of');
    }

    public function variants()
    {
        return $this->hasMany(self::class, 'variant_of');
    }

    public function ads()
    {
        return $this->hasMany(AdAd::class, 'creative_id');
    }

    public function creativeVariants()
    {
        return $this->hasMany(AdCreativeVariant::class, 'creative_id');
    }

    public function assetLinks()
    {
        return $this->hasMany(AdAssetLink::class, 'creative_id');
    }
}
