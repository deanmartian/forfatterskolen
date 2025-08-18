<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursesTaken extends Model
{
    use Loggable;
    use SoftDeletes;

    protected $table = 'courses_taken';

    protected $fillable = ['user_id', 'package_id', 'gift_purchase_id', 'is_active', 'started_at', 'start_date',
        'end_date', 'access_lessons', 'years', 'is_free', 'send_expiry_reminder', 'is_welcome_email_sent',
        'can_receive_email', 'is_pay_later', 'exclude_in_scheduled_registration', 'in_facebook_group',
        'disable_start_date', 'disable_end_date', 'created_at', 'updated_at'];

    protected $appends = ['order'];

    protected function casts(): array
    {
        return [
            'renewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function manuscripts(): HasMany
    {
        return $this->hasMany(\App\Manuscript::class, 'coursetaken_id')->orderBy('created_at', 'desc');
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i a') : null;
    }

    public function getStartedAtValueAttribute()
    {
        return $this->attributes['started_at'];
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getCreatedAtValueAttribute()
    {
        return $this->attributes['created_at'];
    }

    public function getStartDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getStartDateValueAttribute()
    {
        return $this->attributes['start_date'] ?: null;
    }

    public function getEndDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getEndDateValueAttribute()
    {
        return $this->attributes['end_date'] ?: null;
    }

    public function getEndDateWithValueAttribute()
    {
        if (! $this->attributes['end_date']) {
            $date = \Carbon\Carbon::parse($this->attributes['started_at']);

            return $date->addYear(1);
        } else {
            return date_format(date_create($this->attributes['end_date']), 'M d, Y');
        }
    }

    public function getHasStartedAttribute()
    {
        return ! empty($this->attributes['started_at']);
    }

    /*
     * this is the original code
     * public function getHasEndedAttribute()
    {
        if( $this->attributes['started_at'] ) :
            $date = \Carbon\Carbon::parse($this->attributes['started_at']);
            return $date->diffInYears() >= 1;
        endif;

        return false;
    }*/

    public function getHasEndedAttribute()
    {
        if (! $this->attributes['end_date']) {
            $date = \Carbon\Carbon::parse($this->attributes['started_at']);

            return $date->diffInYears() >= 1;
        } else {
            $date = \Carbon\Carbon::parse($this->attributes['end_date'])->format('Y-m-d');
            $now = \Carbon\Carbon::now()->format('Y-m-d');
            if ($now >= $date) {
                return true;
            }
        }

        return false;
    }

    public function getIsDisabledAttribute(): bool
    {
        $now   = \Carbon\Carbon::now();
        $start = $this->disable_start_date
            ? \Carbon\Carbon::parse($this->disable_start_date)->startOfDay()
            : null;
        $end   = $this->disable_end_date
            ? \Carbon\Carbon::parse($this->disable_end_date)->endOfDay()
            : null;

        // Both null → not disabled
        if (!$start && !$end) {
            return false;
        }

        // Only start set → disabled from start onward
        if ($start && !$end) {
            return $now->greaterThanOrEqualTo($start);
        }

        // Only end set → disabled until end (inclusive), enabled after
        if (!$start && $end) {
            return $now->lessThanOrEqualTo($end);
        }

        // Both set → disabled only between start and end (inclusive)
        return $now->between($start, $end, true);
    }

    public function getAccessLessonsAttribute($value)
    {
        return json_decode($value);
    }

    public function receivedWelcomeEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'courses-taken-welcome')->latest();
    }

    public function receivedFollowUpEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'courses-taken-follow-up')->latest();
    }

    // get the order record
    public function getOrderAttribute()
    {
        return Order::where([
            'user_id' => $this->attributes['user_id'],
            'package_id' => $this->attributes['package_id'],
        ])->with('paymentPlan')->latest()->first();
    }
}
