<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class WebinarScheduledRegistration extends Model
{
    use Loggable;

    protected $fillable = ['webinar_id', 'date'];

    public function webinar()
    {
        return $this->belongsTo('App\Webinar');
    }
}
