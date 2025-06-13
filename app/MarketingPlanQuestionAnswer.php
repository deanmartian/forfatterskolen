<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingPlanQuestionAnswer extends Model
{
    protected $fillable = ['question_id', 'project_id', 'main_answer', 'sub_answer'];

    protected $appends = ['sub_answer_decoded'];

    public function getSubAnswerDecodedAttribute()
    {
        return json_decode($this->attributes['sub_answer']);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(\App\MarketingPlanQuestion::class, 'question_id', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Project::class, 'project_id', 'id');
    }
}
