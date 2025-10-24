<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'attachment', 'attachment_hash', 'for_free_course', 'send_immediately', 'send_to_learners_no_course',
        'send_to_learners_with_unpaid_pay_later'];

    protected $appends = ['send_immediately_text'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(\App\EmailOutRecipient::class);
    }

    public function getSendImmediatelyTextAttribute()
    {
        return $this->attributes['send_immediately'] ? 'Yes' : 'No';
    }
}
