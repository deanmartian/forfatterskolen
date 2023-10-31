<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
    
    protected $table = 'assignment_groups';
    protected $fillable = ['assignment_id', 'title', 'submission_date', 'allow_feedback_download'];
    protected $appends = [
        'submission_date_time_text'
    ];


    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }


    public function learners()
    {
        return $this->hasMany('App\AssignmentGroupLearner')->orderBy('created_at', 'desc');
    }

    public function getSubmissionDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i A') : NULL;
    }

    public function getSubmissionDateTimeTextAttribute()
    {
        $submission_date = $this->attributes['submission_date'];
        return ucwords(strtr(trans('site.learner.submission-date-value'), [
            '_date_' => \Carbon\Carbon::parse($submission_date)->format('d.m.Y'),
             '_time_' => \Carbon\Carbon::parse($submission_date)->format('H:i')
        ]));
    }

}
