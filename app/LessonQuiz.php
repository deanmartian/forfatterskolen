<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonQuiz extends Model
{
    protected $table = 'lesson_quizzes';

    protected $fillable = ['lesson_id', 'question', 'options', 'correct_option', 'order'];

    protected $casts = [
        'options' => 'array',
        'correct_option' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(\App\Lesson::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(\App\LessonQuizAnswer::class);
    }
}
