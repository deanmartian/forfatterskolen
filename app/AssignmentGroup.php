<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
    
    protected $table = 'assignment_groups';
    protected $fillable = ['assignment_id', 'title', 'submission_date', 'allow_feedback_download'];



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

}
