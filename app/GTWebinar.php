<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GTWebinar extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'go_to_webinars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'gt_webinar_key', 'webinar_date', 'reminder_date', 'send_reminder', 'reminder_email',
        'confirmation_email'];
}
