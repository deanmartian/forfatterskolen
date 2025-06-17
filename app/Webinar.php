<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(\App\WebinarRegistrant::class);
    }

    public function webinar_presenters(): HasMany
    {
        return $this->hasMany(\App\WebinarPresenter::class);
    }

    #[Scope]
    protected function active($query)
    {
        return $query->where('status', '=', 1);
    }

    #[Scope]
    protected function notReplay($query)
    {
        return $query->where('set_as_replay', '=', 0);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(\App\WebinarScheduledRegistration::class);
    }

    public function webinar_editors(): HasMany
    {
        return $this->hasMany(\App\WebinarEditor::class);
    }
}
