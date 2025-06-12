<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'survey_question';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'question_type', 'option_name'];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(\App\Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(\App\SurveyAnswer::class);
    }
}
