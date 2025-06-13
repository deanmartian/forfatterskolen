<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeCourseDelayedEmail extends Model
{
    protected $table = 'free_course_delayed_email';

    protected $fillable = ['user_id', 'course_id', 'send_at'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
