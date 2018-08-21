<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimilarCourse extends Model
{
    protected $table = 'similar_courses';
    protected $fillable = ['course_id', 'similar_course_id'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }


    public function similar_course()
    {
        return $this->belongsTo('App\Course', 'similar_course_id');
    }
}
