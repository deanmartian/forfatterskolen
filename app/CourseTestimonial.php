<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseTestimonial extends Model
{

    protected $fillable = ['name', 'course_id', 'testimony', 'user_image', 'is_video'];


    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}
