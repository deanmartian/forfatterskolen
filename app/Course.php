<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = ['title', 'description', 'description_simplemde', 'course_image', 'type', 'email',
        'course_plan', 'course_plan_data','start_date', 'end_date', 'extend_courses', 'instructor', 'auto_list_id',
        'photographer', 'is_free', 'hide_price'];
    protected $appends = ['is_webinar_pakke'];


    public function packages()
    {
        return $this->hasMany('App\Package')
            ->where('is_reward', 0)
            ->orderBy('full_payment_price', 'asc');
    }

    public function packagesIsShow()
    {
        return $this->hasMany('App\Package')
            ->where('is_reward', 0)
            ->where('is_show', 1)
            ->orderBy('full_payment_price', 'asc');
    }

    public function allPackages()
    {
        return $this->hasMany('App\Package')
            ->orderBy('full_payment_price', 'asc');
    }

    public function rewardPackages()
    {
        return $this->hasMany('App\Package')
            ->where('is_reward', 1)
            ->orderBy('full_payment_price', 'asc');
    }


    public function workshops()
    {
        return $this->hasMany('App\Workshop')->orderBy('created_at', 'desc');
    }


    public function webinars()
    {
        //display id of 24 first then other record is by start date
        return $this->hasMany('App\Webinar')->orderByRaw("id=24 DESC")->orderBy('start_date', 'asc');
    }

    public function assignments()
    {
        return $this->hasMany('App\Assignment')->whereNull('parent')->orderBy('created_at', 'desc');
    }

    public function activeAssignments()
    {
        return $this->hasMany('App\Assignment')
            // commented because the field now accepts int also not just date
            /*->where(function($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('submission_date','>', Carbon::now());
            })*/
            ->where(function($query) {
                // check if available date is less than or equal to date or if it's null
                $query->where('available_date','<=', Carbon::now());
                $query->orWhereNull('available_date');
            })
            ->whereNull('parent')
            ->orderBy('created_at', 'desc');
    }

    public function expiredAssignments()
    {
        return $this->hasMany('App\Assignment')
            // commented because the field now accepts int also not just date
            /*->where(function($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('submission_date','<', Carbon::now());
            })*/
            ->orderBy('created_at', 'desc');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson')->orderBy('order', 'asc');
    }

    public function lesson_kursplan()
    {
        return $this->lessons()->where('title','Kursplan');
    }

    public function discounts()
    {
        return $this->hasMany('App\CourseDiscount')->orderBy('id', 'asc');
    }

    public function notes()
    {
        return $this->hasMany('App\CalendarNote');
    }

    public function similar_courses()
    {
        return $this->hasMany('App\SimilarCourse')->orderBy('created_at', 'desc');
    }

    public function testimonials()
    {
        return $this->hasMany('App\CourseTestimonial');
    }

    public function emailOut()
    {
        return $this->hasMany('App\EmailOut');
    }

    public function emailOutLog()
    {
        return $this->hasMany('App\EmailOutLog');
    }

    public function rewardCoupons()
    {
        return $this->hasMany('App\CourseRewardCoupon');
    }

    public static function free()
    {
        return self::where('is_free', '=', 1)->get();
    }

    public function expiryReminders()
    {
        return $this->hasOne('App\CourseExpiryReminder');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    //for deleting the children
    /*public static function boot()
    {
        parent::boot();

        // cause a delete of a product to cascade to children so they are also deleted
        static::deleted(function($course)
        {
            $course->testimonials->delete();
        });
    }*/

    public function getManuscriptsAttribute()
    {
        $packages_ids = $this->packages()->pluck('id')->toArray();
        $coursesTaken_ids = CoursesTaken::whereIn('package_id', $packages_ids)->pluck('id')->toArray();
        $manuscripts = Manuscript::whereIn('coursetaken_id', $coursesTaken_ids)->orderBy('created_at', 'desc');
        return $manuscripts;
    }

    public function getUrlAttribute()
    {
        return url('/').'/course/'.$this->attributes['id'];
    }

    public function getDescriptionRawAttribute()
    {
        return strip_tags($this->attributes['description']);
    }

    public function getLearnersAttribute()
    {
        $packageIds = $this->packages()->pluck('id')->toArray();
        return CoursesTaken::whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }



    public function getStartDateAttribute($value)
    {
        if( $value ) :
            return date_format(date_create($value), 'M d, Y');
        endif;
        return false;
    }



    public function getEndDateAttribute($value)
    {
        if( $value ) :
            return date_format(date_create($value), 'M d, Y');
        endif;
        return false;
    }

    public function getIsAvailableAttribute()
    {
        $start_date = $this->attributes['start_date'];
        $end_date = $this->attributes['end_date'];
        if( $start_date || $end_date ) :
            $now = time();
            if( $start_date ) :
                if( $now < strtotime($start_date)) return false;
            endif;
            if( $end_date ) :
                if( $now > strtotime($end_date)) return false;
            endif;
        endif;
        return true;
    }

    public function getIsActiveAttribute()
    {
        $status = $this->attributes['status'];
        if ($status) {
            return true;
        }
        return false;
    }

    public function getIsWebinarPakkeAttribute()
    {
        return $this->attributes['id'] === 17 ? true : false;
    }

}
