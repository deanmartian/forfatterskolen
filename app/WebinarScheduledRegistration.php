<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarScheduledRegistration extends Model
{

    protected $fillable = ['webinar_id', 'date'];

    public function webinar()
    {
        return $this->belongsTo('App\Webinar');
    }

}
