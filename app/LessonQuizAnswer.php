<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonQuizAnswer extends Model
{
    protected $table = 'lesson_quiz_answers';

    protected $fillable = ['user_id', 'lesson_quiz_id', 'selected_option', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(\App\LessonQuiz::class, 'lesson_quiz_id');
    }
}
