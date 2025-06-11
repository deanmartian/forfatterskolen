<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketingPlanQuestionAnswer extends Model
{
    protected $fillable = ['question_id', 'project_id', 'main_answer', 'sub_answer'];

    protected $appends = ['sub_answer_decoded'];

    public function getSubAnswerDecodedAttribute()
    {
        return json_decode($this->attributes['sub_answer']);
    }

    public function question()
    {
        return $this->belongsTo('App\MarketingPlanQuestion', 'question_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id', 'id');
    }
}
