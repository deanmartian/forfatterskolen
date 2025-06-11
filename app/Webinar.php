<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    use Loggable;

    protected $table = 'webinars';

    protected $fillable = [
        'course_id', 'title', 'description', 'host', 'start_date', 'image', 'link', 'set_as_replay', 'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($query) {
            $query->webinar_editors()->delete();
        });
    }

    public function course()
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function registrants()
    {
        return $this->hasMany(\App\WebinarRegistrant::class);
    }

    public function webinar_presenters()
    {
        return $this->hasMany(\App\WebinarPresenter::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeNotReplay($query)
    {
        return $query->where('set_as_replay', '=', 0);
    }

    public function schedule()
    {
        return $this->hasOne(\App\WebinarScheduledRegistration::class);
    }

    public function webinar_editors()
    {
        return $this->hasMany(\App\WebinarEditor::class);
    }
}
