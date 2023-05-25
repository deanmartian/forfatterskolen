<?php
namespace App;

use App\Http\FrontendHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FreeManuscript
 * @package App
 * @mixin \Eloquent
 */
class FreeManuscript extends Model
{
    
    protected $table = 'free_manuscripts';
    protected $fillable = ['name', 'last_name', 'email', 'content', 'editor_id', 'genre', 'from', 'deadline'];
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

    public function getFollowUpEmailAttribute()
    {
        return DelayedEmail::where('parent', 'free-manuscript-follow-up')
            ->where('parent_id', $this->attributes['id'])->first();
    }

    public function getHasPaidCourseAttribute()
    {
        $hasCourse = false;
        $user = User::where('email', $this->attributes['email'])->first();
        if ($user && $user->coursesTakenNoFree->count()) {
            $hasCourse = true;
        }

        return $hasCourse;
    }

    public function getDeadlineDateAttribute()
    {
        return $this->attributes['deadline'] ? FrontendHelpers::formatDate($this->attributes['deadline'])
            : FrontendHelpers::formatDate(Carbon::parse($this->attributes['created_at'])->addDays(6));
    }

}
