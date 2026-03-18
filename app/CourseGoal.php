<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseGoal extends Model
{
    protected $table = 'course_goals';

    protected $fillable = ['user_id', 'courses_taken_id', 'goal'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function coursesTaken(): BelongsTo
    {
        return $this->belongsTo(\App\CoursesTaken::class, 'courses_taken_id');
    }
}
