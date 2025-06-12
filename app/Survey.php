<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'survey';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'course_id', 'start_date', 'end_date'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(\App\SurveyQuestion::class);
    }

    public function getResponse()
    {
        if (! empty($this->id)) {
            if (! empty($this->questions)) {
                $questionSubSelects = [];
                foreach ($this->questions as $question) {
                    $questionSubSelects[] = "(select group_concat(answer, ' ') from survey_answer sa
                                          where sa.survey_id = s.id and
                                          sa.survey_question_id = {$question->id}) as question_{$question->id}";
                }
                $questionSubSelectSql = implode(', ', $questionSubSelects);
                $sql = "select s.*, $questionSubSelectSql from survey s where s.id = ".$this->id;

                return \DB::select($sql);
            }
        }

        return false;
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    protected static function boot()
    {
        parent::boot();

        // delete the data from the relation
        static::deleting(function ($survey) { // before delete() method call this
            $survey->questions()->delete(); // delete lesson document
        });
    }
}
