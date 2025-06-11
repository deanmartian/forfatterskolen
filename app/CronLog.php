<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'courses_email_out';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['activity'];
}
