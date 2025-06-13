<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingPlanQuestion extends Model
{
    protected $fillable = ['marketing_plan_id', 'main_question', 'sub_question'];

    protected $appends = ['sub_question_decoded'];

    public function getSubQuestionDecodedAttribute()
    {
        return json_decode($this->attributes['sub_question']);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(\App\MarketingPlanQuestionAnswer::class, 'question_id');
    }
}
