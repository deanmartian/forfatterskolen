<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    protected $table = 'webinars';
    protected $fillable = ['course_id', 'title', 'description', 'start_date', 'image' , 'link', 'set_as_replay', 'status'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }



    public function webinar_presenters()
    {
        return $this->hasMany('App\WebinarPresenter');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }


}
