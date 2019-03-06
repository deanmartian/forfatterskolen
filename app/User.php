<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use File;

/**
 * @mixin \Eloquent
 */

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'role', 'gender', 'birthday', 'profile_image', 'need_pass_update'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAddressAttribute()
    {
        $address = \App\Address::where('user_id', $this->attributes['id'])->first();

        if( !$address ) :
            $empty_address = new \App\Address();
            return $empty_address;
        endif;

        return $address;
    }

    public function getSocialAttribute()
    {
        $social = \App\UserSocial::where('user_id', $this->attributes['id'])->first();

        if( !$social ) :
            $empty_social = new \App\UserSocial();
            return $empty_social;
        endif;

        return $social;
    }

    public function getManuscriptsAttribute()
    {
        $coursesTaken = $this->coursesTaken->pluck('id')->toArray();
        $manuscripts = \App\Manuscript::whereIn('coursetaken_id', $coursesTaken)->orderBy('created_at', 'desc')->get();
        return $manuscripts;
    }

    public function coursesTaken()
    {
        return $this->hasMany('App\CoursesTaken')->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld()
    {
        return $this->hasMany('App\CoursesTaken')
            ->where(function($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('end_date','>=', Carbon::now()->subDays(60))
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld2()
    {
        $webinarPakkePackages = Course::find(17)->packages()->pluck('id')->toArray();
        return $this->hasMany('App\CoursesTaken')
            ->where(function($query) use($webinarPakkePackages) {
                $query->where('created_at', '>=', Carbon::now()->subYear(1))
                    ->orWhereNull('end_date')
                    ->orWhereIn('package_id', $webinarPakkePackages);
            })
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenOld()
    {
        return $this->hasMany('App\CoursesTaken')
            ->where('end_date','<=', Carbon::now()->subDays(60))
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotExpired()
    {
        return $this->hasMany('App\CoursesTaken')
            ->where('end_date','<=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc');
    }

    public function shopManuscriptsTaken()
    {
        return $this->hasMany('App\ShopManuscriptsTaken')->orderBy('created_at', 'desc');
    }

    public function freeCourses()
    {
        return $this->hasMany('App\CoursesTaken')->where('is_free', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function workshopsTaken()
    {
        return $this->hasMany('App\WorkshopsTaken')->orderBy('created_at', 'desc');
    }

    public function workshopTakenCount()
    {
        return $this->hasOne('App\WorkshopTakenCount');
    }

    public function logins()
    {
        return $this->hasMany('App\LearnerLogin')->orderBy('created_at', 'desc')->take(5);
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice')->orderBy('created_at', 'desc');
    }

    public function books()
    {
        return $this->hasMany('App\PilotReaderBook');
    }

    public function readingBooks()
    {
        return $this->hasMany('App\PilotReaderBookReading')
            ->where('status',0);
    }

    public function finishedBooks()
    {
        return $this->hasMany('App\PilotReaderBookReading')
            ->where('status',1);
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification')->orderBy('created_at', 'desc');
    }

    public function pageAccess()
    {
        return $this->hasMany('App\PageAccess');
    }

    public function wordWritten()
    {
        return $this->hasMany('App\WordWritten')->orderBy('date','ASC');
    }

    public function wordWrittenGoal()
    {
        return $this->hasMany('App\WordWrittenGoal');
    }

    public function getProfileImageAttribute($value)
    {
        $image = substr($this->attributes['profile_image'], 1);
        if(File::exists($image)) return $value;

        return asset('images/user.png');
        
    }


    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }


    public function getHasProfileImageAttribute()
    {
        $image = substr($this->attributes['profile_image'], 1);
        return File::exists($image);
    }

    public function emails()
    {
        return $this->hasMany('App\LearnerEmail')->orderBy('created_at', 'desc');
    }

    public function getIsAdminAttribute()
    {
        return $this->attributes['role'] == 1 ? 1 : 0;
    }

    public function coachingTimers()
    {
        return $this->hasMany('App\CoachingTimerManuscript')->orderBy('created_at', 'desc');
    }

    public function corrections()
    {
        return $this->hasMany('App\CorrectionManuscript')->orderBy('created_at', 'desc');
    }

    public function copyEditings()
    {
        return $this->hasMany('App\CopyEditingManuscript')->orderBy('created_at', 'desc');
    }

    public function coachingTimersTaken()
    {
        return $this->hasMany('App\CoachingTimerTaken');
    }

    public function diplomas()
    {
        return $this->hasMany('App\Diploma');
    }

    public function assignedCoachingTimers()
    {
        return $this->hasMany('App\CoachingTimerManuscript','editor_id', 'id')
            ->where('is_approved', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCorrections()
    {
        return $this->hasMany('App\CorrectionManuscript','editor_id', 'id')
            ->where('status', '!=', 2)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCopyEditing()
    {
        return $this->hasMany('App\CopyEditingManuscript','editor_id', 'id')
            ->where('status', '!=', 2)
            ->orderBy('created_at', 'desc');
    }
}
