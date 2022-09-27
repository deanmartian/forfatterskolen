<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentGroupLearner extends Model
{
    
    protected $table = 'assignment_group_learners';
    // could_send_feedback_to - stores the group learner id
    protected $fillable = ['assignment_group_id', 'user_id', 'could_send_feedback_to'];




    public function user()
    {
        return $this->belongsTo('App\User');
    }



    public function group()
    {
        return $this->belongsTo('App\AssignmentGroup', 'assignment_group_id');
    }

    public function getCouldSendFeedbackToIdListAttribute()
    {
        return $this->attributes['could_send_feedback_to'] ? array_map('intval',explode(', ', $this->attributes['could_send_feedback_to'])) : NULL;
    }

}
