<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarEmailOut extends Model
{
    protected $table = 'webinar_email_out';
    protected $fillable = ['webinar_id', 'course_id', 'subject', 'send_date', 'message'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Webinar');
    }


}
