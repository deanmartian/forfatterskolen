<?php
namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class AssignmentFeedback extends Model
{
    use Loggable;
    
    protected $table = 'assignment_feedbacks';
    protected $fillable = ['assignment_group_learner_id', 'user_id', 'filename', 'is_admin', 'is_active', 'availability', 'hours_worked', 'notes_to_head_editor'];



    public function assignment_group_learner()
    {
        return $this->belongsTo('App\AssignmentGroupLearner', 'assignment_group_learner_id');
    }



    public function user()
    {
        return $this->belongsTo('App\User');
    }



}
