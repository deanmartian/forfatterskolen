<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarCourse extends Model
{
    protected $table = 'similar_courses';

    protected $fillable = ['course_id', 'similar_course_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function similar_course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class, 'similar_course_id');
    }
}
