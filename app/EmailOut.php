<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class EmailOut extends Model
{
    use Loggable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'courses_email_out';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'subject', 'message', 'delay', 'from_name', 'from_email', 'allowed_package',
        'attachment', 'attachment_hash', 'for_free_course', 'send_immediately'];

    protected $appends = ['send_immediately_text'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function recipients()
    {
        return $this->hasMany('App\EmailOutRecipient');
    }

    public function getSendImmediatelyTextAttribute()
    {
        return $this->attributes['send_immediately'] ? 'Yes' : 'No';
    }
}
