<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class FreeCourse extends Model
{
    use Loggable;

    protected $table = 'free_courses';

    protected $fillable = ['title', 'description', 'course_image', 'url'];

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
