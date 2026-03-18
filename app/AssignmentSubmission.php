<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $table = 'assignment_submissions';

    protected $fillable = [
        'lesson_assignment_id', 'user_id', 'answer_text',
        'ai_feedback', 'approved_feedback', 'status',
        'approved_by', 'approved_at', 'seen_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'seen_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\LessonAssignment::class, 'lesson_assignment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }
}
