<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseExpiryReminder extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course_expiration_reminder';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'subject', 'message'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}