<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'survey_answer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['answer'];

    public function question()
    {
        return $this->belongsTo('App\SurveyQuestion');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}