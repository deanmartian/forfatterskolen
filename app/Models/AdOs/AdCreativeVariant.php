<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdCreativeVariant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'performance_score' => 'decimal:2',
        'metrics_snapshot' => 'array',
    ];

    public function creative()
    {
        return $this->belongsTo(AdCreative::class, 'creative_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function experiment()
    {
        return $this->belongsTo(AdExperiment::class, 'experiment_id');
    }
}
