<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $table = 'workshops';

    protected $fillable = ['course_id', 'title', 'description', 'price', 'image', 'date', 'faktura_date', 'duration', 'seats',
        'location', 'gmap', 'fiken_product', 'email_title', 'email_body'];

    public function course()
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function presenters()
    {
        return $this->hasMany(\App\WorkshopPresenter::class)->orderBy('created_at', 'desc');
    }

    public function taken()
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function menus()
    {
        return $this->hasMany(\App\WorkshopMenu::class)->orderBy('created_at', 'desc');
    }

    public function attendees()
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function emailLog()
    {
        return $this->hasMany(\App\WorkshopEmailLog::class)->orderBy('created_at', 'desc');
    }
}
