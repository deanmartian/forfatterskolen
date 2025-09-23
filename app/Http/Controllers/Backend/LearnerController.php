<?php

namespace App\Http\Controllers\Backend;

use App\Address;
use App\Assignment;
use App\AssignmentAddon;
use App\AssignmentGroupLearner;
use App\AssignmentManuscript;
use App\AssignmentTemplate;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Course;
use App\CourseCertificate;
use App\CoursesTaken;
use App\Diploma;
use App\EmailHistory;
use App\EmailTemplate;
use App\Exports\GenericExport;
use App\FormerCourse;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\UpdateFikenContactDetailsJob;
use App\LearnerLogin;
use App\Lesson;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\PrivateMessage;
use App\Project;
use App\RequestToEditor;
use App\SelfPublishing;
use App\SelfPublishingLearner;
use App\SelfPublishingOrder;
use App\Services\CourseService;
use App\Services\LearnerService;
use App\ShopManuscript;
use App\ShopManuscriptComment;
use App\ShopManuscriptsTaken;
use App\StorageDetail;
use App\TimeRegister;
use App\User;
use App\UserAutoRegisterToCourseWebinar;
use App\UserBookForSale;
use App\UserBookSale;
use App\UserEmail;
use App\UserPreferredEditor;
use App\Workshop;
use App\WorkshopMenu;
use App\WorkshopsTaken;
use App\WorkshopTakenCount;
use App\Console\Commands\CheckFikenContactCommand;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Validator;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class LearnerController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
    // Forfatterskolen: forfatterskolen-as
    // DemoAS: fiken-demo-glede-og-bil-as2
    public $fikenInvoices = 'https://fiken.no/api/v1/companies/forfatterskolen-as/invoices/';

    public $username = 'cleidoscope@gmail.com';

    public $password = 'moonfang';

    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json',
    ];

    public function __construct()
    {
        $this->middleware('checkPageAccess:4');
    }

    public function index(Request $request, User $user): View
    {
        $learners = $user->newQuery();
        if ($request->sid || $request->sfname || $request->slname || $request->semail) {
            if ($request->sid) {
                $learners->where('id', $request->sid);
            }

            if ($request->sfname) {
                $learners->where('first_name', 'LIKE', '%'.$request->sfname.'%');
            }

            if ($request->slname) {
                $learners->where('last_name', 'LIKE', '%'.$request->slname.'%');
            }

            if ($request->semail) {
                $learners->where('email', 'LIKE', '%'.$request->semail.'%');
            }

            $learners->orderBy('first_name', 'asc')
                ->orderBy('email', 'asc');
            /*$learners->where(function($query) use ($request) {
                $query->where('first_name', 'LIKE', '%' . $request->search  . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search  . '%');
            })
            ->orderBy('first_name', 'asc')
            ->orderBy('email', 'asc');*/
        }

        if ($request->has('free-course')) {
            $learners->has('freeCourses');
        }

        if ($request->has('workshop')) {
            $learners->has('workshopsTaken');
        }

        if ($request->has('shop-manuscript')) {
            $learners->has('shopManuscriptsTaken');
        }

        if ($request->has('course')) {
            if ($request->has('free-course')) {
                $learners->has('coursesTaken');
            } else {
                $learners->has('coursesTakenNoFree');
            }
        }

        $learners->orderBy('created_at', 'desc');
        $learners = $learners->paginate(25);

        return view('backend.learner.index', compact('learners'));
    }

    public function show($id): View
    {
        $learner = User::findOrFail($id);
        $learnerAssignments = $learner->assignments;

        $learnerAssignmentManuscripts = $learner->assignmentManuscripts->pluck('id');
        $learnerShopManuscriptsTaken = $learner->shopManuscriptsTaken->pluck('id');
        $learnerCoursesTaken = $learner->coursesTaken->pluck('id');
        $learnerInvoices = $learner->invoices->pluck('id');
        $registeredWebinarLists = $learner->registeredWebinars->pluck('id');
        $registeredWebinars = $learner->registeredWebinars()->latest()->get();
        $learnerGiftPurchases = $learner->giftPurchases->pluck('id');
        $assignmentTemplates = AssignmentTemplate::get();
        $learnerSelfPublishingList = $learner->selfPublishingList()->whereHas('selfPublishing')->get();
        $timeRegisters = TimeRegister::where('user_id', $learner->id)->with('project')->get();
        $selfPublishingList = SelfPublishing::whereNotIn('id',
            $learner->selfPublishingList()->pluck('self_publishing_id')->toArray())->get();

        $emailHistories = [];
        if ($learner->id != 4) {
            $emailHistories = DB::table('email_history')
                // ->select('id', 'parent', 'parent_id', 'recipient', 'subject', 'from_email', 'date_open')
                ->where(function ($query) use ($learnerAssignmentManuscripts) {
                    $query->where('parent', 'LIKE', 'assignment-manuscripts%');
                    $query->whereIn('parent_id', $learnerAssignmentManuscripts);
                })
                ->orWhere(function ($query) use ($learnerShopManuscriptsTaken) {
                    $query->where('parent', 'LIKE', 'shop-manuscripts-taken%');
                    $query->whereIn('parent_id', $learnerShopManuscriptsTaken);
                })
                ->orWhere(function ($query) use ($learnerCoursesTaken) {
                    $query->where('parent', 'LIKE', 'courses-taken%');
                    $query->whereIn('parent_id', $learnerCoursesTaken);
                })
                ->orWhere(function ($query) use ($registeredWebinarLists) {
                    $query->where('parent', '=', 'webinar-registrant');
                    $query->whereIn('parent_id', $registeredWebinarLists);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', '=', 'learner');
                    $query->where('parent_id', $learner->id);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', '=', 'free-manuscripts');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learnerInvoices) {
                    $query->where('parent', '=', 'invoice');
                    $query->whereIn('parent_id', $learnerInvoices);
                })
                ->orWhere(function ($query) use ($learnerInvoices) {
                    $query->where('parent', '=', 'invoice');
                    $query->whereIn('parent_id', $learnerInvoices);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'copy-editing%');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'correction%');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'gift-purchase');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('recipient', $learner->email);
                })
                ->latest()
                ->get();
            /* $emailHistories = EmailHistory::select('id', 'parent', 'parent_id', 'recipient', 'subject', 'from_email', 'date_open')
            ->withTrashed()
            ->limit(20)
            ->get(); */
        }

        $projects = Project::with(['registrations' => function ($query) {
            $query->where('field', 'isbn');
        }])->where('user_id', $learner->id)->get();

        // get course certificates based on users course taken
        $certificates = \DB::table('course_certificates')
            ->leftJoin('courses', 'course_certificates.course_id', '=', 'courses.id')
            ->leftJoin('packages', 'packages.id', '=', 'course_certificates.package_id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->select('course_certificates.*', 'courses.title as course_title')
            ->whereNotNull('courses.completed_date')
            ->whereNotNull('courses.issue_date')
            ->where('courses_taken.user_id', $id)
            ->whereNull('courses_taken.deleted_at')
            ->groupBy('course_certificates.id')
            ->get();

        $projects = Project::where('user_id', $learner->id)->get();
        $bookSale = new UserBookSale;
        $bookSaleTypes = $bookSale->saleTypes();
        $tasks = $learner->tasks()->get();

        return view('backend.learner.show', compact('learner', 'learnerAssignments', 'emailHistories',
            'registeredWebinars', 'assignmentTemplates', 'selfPublishingList', 'learnerSelfPublishingList',
            'timeRegisters', 'projects', 'certificates', 'projects', 'bookSaleTypes', 'tasks'));
    }

    public function update($id, Request $request)
    {
        $learner = User::findOrFail($id);

        switch ($request->field) {
            case 'password':
                $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }

                $learner->password = bcrypt($request->password);
                $learner->save();

                return redirect()->back()->with(['profile_success' => 'Password updated successfully.']);
                break;

            case 'contact':
                $learner->first_name = $request->first_name;
                $learner->last_name = $request->last_name;
                $learner->save();

                $address = Address::firstOrNew([
                    'user_id' => $learner->id,
                ]);
                $address->phone = $request->phone;
                $address->street = $request->street;
                $address->zip = $request->zip;
                $address->city = $request->city;
                $address->save();

                if (! $learner->fiken_contact_id || $learner->fiken_contact_id == 'none') {
                    CheckFikenContactCommand::updateFikenContactId($learner);
                }

                if ($learner->fiken_contact_id && $learner->fiken_contact_id != 'none') {
                    dispatch(new UpdateFikenContactDetailsJob($learner));
                }

                return redirect()->back()->with(['profile_success' => 'Contact Info updated successfully.']);
                break;
        }

    }

    public function removeLearner(Request $request)/* : RedirectResponse */
    {
        $learner = User::findOrFail($request->learner_id);
        $package = Package::findOrFail($request->package_id);
        $course = Course::findOrFail($package->course_id);

        $packageIds = $course->packages->pluck('id')->toArray();
        $courseTaken = CoursesTaken::where('user_id', $learner->id)->whereIn('package_id', $packageIds)
            ->withTrashed()->first();

        if ($courseTaken) {
            // Check if course has year extension
            if ($courseTaken->package->course->extend_courses > 0) {
                foreach ($learner->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken) {
                    $learnerCourseTaken->years = 1;
                    $learnerCourseTaken->save();
                }
            }
            // delete related email history
            /*EmailHistory::where('parent', 'LIKE', '%courses-taken%')
                ->where('parent_id', $courseTaken->id)->delete();*/

            if ($request->has('is_permanent')) {
                //if ($courseTaken->is_pay_later) {
                    Order::where(
                        [
                            'user_id' => $courseTaken->user_id,
                            'package_id' => $courseTaken->package_id,
                            'is_processed' => 1,
                            'is_pay_later' => 1
                        ]
                    )->delete();
                //}
                $courseTaken->forceDelete();
            } else {
                $courseTaken->delete();
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner removed successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function addLearner(Request $request): RedirectResponse
    {
        $learner = User::findOrFail($request->learner_id);
        $package = Package::findOrFail($request->package_id);
        $course = Course::findOrFail($package->course_id);

        $packageIds = $course->packages->pluck('id')->toArray();
        $courseTaken = CoursesTaken::where('user_id', $learner->id)->whereIn('package_id', $packageIds)->first();

        if (! $courseTaken) {
            $courseTaken = new CoursesTaken;
            $courseTaken->user_id = $learner->id;
            $courseTaken->package_id = $package->id;
        }

        $courseTaken->started_at = null;
        $courseTaken->is_active = 1;

        if ($course->is_free) {
            $started_at = now();
            $dayCount = $course->free_for_days == 0 ? 30 : $course->free_for_days;
            $end_date = Carbon::today()->addDays($dayCount)->format('Y-m-d');

            $courseTaken->is_free = 1;
            $courseTaken->started_at = $started_at;
            $courseTaken->end_date = $end_date;
        }

        $courseTaken->save();

        // Check if course has year extension
        if ($courseTaken->package->course->extend_courses > 0) {
            foreach ($learner->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken) {
                $learnerCourseTaken->years = $courseTaken->package->course->extend_courses;
                $learnerCourseTaken->save();
            }
        }

        // Check for included courses
        if ($courseTaken->package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                $includedCourse = CoursesTaken::firstOrNew(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id]);
                $includedCourse->is_active = true;
                $includedCourse->save();
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner added successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function addBulkLearners(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        $course = Course::findOrFail($package->course_id);

        $packageIds = $course->packages->pluck('id')->toArray();

        foreach($request->learner_ids as $learner_id) {
            $learner = User::findOrFail($learner_id);
            $courseTaken = CoursesTaken::where('user_id', $learner->id)->whereIn('package_id', $packageIds)->first();

            if (! $courseTaken) {
                $courseTaken = new CoursesTaken;
                $courseTaken->user_id = $learner->id;
                $courseTaken->package_id = $package->id;
            }

            $courseTaken->started_at = null;
            $courseTaken->is_active = 1;

            if ($course->is_free) {
                $started_at = now();
                $dayCount = $course->free_for_days == 0 ? 30 : $course->free_for_days;
                $end_date = Carbon::today()->addDays($dayCount)->format('Y-m-d');

                $courseTaken->is_free = 1;
                $courseTaken->started_at = $started_at;
                $courseTaken->end_date = $end_date;
            }

            $courseTaken->save();

            // Check if course has year extension
            if ($courseTaken->package->course->extend_courses > 0) {
                foreach ($learner->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken) {
                    $learnerCourseTaken->years = $courseTaken->package->course->extend_courses;
                    $learnerCourseTaken->save();
                }
            }

            // Check for included courses
            if ($courseTaken->package->included_courses->count() > 0) {
                foreach ($package->included_courses as $included_course) {
                    $includedCourse = CoursesTaken::firstOrNew(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id]);
                    $includedCourse->is_active = true;
                    $includedCourse->save();
                }
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learners added successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function activate_course_taken(Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        $isGroupCourse = 0;

        // added this line
        if ($courseTaken->package->course->type == 'Group') {
            $courseTaken->started_at = Carbon::now();
            $courseTaken->end_date = $courseTaken->package->validity_period > 0
                ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
            $isGroupCourse++;
        }

        $courseTaken->is_active = 1;
        $courseTaken->save();

        // Check if course has year extension
        if ($courseTaken->package->course->extend_courses > 0) {
            foreach ($courseTaken->user->coursesTaken->where('id', '<>', $courseTaken->id) as $learnerCourseTaken) {
                $learnerCourseTaken->years = $courseTaken->package->course->extend_courses;
                $learnerCourseTaken->save();
            }
        }

        // Check for included courses
        if ($courseTaken->package->included_courses->count() > 0) {
            foreach ($courseTaken->package->included_courses as $included_course) {
                $hasIncludedCourseAlready = CoursesTaken::where(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id])->first();
                $includedCourse = CoursesTaken::firstOrNew(['user_id' => $courseTaken->user->id, 'package_id' => $included_course->included_package_id]);

                // check if not started yet
                if ($hasIncludedCourseAlready && ! $includedCourse->started_at) {
                    $includedCourse->started_at = Carbon::now(); // added this one
                }

                $includedCourse->end_date = $courseTaken->package->validity_period > 0
                    ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
                $includedCourse->is_active = true;
                $includedCourse->save();
            }
        }

        // check if the course to activate is group course
        // then update all of the end date to the same date
        if ($isGroupCourse > 0) {
            $user_id = $courseTaken->user_id;
            $end_date = $courseTaken->package->validity_period > 0
                ? Carbon::today()->addMonth($courseTaken->package->validity_period) : Carbon::today()->addYear(1);
            CoursesTaken::where('user_id', $user_id)
                ->update(['end_date' => $end_date]);
        }

        return redirect()->back();
    }

    public function delete_course_taken(Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        // delete related email history
        /*EmailHistory::where('parent', 'LIKE', '%courses-taken%')
            ->where('parent_id', $courseTaken->id)->delete();*/
        $courseTaken->delete();

        return redirect()->back();
    }

    public function activate_shop_manuscript_taken(Request $request): RedirectResponse
    {
        $courseTaken = ShopManuscriptsTaken::findOrFail($request->shop_manuscript_id);
        $courseTaken->is_active = 1;
        $courseTaken->save();

        return redirect()->back();
    }

    public function delete_shop_manuscript_taken(Request $request): RedirectResponse
    {
        $courseTaken = ShopManuscriptsTaken::findOrFail($request->shop_manuscript_id);
        $courseTaken->forceDelete();

        return redirect()->back();
    }

    public function shopManuscriptTakenShow($id, $shopManuscriptTakenID): View
    {
        $learner = User::findOrFail($id);
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $shopManuscriptTakenID)->where('user_id', $learner->id)->firstOrFail();

        $eEFDate = strftime('%Y-%m-%d', strtotime($shopManuscriptTaken->editor_expected_finish));
        $hiddenEditors = \DB::select("CALL getIDWhereHidden('$eEFDate')");
        $hiddenEditorIds = [];
        if ($hiddenEditors) {
            foreach ($hiddenEditors as $key) {
                $hiddenEditorIds[] = $key->editor_id;
            }
        }
        $editor = User::where(function ($query) {
            $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
        })
            ->whereHas('editorGenrePreferences', function ($q) use ($shopManuscriptTaken) {
                $q->where('genre_id', $shopManuscriptTaken->genre);
            })
            ->where('is_active', 1)
            ->whereNotIn('users.id', $hiddenEditorIds)
            ->orderBy('id', 'desc')
            ->get();
        if ($editor->count() < 1) {
            $editor = User::where(function ($query) {
                $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
            })
                ->where('is_active', 1)
                ->whereNotIn('users.id', $hiddenEditorIds)
                ->orderBy('id', 'desc')
                ->get();
        }
        $emailTemplate = EmailTemplate::where('page_name', '=', 'Manuscript')->first();

        return view('backend.learner.shopManuscriptTaken', compact('shopManuscriptTaken', 'learner', 'emailTemplate', 'editor'));
    }

    public function shopManuscriptTakenShowEditorPreview($id, $shopManuscriptTakenID): View
    {
        $learner = User::findOrFail($id);
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $shopManuscriptTakenID)->where('user_id', $learner->id)->firstOrFail();
        $editor = User::where(function ($query) {
            $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
        })
            ->whereHas('editorGenrePreferences', function ($q) use ($shopManuscriptTaken) {
                $q->where('genre_id', $shopManuscriptTaken->genre);
            })
            ->orderBy('id', 'desc')
            ->get();
        if ($editor->count() < 1) {
            $editor = User::where(function ($query) {
                $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
            })
                ->orderBy('id', 'desc')
                ->get();
        }
        $emailTemplate = EmailTemplate::where('page_name', '=', 'Manuscript')->first();

        return view('backend.editor.shopManuscriptTakenPreview', compact('shopManuscriptTaken', 'learner', 'emailTemplate', 'editor'));
    }

    public function shopManuscriptTakenShowComment($id, $shopManuscriptTakenID, Request $request)
    {
        $learner = User::findOrFail($id);
        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $shopManuscriptTakenID)->where('user_id', $learner->id)->firstOrFail();
        if (! empty($request->comment) && $shopManuscriptTaken->is_active) {
            $ShopManuscriptComment = new ShopManuscriptComment;
            $ShopManuscriptComment->shop_manuscript_taken_id = $shopManuscriptTaken->id;
            $ShopManuscriptComment->user_id = Auth::user()->id;
            $ShopManuscriptComment->comment = $request->comment;
            $ShopManuscriptComment->save();

            return redirect()->back();
        } else {
            return abort('503');
        }
    }

    /**
     *  Get the statistics
     */
    public function goalStatistic($goal_id): JsonResponse
    {
        $statistics = [];
        $totalStatistic = 0;
        $goal = \App\WordWrittenGoal::find($goal_id);
        $from_ymd = date('Y-m-d', strtotime($goal->from_date));
        $to_ymd = date('Y-m-d', strtotime($goal->to_date));

        $statisticsData = \App\WordWritten::where('user_id', $goal->user_id)
            ->whereBetween('date', [$from_ymd, $to_ymd])
            ->select(\DB::raw('sum(words) as `words`'), \DB::raw('YEAR(date) year, MONTH(date) month'))
            ->groupby('year', 'month')
            ->get();

        foreach ($statisticsData as $statistic) {
            $statistics[] = [
                'words' => (int) $statistic['words'],
                'year' => $statistic['year'],
                'month' => FrontendHelpers::convertMonthLanguage($statistic['month']),
            ];
            $totalStatistic += $statistic['words'];
        }
        $statistics[] = [
            'words' => $totalStatistic,
            'month' => 'Total Words',
        ];

        return response()->json($statistics);
    }

    public function updateInvoiceDue($invoice_id, Request $request): RedirectResponse
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            $company = 'forfatterskolen-as';
            $fikenInvoiceUrl = 'https://api.fiken.no/api/v2/companies/'.$company.'/invoices/'
                .$invoice->fiken_invoice_id;
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.config('services.fiken.personal_api_key'),
                'Content-Type: Application/json',
            ];

            $fields = [
                'newDueDate' => $request->due_date,
            ];
            $field_string = json_encode($fields, true);

            $ch = curl_init($fikenInvoiceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
            $data = curl_exec($ch);
            Log::info('update due invoice after curl request');
            Log::info(json_encode($data));
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
                abort($http_code); // display error page instead of the Whoops page
            }

            curl_close($ch);

            $invoice->fiken_dueDate = $request->due_date;
            $invoice->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Due date updated'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete learner invoice
     */
    public function deleteInvoice($invoice_id): RedirectResponse
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            $invoice->forceDelete();

            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Invoice deleted successfully.'),
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    public function addFikenCreditNote($invoice_id, Request $request)
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            $request->validate([
                'issue_date' => 'required',
            ]);

            $fields = [
                'invoiceId' => $invoice->fiken_invoice_id,
                'issueDate' => $request->issue_date,
                'creditNoteText' => $request->credit_note,
            ];

            $company = 'forfatterskolen-as';
            $fikenUrl = 'https://api.fiken.no/api/v2/companies/'.$company.'/creditNotes/full';
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.config('services.fiken.personal_api_key'),
                'Content-Type: Application/json',
            ];

            $field_string = json_encode($fields, true);
            $ch = curl_init($fikenUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            // this function is called by curl for each header received
            $curlHeaders = [];
            curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                function ($curl, $header) use (&$curlHeaders) {
                    $len = strlen($header);
                    $header = explode(':', $header, 2);
                    if (count($header) < 2) { // ignore invalid headers
                        return $len;
                    }

                    $curlHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

                    return $len;
                }
            );

            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $data = substr($response, $header_size);

            // get the http code response
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
                $data = json_decode($data);

                if (isset($data->error_description)) {
                    $error_message = $data->error_description;
                } else {
                    $error_message = $data[0]->message;
                }

                return redirect()->back()->with([
                    'alert_type' => 'danger',
                    'errors' => AdminHelpers::createMessageBag($error_message),
                    'not-former-courses' => true,
                ]);
            }

            curl_close($ch);

            $invoice->fiken_is_paid = 3;
            $invoice->save();

            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Credit note saved.'),
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Remove learner from webinar-pakke
     */
    public function deleteFromCourse($course_taken_id, Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::find($course_taken_id);
        if ($courseTaken) {

            // remove from mailing list
            $user_email = $courseTaken->user->email;
            $automation_id = 82;
            $user_name = $courseTaken->user->first_name;

            // check if webinar-pakke and add to automation
            if ($courseTaken->package->course->id == 17) {
                AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
            }

            // delete related email history
            /*EmailHistory::where('parent', 'LIKE', '%courses-taken%')
            ->where('parent_id', $courseTaken->id)->delete();*/
            if ($request->has('is_permanent')) {
                //if ($courseTaken->is_pay_later) {
                    Order::where(
                        [
                            'user_id' => $courseTaken->user_id,
                            'package_id' => $courseTaken->package_id,
                            'is_processed' => 1,
                            'is_pay_later' => 1
                        ]
                    )->delete();
                //}
                $courseTaken->forceDelete();
            } else {
                $courseTaken->delete();
            }

            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Learner removed from '
            .$courseTaken->package->course->title.' successfully.'),
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Renew a learners course
     */
    public function renewCourse($learner_id, $course_taken_id): RedirectResponse
    {
        $courseTaken = CoursesTaken::where(['user_id' => $learner_id, 'id' => $course_taken_id])->first();
        if ($courseTaken) {
            $user = User::find($learner_id);
            $package = Package::findOrFail($courseTaken->package_id);
            $payment_mode = 'Bankoverføring';
            $price = (int) 1490 * 100;
            $product_ID = 280763803; // $package->full_price_product;
            $send_to = $user->email;
            $dueDate = Carbon::today()->addDay(14)->format('Y-m-d');

            $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
            $comment .= 'Betalingsmodus: '.$payment_mode.')';

            $invoice_fields = [
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $product_ID,
                'email' => $send_to,
                'telephone' => $user->address->phone,
                'address' => $user->address->street,
                'postalPlace' => $user->address->city,
                'postalCode' => $user->address->zip,
                'comment' => $comment,
                'payment_mode' => 'Faktura',
            ];
            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);

            // update all the started at of each courses taken
            foreach ($user->coursesTaken as $coursesTaken) {
                $notExpiredCourses = $courseTaken->user->coursesTakenNotExpired()->pluck('id')->toArray();
                // check if there's other course that's not expired yet and update it
                //if ($coursesTaken->id !== $courseTaken->id) {
                    // check if course taken have set end date and add one year to it
                    if ($coursesTaken->end_date) {
                        $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                        $dateToday = Carbon::today();

                        // check if the end date after adding a year is still less than today
                        // add another year on date today
                        if (Carbon::parse($addYear)->lt($dateToday)) {
                            $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($dateToday)).' + 1 year'));
                        }

                        $coursesTaken->end_date = $addYear;
                    }

                    $coursesTaken->renewed_at = Carbon::now();
                    $coursesTaken->save();
                //}
            }

            // check if course taken have set end date and add one year to it
            /* if ($courseTaken->end_date) {
                $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($courseTaken->end_date)).' + 1 year'));
                $courseTaken->end_date = $addYear;
            } */

            /* $coursesTaken->renewed_at = Carbon::now();
            $courseTaken->started_at = Carbon::now();
            $courseTaken->save(); */

            // create order record
            $newOrder['user_id'] = $user->id;
            $newOrder['item_id'] = $package->course_id;
            $newOrder['type'] = Order::COURSE_TYPE;
            $newOrder['package_id'] = $package->id;
            $newOrder['plan_id'] = 8; // Full payment
            $newOrder['price'] = $price / 100;
            $newOrder['discount'] = 0;
            $newOrder['payment_mode_id'] = 3; // Faktura
            $newOrder['is_processed'] = 1;
            $order = Order::create($newOrder);

            // add to automation
            $user_email = $user->email;
            $automation_id = 73;
            $user_name = $user->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar-pakke renewed'),
                'alert_type' => 'success', 'not-former-courses' => true]);

        }

        return redirect()->back();
    }

    public function updateDocumentShopManuscriptTaken($id, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($id);

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {

            $extensions = ['pdf', 'docx', 'odt'];
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();

            if (! in_array($extension, $extensions)) {
                return redirect()->back();
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts';
            $filePath = AdminHelpers::checkFileName($destinationPath, $shopManuscriptTaken->user_id, $extension); // rename document
            $expFileName = explode('/', $filePath);

            $request->manuscript->move($destinationPath, end($expFileName));

            if ($extension == 'pdf') {
                $pdf = new \PdfToText($filePath);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($filePath);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = (int) $word_count;
            $shopManuscriptTaken->file = '/'.$filePath;
            $shopManuscriptTaken->manuscript_uploaded_date = Carbon::now()->toDateTimeString();
            $shopManuscriptTaken->words = $word_count;
        }

        $shopManuscriptTaken->save();

        return redirect()->back();
    }

    public function addShopManuscript($id, Request $request)/* : RedirectResponse */
    {
        $learner = User::findOrFail($id);
        $shopManuscript = ShopManuscript::findOrFail($request->shop_manuscript_id);

        $shopManuscriptTaken = new ShopManuscriptsTaken;
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $time = time();
            $destinationPath = 'storage/shop-manuscripts/'; // upload path
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            
            $extensions = ['pdf', 'docx', 'odt'];
            if (! in_array($extension, $extensions)) {
                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('File extension must be pdf, docx, odt.'),
                    'alert_type' => 'danger',
                    'not-former-courses' => true,
                ]);
            }

            // count words
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.$fileName);
                $pdf_content = $pdf->Text;
                $word_count = AdminHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = AdminHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.$fileName);
                $word_count = AdminHelpers::get_num_of_words($doc);
            }
            $shopManuscriptTaken->file = '/'.$destinationPath.$fileName;
            $shopManuscriptTaken->words = $word_count;
        }
        $shopManuscriptTaken->user_id = $learner->id;
        $shopManuscriptTaken->shop_manuscript_id = $shopManuscript->id;
        $shopManuscriptTaken->is_active = true;
        $shopManuscriptTaken->save();

        return redirect()->back();
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $learner = User::findOrFail($id);

        if ($request->has('move_learner_id')) {
            $moveLearner = User::findOrFail($request->move_learner_id);

            if ($request->moveStatus && count($request->moveItems) > 0 && $request->move_learner_id) {
                if (in_array('courses_taken', $request->moveItems)) {
                    $learner->coursesTaken()->update([
                        'user_id' => $moveLearner->id,
                    ]);
                }

                if (in_array('shop_manuscripts', $request->moveItems)) {
                    $learner->shopManuscriptsTaken()->update([
                        'user_id' => $moveLearner->id,
                    ]);
                }

                if (in_array('invoices', $request->moveItems)) {
                    $learner->invoices()->update([
                        'user_id' => $moveLearner->id,
                    ]);
                }

                if (in_array('assignments', $request->moveItems)) {
                    AssignmentGroupLearner::where('user_id', $id)->update([
                        'user_id' => $moveLearner->id,
                    ]);
                    $learner->assignmentManuscripts()->update([
                        'user_id' => $moveLearner->id,
                    ]);
                }

                if (in_array('diplomas', $request->moveItems)) {
                    $learner->diplomas()->update([
                        'user_id' => $moveLearner->id,
                    ]);
                }
            }

            $learner->orders()->update([
                'user_id' => $moveLearner->id,
            ]);

            $learner->courseOrderAttachments()->update([
                'user_id' => $moveLearner->id,
            ]);

            $learnerAssignmentManuscripts = $learner->assignmentManuscripts->pluck('id');
            $learnerShopManuscriptsTaken = $learner->shopManuscriptsTaken->pluck('id');
            $learnerCoursesTaken = $learner->coursesTaken->pluck('id');
            $registeredWebinarLists = $learner->registeredWebinars->pluck('id');
            $learnerInvoices = $learner->invoices->pluck('id');
            $emailHistories = EmailHistory::where(function ($query) use ($learnerAssignmentManuscripts) {
                $query->where('parent', 'LIKE', 'assignment-manuscripts%');
                $query->whereIn('parent_id', $learnerAssignmentManuscripts);
            })
                ->orWhere(function ($query) use ($learnerShopManuscriptsTaken) {
                    $query->where('parent', 'LIKE', 'shop-manuscripts-taken%');
                    $query->whereIn('parent_id', $learnerShopManuscriptsTaken);
                })
                ->orWhere(function ($query) use ($learnerCoursesTaken) {
                    $query->where('parent', 'LIKE', 'courses-taken%');
                    $query->whereIn('parent_id', $learnerCoursesTaken);
                })
                ->orWhere(function ($query) use ($registeredWebinarLists) {
                    $query->where('parent', '=', 'webinar-registrant');
                    $query->whereIn('parent_id', $registeredWebinarLists);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', '=', 'learner');
                    $query->where('parent_id', $learner->id);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', '=', 'free-manuscripts');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learnerInvoices) {
                    $query->where('parent', '=', 'invoice');
                    $query->whereIn('parent_id', $learnerInvoices);
                })
                ->orWhere(function ($query) use ($learnerInvoices) {
                    $query->where('parent', '=', 'invoice');
                    $query->whereIn('parent_id', $learnerInvoices);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'copy-editing%');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'correction%');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('parent', 'LIKE', 'gift-purchase');
                    $query->where('recipient', $learner->email);
                })
                ->orWhere(function ($query) use ($learner) {
                    $query->where('recipient', $learner->email);
                })
                ->latest()
                ->withTrashed()
                ->pluck('id')->toArray();

            EmailHistory::whereIn('id', $emailHistories)->update([
                'recipient' => $moveLearner->email,
            ]);
        }

        // $learner->forceDelete();
        $learner->delete();

        return redirect(route('admin.learner.index'))->with([
            'errors' => AdminHelpers::createMessageBag('Learner deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * update the course taken started at field
     */
    public function updateCourseTakenStartedAt($id, Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $courseTaken->started_at = $request->started_at;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Course started at updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setCourseTakenAvailability($id, Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        // check if the course to update is Webinar pakke then update all the courses end date
        if ($courseTaken->package->course_id == 17) {
            $userCourses = CoursesTaken::where('user_id', $courseTaken->user_id);
            $userCourses->update(['end_date' => $request->end_date]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Course taken availability updated successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        $courseTaken->start_date = $request->start_date;
        $courseTaken->end_date = $request->end_date;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Course taken availability updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setCourseTakenDisableDate($id, Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        $courseTaken->disable_start_date = $request->disable_start_date;
        $courseTaken->disable_end_date = $request->disable_end_date;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Course taken disable date updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setLearnerDisableDate($id, Request $request)
    {
        $user = User::findOrFail($id);

        $user->disable_start_date = $request->disable_start_date;
        $user->disable_end_date = $request->disable_end_date;
        $user->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('User disable date updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function removeCourseTakenDisableDate($id)
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        $courseTaken->disable_start_date = null;
        $courseTaken->disable_end_date = null;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Course taken disable date removed successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function removeLearnerDisableDate($id)
    {
        $user = User::findOrFail($id);

        $user->disable_start_date = null;
        $user->disable_end_date = null;
        $user->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('User disable date removed successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function sendRegretForm($course_taken_id, Request $request, CourseService $courseService): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($course_taken_id);
        $package = $courseTaken->package;
        $user = $courseTaken->user;

        $attachments = [
            public_path($courseService->generateDocx($courseTaken->user_id, $courseTaken->package_id)),
            public_path('/email-attachments/skjema-for-opplysninger-om-angrerett.docx')
        ];

        $email_content = $request->email_content ?: '';
        dispatch(new AddMailToQueueJob($user->email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order', $courseTaken->id));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Regret schema sent.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function allow_lesson_access($course_taken_id, $lesson_id): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($course_taken_id);
        $lesson = Lesson::findOrFail($lesson_id);
        if ($courseTaken->package->course->id == $lesson->course->id) {
            $lesson_access = $courseTaken->access_lessons;
            if (! in_array($lesson->id, $lesson_access)) {
                $lesson_access[] = $lesson->id;
            }
            $courseTaken->access_lessons = json_encode($lesson_access);
            $courseTaken->save();
        }

        return redirect()->back();
    }

    public function default_lesson_access($course_taken_id, $lesson_id): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($course_taken_id);
        $lesson = Lesson::findOrFail($lesson_id);
        if ($courseTaken->package->course->id == $lesson->course->id) {
            $lesson_access = $courseTaken->access_lessons;
            $new_lesson_access = array_diff($lesson_access, [$lesson->id]);
            $courseTaken->access_lessons = json_encode($new_lesson_access);
            $courseTaken->save();
        }

        return redirect()->back();
    }

    public function setCourseTakenExpiryReminder($id, Request $request): JsonResponse
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $courseTaken->send_expiry_reminder = $request->send_expiry_reminder;
        $courseTaken->save();

        return response()->json();
    }

    public function addToWorkshop(Request $request): RedirectResponse
    {
        $workshop = Workshop::find($request->workshop_id);
        $menu = WorkshopMenu::where('workshop_id', $request->workshop_id)->first();

        if (! $menu) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Please add a menu on the workshop before assigning it to learner.'),
                'alert_type' => 'danger',
                'not-former-courses' => true,
            ]);
        }

        $workshopTaken = new WorkshopsTaken;
        $workshopTaken->user_id = $request->user_id;
        $workshopTaken->workshop_id = $workshop->id;
        $workshopTaken->menu_id = $menu->id;
        $workshopTaken->notes = null;
        $workshopTaken->is_active = false;
        $workshopTaken->save();

        return redirect()->back();
    }

    /**
     * Update the workshop count of the leaner
     */
    public function updateWorkshopCount($id, Request $request): RedirectResponse
    {
        $workshopTakenCount = WorkshopTakenCount::firstOrNew([
            'user_id' => $id,
        ]);
        $workshopTakenCount->workshop_count = $request->workshop_count;
        $workshopTakenCount->save();

        return redirect()->back();
    }

    /**
     * Download the synopsis attached to the manuscript
     *
     * @param  $id  ShopManuscriptsTaken id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscriptSynopsis($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($id);
        if ($shopManuscriptTaken) {
            $filename = $shopManuscriptTaken->synopsis;

            return response()->download(public_path($filename));
        }

        return redirect('shop-manuscript');
    }

    /**
     * Update the synopsis field
     *
     * @param  $id  ShopManuscriptsTaken id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSynopsis($id, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($id);
        if ($shopManuscriptTaken) {
            if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
                $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);
                $extensions = ['pdf', 'docx', 'odt'];

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $time = time();
                $destinationPath = 'storage/shop-manuscripts-synopsis/';
                $fileName = $time.'.'.$extension; // rename document
                $request->synopsis->move($destinationPath, $fileName);
                $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
                $shopManuscriptTaken->save();
            }

            return redirect()->back();
        }

        return redirect('shop-manuscript');
    }

    /**
     * Send Email to learner
     */
    public function sendLearnerEmail($id, Request $request): RedirectResponse
    {
        $learner = User::find($id);
        if (! $learner) {
            return redirect()->back();
        }

        $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);

        $data = $request->except('_token');
        $data['email'] = $data['message'];
        $learner->emails()->create($data);

        $from_email = $request->from_email ?: 'postmail@forfatterskolen.no';
        $from_name = $request->from_name ?: 'Forfatterskolen';

        $email = $learner->email;
        $encode_email = encrypt($email);
        $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
        $password = $learner->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

        if (strpos($request->message, '[redirect]')) {
            $extractLink = FrontendHelpers::getTextBetween($request->message, '[redirect]', '[/redirect]');
            $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
            $redirectLabel = FrontendHelpers::getTextBetween($request->message, '[redirect_label]', '[/redirect_label]');
            $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
            $search_string = [
                '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                ':firstname',
            ];
            $replace_string = [
                $redirectLink, '', $learner->first_name,
            ];
            $message = str_replace($search_string, $replace_string, $request->message);
        } else {
            $search_string = [
                '[login_link]', '[username]', '[password]', ':firstname',
            ];
            $replace_string = [
                $loginLink, $email, $password, $learner->first_name,
            ];
            $message = str_replace($search_string, $replace_string, $request->message);
        }

        $emailData['email_subject'] = $request->subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = $from_name;
        $emailData['from_email'] = $from_email;
        $emailData['attach_file'] = null;

        // \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
        dispatch(new AddMailToQueueJob($email, $request->subject, $message, $from_email, $from_name, null,
            'learner', $learner->id));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function sendEmail($id, Request $request): RedirectResponse
    {
        $learner = User::findOrFail($id);
        $to = $learner->email;
        $from = 'elin@forfatterskolen.no'; // $request->from_email;
        $message = $request->message;
        $subject = $request->subject;
        // AdminHelpers::send_mail( $to, $subject, $message, $from);
        /*AdminHelpers::send_email($subject,
            $from, $to, $message);*/

        $search_string = [
            ':firstname',
        ];
        $replace_string = [
            $learner->first_name,
        ];

        $message = str_replace($search_string, $replace_string, $message);

        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = $from;
        $emailData['attach_file'] = null;

        // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        dispatch(new AddMailToQueueJob($to, $subject, $message, $from, null, null,
            'shop-manuscripts-taken', $request->shop_manuscripts_taken_id));

        return redirect()->back();
    }

    public function sendWebinarRegistrantEmail($learner_id, $registrant_id, Request $request): RedirectResponse
    {
        $learner = User::findOrFail($learner_id);
        $to = $learner->email;
        $from = $request->from_email;
        $subject = $request->subject;
        $message = $request->message;

        $message = AdminHelpers::formatEmailContent($message, '', $learner->first_name, '');
        $message = str_replace(':url', $request->join_url, $message);

        dispatch(new AddMailToQueueJob($to, $subject, $message, $from, null, null,
            'webinar-registrant', $registrant_id));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Email sent.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    public function addNotes($id, Request $request): RedirectResponse
    {
        $user = User::find($id);
        if ($user) {
            $user->notes = $request->notes;
            $user->save();
        }

        return redirect()->back();
    }

    /**
     * List learners that have notes
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listNotes(): View
    {
        $userNotes = User::whereNotNull('notes')->where('notes', '<>', '')
            ->orderBy('id', 'DESC')
            ->paginate(25);

        return view('backend.learner.list_notes', compact('userNotes'));
    }

    public function generatePassword()
    {
        return AdminHelpers::generateHash(8);
    }

    public function registerLearner(Request $request, LearnerService $learnerService): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        $learnerService->registerLearner($request);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Learner created successfully.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

    /**
     * Update manuscript locked status
     */
    public function updateManuscriptLockedStatus(Request $request): JsonResponse
    {
        $shopManuscriptsTaken = ShopManuscriptsTaken::find($request->shop_manuscript_taken_id);
        $success = false;

        if ($shopManuscriptsTaken) {
            $shopManuscriptsTaken->is_manuscript_locked = $request->is_manuscript_locked;
            $shopManuscriptsTaken->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function loginActivity($login_id)
    {
        $login = LearnerLogin::find($login_id);
        if ($login) {
            return view('backend.learner.login_activity', compact('login'));
        }

        return redirect()->back();
    }

    /**
     * Add to correction or copy editing
     */
    public function addOtherService($user_id, Request $request): RedirectResponse
    {
        if ($user = User::find($user_id)) {
            $data = $request->except('_token');

            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                    ]);
                }

                $destinationPath = 'storage/correction-manuscripts/'; // upload path

                if ($data['is_copy_editing'] == 1) {
                    $destinationPath = 'storage/copy-editing-manuscripts/'; // upload path
                }

                $time = time();
                $fileName = $time.'.'.$extension; // $original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;

                $word_per_price = 1000;
                $price_per_word = 25;
                $title = 'Korrektur';

                if ($data['is_copy_editing'] == 1) {
                    $word_per_price = 1000;
                    $price_per_word = 30;
                    $title = 'Språkvask';
                }

                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);
                $calculated_price = ($rounded_word / $word_per_price) * $price_per_word;
                $productID = $data['is_copy_editing'] == 1 ? 599886093 : 599110997;
                $data['price'] = $calculated_price;

                // check if the admin wants to send out invoice
                if (isset($data['send_invoice'])) {
                    $paymentMode = PaymentMode::findOrFail(3); // hardcoded faktura payment
                    $paymentPlan = PaymentPlan::findOrFail(6);
                    $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

                    $comment = '(Manuskript: '.$title.', ';
                    $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
                    $comment .= 'Betalingsplan: 14 dager)';

                    $dueDate = date('Y-m-d');
                    $dueDate = Carbon::parse($dueDate);

                    $dueDate->addDays(14);

                    $dueDate = date_format(date_create($dueDate), 'Y-m-d');
                    $price = $data['price'] * 100;

                    $invoice_fields = [
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'netAmount' => $price,
                        'dueDate' => $dueDate,
                        'description' => 'Kursordrefaktura',
                        'productID' => $productID,
                        'email' => $user->email,
                        'telephone' => $user->telephone,
                        'address' => $user->street,
                        'postalPlace' => $user->city,
                        'postalCode' => $user->zip,
                        'comment' => $comment,
                        'payment_mode' => $paymentMode->mode,
                    ];

                    $invoice = new FikenInvoice;
                    $invoice->create_invoice($invoice_fields);
                }

                $manuType = 'Correction';
                if ($data['is_copy_editing'] == 1) {
                    $manuType = 'Copy Editing';
                    CopyEditingManuscript::create([
                        'user_id' => $user_id,
                        'file' => $file,
                        'payment_price' => $data['price'],
                        'editor_id' => $request->exists('editor_id') ? $data['editor_id'] : null,
                    ]);
                } else {
                    CorrectionManuscript::create([
                        'user_id' => $user_id,
                        'file' => $file,
                        'payment_price' => $data['price'],
                        'editor_id' => $request->exists('editor_id') ? $data['editor_id'] : null,
                    ]);
                }

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                    'alert_type' => 'success',
                    'not-former-courses' => true,
                ]);
            }

        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Assign editor to other service manuscript
     */
    public function otherServiceAssignEditor($service_id, $service_type, Request $request): RedirectResponse
    {
        if ($service_type == 1 || $service_type == 2 || $service_type == 3) {
            if ($service_type == 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->editor_id = $request->editor_id;
                $copyEditing->save();
            }

            if ($service_type == 2) {
                $correction = CorrectionManuscript::find($service_id);
                $correction->editor_id = $request->editor_id;
                $correction->save();
            }

            if ($service_type == 3) {
                $correction = CoachingTimerManuscript::find($service_id);
                $correction->editor_id = $request->editor_id;
                $correction->save();
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Editor assigned successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    public function deleteOtherService($service_id, $service_type): RedirectResponse
    {
        if ($service_type == 1 || $service_type == 2 || $service_type == 3) {
            if ($service_type == 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->delete();
            }

            if ($service_type == 2) {
                $correction = CorrectionManuscript::find($service_id);
                $correction->delete();
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Add coaching session for a user
     */
    public function addCoachingTimer($user_id, Request $request): RedirectResponse
    {
        if ($user = User::find($user_id)) {
            $data = $request->except('_token');
            $data['price'] = 1690;
            /*$suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }*/

            $extensions = ['doc', 'docx', 'odt', 'pdf'];
            $file = null;

            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                    ]);
                }

                $destinationPath = 'storage/coaching-timer-manuscripts/'; // upload path

                $time = time();
                $fileName = $time.'.'.$extension; // $original_filename; // rename document0
                $file = $destinationPath.$fileName;
                $request->manuscript->move($destinationPath, $fileName);

                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);

                $word_7500_price = 690;
                $excess_word = 0;
                $excess_word_price = 0;

                // the initial calculated word is 7500 if excess then calculate the total excess price
                if ($word_count > 7500) {
                    $excess_word = $word_count - 7500;
                    // 69 is the price for every 1250 that is excess
                    $excess_word_price = ceil($excess_word / 1250) * 69;
                }

                $price = $word_7500_price + $excess_word_price;
                $data['price'] = $data['price'] + $price;

            }

            // check if the admin wants to send an invoice to the user
            if (isset($data['send_invoice'])) {

                $title = 'Coaching time';
                if ($data['plan_type'] == 1) {
                    $title .= ' (1 time)';
                    $productID = 601355457;
                } else {
                    $title .= ' (0,5 time)';
                    $productID = 601355458;
                }

                $paymentMode = PaymentMode::findOrFail(3); // hardcoded faktura payment
                $paymentPlan = PaymentPlan::findOrFail(6);
                $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

                $comment = '(Manuskript: '.$title.', ';
                $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
                $comment .= 'Betalingsplan: 14 dager)';

                $dueDate = date('Y-m-d');
                $dueDate = Carbon::parse($dueDate);

                $dueDate->addDays(14);

                $dueDate = date_format(date_create($dueDate), 'Y-m-d');
                $price = $data['price'] * 100;

                $invoice_fields = [
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'netAmount' => $price,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $productID,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'address' => $user->street,
                    'postalPlace' => $user->city,
                    'postalCode' => $user->zip,
                    'comment' => $comment,
                    'payment_mode' => $paymentMode->mode,
                ];

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields);
            }

            CoachingTimerManuscript::create([
                'user_id' => $user_id,
                'file' => $file,
                'payment_price' => $data['price'],
                'plan_type' => $data['plan_type'],
                'editor_id' => $request->exists('editor_id') ? $data['editor_id'] : null,
                'is_approved' => 1,
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Coaching session added successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);

        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Add diploma to user
     */
    public function addDiploma($learner_id, Request $request): RedirectResponse
    {
        if ($learner = User::find($learner_id)) {
            $data = $request->except('_token');
            $extensions = ['pdf'];

            if ($request->hasFile('diploma') && $request->file('diploma')->isValid()) {
                $extension = pathinfo($_FILES['diploma']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->diploma->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                        'not-former-courses' => true,
                    ]);
                }

                $destinationPath = 'storage/diploma'; // upload path

                // check if path not exists then create it
                if (! File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                // check the file name and add/increment number if the filename already exists
                $file = AdminHelpers::checkFileName($destinationPath, $filename, $extension);

                $request->diploma->move($destinationPath, $file);

                $data['diploma'] = $file;

                $learner->diplomas()->create($data);

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Diploma added successfully.'),
                    'alert_type' => 'success',
                    'not-former-courses' => true,
                ]);
            }
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Edit diploma details
     */
    public function editDiploma($id, Request $request): RedirectResponse
    {
        if ($diploma = Diploma::find($id)) {
            $data = $request->except('_token');
            $extensions = ['pdf'];
            if ($request->hasFile('diploma') && $request->file('diploma')->isValid()) {
                $extension = pathinfo($_FILES['diploma']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->diploma->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                        'not-former-courses' => true,
                    ]);
                }

                $destinationPath = 'storage/diploma'; // upload path

                // check if path not exists then create it
                if (! File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                // remove the previous file from server
                if (File::exists($diploma->diploma)) {
                    File::delete($diploma->diploma);
                }

                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                // check the file name and add/increment number if the filename already exists
                $file = AdminHelpers::checkFileName($destinationPath, $filename, $extension);

                $request->diploma->move($destinationPath, $file);

                $data['diploma'] = $file;
            }

            $diploma->update($data);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Diploma updated successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Delete the diploma the file inclded
     */
    public function deleteDiploma($id): RedirectResponse
    {
        if ($diploma = Diploma::find($id)) {

            // check first if the file exists to prevent error on deleting file
            if (File::exists($diploma->diploma)) {
                File::delete($diploma->diploma);
            }

            $diploma->delete();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Diploma deleted successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Download the diploma
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDiploma($id)
    {
        $shopManuscriptTaken = Diploma::find($id);
        if ($shopManuscriptTaken) {
            $filename = $shopManuscriptTaken->diploma;

            return response()->download(public_path($filename));
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Approve a coaching timer
     */
    public function approveCoachingTimer($id): RedirectResponse
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $coachingTimer->is_approved = 1;
            $coachingTimer->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Coaching timer approved successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->route('backend.dashboard');
    }

    /**
     * Update the note for a workshop taken
     */
    public function updateWorkshopTakenNotes($workshop_taken_id, Request $request): RedirectResponse
    {
        if ($workshopTaken = WorkshopsTaken::find($workshop_taken_id)) {
            $workshopTaken->notes = $request->notes;
            $workshopTaken->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Workshop note updated successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Add secondary email to user
     */
    public function addSecondaryEmail($learner_id, Request $request): RedirectResponse
    {
        $validator = Validator::make(($request->all()), [
            'email' => 'required|email|unique:users|unique:user_emails',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->with([
                    'alert_type' => 'danger',
                    'not-former-courses' => true,
                ]);
        }

        UserEmail::create([
            'user_id' => $learner_id,
            'email' => $request->email,
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email added successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * Set a new primary email
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function setPrimaryEmail($email_id)
    {
        $userEmail = UserEmail::find($email_id);
        $user = $userEmail->users->first();
        $primary = $userEmail->email;
        $secondary = $user->email;
        if (! $user->update(['email' => $primary])) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        if (! $userEmail->update(['email' => $secondary])) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        \DB::commit();

        $searchEmail = $secondary;
        $result = AdminHelpers::getActiveCampaignDataByEmail($searchEmail);
        // check if exists in any list
        if (isset($result['lists'])) {
            // check if subscriber in list 40
            if (isset($result['lists'][40])) {
                $list_data = $result['lists'][40];
                $user_id = $list_data['subscriberid'];

                $newEmail = $primary;
                AdminHelpers::updateActiveCampaignContactEmailForList($user_id, $newEmail, 40);
            }
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Secondary email set as primary.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);

    }

    public function removeSecondaryEmail($email_id): RedirectResponse
    {
        $userEmail = UserEmail::findOrFail($email_id);
        $userEmail->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Secondary email removed successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function saveForSaleBooks($user_id, Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => 'required',
        ]);
        $request->merge(['user_id' => $user_id]);

        UserBookForSale::updateOrCreate([
            'id' => $request->id,
        ], $request->except('id'));

        if ($request->isbn) {
            StorageDetail::where('user_book_for_sale_id', $request->id)
                ->update([
                    'isbn' => $request->isbn,
                ]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book for sale saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function deleteForSaleBooks($user_id, $id): RedirectResponse
    {
        UserBookForSale::find($id)->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book for sale deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function saveBookSales($user_id, Request $request): RedirectResponse
    {
        $request->merge(['user_id' => $user_id, 'user_book_for_sale_id' => $request->book_id]);

        UserBookSale::updateOrCreate([
            'id' => $request->id,
        ], $request->except('id', 'book_id'));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book sale saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function deleteBookSales($user_id, $id): RedirectResponse
    {
        UserBookSale::find($id)->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book sale deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * Create private message
     */
    public function addPrivateMessage($learner_id, Request $request): RedirectResponse
    {
        $learner = User::find($learner_id);
        if (! $learner) {
            return redirect()->to('/learner');
        }

        $request->validate([
            'message' => 'required',
        ]);

        PrivateMessage::create([
            'user_id' => $learner_id,
            'from_user' => Auth::user()->id,
            'message' => $request->message,
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Private message saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * Update private message
     */
    public function updatePrivateMessage($learner_id, $id, Request $request): RedirectResponse
    {
        $learner = User::find($learner_id);
        if (! $learner) {
            return redirect()->to('/learner');
        }

        $request->validate([
            'message' => 'required',
        ]);

        $privateMessage = PrivateMessage::find($id);
        $privateMessage->message = $request->message;
        $privateMessage->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Private message saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * Delete private message
     */
    public function deletePrivateMessage($learner_id, $id): RedirectResponse
    {
        $learner = User::find($learner_id);
        if (! $learner) {
            return redirect()->to('/learner');
        }

        $privateMessage = PrivateMessage::find($id);
        $privateMessage->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Private message deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setPreferredEditor($learner_id, Request $request): RedirectResponse
    {
        $learner = User::find($learner_id);
        if (! $learner) {
            return redirect()->to('/learner');
        }

        $preferredEditor = UserPreferredEditor::firstOrNew(['user_id' => $learner_id]);
        $preferredEditor->editor_id = $request->editor_id;
        $preferredEditor->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Preferred Editor saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function addSelfPublishing($learner_id, Request $request): RedirectResponse
    {
        SelfPublishingLearner::create([
            'user_id' => $learner_id,
            'self_publishing_id' => $request->self_publishing_id,
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self Publishing added successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function assignment($learner_id, $assignment_id)
    {
        $learner = User::find($learner_id);
        $assignment = Assignment::find($assignment_id);
        if (! $learner || ! $assignment) {
            return redirect()->to('/assignment?tab=learner');
        }
        $manuscript = AssignmentManuscript::where([
            'user_id' => $learner_id,
            'assignment_id' => $assignment_id,
        ])->first();
        $editors = AdminHelpers::editorList();

        return view('backend.learner.assignment', compact('assignment', 'learner', 'editors', 'manuscript'));
    }

    /**
     * Delete assignment add-on record
     */
    public function deleteAssignmentAddOn($learner_id, $assignment_id): RedirectResponse
    {
        $addOn = AssignmentAddon::where([
            'user_id' => $learner_id,
            'assignment_id' => $assignment_id,
        ])->firstOrFail();

        $addOn->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Assignment add-on deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setAutoRenewCourses($user_id, Request $request): RedirectResponse
    {
        $user = User::find($user_id);
        $user->auto_renew_courses = $request->auto_renew;
        $user->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Auto renew updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function setCouldBuyCourse($user_id, Request $request): RedirectResponse
    {
        $user = User::find($user_id);
        $user->could_buy_course = $request->could_buy_course;
        $user->save();

        $message = $request->could_buy_course ? 'User is allowed to buy course' : 'User is not allowed to buy course';

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($message),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function isPublishingLearner($user_id, Request $request)
    {
        $user = User::find($user_id);
        $user->is_self_publishing_learner = $request->is_self_publishing_learner;
        $user->save();
    }

    public function downloadCourseCertificate($user_id, $certificate_id)
    {
        $certificate = CourseCertificate::findOrFail($certificate_id);
        $course = $certificate->course;

        $user = User::find($user_id);

        $courseLearner = $user->coursesTaken()->withTrashed()->whereIn('package_id', $course->packages()->pluck('id'))
            ->firstOrFail();

        $issueDate = Carbon::parse($course->type === 'Single' ? Carbon::parse($courseLearner->started_at)->addDays(80) : $course->issue_date);

        $template = str_replace([
            '{LEARNERNAME}',
            '{COURSENAME}',
            '{COMPLETEDDATE}',
            '{ISSUEDDATE}',
        ],
            [
                $user->full_name,
                $course->title,
                $course->completed_date,
                $issueDate->format('d').'. '.FrontendHelpers::convertMonthLanguage($issueDate->format('n')).' '.$issueDate->format('Y'),
            ],
            $certificate->template
        );

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML($template);

        return $pdf->download($course->title.' certificate.pdf');
    }

    public function selfPublishingOrders($orderId): View
    {
        $orders = SelfPublishingOrder::where('order_id', $orderId)->get();

        return view('backend.learner._self-publishing-orders', compact('orders'));
    }

    public function autoRegisterCourseWebinar($user_id, Request $request)
    {
        $user = User::find($user_id);
        $course_id = 17; // webinar-pakke course

        $autoRenewToCourse = UserAutoRegisterToCourseWebinar::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course_id,
        ]);

        // check if not auto renew then delete the record
        if (! $request->auto_renew) {
            $autoRenewToCourse->delete();
        }
    }

    public function vippsEFaktura($invoice_id, Request $request): RedirectResponse
    {
        $invoice = Invoice::find($invoice_id);
        $user = $invoice->user;
        $fikenInvoice = new FikenInvoice;
        $fikenInvoice->setMobileNumber($request->mobile_number);
        $fikenInvoice->setFikenInvoiceId($invoice->fiken_invoice_id);

        $response = $fikenInvoice->vippsEFaktura($user);
        $alert_type = 'success';
        $message = 'Invoice sent.';

        if ($response['code'] != 200) {
            $alert_type = 'danger';
            $message = $response['message'];
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($message),
            'alert_type' => $alert_type,
            'not-former-courses' => true,
        ]);
    }

    /**
     * Set the phone number that would be use for sending vipss-efaktura
     */
    public function setVippsEFaktura($user_id, Request $request): RedirectResponse
    {
        if ($request->mobile_number) {
            $request->validate([
                'mobile_number' => 'digits:8',
            ]);
        }

        $address = Address::firstOrNew([
            'user_id' => $user_id,
        ]);
        $address->vipps_phone_number = $request->mobile_number ?: null;
        $address->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record saved.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function exportLearnerWithVipps()
    {
        $address = Address::with('user')->whereNotNull('vipps_phone_number')->get();
        $userList = [];

        foreach ($address as $addr) {
            $userList[] = [
                'name' => $addr->user->full_name,
                'email' => $addr->user->email,
                'phone' => $addr->vipps_phone_number,
            ];
        }

        $headers = ['name', 'email', 'phone'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Learner with vipps.xlsx');
    }

    public function learnerEmailHistory($learner_id): View
    {
        $learner = User::find($learner_id);

        $learnerAssignmentManuscripts = $learner->assignmentManuscripts->pluck('id');
        $learnerShopManuscriptsTaken = $learner->shopManuscriptsTaken->pluck('id');
        $learnerCoursesTaken = $learner->coursesTaken->pluck('id');
        $registeredWebinarLists = $learner->registeredWebinars->pluck('id');
        $learnerInvoices = $learner->invoices->pluck('id');

        $emailHistories = DB::table('email_history')->where(function ($query) use ($learnerAssignmentManuscripts) {
            $query->where('parent', 'LIKE', 'assignment-manuscripts%');
            $query->whereIn('parent_id', $learnerAssignmentManuscripts);
        })
            ->orWhere(function ($query) use ($learnerShopManuscriptsTaken) {
                $query->where('parent', 'LIKE', 'shop-manuscripts-taken%');
                $query->whereIn('parent_id', $learnerShopManuscriptsTaken);
            })
            ->orWhere(function ($query) use ($learnerCoursesTaken) {
                $query->where('parent', 'LIKE', 'courses-taken%');
                $query->whereIn('parent_id', $learnerCoursesTaken);
            })
            ->orWhere(function ($query) use ($registeredWebinarLists) {
                $query->where('parent', '=', 'webinar-registrant');
                $query->whereIn('parent_id', $registeredWebinarLists);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', '=', 'learner');
                $query->where('parent_id', $learner->id);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', '=', 'free-manuscripts');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learnerInvoices) {
                $query->where('parent', '=', 'invoice');
                $query->whereIn('parent_id', $learnerInvoices);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'copy-editing%');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'correction%');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'gift-purchase');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('recipient', $learner->email);
            })
            ->latest()
            ->paginate(20);

        return view('backend.learner.email-history', compact('learner', 'emailHistories'));
    }

    public function sendUsernameAndPassword($userId, Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);

        $user = User::findOrFail($userId);
        $loginLink = route('auth.login.emailRedirect', [encrypt($user->email), encrypt(route('learner.profile'))]);
        $searchString = [
            ':firstname',
            '_username_',
            ':login_link',
            ' :end_login_link',
        ];

        $replaceString = [
            $user->first_name,
            $user->email,
            "<a href='$loginLink'>",
            '</a>',
        ];

        $message = str_replace($searchString, $replaceString, $request->message);
        $toMail = $user->email;
        // add email to queue
        dispatch(new AddMailToQueueJob($toMail, $request->subject, $message,
            $request->from_email, null, null, 'learner', $user->id));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email sent.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function restoreCourse($user_id, $former_course_id, Request $request): RedirectResponse
    {
        $formerCourse = FormerCourse::find($former_course_id);
        $courseTaken = CoursesTaken::where('user_id', $user_id)
            ->where('package_id', $formerCourse->package_id)
            ->withTrashed()
            ->first();
        if ($courseTaken) {
            $courseTaken->end_date = $request->end_date;
            $courseTaken->deleted_at = null;
            $courseTaken->save();
        } else {
            CoursesTaken::create([
                'user_id' => $formerCourse->user_id,
                'package_id' => $formerCourse->package_id,
                'is_active' => 1,
                'started_at' => $formerCourse->started_at,
                'start_date' => $formerCourse->start_date,
                'end_date' => $request->end_date,
                'access_lessons' => $formerCourse->access_lessons,
                'years' => 1,
                'sent_renew_email' => $formerCourse->sent_renew_email,
                'is_free' => $formerCourse->is_free,
                'created_at' => $formerCourse->created_at,
                'updated_at' => $formerCourse->updated_at,
            ]);
        }

        $formerCourse->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Course restored successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function createSveaCreditNote($order_id): RedirectResponse
    {
        $order = Order::find($order_id);

        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_ADMIN_BASE_URL;

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Credit Order Amount
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException
             * \Svea\Checkout\Exception\SveaApiException
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutAdminClient($conn);

            if ($order->svea_payment_type === 'Card') {
                $data = [
                    'orderId' => (int) $order->svea_order_id, // required - Long  filed (Specified Checkout order for cancel amount)
                    'deliveryId' => (int) $order->svea_delivery_id, // required - Int - Id of order delivery
                    'creditedAmount' => (int) $order->total_price * 100, // required - Int Amount to be credit minor currency,
                ];
                $response = $checkoutClient->creditOrderAmount($data);
            } else {
                $data = [
                    'orderId' => (int) $order->svea_order_id, // required - Long  filed (Specified Checkout order for cancel amount)
                    'deliveryId' => (int) $order->svea_delivery_id, // required - Long - Id of the specified delivery.
                    'orderRowIds' => [1], // required - Array - Ids of the delivered order rows that will be credited.
                ];
                $response = $checkoutClient->creditOrderRows($data);
            }

            $order->is_credited_amount = 1;
            $order->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Order credited.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);

        } catch (\Svea\Checkout\Exception\SveaApiException $ex) {
            $error = $ex->getMessage();
        } catch (\Svea\Checkout\Exception\SveaConnectorException $ex) {
            $error = $ex->getMessage();
        } catch (\Svea\Checkout\Exception\SveaInputValidationException $ex) {
            $error = $ex->getMessage();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($error),
            'alert_type' => 'danger',
            'not-former-courses' => true,
        ]);
    }

    public function deliverSveaOrder($order_id): RedirectResponse
    {
        $order = Order::find($order_id);

        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_ADMIN_BASE_URL;

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Credit Order Amount
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException
             * \Svea\Checkout\Exception\SveaApiException
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutAdminClient($conn);
            $data = [
                'orderId' => (int) $order->svea_order_id,
                /* To deliver whole order just send orderRowIds as empty array */
                'orderRowIds' => [],
            ];
            $response = $checkoutClient->deliverOrder($data);
            $order->svea_delivery_id = $response['DeliveryId'];
            $order->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Order delivered successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);

        } catch (\Svea\Checkout\Exception\SveaApiException $ex) {
            $error = $ex->getMessage();
        } catch (\Svea\Checkout\Exception\SveaConnectorException $ex) {
            $error = $ex->getMessage();
        } catch (\Svea\Checkout\Exception\SveaInputValidationException $ex) {
            $error = $ex->getMessage();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($error),
            'alert_type' => 'danger',
            'not-former-courses' => true,
        ]);
    }

    public function sendRequestToEditor($id, Request $request): RedirectResponse
    {
        // set expected finish
        $shopManuscriptsTaken = ShopManuscriptsTaken::find($id);
        $shopManuscriptsTaken->expected_finish = $request->expected_finish;
        $shopManuscriptsTaken->editor_expected_finish = $request->editor_expected_finish;
        $shopManuscriptsTaken->save();

        $request->validate([
            'editor_id' => 'required',
            'answer_until' => 'required',
        ]);

        $data['from_type'] = 'shop-manuscript';
        $data['editor_id'] = $request->editor_id;
        $data['manuscript_id'] = $id;
        $data['answer_until'] = $request->answer_until;

        $requestToEditor = RequestToEditor::create($data);

        // send email
        $to = User::where('id', $request->editor_id)->pluck('email');

        $editor_expected_finish = Carbon::parse($request->editor_expected_finish)->format('d.m.Y');
        $expected_finish = Carbon::parse($request->expected_finish)->format('d.m.Y');
        $emailTemplate_content = $request->message;
        $emailTemplate_content = str_replace(':editor_expected_finish', $editor_expected_finish, $emailTemplate_content);
        $emailTemplate_content = str_replace(':manuscript_finish', $expected_finish, $emailTemplate_content);
        $emailTemplate_content = str_replace(':login_link',
            "<a href='".route('editor.login.email', encrypt($to))."'>".trans('site.front.form.login').'</a>', $emailTemplate_content);

        dispatch(new AddMailToQueueJob($to, $request->subject, $emailTemplate_content, $request->from_email,
            null, null,
            'shop-manuscript-new-request-to-editor', $id));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record successfully saved.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }
}
