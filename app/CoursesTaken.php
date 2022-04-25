<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursesTaken extends Model
{
    use SoftDeletes;

    protected $table = 'courses_taken';
    protected $fillable = ['user_id', 'package_id', 'gift_purchase_id', 'is_active', 'started_at', 'start_date',
        'end_date', 'access_lessons', 'years', 'is_free', 'send_expiry_reminder', 'is_welcome_email_sent',
        'can_receive_email'];
    protected $dates = ['renewed_at'];

    protected $appends = ['order'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function manuscripts()
    {
        return $this->hasMany('App\Manuscript', 'coursetaken_id')->orderBy('created_at', 'desc');
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i a') : NULL;
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
        if( $value ) :
            return date_format(date_create($value), 'M d, Y');
        endif;
        return false;
    }

    public function getStartDateValueAttribute()
    {
        return $this->attributes['start_date'] ?: NULL;
    }



    public function getEndDateAttribute($value)
    {
        if( $value ) :
            return date_format(date_create($value), 'M d, Y');
        endif;
        return false;
    }

    public function getEndDateValueAttribute()
    {
        return $this->attributes['end_date'] ?: NULL;
    }

    public function getEndDateWithValueAttribute()
    {
        if(!$this->attributes['end_date'] ) {
            $date = \Carbon\Carbon::parse($this->attributes['started_at']);
            return $date->addYear(1);
        } else {
            return date_format(date_create($this->attributes['end_date']), 'M d, Y');
        }
    }



    public function getHasStartedAttribute()
    {
        return !empty($this->attributes['started_at']);
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
        if(!$this->attributes['end_date'] ) {
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



    public function getAccessLessonsAttribute($value)
    {
        return json_decode($value);
    }

    public function receivedWelcomeEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'courses-taken-welcome')->latest();
    }

    public function receivedFollowUpEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'courses-taken-follow-up')->latest();
    }

    // get the order record
    public function getOrderAttribute()
    {
        return Order::where([
            'user_id' => $this->attributes['user_id'],
            'package_id' => $this->attributes['package_id']
        ])->with('paymentPlan')->latest()->first();
    }

}
