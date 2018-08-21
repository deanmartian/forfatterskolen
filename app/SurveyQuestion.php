<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model {

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

    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }

    public function answers()
    {
        return $this->hasMany('App\SurveyAnswer');
    }
}