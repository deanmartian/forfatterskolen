<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FreeManuscript
 * @package App
 * @mixin \Eloquent
 */
class FreeManuscript extends Model
{
    
    protected $table = 'free_manuscripts';
    protected $fillable = ['name', 'email', 'content', 'editor_id', 'genre'];


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

}
