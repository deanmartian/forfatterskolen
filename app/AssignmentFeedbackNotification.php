<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentFeedbackNotification extends Model
{
    protected $fillable = ['user_id', 'assignment_feedback_id', 'availability'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedback()
    {
        return $this->belongsTo(AssignmentFeedback::class, 'assignment_feedback_id', 'id');
    }
}
