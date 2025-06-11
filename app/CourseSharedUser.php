<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSharedUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'courses_shared_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'course_shared_id'];
}
