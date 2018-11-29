<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseDiscount extends Model
{
    protected $fillable = ['course_id', 'coupon', 'discount', 'valid_from', 'valid_to'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}