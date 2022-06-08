<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use File;
use Illuminate\Support\Facades\DB;


/**
 * @mixin \Eloquent
 */

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'password', 'email', 'role', 'gender', 'birthday', 'profile_image',
        'need_pass_update', 'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $with = ['preferredEditor'];
    protected $appends = ['is_webinar_pakke_active', 'assigned_with_no_feedback', 'address', 'full_name'];

    // filter admins and exclude the user of Sven
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', array(1,3))
            ->where('id', '!=', 1376);// 1376 is the id of sven.inge@forfatterskolen.no
    }

    public function getAddressAttribute()
    {
        $address = \App\Address::where('user_id', $this->attributes['id'])->first();

        if( !$address ) :
            $empty_address = new \App\Address();
            return $empty_address;
        endif;

        return $address;
    }

    public function getFullAddressAttribute()
    {
        if (!$this->address) {
            return null;
        }

        $fullAddress = '';

        if ($this->address->street) {
            $fullAddress .= $this->address->street. ', ';
        }

        if ($this->address->city) {
            $fullAddress .= $this->address->city. ', ';
        }

        if ($this->address->zip) {
            $fullAddress .= $this->address->zip;
        }

        return $fullAddress;
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

    public function getIsWebinarPakkeActiveAttribute()
    {
        $courseTaken = $this->coursesTaken->where('package_id', 29)->first();
        if ($courseTaken) {
            $end_date = $courseTaken->end_date ?: Carbon::parse($courseTaken->started_at)->addYear(1);

            if (Carbon::parse($end_date)->gt(Carbon::today())) {
                return true;
            }
        }

        return false;
    }

    public function userAutoRegisterToCourseWebinar()
    {
        return $this->hasOne('App\UserAutoRegisterToCourseWebinar');
    }

    public function coursesTaken()
    {
        return $this->hasMany('App\CoursesTaken')->orderBy('created_at', 'desc');
    }

    public function coursesTakenNoFree()
    {
        return $this->hasMany('App\CoursesTaken')->where('is_free','=',0)
            ->orderBy('created_at', 'desc');
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
            ->where(function($query) {
                $query->where('started_at', '>=', Carbon::now()->subYear(1))
                    ->orWhere('end_date', '>=', Carbon::now())
                    ->orWhereNull('end_date');
            })
            ->whereIn('package_id', $webinarPakkePackages)
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenOld()
    {
        return $this->hasMany('App\CoursesTaken')
            ->where('end_date','<=', Carbon::now()->subDays(60))
            ->orderBy('created_at', 'desc');
    }

    public function formerCourses()
    {
        return $this->hasMany('App\FormerCourse')->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotExpired()
    {
        return $this->hasMany('App\CoursesTaken')
            ->where('end_date','>=', Carbon::now()->subDays(1))
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
        return $this->hasMany('App\LearnerLogin')->orderBy('created_at', 'desc')->take(15);
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice')->orderBy('created_at', 'desc');
    }

    public function orders()
    {
        return $this->hasMany('App\Order')->where('is_processed', 1)
            ->orderBy('created_at', 'desc');
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

        return 'https://www.forfatterskolen.no/images/user.png';
        
    }


    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function HowManyManuscriptYouCanTake()
    {
        return $this->hasMany('App\ManuscriptEditorCanTake','editor_id', 'id')
        ->orderBy('date_from', 'DESC');
    }

    public function HowManyManuscriptYouCanTakeActive()
    {
        return $this->hasMany('App\ManuscriptEditorCanTake','editor_id', 'id')
            ->whereDate('date_to', '>=', \Carbon\Carbon::today()->format('Y-m-d'))
            ->orderBy('date_from', 'DESC');
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

    public function secondaryEmails()
    {
        return $this->hasMany('App\UserEmail');
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

    public function isSuperUser()
    {
        $ids = [1376, 1070];
        return in_array($this->attributes['id'], $ids) ? true : false;
    }

    public function surveyTaken()
    {
        return $this->hasMany(SurveyAnswer::class)->groupBy("survey_id");
    }

    public function tasks()
    {
        return $this->hasMany(UserTask::class)->where('status',0);
    }

    public function assignments()
    {
        return $this->hasMany('App\Assignment', 'parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    // active assignment assigned
    public function activeAssignments()
    {
        return $this->hasMany('App\Assignment','parent_id', 'id')
            ->where('parent', 'users')
            ->where(function($query) {
                // check if available date is less than or equal to date or if it's null
                $query->where('available_date','<=', Carbon::now());
                $query->orWhereNull('available_date');
            });
    }

    // expired assignment assigned
    public function expiredAssignments()
    {
        return $this->hasMany('App\Assignment','parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    public function assignmentManuscripts()
    {
        return $this->hasMany('App\AssignmentManuscript');
    }

    public function assignmentAddOns()
    {
        return $this->hasMany('App\AssignmentAddon', 'user_id', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function personalTrainerApplication()
    {
        return $this->hasMany('App\PersonalTrainerApplicant');
    }

    public function comeptitionApplication()
    {
        return $this->hasMany('App\CompetitionApplicant');
    }

    public function messages() {
        return $this->hasMany('App\PrivateMessage','user_id','id');
    }

    public function courseOrderAttachments()
    {
        return $this->hasMany('App\CourseOrderAttachment', 'user_id', 'id');
    }

    public function preferredEditor()
    {
        return $this->hasOne('App\UserPreferredEditor', 'user_id', 'id');
    }

    public function registeredWebinars()
    {
        return $this->hasMany('App\WebinarRegistrant', 'user_id', 'id');
    }

    public function editorGenrePreferences(){
        return $this->hasMany('App\EditorGenrePreferences', 'editor_id', 'id');
    }

    public function assignmentManuscriptEditorCanTake(){
        return $this->hasMany('App\AssignmentManuscriptEditorCanTake', 'editor_id', 'id');
    }

    public function getAssignedWithNoFeedbackAttribute(){ //not availble if currently assigned on manuscript assignment
        $query = \App\AssignmentManuscript::where('editor_id', $this->attributes['id'])->where('has_feedback', 0)->get();
        return count($query);
    }

    public function shopManuscriptRequests()
    {
        return $this->hasMany('App\RequestToEditor', 'editor_id', 'id')->where('from_type', 'shop-manuscript');
    }

    public function assignedWebinars()
    {
        return $this->hasMany('App\WebinarEditor', 'editor_id', 'id');
    }

    public function checkoutLogs()
    {
        return $this->hasMany('App\CheckoutLog');
    }

    public function giftPurchases()
    {
        return $this->hasMany('App\GiftPurchase');
    }
}
