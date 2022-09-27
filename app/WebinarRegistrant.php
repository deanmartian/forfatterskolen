<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarRegistrant extends Model {

    protected $table = 'webinar_registrants';
    protected $fillable = ['webinar_id', 'user_id', 'join_url'];
    protected $with = ['user'];

    public function webinar()
    {
        return $this->belongsTo('App\Webinar');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}