<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class MarketingPlan extends Model
{
    protected $fillable = ['name'];

    public function questions(): HasMany
    {
        return $this->hasMany(MarketingPlanQuestion::class);
    }
}
