<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoursesTaken extends Model
{
    protected $table = 'courses_taken';

    protected $fillable = ['user_id', 'package_id', 'is_active', 'started_at', 'start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function manuscripts()
    {
        return $this->hasMany('App\Manuscript', 'coursetaken_id')->orderBy('created_at', 'desc');
    }

    public function getStartedAtAttribute()
    {
        return date_format(date_create($this->attributes['started_at']), 'M d, Y h:i a');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getStartDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getEndDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getHasStartedAttribute()
    {
        return ! empty($this->attributes['started_at']);
    }

    public function getHasEndedAttribute()
    {
        if ($this->attributes['started_at']) {
            $date = \Carbon\Carbon::parse($this->attributes['started_at']);

            return $date->diffInYears() >= 1;
        }

        return false;
    }

    public function getAccessLessonsAttribute($value)
    {
        return json_decode($value);
    }
}
