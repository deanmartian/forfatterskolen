<?php

namespace App;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use Loggable;

    protected $table = 'courses';

    // completed_date and issue_date is used on downloading certificate
    protected $fillable = ['title', 'description', 'description_simplemde', 'course_image', 'type', 'email',
        'course_plan', 'course_plan_data', 'start_date', 'end_date', 'completed_date', 'issue_date', 'extend_courses',
        'instructor', 'auto_list_id', 'photographer', 'pay_later_with_application', 'payment_plan_ids', 'is_free', 'hide_price',
        'meta_title', 'meta_description', 'meta_image'];

    protected $appends = ['is_webinar_pakke'];

    protected $casts = [
        'payment_plan_ids' => 'array',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(\App\Package::class)
            ->where('is_reward', 0)
            ->orderBy('full_payment_price', 'asc');
    }

    public function packagesIsShow(): HasMany
    {
        return $this->hasMany(\App\Package::class)
            ->where('is_reward', 0)
            ->where('is_show', 1)
            ->where('variation', '!=', 'Editor Package')
            ->orderBy('full_payment_price', 'asc');
    }

    public function allPackages(): HasMany
    {
        return $this->hasMany(\App\Package::class)
            ->orderBy('full_payment_price', 'asc');
    }

    public function rewardPackages(): HasMany
    {
        return $this->hasMany(\App\Package::class)
            ->where('is_reward', 1)
            ->orderBy('full_payment_price', 'asc');
    }

    public function standardPackage()
    {
        return $this->hasOne(\App\Package::class)
            ->where('is_standard', 1);
    }

    public function workshops(): HasMany
    {
        return $this->hasMany(\App\Workshop::class)->orderBy('created_at', 'desc');
    }

    public function webinars(): HasMany
    {
        // display id of 24 first then other record is by start date
        return $this->hasMany(\App\Webinar::class)->orderByRaw('id=24 DESC')->orderBy('start_date', 'asc');
    }

    public function activeWebinars(): HasMany
    {
        // display id of 24 first then other record is by start date
        return $this->hasMany(\App\Webinar::class)->orderByRaw('id=24 DESC')
            ->where('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Assignment::class)
            ->where(function ($query) {
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->orderBy('created_at', 'desc');
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(\App\Assignment::class)
            // commented because the field now accepts int also not just date
            /*->where(function($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('submission_date','>', Carbon::now());
            })*/
            ->where(function ($query) {
                // check if available date is less than or equal to date or if it's null
                $query->where('available_date', '<=', Carbon::now());
                $query->orWhereNull('available_date');
            })
            ->where(function ($query) {
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->oldest('submission_date');
        // ->orderBy('created_at', 'desc');
    }

    public function expiredAssignments(): HasMany
    {
        return $this->hasMany(\App\Assignment::class)
            // commented because the field now accepts int also not just date
            /*->where(function($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('submission_date','<', Carbon::now());
            })*/
            ->whereNull('parent')
            ->orderBy('created_at', 'desc');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(\App\Lesson::class)->orderBy('order', 'asc');
    }

    public function lesson_kursplan()
    {
        return $this->lessons()->where('title', 'Kursplan');
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(\App\CourseDiscount::class)->orderBy('id', 'asc');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(\App\CalendarNote::class);
    }

    public function similar_courses(): HasMany
    {
        return $this->hasMany(\App\SimilarCourse::class)->orderBy('created_at', 'desc');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(\App\CourseTestimonial::class);
    }

    public function emailOut(): HasMany
    {
        return $this->hasMany(\App\EmailOut::class);
    }

    public function emailOutOrdered(): HasMany
    {
        return $this->hasMany(\App\EmailOut::class)->orderByRaw('delay + 0 ASC')
            ->orderBy('delay', 'asc');
    }

    public function emailOutActive(): HasMany
    {
        $today = now()->toDateString();

        return $this->hasMany(\App\EmailOut::class)
            ->where(function ($query) use ($today) {
                $query->where('delay', '>=', $today)
                    ->orWhereRaw('delay REGEXP "^[0-9]+$"')
                    ->orWhere('send_immediately', 1);
            })
            ->orderByRaw('delay + 0 ASC')
            ->orderBy('delay', 'asc');
    }

    public function emailOutArchive(): HasMany
    {
        $today = now()->toDateString();

        return $this->hasMany(\App\EmailOut::class)
            ->where('delay', '<', $today)
            ->whereRaw('delay REGEXP "[0-9]{4}-[0-9]{2}-[0-9]{2}"')
            ->orderByRaw('delay + 0 ASC')
            ->orderBy('delay', 'asc');
    }

    public function emailOutLog(): HasMany
    {
        return $this->hasMany(\App\EmailOutLog::class);
    }

    public function rewardCoupons(): HasMany
    {
        return $this->hasMany(\App\CourseRewardCoupon::class);
    }

    public static function free()
    {
        return self::where('is_free', '=', 1)->get();
    }

    public function expiryReminders(): HasOne
    {
        return $this->hasOne(\App\CourseExpiryReminder::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(\App\CourseCertificate::class);
    }

    // for deleting the children
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

        return $manuscripts->get();
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
        $packageIds = $this->packages()->where('variation', '!=', 'Editor Package')->pluck('id')->toArray();

        return CoursesTaken::whereHas('user')->whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc');
    }

    public function getLearnersWithExpiredAttribute()
    {
        $packageIds = $this->packages()->pluck('id')->toArray();

        return CoursesTaken::whereHas('user')->whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->withTrashed()
            ->orderBy('updated_at', 'desc');
    }

    public function getwebinarLearnersAttribute()
    {
        $packageIds = $this->packages()->pluck('id')->toArray();

        return CoursesTaken::whereHas('user')->whereIn('package_id', $packageIds)
            ->where('is_active', true)
            ->where('exclude_in_scheduled_registration', 0)
            ->orderBy('updated_at', 'desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getStartDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getEndDateAttribute($value)
    {
        if ($value) {
            return date_format(date_create($value), 'M d, Y');
        }

        return false;
    }

    public function getIsAvailableAttribute()
    {
        $start_date = $this->attributes['start_date'];
        $end_date = $this->attributes['end_date'];
        if ($start_date || $end_date) {
            $now = time();
            if ($start_date) {
                if ($now < strtotime($start_date)) {
                    return false;
                }
            }
            if ($end_date) {
                if ($now > strtotime($end_date)) {
                    return false;
                }
            }
        }

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
