<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentFeedbackNoGroup extends Model
{

    protected $table = 'assignment_feedbacks_no_group';
    protected $fillable = ['assignment_manuscript_id', 'learner_id','feedback_user_id', 'filename', 'is_admin', 'is_active', 'availability'];




    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function manuscript()
    {
        return $this->belongsTo('App\AssignmentManuscript','assignment_manuscript_id','id');
    }

    public function feedbackUser()
    {
        return $this->belongsTo('App\User','feedback_user_id','id');
    }

    public function learner()
    {
        return $this->belongsTo('App\User','learner_id','id');
    }

}
