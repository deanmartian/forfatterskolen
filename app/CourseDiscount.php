<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseDiscount extends Model
{
    protected $fillable = ['course_id', 'coupon', 'discount'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}