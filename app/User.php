<?php

namespace App;

use App\Traits\Loggable;
use Carbon\Carbon;
use File;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Loggable;
    use Notifiable;
    use SoftDeletes;

    const AdminRole = 1;

    const LearnerRole = 2;

    const EditorRole = 3;

    const GiutbokRole = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'password', 'email', 'role', 'gender', 'birthday', 'profile_image',
        'default_password', 'need_pass_update', 'is_active', 'admin_with_giutbok_access', 'is_self_publishing_learner',
        'is_ghost_writer_admin', 'is_copy_editing_admin', 'is_correction_admin', 'is_coaching_admin', 'fiken_contact_id',
        'email_verified_at', 'email_verification_token',
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

    protected $appends = ['address', 'full_name']; // 'is_webinar_pakke_active', 'assigned_with_no_feedback',

    // filter admins and exclude the user of Sven
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [1, 3, 4])
            ->where('id', '!=', 1376); // 1376 is the id of sven.inge@forfatterskolen.no
    }

    public function getAddressAttribute()
    {
        $address = \App\Address::where('user_id', $this->attributes['id'])->first();

        if (! $address) {
            $empty_address = new \App\Address;

            return $empty_address;
        }

        return $address;
    }

    public function getFullAddressAttribute()
    {
        if (! $this->address) {
            return null;
        }

        $fullAddress = '';

        if ($this->address->street) {
            $fullAddress .= $this->address->street.', ';
        }

        if ($this->address->city) {
            $fullAddress .= $this->address->city.', ';
        }

        if ($this->address->zip) {
            $fullAddress .= $this->address->zip;
        }

        return $fullAddress;
    }

    public function getSocialAttribute()
    {
        $social = \App\UserSocial::where('user_id', $this->attributes['id'])->first();

        if (! $social) {
            $empty_social = new \App\UserSocial;

            return $empty_social;
        }

        return $social;
    }

    public function getManuscriptsAttribute()
    {
        $coursesTaken = $this->coursesTaken->pluck('id')->toArray();
        $manuscripts = \App\Manuscript::whereIn('coursetaken_id', $coursesTaken)->orderBy('created_at', 'desc')->get();

        return $manuscripts;
    }

    /**
     * function is moved to AdminHelpers::isWebinarPakkeActive()
     *
     * @return bool
     */
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
        return $this->hasOne(\App\UserAutoRegisterToCourseWebinar::class);
    }

    public function coursesTaken()
    {
        return $this->hasMany(\App\CoursesTaken::class)->orderBy('created_at', 'desc');
    }

    public function coursesTakenNoFree()
    {
        return $this->hasMany(\App\CoursesTaken::class)->where('is_free', '=', 0)
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld()
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where(function ($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('end_date', '>=', Carbon::now()->subDays(60))
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld2()
    {
        $webinarPakkePackages = Course::find(17)->packages()->pluck('id')->toArray();

        return $this->hasMany(\App\CoursesTaken::class)
            ->where(function ($query) {
                $query->where('started_at', '>=', Carbon::now()->subYear(1))
                    ->orWhere('end_date', '>=', Carbon::now())
                    ->orWhereNull('end_date');
            })
            ->whereIn('package_id', $webinarPakkePackages)
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenOld()
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where('end_date', '<=', Carbon::now()->subDays(60))
            ->orderBy('created_at', 'desc');
    }

    public function formerCourses()
    {
        return $this->hasMany(\App\FormerCourse::class)->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotExpired()
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where('end_date', '>=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc');
    }

    public function shopManuscriptsTaken()
    {
        return $this->hasMany(\App\ShopManuscriptsTaken::class)->orderBy('created_at', 'desc');
    }

    public function freeCourses()
    {
        return $this->hasMany(\App\CoursesTaken::class)->where('is_free', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function workshopsTaken()
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function workshopTakenCount()
    {
        return $this->hasOne(\App\WorkshopTakenCount::class);
    }

    public function logins()
    {
        return $this->hasMany(\App\LearnerLogin::class)->orderBy('created_at', 'desc')->take(15);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Invoice::class)->orderBy('created_at', 'desc');
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class)->where('is_processed', 1)
            ->orderBy('created_at', 'desc');
    }

    public function books()
    {
        return $this->hasMany(\App\PilotReaderBook::class);
    }

    public function readingBooks()
    {
        return $this->hasMany(\App\PilotReaderBookReading::class)
            ->where('status', 0);
    }

    public function finishedBooks()
    {
        return $this->hasMany(\App\PilotReaderBookReading::class)
            ->where('status', 1);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Notification::class)->orderBy('created_at', 'desc');
    }

    public function pageAccess()
    {
        return $this->hasMany(\App\PageAccess::class);
    }

    public function wordWritten()
    {
        return $this->hasMany(\App\WordWritten::class)->orderBy('date', 'ASC');
    }

    public function wordWrittenGoal()
    {
        return $this->hasMany(\App\WordWrittenGoal::class);
    }

    public function projects()
    {
        return $this->hasMany(\App\Project::class);
    }

    public function standardProject()
    {
        // Attempt to get the first project where `is_standard` is 1
        $project = $this->hasMany(\App\Project::class)->where('is_standard', 1)->first();

        // If no project is found, return the first project
        return $project ?? $this->hasMany(\App\Project::class)->first();
    }

    public function getProfileImageAttribute($value)
    {
        $image = substr($this->attributes['profile_image'], 1);
        if (File::exists($image)) {
            return $value;
        }

        return 'https://www.forfatterskolen.no/images/user.png';

    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    public function HowManyManuscriptYouCanTake()
    {
        return $this->hasMany(\App\ManuscriptEditorCanTake::class, 'editor_id', 'id')
            ->orderBy('date_from', 'DESC');
    }

    public function HowManyManuscriptYouCanTakeActive()
    {
        return $this->hasMany(\App\ManuscriptEditorCanTake::class, 'editor_id', 'id')
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
        return $this->hasMany(\App\LearnerEmail::class)->orderBy('created_at', 'desc');
    }

    public function secondaryEmails()
    {
        return $this->hasMany(\App\UserEmail::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->attributes['role'] == 1 ? 1 : 0;
    }

    public function coachingTimers()
    {
        return $this->hasMany(\App\CoachingTimerManuscript::class)->orderBy('created_at', 'desc');
    }

    public function corrections()
    {
        return $this->hasMany(\App\CorrectionManuscript::class)->orderBy('created_at', 'desc');
    }

    public function copyEditings()
    {
        return $this->hasMany(\App\CopyEditingManuscript::class)->orderBy('created_at', 'desc');
    }

    public function coachingTimersTaken()
    {
        return $this->hasMany(\App\CoachingTimerTaken::class);
    }

    public function diplomas()
    {
        return $this->hasMany(\App\Diploma::class);
    }

    public function assignedCoachingTimers()
    {
        return $this->hasMany(\App\CoachingTimerManuscript::class, 'editor_id', 'id')
            ->where('is_approved', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCorrections()
    {
        return $this->hasMany(\App\CorrectionManuscript::class, 'editor_id', 'id')
            ->where('status', '!=', 2)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCopyEditing()
    {
        return $this->hasMany(\App\CopyEditingManuscript::class, 'editor_id', 'id')
            ->where('status', '!=', 2)
            ->orderBy('created_at', 'desc');
    }

    public function isSuperUser()
    {
        $ids = [1376, 1070, 4464];

        return in_array($this->attributes['id'], $ids) ? true : false;
    }

    public function surveyTaken()
    {
        return $this->hasMany(SurveyAnswer::class)->groupBy('survey_id');
    }

    public function tasks()
    {
        return $this->hasMany(UserTask::class)->where('status', 0);
    }

    public function assignments()
    {
        return $this->hasMany(\App\Assignment::class, 'parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    // active assignment assigned
    public function activeAssignments()
    {
        return $this->hasMany(\App\Assignment::class, 'parent_id', 'id')
            ->where('parent', 'users')
            ->where(function ($query) {
                // check if available date is less than or equal to date or if it's null
                $query->where('available_date', '<=', Carbon::now());
                $query->orWhereNull('available_date');
            });
    }

    // expired assignment assigned
    public function expiredAssignments()
    {
        return $this->hasMany(\App\Assignment::class, 'parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    public function assignmentManuscripts()
    {
        return $this->hasMany(\App\AssignmentManuscript::class);
    }

    public function assignmentAddOns()
    {
        return $this->hasMany(\App\AssignmentAddon::class, 'user_id', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function personalTrainerApplication()
    {
        return $this->hasMany(\App\PersonalTrainerApplicant::class);
    }

    public function comeptitionApplication()
    {
        return $this->hasMany(\App\CompetitionApplicant::class);
    }

    public function messages()
    {
        return $this->hasMany(\App\PrivateMessage::class, 'user_id', 'id');
    }

    public function courseOrderAttachments()
    {
        return $this->hasMany(\App\CourseOrderAttachment::class, 'user_id', 'id');
    }

    public function preferredEditor()
    {
        return $this->hasOne(\App\UserPreferredEditor::class, 'user_id', 'id');
    }

    public function registeredWebinars()
    {
        return $this->hasMany(\App\WebinarRegistrant::class, 'user_id', 'id');
    }

    public function editorGenrePreferences()
    {
        return $this->hasMany(\App\EditorGenrePreferences::class, 'editor_id', 'id');
    }

    public function assignmentManuscriptEditorCanTake()
    {
        return $this->hasMany(\App\AssignmentManuscriptEditorCanTake::class, 'editor_id', 'id');
    }

    public function getAssignedWithNoFeedbackAttribute() // not availble if currently assigned on manuscript assignment
    {$query = \App\AssignmentManuscript::where('editor_id', $this->attributes['id'])->where('has_feedback', 0)->get();

        return count($query);
    }

    public function shopManuscriptRequests()
    {
        return $this->hasMany(\App\RequestToEditor::class, 'editor_id', 'id')->where('from_type', 'shop-manuscript');
    }

    public function assignedWebinars()
    {
        return $this->hasMany(\App\WebinarEditor::class, 'editor_id', 'id');
    }

    public function checkoutLogs()
    {
        return $this->hasMany(\App\CheckoutLog::class);
    }

    public function giftPurchases()
    {
        return $this->hasMany(\App\GiftPurchase::class);
    }

    public function selfPublishingList()
    {
        return $this->hasMany(\App\SelfPublishingLearner::class);
    }

    public function timeRegisters()
    {
        return $this->hasMany(\App\TimeRegister::class);
    }

    public function booksForSale()
    {
        return $this->hasMany(\App\UserBookForSale::class);
    }

    public function bookSales()
    {
        return $this->hasMany(\App\UserBookSale::class);
    }
}
