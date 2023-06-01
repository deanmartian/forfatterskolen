<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarEditor extends Model
{
    Protected $fillable = ['editor_id', 'webinar_id', 'name', 'presenter_url'];

    public function editor()
    {
        return $this->belongsTo('App\User');
    }
    public function webinar()
    {
        return $this->belongsTo('App\Webinar');
    }
}
