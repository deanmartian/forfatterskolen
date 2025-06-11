<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRenewedCourse extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_renewed_course';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'course_id'];
}
