<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOrderAttachment extends Model
{
    protected $table = 'course_order_attachments';

    protected $guarded = ['id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }
}
