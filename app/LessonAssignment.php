<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonAssignment extends Model
{
    protected $table = 'lesson_assignments';

    protected $fillable = ['lesson_id', 'question_text', 'order'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(\App\Lesson::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(\App\AssignmentSubmission::class);
    }
}
