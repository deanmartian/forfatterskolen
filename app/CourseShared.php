<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseShared extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'courses_shared';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'package_id', 'hash'];

    public function course()
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function package()
    {
        return $this->belongsTo(\App\Package::class);
    }
}
