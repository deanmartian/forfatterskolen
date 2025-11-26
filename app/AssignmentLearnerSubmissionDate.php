<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentLearnerSubmissionDate extends Model
{
    protected $table = 'assignment_learner_submission_dates';

    protected $fillable = ['assignment_id', 'user_id', 'submission_date'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}