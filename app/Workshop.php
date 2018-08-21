<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $table = 'workshops';
    protected $fillable = ['course_id', 'title', 'description', 'price', 'image', 'date', 'duration', 'seats',
        'location', 'gmap', 'fiken_product', 'email_title', 'email_body'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }


    public function presenters()
    {
        return $this->hasMany('App\WorkshopPresenter')->orderBy('created_at', 'desc');
    }


    public function taken()
    {
        return $this->hasMany('App\WorkshopsTaken')->orderBy('created_at', 'desc');
    }




    public function menus()
    {
        return $this->hasMany('App\WorkshopMenu')->orderBy('created_at', 'desc');
    }


    public function attendees()
    {
        return $this->hasMany('App\WorkshopsTaken')->orderBy('created_at', 'desc');
    }
}
