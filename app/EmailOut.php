<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailOut extends Model
{
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
        'attachment', 'attachment_hash', 'for_free_course'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function recipients()
    {
        return $this->hasMany('App\EmailOutRecipient');
    }
}
