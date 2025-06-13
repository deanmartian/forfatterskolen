<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestimonial extends Model
{
    protected $fillable = ['name', 'course_id', 'testimony', 'user_image', 'is_video'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
