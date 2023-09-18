<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseDiscount extends Model
{
    protected $fillable = ['course_id', 'coupon', 'discount', 'valid_from', 'valid_to', 'type'];

    protected $types = [
        0 => 'Additional',
        1 => 'Total'
    ];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function typeList()
    {
        return $this->types;
    }
}