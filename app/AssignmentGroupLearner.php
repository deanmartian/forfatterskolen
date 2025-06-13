<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssignmentGroupLearner extends Model
{
    use Loggable;

    protected $table = 'assignment_group_learners';

    // could_send_feedback_to - stores the group learner id
    protected $fillable = ['assignment_group_id', 'user_id', 'could_send_feedback_to'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(\App\AssignmentGroup::class, 'assignment_group_id');
    }

    public function getCouldSendFeedbackToIdListAttribute()
    {
        return $this->attributes['could_send_feedback_to'] ? array_map('intval', explode(', ', $this->attributes['could_send_feedback_to'])) : null;
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(\App\AssignmentFeedback::class, 'assignment_group_learner_id', 'id');
    }

    public function learnerManuscript()
    {
        return $this->group->assignment->manuscripts->where('user_id', $this->attributes['user_id'])->first();
    }
}
