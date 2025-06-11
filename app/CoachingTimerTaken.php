<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoachingTimerTaken extends Model
{
    protected $table = 'coaching_timer_taken';

    protected $fillable = ['user_id', 'course_taken_id'];
}
