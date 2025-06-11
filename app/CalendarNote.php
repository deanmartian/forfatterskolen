<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarNote extends Model
{
    protected $table = 'calendar_note';

    protected $fillable = ['note', 'from_date', 'to_date', 'course_id'];

    public function getDateAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y') : null;
    }

    public function course()
    {
        return $this->belongsTo(\App\Course::class);
    }
}
