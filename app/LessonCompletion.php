<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    protected $table = 'lesson_completions';

    protected $fillable = ['user_id', 'lesson_id', 'course_id', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(\App\Lesson::class);
    }
}
