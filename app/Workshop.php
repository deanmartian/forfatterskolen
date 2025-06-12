<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    protected $table = 'workshops';

    protected $fillable = ['course_id', 'title', 'description', 'price', 'image', 'date', 'faktura_date', 'duration', 'seats',
        'location', 'gmap', 'fiken_product', 'email_title', 'email_body'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function presenters(): HasMany
    {
        return $this->hasMany(\App\WorkshopPresenter::class)->orderBy('created_at', 'desc');
    }

    public function taken(): HasMany
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(\App\WorkshopMenu::class)->orderBy('created_at', 'desc');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function emailLog(): HasMany
    {
        return $this->hasMany(\App\WorkshopEmailLog::class)->orderBy('created_at', 'desc');
    }
}
