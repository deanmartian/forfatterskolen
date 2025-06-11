<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseOrderAttachment extends Model
{
    protected $table = 'course_order_attachments';

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }
}
