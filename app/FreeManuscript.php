<?php
namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FreeManuscript
 * @package App
 * @mixin \Eloquent
 */
class FreeManuscript extends Model
{
    
    protected $table = 'free_manuscripts';
    protected $fillable = ['name', 'email', 'content', 'editor_id', 'genre', 'deadline'];
    protected $appends = ['deadline_date'];


    public function editor()
    {
    	return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    public function latestFeedbackHistory()
    {
        return $this->hasOne('App\FreeManuscriptFeedbackHistory')->latest();
    }

    public function feedbackHistory()
    {
        return $this->hasMany('App\FreeManuscriptFeedbackHistory');
    }

    public function getDeadlineDateAttribute()
    {
        return $this->attributes['deadline'] ? FrontendHelpers::formatDate($this->attributes['deadline'])
            : FrontendHelpers::formatDate($this->attributes['created_at']);
    }

}
