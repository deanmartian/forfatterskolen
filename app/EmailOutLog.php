<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOutLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course_email_out_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'subject', 'message', 'learners', 'from_name', 'from_email', 'attachment'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function getDateSentAttribute()
    {
        $date = $this->attributes['created_at'];

        return date_format(date_create($date), 'M d, Y h:i a');
    }
}
