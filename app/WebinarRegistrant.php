<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarRegistrant extends Model {

    protected $table = 'webinar_registrants';
    protected $fillable = ['webinar_id', 'user_id', 'join_url'];

}