<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketingPlan extends Model
{

    protected $fillable = ['name'];

    public function questions()
    {
        return $this->hasMany(MarketingPlanQuestion::class);
    }

}
