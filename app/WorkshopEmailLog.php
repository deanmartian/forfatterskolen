<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkshopEmailLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workshop_email_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['workshop_id', 'subject', 'message', 'learners', 'from_name', 'from_email'];

    public function workshop()
    {
        return $this->belongsTo('App\Workshop');
    }

    public function getDateSentAttribute()
    {
        $date = $this->attributes['created_at'];
        return date_format(date_create($date), 'M d, Y h:i a');
    }
}
