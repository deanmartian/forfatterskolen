<?php

namespace App;

use App\Traits\Loggable;
use Carbon\Carbon;
use File;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
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
        'email_verified_at', 'email_verification_token', 'disable_start_date', 'disable_end_date'
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
    #[Scope]
    protected function admins($query)
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
     */
    public function getIsWebinarPakkeActiveAttribute(): bool
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

    public function userAutoRegisterToCourseWebinar(): HasOne
    {
        return $this->hasOne(\App\UserAutoRegisterToCourseWebinar::class);
    }

    public function coursesTaken(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)->orderBy('created_at', 'desc');
    }

    public function coursesTakenNoFree(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)->where('is_free', '=', 0)
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where(function ($query) {
                // check if expired 2 months ago or the end date is not yet set
                $query->where('end_date', '>=', Carbon::now()->subDays(60))
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotOld2(): HasMany
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

    public function coursesTakenOld(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where('end_date', '<=', Carbon::now()->subDays(60))
            ->orderBy('created_at', 'desc');
    }

    public function formerCourses(): HasMany
    {
        return $this->hasMany(\App\FormerCourse::class)->orderBy('created_at', 'desc');
    }

    public function coursesTakenNotExpired(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where('end_date', '>=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc');
    }

    public function activePaidCoursesTakenNotExpired(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)
            ->where('is_free', '=', 0)
            ->where('end_date', '>=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc');
    }

    public function shopManuscriptsTaken(): HasMany
    {
        return $this->hasMany(\App\ShopManuscriptsTaken::class)->orderBy('created_at', 'desc');
    }

    public function freeCourses(): HasMany
    {
        return $this->hasMany(\App\CoursesTaken::class)->where('is_free', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function workshopsTaken(): HasMany
    {
        return $this->hasMany(\App\WorkshopsTaken::class)->orderBy('created_at', 'desc');
    }

    public function workshopTakenCount(): HasOne
    {
        return $this->hasOne(\App\WorkshopTakenCount::class);
    }

    public function logins(): HasMany
    {
        return $this->hasMany(\App\LearnerLogin::class)->orderBy('created_at', 'desc')->take(15);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Invoice::class)->orderBy('created_at', 'desc');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Order::class)->where('is_processed', 1)
            ->orderBy('created_at', 'desc');
    }

    public function books(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBook::class);
    }

    public function readingBooks(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookReading::class)
            ->where('status', 0);
    }

    public function finishedBooks(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookReading::class)
            ->where('status', 1);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Notification::class)->orderBy('created_at', 'desc');
    }

    public function pageAccess(): HasMany
    {
        return $this->hasMany(\App\PageAccess::class);
    }

    public function wordWritten(): HasMany
    {
        return $this->hasMany(\App\WordWritten::class)->orderBy('date', 'ASC');
    }

    public function wordWrittenGoal(): HasMany
    {
        return $this->hasMany(\App\WordWrittenGoal::class);
    }

    public function projects(): HasMany
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

    public function HowManyManuscriptYouCanTake(): HasMany
    {
        return $this->hasMany(\App\ManuscriptEditorCanTake::class, 'editor_id', 'id')
            ->orderBy('date_from', 'DESC');
    }

    public function HowManyManuscriptYouCanTakeActive(): HasMany
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

    public function emails(): HasMany
    {
        return $this->hasMany(\App\LearnerEmail::class)->orderBy('created_at', 'desc');
    }

    public function secondaryEmails(): HasMany
    {
        return $this->hasMany(\App\UserEmail::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->attributes['role'] == 1 ? 1 : 0;
    }

    public function coachingTimers(): HasMany
    {
        return $this->hasMany(\App\CoachingTimerManuscript::class)->orderBy('created_at', 'desc');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(\App\CorrectionManuscript::class)->orderBy('created_at', 'desc');
    }

    public function copyEditings(): HasMany
    {
        return $this->hasMany(\App\CopyEditingManuscript::class)->orderBy('created_at', 'desc');
    }

    public function coachingTimersTaken(): HasMany
    {
        return $this->hasMany(\App\CoachingTimerTaken::class);
    }

    public function diplomas(): HasMany
    {
        return $this->hasMany(\App\Diploma::class);
    }

    public function assignedCoachingTimers(): HasMany
    {
        return $this->hasMany(\App\CoachingTimerManuscript::class, 'editor_id', 'id')
            ->where('is_approved', '=', 1)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCorrections(): HasMany
    {
        return $this->hasMany(\App\CorrectionManuscript::class, 'editor_id', 'id')
            ->where('status', '!=', 2)
            ->orderBy('created_at', 'desc');
    }

    public function assignedCopyEditing(): HasMany
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

    public function surveyTaken(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class)->groupBy('survey_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(UserTask::class)->where('status', 0);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Assignment::class, 'parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    // active assignment assigned
    public function activeAssignments(): HasMany
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
    public function expiredAssignments(): HasMany
    {
        return $this->hasMany(\App\Assignment::class, 'parent_id', 'id')
            ->where('parent', 'users')
            ->orderBy('created_at', 'desc');
    }

    public function assignmentManuscripts(): HasMany
    {
        return $this->hasMany(\App\AssignmentManuscript::class);
    }

    public function assignmentAddOns(): HasMany
    {
        return $this->hasMany(\App\AssignmentAddon::class, 'user_id', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function personalTrainerApplication(): HasMany
    {
        return $this->hasMany(\App\PersonalTrainerApplicant::class);
    }

    public function comeptitionApplication(): HasMany
    {
        return $this->hasMany(\App\CompetitionApplicant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(\App\PrivateMessage::class, 'user_id', 'id');
    }

    public function courseOrderAttachments(): HasMany
    {
        return $this->hasMany(\App\CourseOrderAttachment::class, 'user_id', 'id');
    }

    public function preferredEditor(): HasOne
    {
        return $this->hasOne(\App\UserPreferredEditor::class, 'user_id', 'id');
    }

    public function registeredWebinars(): HasMany
    {
        return $this->hasMany(\App\WebinarRegistrant::class, 'user_id', 'id');
    }

    public function editorGenrePreferences(): HasMany
    {
        return $this->hasMany(\App\EditorGenrePreferences::class, 'editor_id', 'id');
    }

    public function assignmentManuscriptEditorCanTake(): HasMany
    {
        return $this->hasMany(\App\AssignmentManuscriptEditorCanTake::class, 'editor_id', 'id');
    }

    public function getAssignedWithNoFeedbackAttribute() // not availble if currently assigned on manuscript assignment
    {
        $query = \App\AssignmentManuscript::where('editor_id', $this->attributes['id'])->where('has_feedback', 0)->get();

        return count($query);
    }

    public function shopManuscriptRequests(): HasMany
    {
        return $this->hasMany(\App\RequestToEditor::class, 'editor_id', 'id')->where('from_type', 'shop-manuscript');
    }

    public function assignedWebinars(): HasMany
    {
        return $this->hasMany(\App\WebinarEditor::class, 'editor_id', 'id');
    }

    public function checkoutLogs(): HasMany
    {
        return $this->hasMany(\App\CheckoutLog::class);
    }

    public function giftPurchases(): HasMany
    {
        return $this->hasMany(\App\GiftPurchase::class);
    }

    public function selfPublishingList(): HasMany
    {
        return $this->hasMany(\App\SelfPublishingLearner::class);
    }

    public function timeRegisters(): HasMany
    {
        return $this->hasMany(\App\TimeRegister::class);
    }

    public function booksForSale(): HasMany
    {
        return $this->hasMany(\App\UserBookForSale::class);
    }

    public function bookSales(): HasMany
    {
        return $this->hasMany(\App\UserBookSale::class);
    }
}
