<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\Assignment;
use App\AssignmentAddon;
use App\AssignmentFeedback;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentGroup;
use App\AssignmentGroupLearner;
use App\AssignmentManuscript;
use App\CalendarNote;
use App\EditorTimeSlot;
use App\CoachingTimeRequest;
use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\Console\Commands\CheckFikenContactCommand;
use App\Contract;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Course;
use App\CourseCertificate;
use App\CourseDiscount;
use App\CoursesTaken;
use App\Diploma;
use App\EmailConfirmation;
use App\Genre;
use App\GiftPurchase;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Http\Middleware\Admin;
use App\Http\Requests\ProfileUpdateRequest;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\CourseOrderJob;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Jobs\UpdateFikenContactDetailsJob;
use App\Lesson;
use App\LessonContent;
use App\LessonDocuments;
use App\Log;
use App\Mail\AssignmentSubmittedEmail;
use App\Mail\CoachingSuggestionDateEmail;
use App\Mail\SendEmailMessageOnly;
use App\Mail\SubjectBodyEmail;
use App\Manuscript;
use App\MarketingPlan;
use App\MarketingPlanQuestionAnswer;
use App\Notification;
use App\Order;
use App\OrderCompany;
use App\OtherServiceFeedback;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\Paypal;
use App\Project;
use App\ProjectBook;
use App\ProjectBookFormatting;
use App\ProjectBookSale;
use App\ProjectGraphicWork;
use App\ProjectInvoice;
use App\ProjectMarketing;
use App\ProjectRegistration;
use App\ProjectRegistrationDistribution;
use App\PublishingService as AppPublishingService;
use App\Repositories\Services\CompetitionService;
use App\Repositories\Services\PublishingService;
use App\Repositories\Services\WritingGroupService;
use App\SelfPublishing;
use App\SelfPublishingLearner;
use App\SelfPublishingPortalRequest;
use App\Services\AssignmentService;
use App\Services\CourseService;
use App\Services\DocumentConversionService;
use App\Services\ProjectService;
use App\Services\ShopManuscriptService;
use App\Settings;
use App\ShopManuscript;
use App\ShopManuscriptComment;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptTakenFeedback;
use App\ShopManuscriptUpgrade;
use App\StoragePayout;
use App\StorageSale;
use App\Survey;
use App\SurveyAnswer;
use App\TimeRegister;
use App\User;
use App\UserAutoRegisterToCourseWebinar;
use App\UserBookForSale;
use App\UserBookSale;
use App\UserEmail;
use App\UserRenewedCourse;
use App\UserSocial;
use App\WebinarRegistrant;
use App\WordWrittenGoal;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use File;
use Firebase\JWT\JWT;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

require app_path('/Http/PaypalIPN/PaypalIPN.php');

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\In;
use Pdf as GlobalPdf;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class LearnerController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = 'https://fiken.no/api/v1/companies/forfatterskolen-as/invoices';

    public $username = 'elybutabara@yahoo.com';

    public $password = 'janiel12';

    /* old
     * public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];*/
    protected $headers = [];

    public function __construct()
    {
        $this->headers[] = 'Accept: application/json';
        $this->headers[] = 'Authorization: Bearer '.config('services.fiken.personal_api_key');
        $this->headers[] = 'Content-Type: Application/json';
    }

    public function dashboard()
    {
        $user = Auth::user();
        $coursesTaken = $user->coursesTaken()->limit(3)->get();
        $invoices = $user->invoices()->limit(5)->get();
        $packageArr = Auth::user()->coursesTaken()->pluck('package_id')->toArray();
        $courses = Package::whereIn('id', $packageArr)->pluck('course_id')->toArray();
        $surveyTaken = Auth::user()->surveyTaken()->pluck('survey_id')->toArray();
        $today = date('Y-m-d');
        $surveys = DB::table('survey')->whereIn('course_id', $courses)
            ->whereNotIn('id', $surveyTaken)
            ->where(function ($query) use ($today) {
                $query->whereDate('start_date', '<=', $today);
                $query->whereDate('end_date', '>=', $today);
            })
            ->get();
        $assignments = $this->dashboardAssignment();

        /* $selfPublishingLearners = SelfPublishingLearner::where('user_id', Auth::user()->id)
            ->pluck('self_publishing_id')->toArray();
        $selfPublishingList = SelfPublishing::whereIn('id', $selfPublishingLearners)->get(); */
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        $inventorySummaries = [];
        $selfPublishingList = $standardProject
            ? SelfPublishing::where('project_id', $standardProject->id)->latest()->get()
            : [];
        $projects = Project::where('user_id', Auth::user()->id)->get();

        if ($standardProject) {
            $projectCentralDistributions = $standardProject->registrations()
                ->where('field', 'central-distribution')
                ->where('in_storage', 1)
                ->get()
                ->filter(function ($distribution) use ($standardProject) {
                    return $standardProject->registrations()
                        ->where('field', 'ISBN')
                        ->where('value', $distribution->value)
                        ->whereIn('type', [1, 2])
                        ->exists();
                });

            $types = [
                'quantity-sold' => 'Quantity Sold',
                'turned-over' => 'Turned Over',
                'free' => 'Free',
                'commission' => 'Commission',
                'shredded' => 'Shredded',
                'defective' => 'Defective',
                'corrections' => 'Corrections',
                'counts' => 'Counts',
                // 'returns' => 'Returns'
            ];

            foreach ($projectCentralDistributions as $distribution) {
                $inventorySalesGroup = StorageSale::where('project_book_id', $distribution->id)
                    ->where('type', 'like', 'inventory_%')
                    ->select('type', \DB::raw('SUM(value) as total_sales'))
                    ->groupBy('type')
                    ->get();

                $inventoryPhysicalItems = 0;
                $inventoryDelivered = 0;
                $inventoryReturns = 0;

                foreach ($inventorySalesGroup as $sale) {
                    switch ($sale->type) {
                        case 'inventory_physical_items':
                            $inventoryPhysicalItems = $sale->total_sales;
                            break;
                        case 'inventory_delivered':
                            $inventoryDelivered = $sale->total_sales;
                            break;
                        case 'inventory_returns':
                            $inventoryReturns = $sale->total_sales;
                            break;
                    }
                }

                $inventoryTotal = $inventoryPhysicalItems + $inventoryDelivered + $inventoryReturns;

                $baseQuery = ProjectBookSale::leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
                    ->where('project_registration_id', $distribution->id)
                    ->where('project_id', $standardProject->id);

                $quantitySold = (clone $baseQuery)
                    ->when(request()->filled('year') && request('year') != 'all', function ($query) {
                        $query->whereYear('date', request('year'));
                    })
                    ->when(request()->filled('month') && request('month') != 'all', function ($query) {
                        $query->whereMonth('date', request('month'));
                    })
                    ->sum('quantity');

                $totalQuantitySold = (clone $baseQuery)->sum('quantity');

                $dataMapper = function ($typeKey, $typeName, $field) use ($distribution, $quantitySold) {
                    return [
                        'name' => $typeName,
                        'value' => $typeKey == 'quantity-sold'
                            ? $quantitySold
                            : ($distribution ? $this->storageSalesByTypeArray($distribution->id, $typeKey)[$field] : 0),
                    ];
                };

                $overallData = array_map(function ($key, $name) use ($dataMapper) {
                    return $dataMapper($key, $name, 'overall');
                }, array_keys($types), $types);

                $calculatedBalance = array_reduce($overallData, function ($sum, $data) {
                    return ! in_array($data['name'], ['Quantity Sold']) ? $sum + $data['value'] : $sum;
                }, 0);

                $balanceCount = $this->salesReportCounter($distribution->id, 'balance');

                $totalBalance = $balanceCount ? $balanceCount
                    : $inventoryTotal - ($calculatedBalance + $totalQuantitySold);

                $inventorySummaries[] = [
                    'registration_id' => $distribution->id,
                    'isbn' => $distribution->value,
                    'inventory_physical_items' => $inventoryPhysicalItems,
                    'inventory_delivered' => $inventoryDelivered,
                    'inventory_returns' => $inventoryReturns,
                    'inventory_total' => $inventoryTotal,
                    'quantity_sold' => $totalQuantitySold,
                    'total_balance' => $totalBalance,
                ];
            }
        }

        $dashboardCalendar = $this->dashboardCalendar();
        $freeCourses = FrontendHelpers::getFreeCourses();

        $view = Session::get('current-portal') === 'self-publishing'
            ? 'frontend.learner.self-publishing.dashboard'
            : 'frontend.learner.dashboard';

        return view($view, compact('surveys', 'assignments', 'selfPublishingList', 'coursesTaken', 'invoices',
            'dashboardCalendar', 'freeCourses', 'projects', 'inventorySummaries'));
    }

    public function course(): View
    {
        $user = Auth::user();
        $surveys = Survey::all();
        $coursesTaken = $user->coursesTaken()->paginate(5);
        $formerCourses = $user->formerCourses;

        return view('frontend.learner.course', compact('surveys', 'coursesTaken', 'formerCourses'));
    }

    /**
     * Display the survey page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function survey($id)
    {
        $surveyTaken = Auth::user()->surveyTaken()->pluck('survey_id')->toArray();
        $survey = Survey::with('questions')->where('id', $id)
            ->whereNotIn('id', $surveyTaken)->first();
        if (! $survey) {
            return redirect()->route('learner.dashboard');
        }

        return view('frontend.learner.survey', compact('survey'));
    }

    /**
     * Survey Submit
     *
     * @return array
     */
    public function takeSurvey($id, Request $request)
    {
        $data = $request->all();
        $filtered_data = array_filter($data);
        foreach ($filtered_data as $key => $value) {
            if ($value) {
                $answer = new SurveyAnswer;
                if (strpos($value, ', ') !== false) {
                    $value = json_encode(explode(', ', $value));
                }

                $answer->answer = $value;
                $answer->survey_question_id = $key;
                $answer->user_id = Auth::id();
                $answer->survey_id = $id;

                $answer->save();
            }
        }

        return $filtered_data;
    }

    public function shopManuscript(): View
    {
        $shopManuscriptsTaken = Auth::user()->shopManuscriptsTaken()->paginate(4);

        return view('frontend.learner.shop-manuscript', compact('shopManuscriptsTaken'));
    }

    public function shopManuscriptShow($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('user_id', Auth::user()->id)->where('id', $id)->where('is_active', true)->first();
        if ($shopManuscriptTaken) {
            return view('frontend.learner.shopManuscriptShow', compact('shopManuscriptTaken'));
        }

        return abort('503');
    }

    /**
     * Download shop-manuscript file
     *
     * @param  $type  string
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscript($id, $type)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($id);
        if ($shopManuscriptTaken) {
            $file = $shopManuscriptTaken->file;
            if ($type == 'synopsis') {
                $file = $shopManuscriptTaken->synopsis;
            }
            $fileInfo = pathinfo(public_path($file));
            $filename = $fileInfo['filename'];
            $fileExt = $fileInfo['extension'];
            $newName = $filename.'.'.$fileExt;

            if ($type == 'synopsis') {
                $newName = $filename.'-synopsis.'.$fileExt;
            }

            return response()->download(public_path($file), $newName);
        }

        return redirect('shop-manuscript');
    }

    /**
     * Download the manuscript feedback
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscriptFeedback($id, $feedback_id)
    {
        $shopmanuscriptTaken = ShopManuscriptsTaken::find($id);
        $shopManuFeed = ShopManuscriptTakenFeedback::where([
            'id' => $feedback_id,
            'shop_manuscript_taken_id' => $id,
        ])->first();

        if (! $shopManuFeed) {
            return redirect()->back();
        }

        $feedbacks = $shopManuFeed->filename;
        if (count($feedbacks) > 1) {
            $zipFileName = $shopmanuscriptTaken->shop_manuscript->title.' Feedbacks.zip';
            $public_dir = public_path('storage');
            $zip = new \ZipArchive;

            // open zip file connection and create the zip
            if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($feedbacks as $feedFile) {
                if (file_exists(public_path().'/'.trim($feedFile))) {

                    // get the correct filename
                    $expFileName = explode('/', $feedFile);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                }
            }

            $zip->close(); // close zip connection

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }

        return response()->download(public_path($feedbacks[0]));
    }

    public function workshop(): View
    {
        return view('frontend.learner.workshop');
    }

    /**
     * Approve the coaching date set by admin
     */
    public function approveCoachingDate($id, Request $request): RedirectResponse
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $data = $request->except('_token');
            $coachingTimer->update($data);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Date approved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Suggest coaching date
     */
    public function suggestCoachingDate($id, Request $request): RedirectResponse
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $data = $request->except('_token');
            $suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }

            $data['suggested_date'] = json_encode($suggested_dates);
            $data['is_approved'] = 0;

            $coachingTimer->update($data);

            $email_data['sender'] = Auth::user()->full_name;
            $email_data['suggested_dates'] = $data['suggested_date'];
            $toMail = 'post@forfatterskolen.no';
            // use queue to send email on background
            Mail::to($toMail)->queue(new CoachingSuggestionDateEmail($email_data));

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Suggested date saved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    public function webinar(Request $request)
    {
        $isPost = 0;
        $isReplay = 0;
        $searchResult = [];

        /* if ($request->exists('search_upcoming')) {
            $query = DB::table('courses_taken')
                ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                ->join('courses', 'packages.course_id', '=', 'courses.id')
                ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
                ->where('user_id',Auth::user()->id)
                ->where('courses.id',17) // just added this line to show all webinar pakke webinars
                ->whereNotIn('webinars.id',[24, 25, 31])
                ->where('webinars.start_date', '>=' ,Carbon::today())
                ->where('webinars.title','LIKE','%'.$request->search_upcoming.'%')
                ->where('set_as_replay',0)
                ->orderBy('courses.type', 'ASC')
                ->orderBy('webinars.start_date', 'ASC');

            $searchResult = $query->get();
            $isPost = 1;
        } */

        // check if webinar-pakke is replay
        $webinarsRepriser = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select('webinars.*', 'courses_taken.id as courses_taken_id', 'courses.title as course_title')
            ->where('user_id', Auth::user()->id)
            ->where('courses.id', 17) // just added this line to show all webinar pakke webinars
            ->where(function ($query) {
                $query->whereIn('webinars.id', [24, 25, 31]);
                $query->orWhere('set_as_replay', 1);
            })
            // ->whereIn('webinars.id',[24, 25, 31]) // remove this to return the original
            ->orderBy('courses.type', 'ASC')
            ->orderBy('webinars.start_date', 'ASC')
            ->get();

        if ($request->exists('search_replay') && $webinarsRepriser) {
            $searchResult = LessonContent::where('title', 'like', '%'.$request->search_replay.'%')
                ->orWhere('tags', 'like', '%'.$request->search_replay.'%')
                ->get();
            $isPost = 1;
            $isReplay = 1;
        }

        $coursesTaken = Auth::user()->coursesTaken;
        $courses = DB::table('courses')
            ->leftJoin('packages', 'courses.id', '=', 'packages.course_id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->where('courses_taken.user_id', Auth::user()->id)
            ->whereNull('courses_taken.deleted_at')
            ->where(function ($q) {
                // this query is for checking if courses_taken is disabled
                $today = now()->toDateString();

                $q->where(function ($inner) {
                    // Case 1: both null → show
                    $inner->whereNull('courses_taken.disable_start_date')
                        ->whereNull('courses_taken.disable_end_date');
                })
                ->orWhere(function ($inner) use ($today) {
                    // Case 2: start_date in the future → show
                    $inner->whereNotNull('courses_taken.disable_start_date')
                        ->whereRaw("DATE(courses_taken.disable_start_date) > ?", [$today]);
                })
                ->orWhere(function ($inner) use ($today) {
                    // Case 3: end_date in the past → show
                    $inner->whereNotNull('courses_taken.disable_end_date')
                        ->whereRaw("DATE(courses_taken.disable_end_date) < ?", [$today]);
                });
            })
            ->pluck('courses.id')
            ->toArray();

        $replayWebinars = DB::table('lesson_contents')->select('lesson_contents.*')
            ->leftJoin('lessons', 'lesson_contents.lesson_id', '=', 'lessons.id')
            ->leftJoin('courses', 'lessons.course_id', '=', 'courses.id')
            ->where('courses.id', '=', 17)
            ->whereIn('courses.id', $courses);

        if ($request->exists('search_replay')) {
            $replayWebinars = $replayWebinars->where(function ($query) use ($request) {
                $query->where('lesson_contents.title', 'like', '%'.$request->search_replay.'%')
                    ->orWhere('tags', 'like', '%'.$request->search_replay.'%');
            });
        }

        $replayWebinars = $replayWebinars
            ->latest('lesson_contents.id')
            ->paginate(10);

        $subscriptionWebinars = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select(
                'webinars.*',
                'courses_taken.id as courses_taken_id',
                'courses.title as course_title',
                DB::raw('TIMESTAMPDIFF(HOUR, NOW(), webinars.start_date) as diffWithHours')
            )
            ->where('user_id', Auth::user()->id)
            ->where('courses.id', 17) // just added this line to show all webinar pakke webinars
            ->whereNotIn('webinars.id', [24, 25, 31])
            ->where('set_as_replay', 0)
            ->whereNull('courses_taken.deleted_at');

        if ($request->exists('search_upcoming')) {
            $subscriptionWebinars = $subscriptionWebinars->where('webinars.start_date', '>=', Carbon::today())
                ->where('webinars.title', 'LIKE', '%'.$request->search_upcoming.'%');
        }

        $subscriptionWebinars = $subscriptionWebinars->orderBy('courses.type', 'ASC')
            ->orderBy('webinars.start_date', 'ASC')
            ->having('diffWithHours', '>=', 0) // filter results after 'SELECT'
            ->get()
            ->paginate(8);

        return view('frontend.learner.webinar', compact('searchResult', 'isPost', 'isReplay', 'replayWebinars',
            'subscriptionWebinars'));
    }

    /**
     * Register the user to the webinar
     */
    public function webinarRegister($webinar_key, $webinar_id): RedirectResponse
    {
        $user = Auth::user();
        $data = [
            'id' => $webinar_key,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];

        $url = config('services.big_marker.register_link');
        $ch = curl_init();
        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        if (property_exists($decoded_response, 'conference_url')) {
            $registrant['user_id'] = $user->id;
            $registrant['webinar_id'] = $webinar_id;
            $webRegister = WebinarRegistrant::firstOrNew($registrant);
            $webRegister->join_url = $decoded_response->conference_url;
            $webRegister->save();
        } else {
            $message = 'Error! Request cannot be processed.';
            if (isset($decoded_response->error)) {
                $message = $decoded_response->error;
            }

            return redirect()->back()->withInput()->with([
                'errors' => AdminHelpers::createMessageBag($message),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Add the user as registrant on the webinar
     */
    public function webinarRegisterOrig($webinar_key, $webinar_id): RedirectResponse
    {
        $webinar_link = 'https://attendee.gotowebinar.com/register/'.$webinar_key;
        $user = Auth::user();
        $user_email = $user->email;
        $first_name = $user->first_name;
        $last_name = $user->last_name;

        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        $access_token = AdminHelpers::generateWebinarGTAccessToken(); // from here http://app.gotowp.com/
        $org_key = '5169031040578858252';

        $vals['body'] = (object) [
            'firstName' => $first_name,
            'lastName' => $last_name,
            'email' => $user_email,
        ];

        $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$webinar_key.'/registrants';
        $header = [];
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';
        $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
        $header[] = 'Authorization: OAuth oauth_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vals['body']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        if (isset($decoded_response->status)) {
            if ($decoded_response->status == 'APPROVED') {
                $registrant['user_id'] = $user->id;
                $registrant['webinar_id'] = $webinar_id;
                $webRegister = WebinarRegistrant::firstOrNew($registrant);
                $webRegister->join_url = $decoded_response->joinUrl;
                $webRegister->save();

                return redirect()->back()->with('success', true);
            }
        } else {
            return redirect()->to($webinar_link);
        }

        return redirect()->back();
    }

    public function courseWebinar(Request $request)
    {
        $isPost = 0;
        $isReplay = 0;
        $searchResult = [];

        /* if ($request->exists('search_upcoming')) {
            $query = DB::table('courses_taken')
                ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                ->join('courses', 'packages.course_id', '=', 'courses.id')
                ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
                ->where('user_id',Auth::user()->id)
                ->where('courses.id','!=',17) // just added this line to show all webinar pakke webinars
                ->whereNotIn('webinars.id',[24, 25, 31])
                ->where('webinars.start_date', '>=' ,Carbon::today())
                ->where('webinars.title','LIKE','%'.$request->search_upcoming.'%')
                ->where('set_as_replay',0)
                ->orderBy('courses.type', 'ASC')
                ->orderBy('webinars.start_date', 'ASC');

            $searchResult = $query->get();
            $isPost = 1;
        }

        // check if webinar-pakke is replay
        $webinarsRepriser = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
            ->where('user_id',Auth::user()->id)
            ->where('courses.id','!=',17) // just added this line to show all webinar pakke webinars
            ->where(function($query){
                $query->whereIn('webinars.id',[24, 25, 31]);
                $query->orWhere('set_as_replay',1);
            })
            //->whereIn('webinars.id',[24, 25, 31]) // remove this to return the original
            ->orderBy('courses.type', 'ASC')
            ->orderBy('webinars.start_date', 'ASC')
            ->get();

        if ($request->exists('search_replay') && $webinarsRepriser) {
            $searchResult = LessonContent::where('title', 'like', '%'.$request->search_replay.'%')
                ->get();
            $isPost = 1;
            $isReplay = 1;
        } */

        /* this is new query until the end, the top is the old function/query */
        $webinars = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select(
                'webinars.*',
                'courses_taken.id as courses_taken_id',
                'courses.title as course_title',
                'courses_taken.deleted_at',
                DB::raw('TIMESTAMPDIFF(HOUR, NOW(), webinars.start_date) as diffWithHours')
            )
            ->where('user_id', Auth::user()->id)
            ->where('courses.id', '!=', 17) // just added this line to show all webinar pakke webinars
            ->whereNull('courses_taken.deleted_at');

        if ($request->exists('search_upcoming')) {
            $webinars = $webinars->whereNotIn('webinars.id', [24, 25, 31])
                ->where('webinars.start_date', '>=', Carbon::today())
                ->where('webinars.title', 'LIKE', '%'.$request->search_upcoming.'%')
                ->where('set_as_replay', 0);
        } else {
            $webinars = $webinars->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereIn('webinars.id', [24, 25, 31]);
                    $subQuery->orWhere('set_as_replay', 1);
                });

                $query->orWhere(function ($subQuery) {
                    $subQuery->whereNotIn('webinars.id', [24, 25, 31])
                        ->where('set_as_replay', 0);
                });
            });
        }

        $webinars = $webinars->orderBy('courses.type', 'ASC')
            ->orderBy('webinars.set_as_replay', 'DESC')
            ->orderBy('webinars.start_date', 'ASC')
            ->having('diffWithHours', '>=', 0) // filter results after 'SELECT'
            ->get()
            ->paginate(8);

        $lessonContents = [];
        if ($request->exists('search_replay')) {
            $lessonContents = LessonContent::where('title', 'like', '%'.$request->search_replay.'%')
                ->paginate(8);
            $isReplay = 1;
        }

        return view('frontend.learner.course-webinar', compact('isReplay', 'webinars', 'lessonContents',
            /* 'webinarsRepriser', 'isPost', 'searchResult' */));
    }

    public function courseShow($id)
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        if (Auth::user()->can('participateCourse', $courseTaken)) {
            if ($courseTaken->hasEnded || $courseTaken->isDisabled || Auth::user()->isDisabled) {
                return redirect()->route('learner.course');
            }

            return view('frontend.learner.course_show', compact('courseTaken'));
        }

        return abort('450', 'testing here');
    }

    public function notifications(): View
    {
        return view('frontend.learner.notifications');
    }

    public function calendar(): View
    {
        $events = [];

        foreach (Auth::user()->coursesTaken as $courseTaken) {
            // Course lessons
            $token = str_random(10);
            foreach ($courseTaken->package->course->lessons as $lesson) {
                $availability = strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)) * 1000;
                $newAvailability = date('Y-m-d', strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)));
                $events[] = [
                    'id' => $lesson->course->id,
                    'title' => 'Lesson: '.$lesson->title.' from '.$lesson->course->title,
                    'class' => 'event-important',
                    'start' => $newAvailability, // $availability,
                    'end' => $newAvailability, // $availability,
                    'color' => '#d95e66',
                ];
            }

            // Course webinars
            $token = str_random(10);
            foreach ($courseTaken->package->course->webinars as $webinar) {
                $events[] = [
                    'id' => $webinar->course->id,
                    'title' => 'Webinar: '.$webinar->title.' from '.$webinar->course->title,
                    'class' => 'event-warning',
                    'start' => date('Y-m-d', strtotime($webinar->start_date)), // strtotime($webinar->start_date) * 1000,
                    'end' => date('Y-m-d', strtotime($webinar->start_date)), // strtotime($webinar->start_date) * 1000,
                    'color' => '#ff9c00',
                ];
            }

            // manuscripts
            foreach ($courseTaken->manuscripts as $manuscript) {
                $events[] = [
                    'id' => $courseTaken->package->course->id,
                    'title' => 'Manus: '.basename($manuscript->filename).' from '.$courseTaken->package->course->title,
                    'class' => 'event-info',
                    'start' => date('Y-m-d', strtotime($manuscript->expected_finish)), // strtotime($manuscript->expected_finish) * 1000,
                    'end' => date('Y-m-d', strtotime($manuscript->expected_finish)), // strtotime($manuscript->expected_finish) * 1000,
                    'color' => '#29b5f5',
                ];
            }

            // assignments
            foreach ($courseTaken->package->course->assignments as $assignment) {
                $allowedPackage = json_decode($assignment->allowed_package, true);

                if (is_null($allowedPackage) || in_array($courseTaken->package_id, (array) $allowedPackage)) {
                    $events[] = [
                        'id'    => $assignment->course->id,
                        'title' => 'Oppgaver: ' . $assignment->title . ' from ' . $assignment->course->title,
                        'class' => 'event-success-new',
                        'start' => date('Y-m-d', strtotime($assignment->submission_date)),
                        'end'   => date('Y-m-d', strtotime($assignment->submission_date)),
                        'color' => '#44af5e',
                    ];
                }
            }

            // get the calendar notes created by admin for certain course only
            foreach ($courseTaken->package->course->notes as $note) {
                $events[] = [
                    'id' => $note->id,
                    'title' => $note->note,
                    'class' => 'event-inverse',
                    'start' => date('Y-m-d', strtotime($note->from_date)), // strtotime($note->date) * 1000,
                    'end' => date('Y-m-d', strtotime($note->to_date)), // strtotime($note->date) * 1000,
                    'color' => '#1b1b1b', // for full calendar
                ];
            }

        }

        // get the calendar notes created by admin
        /*foreach(CalendarNote::all() as $calendar) :
            $events[] = [
                'id' => $calendar->id,
                'title' => $calendar->note,
                'class' => 'event-inverse',
                'start' => strtotime($calendar->date) * 1000,
                'end' => strtotime($calendar->date) * 1000,
            ];
        endforeach;*/

        $approved_coaching = Auth::user()->coachingTimers()->whereNotNull('approved_date')->get();
        foreach ($approved_coaching as $coaching) {
            $events[] = [
                'id' => $coaching->id,
                'title' => 'Coaching Session at '.date('H:i A', strtotime($coaching->approved_date)),
                'class' => 'event-inverse',
                'start' => date('Y-m-d', strtotime($coaching->approved_date)), // strtotime($note->date) * 1000,
                'end' => date('Y-m-d', strtotime($coaching->approved_date)), // strtotime($note->date) * 1000,
                'color' => '#f00', // for full calendar
            ];
        }

        $event_1 = [
            'title' => 'Event 1',
            'class' => 'event-important',
            'start' => '1494259200000',
            'end' => '1494259300000', 1503292298,
        ];

        return view('frontend.learner.calendar', compact('events'));
    }

    public function documentConverter(): View
    {
        return view('frontend.learner.document-converter');
    }

    public function convertDocument(Request $request, DocumentConversionService $documentConversionService)
    {
        $request->validate([
            'document' => ['required', 'file', 'max:51200'],
        ]);

        $uploadedFile = $request->file('document');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $allowedExtensions = ['doc', 'docx', 'pdf', 'pages'];

        $invalidFileMessage = __('Please upload a PAGES, PDF, DOC or DOCX file.');
        $conversionFailedMessage = __('We could not convert the file. Make sure the document contains selectable text and try again.');

        if (! in_array($extension, $allowedExtensions, true)) {
            return back()
                ->withErrors(['document' => $invalidFileMessage])
                ->with('alert_type', 'danger');
        }

        try {
            $conversion = $documentConversionService->convertUploadedFileToDocx(
                $uploadedFile,
                'learner-document-converter',
                Auth::id()
            );
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Learner document conversion failed', [
                'user_id' => Auth::id(),
                'extension' => $extension,
                'message' => $exception->getMessage(),
            ]);
            $conversion = null;
        }

        if (! $conversion) {
            return back()
                ->withErrors(['document' => $conversionFailedMessage])
                ->with('alert_type', 'danger');
        }

        return response()->download($conversion['full_path'], $conversion['download_name'])
            ->deleteFileAfterSend(true);
    }

    public function assignment()
    {
        $assignments = [];
        $expiredAssignments = [];
        $coursesTaken = Auth::user()->coursesTaken()->whereNotNull('end_date')->get()
        ->filter(function ($courseTaken) {
            // for checking if the course taken is not disabled
            return !$courseTaken->is_disabled; // use the accessor
        });
        $addOns = AssignmentAddon::where('user_id', \Auth::user()->id)->pluck('assignment_id')->toArray();
        $userAssignments = Auth::user()->activeAssignments;
        $userExpiredAssignments = Auth::user()->expiredAssignments;
        $upcomingPersonalAssignments = Assignment::where('parent', 'users')
            ->where('parent_id', Auth::user()->id)
            ->where('submission_date', '>=', Carbon::now())
            ->where('available_date', '>', Carbon::now())
            ->oldest('submission_date')
            ->get();

        $upcomingAssignments = [];
        $waitingForResponse = [];
        $waitingForResponseIDs = [];
        $noWordLimitAssignments = [];

        $assignmentGroupLearners = AssignmentGroupLearner::with(['group.assignment.course'])
            ->where('user_id', Auth::user()->id)->get();

        foreach ($coursesTaken as $courseTaken) {
            foreach ($courseTaken->package->course->activeAssignments as $assignment) {

                $allowed_package = json_decode($assignment->allowed_package);
                $assignmentDisabledLearners = $assignment->disabledLearners()->pluck('user_id')->toArray();
                $package_id = $courseTaken->package->id;
                $course = $courseTaken->package->course;
                // check if the assignment is allowed on the learners package or there's no set package allowed
                if ((! is_null($allowed_package) && in_array($package_id, $allowed_package)) || is_null($allowed_package) || in_array($assignment->id, $addOns)) {
                    if (! in_array($courseTaken->user_id, $assignmentDisabledLearners)) {

                        $assignmentManuscript = AssignmentManuscript::where('user_id', Auth::user()->id)
                            ->where('assignment_id', $assignment->id)
                            ->first();

                        if (! $assignmentManuscript || ($assignmentManuscript && ! $assignmentManuscript->locked
                            && ! $assignmentManuscript->has_feedback)) {
                            // added the condition because of the update for submission date
                            // the original is the else
                            if (! AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                                if ($course->type == 'Single' && $assignment->submission_date == '365') {
                                    if (\Carbon\Carbon::parse($courseTaken->end_date)->gt(Carbon::now())) {
                                        $includeAssignment = $assignment;
                                        $includeAssignment->course_taken_end_date = $courseTaken->end_date; // for displaying submit button
                                        if ($assignment->max_words === 0) {
                                            $noWordLimitAssignments[] = $includeAssignment;
                                        } else {
                                            $assignments[] = $includeAssignment;
                                        }
                                    }
                                } else {
                                    if (\Carbon\Carbon::parse($courseTaken->started_at)->addDays((int) $assignment->submission_date)
                                        ->gt(Carbon::now())) {
                                        if ($assignment->max_words === 0) {
                                            $noWordLimitAssignments[] = $assignment;
                                        } else {
                                            $assignments[] = $assignment;
                                        }
                                    }
                                }
                            } else {
                                // added the && to check if the course taken is not yet expired
                                if (\Carbon\Carbon::parse($assignment->submission_date)->gt(Carbon::now()->subDay()) &&
                                    \Carbon\Carbon::parse($courseTaken->end_date)->gt(Carbon::now())) {
                                    if ($assignment->max_words === 0) {
                                        $noWordLimitAssignments[] = $assignment;
                                    } else {
                                        $assignments[] = $assignment;
                                    }
                                }
                            }
                        }

                        if ($assignmentManuscript && $assignmentManuscript->locked && ! $assignmentManuscript->has_feedback) {
                            $waitingForResponse[] = $assignment;
                            $waitingForResponseIDs[] = $assignment->id;
                        }
                    }
                }
            }

            foreach ($courseTaken->package->course->expiredAssignments as $assignment) {

                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $courseTaken->package->id;
                $course = $courseTaken->package->course;
                // check if the assignment is allowed on the learners package or there's no set package allowed
                if ((! is_null($allowed_package) && in_array($package_id, $allowed_package)) || is_null($allowed_package) || in_array($assignment->id, $addOns)) {
                    // added the condition because of the update for submission date
                    // the original is the else

                    // this waiting for response is new, if it's removed use the commented one
                    $waitingForResponseManuscript = AssignmentManuscript::where('user_id', Auth::user()->id)
                        ->where('editor_id', '!=', 0)
                        ->where('locked', 1)
                        ->where('status', 0)
                        ->where('assignment_id', $assignment->id)->first();

                    if ($waitingForResponseManuscript && ! in_array($assignment->id, $waitingForResponseIDs)) {
                        $waitingForResponse[] = $assignment;
                    }

                    if (! AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                        if ($course->type == 'Single' && $assignment->submission_date == '365') {
                            if (\Carbon\Carbon::parse($courseTaken->end_date)->lt(Carbon::now())) {
                                $expiredAssignments[] = $assignment;
                            }
                        } else {
                            if (\Carbon\Carbon::parse($courseTaken->started_at)->addDays((int) $assignment->submission_date)
                                ->lt(Carbon::now())) {
                                $expiredAssignments[] = $assignment;
                            }
                        }
                    } else {
                        $assignmentManuscript = AssignmentManuscript::where('user_id', Auth::user()->id)
                            ->where('assignment_id', $assignment->id)->first();

                        if (\Carbon\Carbon::parse($assignment->submission_date)->lt(Carbon::now())) {
                            if ($course->type == 'Group') {
                                // check if assignment manuscript has feedback
                                if ($assignmentManuscript) {
                                    $assignmentFeedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $assignmentManuscript->id)->first();
                                    $assignmentGroups = AssignmentGroup::where('assignment_id', $assignment->id)->pluck('id')->toArray();
                                    $userAssignmentGroupLearner = AssignmentGroupLearner::where('user_id', Auth::user()->id)
                                        ->whereIn('assignment_group_id', $assignmentGroups)->first();

                                    // for assignment no group check if there's a feedback and the manuscript status is not 0
                                    if (($assignmentFeedback && $assignmentManuscript->status > 0) || $userAssignmentGroupLearner) {
                                        $expiredAssignments[] = $assignment;
                                    } else {
                                        // $waitingForResponse[] = $assignment; this is the old one
                                    }
                                } else {
                                    $expiredAssignments[] = $assignment;
                                }
                            } else {
                                $expiredAssignments[] = $assignment;
                            }
                        }

                        if (! $assignmentManuscript && AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)
                            && \Carbon\Carbon::parse($assignment->submission_date)->gt(Carbon::now()) &&
                            \Carbon\Carbon::parse($assignment->available_date)->gt(Carbon::now())) {
                            $upcomingAssignments[] = $assignment;
                        }
                    }
                }
            }
        }

        foreach ($userAssignments as $assignment) {
            $manuscript = $assignment->manuscripts->first();
            $feedback = null;
            if ($manuscript) {
                $feedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript['id'])
                    ->where('is_active', 1)->first();
            }

            if (! $feedback) {
                if ($manuscript && $manuscript->locked) {
                    $waitingForResponse[] = $assignment;
                } else {
                    if (\Carbon\Carbon::parse($assignment->submission_date)->gt(Carbon::now())) {
                        $assignments[] = $assignment;
                    }
                }
            }
            /*
             * old code
             * if (\Carbon\Carbon::parse($assignment->submission_date)->gt(Carbon::now())) {
                $assignments[] = $assignment;
            }*/

        }

        foreach ($userExpiredAssignments as $assignment) {
            $manuscript = $assignment->manuscripts->first();
            $feedback = null;
            if ($manuscript) {
                $feedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript['id'])
                    ->where('is_active', 1)->first();
            }

            if ($feedback) {
                $expiredAssignments[] = $assignment;
            }
            /*
             * old code
             * if (\Carbon\Carbon::parse($assignment->submission_date)->lt(Carbon::now())) {
                $expiredAssignments[] = $assignment;
            }*/
        }
        // sort array by created_at
        $expiredAssignmentCreated = array_column($expiredAssignments, 'created_at');
        array_multisort($expiredAssignmentCreated, SORT_DESC, $expiredAssignments);

        foreach ($upcomingPersonalAssignments as $upcomingPersonalAssignment) {
            $upcomingAssignments[] = $upcomingPersonalAssignment;
        }

        usort($upcomingAssignments, function ($a, $b) {
            return strtotime($a->submission_date) - strtotime($b->submission_date);
        });

        $expiredAssignments = array_unique($expiredAssignments);
        // added this to not show any new assignment if learner is disabled
        $assignments = !Auth::user()->isDisabled ? $assignments : [];

        return view('frontend.learner.assignment', compact('assignments', 'expiredAssignments',
            'upcomingAssignments', 'waitingForResponse', 'assignmentGroupLearners', 'noWordLimitAssignments'));
    }

    public function assignmentManuscriptUpload($id, Request $request): RedirectResponse
    {
        $assignment = Assignment::findOrFail($id);
        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $assignment->id)->where('user_id', Auth::user()->id)->first();
        $courseIds = [];
        $coursesTaken = Auth::user()->coursesTaken;
        foreach ($coursesTaken as $course) {
            foreach ($course->package->course as $course) {
                $courseIds[] = $course;
            }
        }

        if ($request->hasFile('filename') &&
            $request->file('filename')->isValid() &&
            (in_array($assignment->course_id, $courseIds) || $assignment->parent === 'users') &&
            ! $assignmentManuscript) {
            $time = time();
            $destinationPath = 'storage/assignment-manuscripts/'; // upload path

            $extensions = ['doc', 'docx', 'odt', 'pdf'];
            if ($assignment->for_editor) {
                $extensions = ['docx', 'doc'];
            }

            $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = Auth::user()->id;
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            $request->filename->move($destinationPath, end($expFileName));

            if (! in_array($extension, $extensions)) {
                return redirect()->back()->withInput()->with(
                    'manuscript_test_error', 'Invalid file format. Allowed formats are DOC, DOCX, ODT, PDF'
                );
            }

            // count words
            $word_count = 0;
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.end($expFileName));
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.end($expFileName));
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.end($expFileName));
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }

            $word_to_deduct = $word_count * 0.02;
            $new_word_count = ceil($word_count - $word_to_deduct);
            $assignment_max_words = $assignment->allow_up_to > 0 ? $assignment->allow_up_to : $assignment->max_words;

            // check if the assignment is for editor only and if it meets the max word
            /* $assignment->for_editor && */
            if ($new_word_count > $assignment_max_words && $assignment->check_max_words) {
                return redirect()->back()->with(['errorMaxWord' => true, 'editorMaxWord' => $assignment->max_words]);
            }

            $join_group = 0;
            if ($assignment->show_join_group_question) {
                $join_group = isset($request->join_group) ? 1 : 0;
            }

            $letterToEditor = null;
            if ($request->hasFile('letter_to_editor') && $request->file('letter_to_editor')->isValid()
                && $assignment->send_letter_to_editor) {
                $destinationPathLetter = 'storage/letter-to-editor';
                $extensionLetter = pathinfo($_FILES['letter_to_editor']['name'], PATHINFO_EXTENSION);
                $actualNameLetter = time(); // pathinfo($_FILES['letter_to_editor']['name'],PATHINFO_FILENAME);
                $fileNameLetter = AdminHelpers::checkFileName($destinationPathLetter, $actualNameLetter, $extension); // rename document
                $expFileNameLetter = explode('/', $fileNameLetter);

                if (! in_array($extensionLetter, $extensions)) {
                    return redirect()->back()->withInput()->with(
                        'manuscript_test_error', 'Invalid file format. Allowed formats are DOC, DOCX, ODT, PDF'
                    );
                }

                $request->letter_to_editor->move($destinationPathLetter, end($expFileNameLetter));
                $letterToEditor = '/'.$fileNameLetter;

            }

            // assigned_editor is used in check_max_words
            $editor_id = $assignment->editor_id ? $assignment->editor_id
                : ($assignment->assigned_editor ? $assignment->assigned_editor : 0);

            $submittedManuscript = AssignmentManuscript::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::user()->id,
                'filename' => '/'.$destinationPath.end($expFileName),
                'words' => $word_count,
                'type' => $request->type,
                'manu_type' => $request->manu_type,
                'join_group' => $join_group,
                'letter_to_editor' => $letterToEditor,
                'editor_id' => $editor_id,
                'uploaded_at' => now(),
            ]);
            Log::create([
                'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for assignment '.$assignment->title,
            ]);

            // Admin notification
            if (($assignment->course && $assignment->course->type === 'Single') || $assignment->parent === 'users') {
                $message = Auth::user()->full_name.' submitted a manuscript for assignment '.$assignment->title;
                $toMail = 'post@forfatterskolen.no'; // post@forfatterskolen.no

                $email_data['email_message'] = $message;
                // use queue to send email on background
                Mail::to($toMail)->queue(new AssignmentSubmittedEmail($email_data));
            }

            if ($assignment->parent === 'users' && $assignment->editor_id) {
                $emailTemplate = AdminHelpers::emailTemplate('Personal Assignment Editor Notification');
                $email_content = str_replace([
                    '_learner_',
                    '_assignment_',
                ], [
                    Auth::user()->full_name,
                    $assignment->title,
                ], $emailTemplate->email_content);

                $editor = User::find($assignment->editor_id);
                $to = $editor->email;
                $emailData = [
                    'email_subject' => $emailTemplate->subject,
                    'email_message' => $email_content,
                    'from_name' => '',
                    'from_email' => 'post@forfatterskolen.no',
                    'attach_file' => null,
                ];
                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            }

            // notify user
            $user_email = Auth::user()->email;
            $confirm_email['email_message'] = 'Oppgaven din er levert, har vi problemer med filen vil vi ta kontakt med med deg.';
            // Mail::to($user_email)->queue(new SendEmailMessageOnly($confirm_email));

            $emailTemplate = AdminHelpers::emailTemplate('Assignment Submitted');
            $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
                Auth::user()->first_name, '');

            dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
                $emailTemplate->from_email, null, null, 'assignment-manuscripts',
                $submittedManuscript->id));

            return redirect()->back()->with('success', true);
        }

        return redirect()->back();
    }

    public function group_show($id): View
    {
        $group = AssignmentGroup::where('id', $id)->whereHas('learners', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->firstOrFail();
        $groupLearners = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', '!=', Auth::user()->id);
        $groupLearner = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', '=', Auth::user()->id)->first();
        $otherLearnersIdList = $groupLearners->pluck('id')->toArray();
        $could_send_feedback_to = $groupLearner->could_send_feedback_to_id_list ?: $otherLearnersIdList;
        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $group->assignment_id)
            ->where('user_id', Auth::user()->id)->first();

        array_push($could_send_feedback_to, $groupLearner->id);
        $groupLearnerList = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->whereIn('id', $could_send_feedback_to)->orderBy('created_at', 'desc')->get();

        return view('frontend.learner.groupShow', compact('group', 'otherLearnersIdList', 'could_send_feedback_to',
            'groupLearnerList', 'assignmentManuscript'));
    }

    public function groupShowDetails($id): View
    {
        $group = AssignmentGroup::where('id', $id)->whereHas('learners', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->firstOrFail();
        $groupLearners = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', '!=', Auth::user()->id);
        $groupLearner = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', '=', Auth::user()->id)->first();
        $otherLearnersIdList = $groupLearners->pluck('id')->toArray();
        $could_send_feedback_to = $groupLearner->could_send_feedback_to_id_list ?: $otherLearnersIdList;
        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $group->assignment_id)
            ->where('user_id', Auth::user()->id)->first();

        array_push($could_send_feedback_to, $groupLearner->id);
        $groupLearnerList = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->whereIn('id', $could_send_feedback_to)->orderBy('created_at', 'desc')->get();

        return view('frontend.partials.assignment._group_show', compact('group', 'otherLearnersIdList', 'could_send_feedback_to',
            'groupLearnerList', 'assignmentManuscript'));
    }

    public function groupLearnerDetails($group_id)
    {
        /* $groupLearners = AssignmentGroupLearner::where('assignment_group_id', $group_id)
            ->where('user_id', '!=', Auth::user()->id);
        $groupLearner = AssignmentGroupLearner::where('assignment_group_id', $group_id)
            ->where('user_id', '=', Auth::user()->id)->first();

        $otherLearnersIdList = $groupLearners->pluck('id')->toArray();
        $could_send_feedback_to = $groupLearner->could_send_feedback_to_id_list ?: $otherLearnersIdList;

        array_push($could_send_feedback_to, $groupLearner->id);
        $groupLearnerList = AssignmentGroupLearner::where('assignment_group_id', $group_id)
            ->whereIn('id', $could_send_feedback_to)->orderBy('created_at', 'desc')->get()->map(function($groupLearner) {
                $groupLearner['feedback'] = AssignmentFeedback::where('assignment_group_learner_id',
                            $groupLearner->id)->where('user_id', Auth::user()->id)->first();
                return $groupLearner;
            });

        return [
            'groupLearnerList' => $groupLearnerList
        ]; */

        $group = AssignmentGroup::where('id', $group_id)->whereHas('learners', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->firstOrFail();

        $userId = Auth::user()->id;

        $groupLearnerList = AssignmentGroupLearner::where('assignment_group_id', $group_id)
            ->where('user_id', '!=', $userId)
            ->orWhere('user_id', $userId)
            ->whereIn(
                'id',
                AssignmentGroupLearner::where('assignment_group_id', $group_id)
                    ->pluck('id')
                    ->toArray()
            )
            ->orderBy('created_at', 'desc')
            ->with(['feedback' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->with('user')
            ->get();

        // Manually call the accessor for each model instance
        $groupLearnerList->map(function ($groupLearner) {
            $groupLearner->learnerManuscript = $groupLearner->learnerManuscript();

            return $groupLearner;
        });

        return [
            'groupLearnerList' => $groupLearnerList,
        ];
    }

    public function submit_feedback($group_id, $id, Request $request)
    {
        $group = AssignmentGroup::where('id', $group_id)->whereHas('learners', function ($query) use ($id) {
            $query->where('id', $id)->where('user_id', '<>', Auth::user()->id);
        })->firstOrFail();
        if ($request->hasFile('filename')) {
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];

            $filesWithPath = '';
            // loop through all the uploaded files
            foreach ($request->file('filename') as $k => $file) {
                $extension = pathinfo($_FILES['filename']['name'][$k], PATHINFO_EXTENSION);
                $actual_name = AssignmentGroupLearner::find($id)->user_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension);
                $filesWithPath .= '/'.AdminHelpers::checkFileName($destinationPath, $actual_name.'f', $extension).', ';

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $file->move($destinationPath, $fileName);

            }

            $filesWithPath = trim($filesWithPath, ', ');

            AssignmentFeedback::create([
                'assignment_group_learner_id' => $id,
                'user_id' => Auth::user()->id,
                'filename' => $filesWithPath,
            ]);

            return redirect()->back();
        }
    }

    public function manuscript(): View
    {
        return view('frontend.learner.manuscript');
    }

    public function invoice(Request $request): View
    {
        $unpaid = Invoice::where('user_id', Auth::user()->id)
            ->where('fiken_is_paid', 0)
            ->orderBy('fiken_dueDate', 'ASC')
            ->get();

        $paid = Invoice::where('user_id', Auth::user()->id)
            ->whereIn('fiken_is_paid', [1, 3])
            ->orderBy('fiken_dueDate', 'DESC')
            ->get();

        $invoices = $unpaid->merge($paid)->paginate(15);

        if ($request->has('filter') && $request->get('filter')) {
            $invoices = Auth::user()->invoices()->where('id', $request->get('filter'))
                ->paginate(15);
        }

        $sveaOrders = Auth::user()->orders()->svea()->with('coachingTime')->paginate(10);
        $payLaterOrders = Auth::user()->orders()
            ->where([
                'is_pay_later' => 1,
                'is_processed' => 1,
                'is_invoice_sent' => 0,
                'is_order_withdrawn' => 0,
            ])
            ->paginate(10);
        $user = Auth::user();

        $orderAttachments = DB::table('course_order_attachments')
            ->leftJoin('courses', 'course_order_attachments.course_id', '=', 'courses.id')
            ->leftJoin('packages', 'course_order_attachments.package_id', '=', 'packages.id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->select('course_order_attachments.*', 'courses.title as course_title',
                'courses_taken.id as course_taken_id', 'courses_taken.deleted_at')
            ->where('courses_taken.user_id', $user->id)
            ->where('course_order_attachments.user_id', $user->id)
            ->whereNull('courses_taken.deleted_at')
            ->groupBy('course_order_attachments.id')
            ->get();

        $giftPurchases = Auth::user()->giftPurchases;

        $orderHistory = Auth::user()->orders;
        $timeRegisters = Auth::user()->timeRegisters->load('project');

        /*$ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};*/
        return view('frontend.learner.invoice', compact(
            'invoices',
            'sveaOrders',
            'user',
            'orderAttachments',
            'giftPurchases',
            'orderHistory',
            'timeRegisters',
            'payLaterOrders'
        ));
    }

    public function invoiceShow($id)
    {
        $invoice = Invoice::findOrFail($id);
        if (Auth::user()->can('viewInvoice', $invoice)) {
            $ch = curl_init($this->fikenInvoices);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $data = curl_exec($ch);
            $data = json_decode($data);
            $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};

            return view('frontend.learner.invoiceShow', compact('invoice', 'fikenInvoices'));
        }

        return abort('503');
    }

    public function generatePayLaterInvoice($order_id, Request $request)
    {
        $order = Order::where('id', $order_id)->where('user_id', auth()->id())->first();

        if (!$order) {
            return redirect()->back();
        }

        $learner = Auth::user();
        $paymentMode = PaymentMode::findOrFail(3);
        $payment_mode = 'Bankoverføring';

        if ($request->has('payment_plan_id')) {
            $paymentPlan = PaymentPlan::find($request->payment_plan_id);
            $payment_plan = (int) $request->payment_plan_id === 10 ? '24 måneder' : $paymentPlan->plan;
            $divisor = (int) $request->payment_plan_id === 10 ? 24 : $paymentPlan->division;
        } else {
            $payment_plan = $request->payment_plan_in_months.' måneder';
            $divisor = $request->payment_plan_in_months;
        }

        $inputtedComment = '';
        $comment = '('.$inputtedComment.' ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $product_ID = 884373255;

        $price = ($order->price - $order->discount) * 100;
        $dueDate = date('Y-m-d');

        // always split the invoice
        $request->merge([
            'split_invoice' => 1
        ]);

        if (isset($request->split_invoice) && $request->split_invoice) {
            $division = $divisor * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            $has_vat = false;

            $baseDate = Carbon::parse($dueDate); // starting due date

            for ($i = 1; $i <= $divisor; $i++) { // loop based on the split count
                //$dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
                $dueDate = $baseDate->copy()->addMonth($i)->format('Y-m-d');
                
                $invoice_fields = [
                    'user_id' => $learner->id,
                    'first_name' => $learner->first_name,
                    'last_name' => $learner->last_name,
                    'netAmount' => $price,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $product_ID,
                    'email' => $learner->email,
                    'telephone' => $learner->address->telephone,
                    'address' => $learner->address->street,
                    'postalPlace' => $learner->address->city,
                    'postalCode' => $learner->address->zip,
                    'comment' => $comment,
                    'payment_mode' => $paymentMode->mode,
                    'index' => $i,
                ];

                if ($request->product_type === 'manuscript_vat') {
                    $invoice_fields['vat'] = ($price / 100) * 25;
                    $has_vat = true;
                }

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields, $has_vat);
            }
        } else {
            $has_vat = false;
            $dueDate = date_format(date_create(Carbon::parse($dueDate)->addDays(14)), 'Y-m-d');
            $invoice_fields = [
                'user_id' => $learner->id,
                'first_name' => $learner->first_name,
                'last_name' => $learner->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $product_ID,
                'email' => $learner->email,
                'telephone' => $learner->address->telephone,
                'address' => $learner->address->street,
                'postalPlace' => $learner->address->city,
                'postalCode' => $learner->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields, $has_vat);
        }

        $order->is_invoice_sent = 1;
        $order->save();

        return redirect()->route('learner.invoice', ['tab' => 'pay-later'])->with([
            'errors' => AdminHelpers::createMessageBag('Invoice created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    public function changePortal($portal): RedirectResponse
    {
        \Session::put('current-portal', $portal);

        return redirect()->route('learner.dashboard');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function invoiceVippsPayment($fiken_invoice_id)
    {
        $invoice = Invoice::where('fiken_invoice_id', $fiken_invoice_id)->first();

        if ($invoice) {
            $orderId = $invoice->fiken_invoice_id;
            $price = $invoice->fiken_balance * 100;
            $transactionText = 'Betaling for fakturanummer'.$orderId;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
                'fallbackUrl' => 'https://www.forfatterskolen.no/thankyou?page=vipps',
            ];

            return $this->vippsInitiatePayment($vippsData);
        }

        return redirect()->back();
    }

    public function downloadOrder($order_id)
    {
        $order = Order::find($order_id);

        $user = \Auth::user();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML(view('frontend.pdf.order-receipt-new', compact('order', 'user')));

        return $pdf->download($order->id.'.pdf');
    }

    public function downloadCreditedOrder($order_id)
    {
        $order = Order::find($order_id);

        $user = \Auth::user();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML(view('frontend.pdf.svea-credit-note', compact('order', 'user')));

        return $pdf->download($order->id.'-Kreditnota.pdf');
    }

    public function saveCompany($order_id, Request $request): JsonResponse
    {
        $request->validate([
            'customer_number' => 'required',
            'company_name' => 'required',
            'street_address' => 'required',
            'post_number' => 'required',
            'place' => 'required',
        ]);

        $orderCompany = OrderCompany::find($request->id) ? OrderCompany::find($request->id) : new OrderCompany;
        $orderCompany->order_id = $request->order_id;
        $orderCompany->customer_number = $request->customer_number;
        $orderCompany->company_name = $request->company_name;
        $orderCompany->street_address = $request->street_address;
        $orderCompany->post_number = $request->post_number;
        $orderCompany->place = $request->place;
        $orderCompany->save();

        return response()->json($orderCompany->order);
    }

    /**
     * Redeem purchased gift
     */
    public function redeemGift(Request $request): RedirectResponse
    {

        $giftPurchase = GiftPurchase::where('redeem_code', $request->redeem_code)->first();

        if (! $giftPurchase || $giftPurchase->is_redeemed || $giftPurchase->user_id === Auth::user()->id
            || ($giftPurchase->expired_at && Carbon::now()->gt($giftPurchase->expired_at))) {

            $errorMessage = '';
            if (! $giftPurchase) {
                $errorMessage = AdminHelpers::createMessageBag('Invalid Redeem code.');
            }

            if ($giftPurchase && $giftPurchase->is_redeemed) {
                $errorMessage = AdminHelpers::createMessageBag('Gift already redeemed.');
            }

            if ($giftPurchase && $giftPurchase->user_id === Auth::user()->id) {
                $errorMessage = AdminHelpers::createMessageBag('Buyer cannot claim the gift.');
            }

            if ($giftPurchase && $giftPurchase->expired_at && Carbon::now()->gt($giftPurchase->expired_at)) {
                $errorMessage = AdminHelpers::createMessageBag('Gift card expired.');
            }

            return redirect()->back()->with([
                'errors' => $errorMessage,
                'alert_type' => 'danger',
            ]);
        }

        if ($giftPurchase->parent === 'course-package') {
            $this->redeemCourse($giftPurchase);
        }

        if ($giftPurchase->parent === 'shop-manuscript') {
            $this->redeemManuscript($giftPurchase);
        }

        $giftPurchase->is_redeemed = 1;
        $giftPurchase->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Gift redeemed successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function redeemCourse(GiftPurchase $giftPurchase)
    {
        $package = $giftPurchase->coursePackage;
        $course_status = 0;
        $courseTaken = CoursesTaken::firstOrNew(['user_id' => Auth::user()->id, 'package_id' => $package->id]);
        $courseTaken->is_active = $course_status;
        $courseTaken->is_welcome_email_sent = 0;
        $courseTaken->gift_purchase_id = $giftPurchase->id;
        $courseTaken->save();

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                $shopManuscriptTaken = new ShopManuscriptsTaken;
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->package_shop_manuscripts_id = $package->shop_manuscripts[0]->id;
                $shopManuscriptTaken->gift_purchase_id = $giftPurchase->id;
                $shopManuscriptTaken->save();
            }
        }

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                // add user to the included course
                $courseIncluded = CoursesTaken::firstOrNew([
                    'user_id' => Auth::user()->id,
                    'package_id' => $included_course->included_package_id,
                ]);
                $courseIncluded->is_active = $course_status;
                $courseIncluded->gift_purchase_id = $giftPurchase->id;
                $courseIncluded->save();
            }
        }

        $user = Auth::user();
        $user_email = $user->email;

        $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
        $search_string = [
            '[username]', '[password]',
        ];
        $replace_string = [
            $courseTaken->user->email, $password,
        ];

        $email_content = str_replace($search_string, $replace_string, $package->course->email);
        $attachments = null;

        $encode_email = encrypt($user_email);
        $redirectLink = encrypt(route('learner.course'));
        $actionUrl = route('auth.login.emailRedirect', [$encode_email, $redirectLink]);
        $actionText = 'Mine Kurs';

        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments, 'courses-taken-order',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));
    }

    public function redeemManuscript($giftPurchase)
    {
        $user = Auth::user();
        $user_email = $user->email;

        $shopManuscriptTaken = new ShopManuscriptsTaken;
        $shopManuscriptTaken->user_id = $user->id;
        $shopManuscriptTaken->description = null;
        $shopManuscriptTaken->shop_manuscript_id = $giftPurchase->parent_id;
        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->coaching_time_later = 0;
        $shopManuscriptTaken->is_welcome_email_sent = 0;
        $shopManuscriptTaken->save();

        $emailTemplate = AdminHelpers::emailTemplate('Shop Manuscript Welcome Email');
        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
            Auth::user()->first_name, 'shop-manuscripts-taken');

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, 'shop-manuscripts-taken', $shopManuscriptTaken->id));
    }

    public function vippsEFaktura($invoice_id, Request $request): RedirectResponse
    {
        $invoice = new Invoice;
        $invoice = $invoice->find($invoice_id);

        $fikenInvoice = new FikenInvoice;
        $fikenInvoice->setMobileNumber($request->mobile_number);
        $fikenInvoice->setFikenInvoiceId($invoice->fiken_invoice_id);

        $response = $fikenInvoice->vippsEFaktura(Auth::user());
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
    public function setVippsEFaktura(Request $request): RedirectResponse
    {
        if ($request->mobile_number) {
            $request->validate([
                'mobile_number' => 'digits:8',
            ]);
        }

        $address = Address::firstOrNew([
            'user_id' => Auth::user()->id,
        ]);
        $address->vipps_phone_number = $request->mobile_number ?: null;
        $address->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record saved.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Download Svea invoice
     *
     * @param  $type  String
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoiceByType($id, $type)
    {
        $order = Order::find($id);

        if ($type === 'receipt') {

            $user = \Auth::user();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $pdf->loadHTML(view('frontend.pdf.order-receipt', compact('order', 'user')));

            return $pdf->download($order->id.'.pdf');

        } else {

            $orderID = $order->svea_order_id;
            $invoiceID = $order->svea_invoice_id;
            $base_url = env('SVEA_PROD_URL').'/pdf/'.$orderID.'/'.$invoiceID;
            $timestamp = gmdate('Y-m-d H:i');
            $merchantId = env('SVEA_CHECKOUTID');
            $secret = env('SVEA_CHECKOUT_SECRET');

            $token = base64_encode($merchantId.':'.hash('sha512', ''.$secret.$timestamp));
            $header = [];
            $header[] = 'Content-type: application/json';
            $header[] = 'Timestamp: '.$timestamp;
            $header[] = 'Authorization: Svea '.$token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Then, after your curl_exec call:
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $responseBody = substr($response, $header_size);

            if ($httpcode === 200) {
                $decodeResponse = json_decode($responseBody);

                if ($decodeResponse->Pdf) {
                    $path = public_path($invoiceID.'.pdf');
                    $contents = base64_decode($decodeResponse->Pdf);

                    // store file temporarily
                    file_put_contents($path, $contents);

                    // download file and delete it
                    return response()->download($path);
                }
            }

            return redirect()->back();

        }
    }

    /**
     * Publishing Page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function publishing(Request $request, PublishingService $publishingService): View
    {
        if ($request->search && ! empty($request->search)) {
            $searchFromGenre = Genre::where('name', 'LIKE', '%'.$request->search.'%')
                ->get(['id'])->toArray();

            $searchCollection = new \Illuminate\Database\Eloquent\Collection;

            // loop through all of the search result from the genre
            // then search it on the field in publishing
            foreach ($searchFromGenre as $searchID) {
                $result = $publishingService->search($searchID['id']);
                $searchCollection = $searchCollection->merge($result); // merge the result found
            }

            $searchCollection = $searchCollection->toArray(); // convert the collection to array
            $total = count($searchCollection);
            $page = Paginator::resolveCurrentPage('page') ?: 1; // get the current page
            $startIndex = ($page - 1) * 15; // 15 is per page
            $results = array_slice($searchCollection, $startIndex, 15);

            // create a paginator based on the search result
            $publishingHouses = new LengthAwarePaginator($results, $total, 15, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

        } else {
            $publishingHouses = $publishingService->paginate(15);
        }

        return view('frontend.learner.publishing', compact('publishingHouses'));
    }

    /**
     * Get the competitions
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function competition(CompetitionService $competitionService): View
    {
        $competitions = $competitionService->getActiveRecords();

        return view('frontend.learner.competition', compact('competitions'));
    }

    /**
     * Display all private messages
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function privateMessage(): View
    {
        $messages = Auth::user()->messages()->paginate(10);

        return view('frontend.learner.private-message', compact('messages'));
    }

    /**
     * Display all writing groups
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function writingGroups(WritingGroupService $writingGroupService): View
    {
        $writingGroups = $writingGroupService->getRecord();

        return view('frontend.learner.writing-groups', compact('writingGroups'));
    }

    /**
     * Get writing group or update the record
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function writingGroup($id, WritingGroupService $writingGroupService, Request $request)
    {
        $writingGroup = $writingGroupService->getRecord($id);

        if ($writingGroup) {
            if ($request->isMethod('put')) {
                $writingGroup->next_meeting = $request->next_meeting;
                $writingGroup->save();
            }

            return view('frontend.learner.writing-group', compact('writingGroup'));
        }

        return redirect()->route('learner.writing-groups');
    }

    public function upgrade(Request $request): View
    {

        // check if from svea payment
        if ($request->has('svea_ord') || $request->has('pl_ord')) {
            $order_id = $request->get('svea_ord') ?? $request->input('pl_ord');
            $order = Order::find($order_id);

            if ($request->has('svea_ord')) {
                SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
            }

            // add shop manuscript to user
            if (! $order->is_processed) {
                $shopManuscriptService = new ShopManuscriptService;
                $shopManuscriptService->upgradeShopManuscript($order);
            }

            $order->is_processed = 1;
            $order->save();
        }

        $assignments = [];
        $coursesTaken = Auth::user()->coursesTaken;
        $today = Carbon::now();

        $addOns = AssignmentAddon::where('user_id', \Auth::user()->id)->pluck('assignment_id')->toArray();

        foreach ($coursesTaken as $course) {
            foreach ($course->package->course->assignments as $assignment) {

                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $course->package->id;
                $submission_date = \App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date) ?
                    Carbon::parse($assignment->submission_date) : Carbon::parse($course->started_at)
                        ->addDays((int) $assignment->submission_date);
                // $submission_date =  Carbon::parse($assignment->submission_date);

                // check if the assignment is allowed on the learners package and the submission date is in future
                // or there's no set package allowed and the submission date is in future
                if ((! is_null($allowed_package) && ! in_array($package_id, $allowed_package) && $today->lt($submission_date) 
                    && ! in_array($assignment->id, $addOns))
                    || (is_null($allowed_package) && $today->lt($submission_date) && ! in_array($assignment->id, $addOns))) {
                    $assignments[] = $assignment;
                }
            }
        }

        $user = Auth::user();

        $coursesTaken = CoursesTaken::where('user_id', $user->id)
            ->whereDoesntHave('package.course', function ($query) {
                $query->where('id', 17); // Exclude course with ID 17
            })
            ->latest('created_at')
            ->get();

        $coursesTaken->each(function ($courseTaken) {
            $courseTaken->otherPackages = Package::where('course_id', $courseTaken->package->course->id)
                ->where('id', '>', $courseTaken->package->id)
                ->where('is_show', 1)
                ->get();
        });

        return view('frontend.learner.upgrade', compact('assignments', 'coursesTaken'));
    }

    public function takeCourse(Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($request->courseTakenId);
        if (Auth::user()->can('participateCourse', $courseTaken) &&
            FrontendHelpers::isCourseTakenAvailable($courseTaken)) {
            // removed because the course is not always active
            // &&FrontendHelpers::isCourseActive($courseTaken->package->course)

            $course = $courseTaken->package->course;
            $courseTaken->started_at = date('Y-m-d h:i:s');

            /*
             * check if validity date is greater than 0 and
             * set an end date (this is for courses that is for sale for a month)
             */
            if ($courseTaken->package->validity_period > 0) {
                if (! $courseTaken->end_date) {
                    $courseTaken->end_date = Carbon::today()->addMonth($courseTaken->package->validity_period);
                }
            } else {
                if (! $courseTaken->end_date) {
                    $courseTaken->end_date = Carbon::parse($course->start_date)->addYear(1);
                }
            }

            $courseTaken->save();

            return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        }

        return redirect()->back();
    }

    public function timeRegister(): View
    {
        /* $timeRegisters = Auth::user()->timeRegisters->load('project'); */
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        $timeRegisters = $standardProject ? TimeRegister::where('project_id', $standardProject->id)->get() : [];

        return view('frontend.learner.self-publishing.time-register', compact('timeRegisters'));
    }

    public function bookSale()
    {
        /* $bookSales = Auth::user()->bookSales; */
        $learner = Auth::user();

        $uniqueYears = ProjectBookSale::selectRaw('YEAR(date) as year')
            ->leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->where('user_id', $learner->id)
            ->distinct()
            ->pluck('year');

        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        $projectUserBook = null;
        $storageCosts = [];
        $paidDistributionYears = [];
        $registration = [];

        if ($standardProject) {
            $registration = ProjectRegistration::centralDistributions()->where('in_storage', 1)
                ->where('project_id', $standardProject->id)->first();

            $projectUserBook = ProjectRegistration::find($registration->id);

            $startYear = 2024; // Change if needed
            $currentYear = Carbon::now()->year;
            $years = range($startYear, $currentYear);
            $quarters = [1, 2, 3, 4]; // Define quarters
            $selectedQuarters = [1, 2, 3, 4]; // Define selected quarters

            // Get Total Sales by Year and Quarter
            $salesData = DB::table('project_books as books')
                ->select(
                    DB::raw('YEAR(sales.date) as year'),
                    DB::raw('QUARTER(sales.date) as quarter'),
                    DB::raw('SUM(amount) as total_sales')
                )
                ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
                ->whereBetween(DB::raw('YEAR(sales.date)'), [$startYear, $currentYear])
                ->where('books.project_id', $standardProject->id)
                ->groupBy('year', 'quarter')
                ->orderBy('year', 'DESC')
                ->get()
                ->groupBy('year'); // ->keyBy('year'); // Store results by year for easy lookup

            // Get Total Distributions by Year and Quarter
            $distributionsData = DB::table('project_registrations as distribution')
                ->select(
                    DB::raw('YEAR(distribution_costs.date) as year'),
                    DB::raw('QUARTER(distribution_costs.date) as quarter'),
                    DB::raw('SUM(amount) as total_distributions')
                )
                ->leftJoin('storage_distribution_costs as distribution_costs',
                    'distribution_costs.project_book_id', '=', 'distribution.id')
                ->where('distribution.id', $registration->id)
                ->groupBy('year', 'quarter')
                ->orderBy('year', 'DESC')
                ->orderBy('quarter', 'ASC')
                ->get()
                ->groupBy('year'); // Store results by year for easy lookup

            // Merge Data for Year and Quarter
            $storageCosts = collect($years)->map(function ($year) use ($salesData, $distributionsData, $quarters, $selectedQuarters) {
                // $sales = isset($salesData[$year]) ? $salesData[$year]->total_sales : 0;
                $allSales = [];
                $allDistributions = [];

                foreach ($quarters as $quarter) {
                    $sales = isset($salesData[$year])
                        ? ($salesData[$year]->firstWhere('quarter', $quarter)->total_sales ?? 0)
                        : 0;

                    $distribution = isset($distributionsData[$year])
                        ? ($distributionsData[$year]->firstWhere('quarter', $quarter)->total_distributions ?? 0) * 1.2
                        : 0;

                    $allSales[$quarter] = $sales;
                    $allDistributions[$quarter] = $distribution;
                }

                // Only include selected quarters in aggregated totals
                $filteredSales = collect($allSales)->only($selectedQuarters);
                $filteredDistributions = collect($allDistributions)->only($selectedQuarters);

                $salesByQuarter = $filteredSales->sum();
                $distributionsByQuarter = $filteredDistributions->sum();
                $payoutByQuarter = $salesByQuarter - $distributionsByQuarter;

                return [
                    'year' => $year,
                    'q1_distributions' => $allDistributions[1],
                    'q1_sales' => $allSales[1],
                    'q2_distributions' => $allDistributions[2],
                    'q2_sales' => $allSales[2],
                    'q3_distributions' => $allDistributions[3],
                    'q3_sales' => $allSales[3],
                    'q4_distributions' => $allDistributions[4],
                    'q4_sales' => $allSales[4],
                    'total_sales' => $salesByQuarter,
                    'total_distributions' => $distributionsByQuarter,
                    'payout' => $payoutByQuarter,
                    'sales_by_quarter' => $salesByQuarter,
                    'distributions_by_quarter' => $distributionsByQuarter,
                    'payout_by_quarter' => $payoutByQuarter,
                ];
            })->sortByDesc('year');

            $registrationDistributionCosts = ProjectRegistrationDistribution::where('project_registration_id', $registration->id)
                ->first();
            $paidDistributionYears = $registrationDistributionCosts->years ?? [];
        }

        $payouts = StoragePayout::where('project_registration_id', $registration->id)->get()->groupBy(['year', 'quarter']);

        return view('frontend.learner.self-publishing.sales', compact('learner', 'uniqueYears', 'projectUserBook',
            'storageCosts', 'paidDistributionYears', 'registration', 'payouts'));
    }

    public function exportStorageCost($project_id, $registration_id, $selectedYear)
    {
        $quarters = [1, 2, 3, 4];
        $selectedQuarters = [1, 2, 3, 4];

        $projectBook = ProjectBook::where('project_id', $project_id)->first();
        $bookName = $projectBook?->book_name;

        // Fetch sales data
        $salesData = DB::table('project_books as books')
            ->select(DB::raw('YEAR(sales.date) as year'), DB::raw('QUARTER(sales.date) as quarter'),
                DB::raw('SUM(amount) as total_sales'))
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereRaw('YEAR(sales.date) = ?', [$selectedYear])
            ->where('books.project_id', $project_id)
            ->groupBy('year', 'quarter')// ->groupBy('year')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get()
            ->groupBy('year'); // ->keyBy('year');

        // Fetch distributions data
        $distributionsData = DB::table('project_registrations as distribution')
            ->select(DB::raw('YEAR(distribution_costs.date) as year'), DB::raw('QUARTER(distribution_costs.date) as quarter'),
                DB::raw('SUM(amount) as total_distributions'))
            ->leftJoin('storage_distribution_costs as distribution_costs',
                'distribution_costs.project_book_id', '=', 'distribution.id')
            ->where('distribution.id', $registration_id)
            ->whereRaw('YEAR(distribution_costs.date) = ?', [$selectedYear])
            ->groupBy('year', 'quarter')
            ->orderBy('year')
            ->orderBy('quarter')
            ->get()
            ->groupBy('year');

        // Process data
        $data = collect([$selectedYear])->map(function ($year) use ($salesData, $distributionsData, $quarters, $selectedQuarters) {
            // $sales = isset($salesData[$year]) ? $salesData[$year]->total_sales : 0;
            $allSales = [];
            $allDistributions = [];

            foreach ($quarters as $quarter) {
                $sales = isset($salesData[$year])
                    ? ($salesData[$year]->firstWhere('quarter', $quarter)->total_sales ?? 0)
                    : 0;

                $distribution = isset($distributionsData[$year])
                    ? ($distributionsData[$year]->firstWhere('quarter', $quarter)->total_distributions ?? 0) * 1.2
                    : 0;

                $allSales[$quarter] = $sales;
                $allDistributions[$quarter] = $distribution;
            }

            // Only include selected quarters in aggregated totals
            $filteredSales = collect($allSales)->only($selectedQuarters);
            $filteredDistributions = collect($allDistributions)->only($selectedQuarters);

            $salesByQuarter = $filteredSales->sum();
            $distributionsByQuarter = $filteredDistributions->sum();
            $payoutByQuarter = $salesByQuarter - $distributionsByQuarter;

            return [
                'year' => $year,
                'q1_distributions' => $allDistributions[1],
                'q1_sales' => $allSales[1],
                'q2_distributions' => $allDistributions[2],
                'q2_sales' => $allSales[2],
                'q3_distributions' => $allDistributions[3],
                'q3_sales' => $allSales[3],
                'q4_distributions' => $allDistributions[4],
                'q4_sales' => $allSales[4],
                'total_sales' => $salesByQuarter,
                'total_distributions' => $distributionsByQuarter,
                'payout' => $payoutByQuarter,
                'sales_by_quarter' => $salesByQuarter,
                'distributions_by_quarter' => $distributionsByQuarter,
                'payout_by_quarter' => $payoutByQuarter,
            ];
        });

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML(view('frontend.pdf.distribution-cost', compact('data')));

        return $pdf->download('Royalty_'.$selectedYear.'_'.$bookName.'.pdf');
        // return $pdf->stream('distribution-cost.pdf');
    }

    public function bookForSale($id)
    {
        $userBookForSaleList = Auth::user()->booksForSale()->pluck('id')->toArray();

        if (! in_array($id, $userBookForSaleList)) {
            return redirect()->route('learner.book-sale');
        }

        $book = UserBookForSale::find($id);
        $totalBookSold = $book->sales()->sum('quantity');
        $totalBookSale = $book->sales()->sum('amount');

        $quantitySoldCount = $this->salesReportCounter($id, 'quantity-sold');
        $turnedOverCount = $this->salesReportCounter($id, 'turned-over');
        $freeCount = $this->salesReportCounter($id, 'free');
        $commissionCount = $this->salesReportCounter($id, 'commission');
        $shreddedCount = $this->salesReportCounter($id, 'shredded');
        $defectiveCount = $this->salesReportCounter($id, 'defective');
        $correctionsCount = $this->salesReportCounter($id, 'corrections');
        $countsCount = $this->salesReportCounter($id, 'counts');
        $returnsCount = $this->salesReportCounter($id, 'returns');

        return view('frontend.learner.self-publishing.book-for-sale', compact('book', 'totalBookSold', 'totalBookSale',
            'quantitySoldCount', 'turnedOverCount', 'freeCount', 'commissionCount', 'shreddedCount',
            'defectiveCount', 'correctionsCount', 'countsCount', 'returnsCount', ));
    }

    public function bookSaleByMonth($year)
    {
        /* $sales =  UserBookSale::select(
            DB::raw('sum(quantity) as total_quantity'),
            DB::raw("DATE_FORMAT(date,'%m') as month")
        )
            ->whereYear('date', date('Y'))
            ->where('user_id', Auth::user()->id)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $data = array_fill(0, 12, 0);

        foreach($sales as $order){
            $data[$order->month-1] = (int) $order->total_quantity;
        }

        return $data; */

        $learner = Auth::user();
        $standardProject = FrontendHelpers::getLearnerStandardProject($learner->id);

        $sales = ProjectBookSale::leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->select(
                DB::raw('SUM(amount) as amount_total'),
                DB::raw("DATE_FORMAT(date, '%m') as month"),
            )
            ->whereYear('date', $year)
            // ->where('user_id', $learner->id)
            ->where('project_id', $standardProject->id)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $data = array_fill(0, 12, 0);

        foreach ($sales as $sale) {
            $data[$sale->month - 1] = round($sale->amount_total, 2);
        }

        return $data;
    }

    public function bookSaleMonthlyDetails($year, $month): JsonResponse
    {
        $year = (int) $year;
        $month = (int) $month;

        if ($month < 1 || $month > 12) {
            return response()->json([]);
        }

        $learner = Auth::user();
        $standardProject = FrontendHelpers::getLearnerStandardProject($learner->id);

        if (! $standardProject) {
            return response()->json([]);
        }

        $sales = ProjectBookSale::select('project_book_sales.*')
            ->leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->where('project_books.project_id', $standardProject->id)
            ->where('project_books.user_id', $learner->id)
            ->whereYear('project_book_sales.date', $year)
            ->whereMonth('project_book_sales.date', $month)
            ->orderBy('project_book_sales.date', 'desc')
            ->get()
            ->map(function (ProjectBookSale $sale) {
                return [
                    'date' => $sale->date ? Carbon::parse($sale->date)->format('Y-m-d') : null,
                    'customer_name' => $sale->customer_name,
                    'quantity' => (int) $sale->quantity,
                    'price' => $sale->price_formatted,
                    'discount' => $sale->discount_formatted,
                    'amount' => $sale->total_amount_formatted,
                ];
            })
            ->values();

        return response()->json($sales);
    }

    public function saveForSaleBooks(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
        ]);
        $request->merge(['user_id' => Auth::user()->id]);

        UserBookForSale::updateOrCreate([
            'id' => $request->id,
        ], $request->except('id'));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book for sale saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function deleteForSaleBooks($id): RedirectResponse
    {
        UserBookForSale::find($id)->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book for sale deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function requestSelfPublishingPortal()
    {
        SelfPublishingPortalRequest::firstOrCreate(['user_id' => Auth::id()]);

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Request submitted to admin.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function project(): View
    {
        $projects = Project::where('user_id', Auth::user()->id)->get();

        return view('frontend.learner.self-publishing.project.index', compact('projects'));
    }

    public function saveProject(Request $request, ProjectService $projectService)
    {
        $request->validate([
            'name' => 'required|no_links',
        ]);

        $nextProjectNumber = DB::table('projects')
            ->select(DB::raw('CAST(identifier AS UNSIGNED) as identifier_numeric'))
            ->orderByRaw('identifier_numeric DESC')
            ->value('identifier') + 1;

        $request->merge([
            'user_id' => Auth::id(),
            'number' => $nextProjectNumber,
            'status' => 'active',
        ]);

        $projectService->saveProject($request);

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Project created.'),
            'alert_type' => 'success',
        ]);
    }

    public function showProject($project_id): View
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $project_id)->firstOrFail();

        return view('frontend.learner.self-publishing.project.show', compact('project'));
    }

    public function setStandardProject($project_id)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $project_id)->firstOrFail();

        $project = Project::where('user_id', Auth::id())->where('id', $project_id)->firstOrFail();

        // Set `is_standard` to 0 for all user's projects
        DB::table('projects')
            ->where('user_id', Auth::id())
            ->update(['is_standard' => 0]);

        // Set `is_standard` to 1 for the selected project
        $project->update(['is_standard' => 1]);

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Standard project updated.'),
            'alert_type' => 'success',
        ]);
    }

    public function orderService($projectId, $serviceId): View
    {
        $service = AppPublishingService::findOrFail($serviceId);

        return view('frontend.learner.self-publishing.project.service-order', compact('service', 'projectId'));
    }

    public function projectGraphicWork($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $covers = ProjectGraphicWork::cover()->where('project_id', $project_id)->get();
        $barCodes = ProjectGraphicWork::barcode()->where('project_id', $project_id)->get();
        $rewriteScripts = ProjectGraphicWork::rewriteScripts()->where('project_id', $project_id)->get();
        $trialPages = ProjectGraphicWork::trialPage()->where('project_id', $project_id)->get();
        $sampleBookPDFs = ProjectGraphicWork::sampleBookPdf()->where('project_id', $project_id)->get();
        $bookFormattingList = ProjectBookFormatting::where('project_id', $project_id)->get();
        $indesigns = ProjectGraphicWork::indesigns()->where('project_id', $project_id)->get();
        $printReadyList = ProjectGraphicWork::printReady()->where('project_id', $project_id)->get();

        return view('frontend.learner.self-publishing.project.graphic-work', compact('project', 'covers',
            'barCodes', 'rewriteScripts', 'trialPages', 'sampleBookPDFs', 'bookFormattingList', 'indesigns', 'printReadyList'));
    }

    public function projectRegistration($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $isbns = ProjectRegistration::isbns()->where('project_id', $project_id)->get();
        $centralDistributions = ProjectRegistration::centralDistributions()->where('project_id', $project_id)->get();
        $mentorBookBases = ProjectRegistration::mentorBookBase()->where('project_id', $project_id)->get();
        $uploadFilesToMentorBookBases = ProjectRegistration::uploadFilesToMentorBookBase()
            ->where('project_id', $project_id)->get();

        return view('frontend.learner.self-publishing.project.registration', compact('project', 'isbns',
            'centralDistributions', 'mentorBookBases', 'uploadFilesToMentorBookBases'));
    }

    public function marketing(): View
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        $marketingPlans = $standardProject ? MarketingPlan::with(['questions.answers' => function ($query) use ($standardProject) {
            $query->where('marketing_plan_question_answers.project_id', $standardProject->id);
        }])->get() : [];

        return view('frontend.learner.self-publishing.marketing', compact('marketingPlans'));
    }

    public function marketingDownload()
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(Auth::id());
        $marketingPlans = MarketingPlan::with(['questions.answers' => function ($query) use ($standardProject) {
            $query->where('marketing_plan_question_answers.project_id', $standardProject->id);
        }])->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML(view('frontend.pdf.marketing-plan', compact('marketingPlans')));

        return $pdf->download('Marketing Plan.pdf');
        // return $pdf->stream('marketing-plan.pdf');
    }

    public function projectMarketing($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $emailBookstores = ProjectMarketing::emailBookstores()->where('project_id', $project_id)->get();
        $emailLibraries = ProjectMarketing::emailLibraries()->where('project_id', $project_id)->get();
        $emailPresses = ProjectMarketing::emailPress()->where('project_id', $project_id)->get();
        $reviewCopiesSent = ProjectMarketing::reviewCopiesSent()->where('project_id', $project_id)->get();
        $setupOnlineStore = ProjectMarketing::setupOnlineStore()->where('project_id', $project_id)->get();
        $setupFacebook = ProjectMarketing::setupFacebook()->where('project_id', $project_id)->get();
        $advertisementFacebook = ProjectMarketing::advertisementFacebook()->where('project_id', $project_id)->get();
        $manuscriptSentToPrint = ProjectMarketing::manuscriptSentToPrint()->where('project_id', $project_id)->get();
        $culturalCouncils = ProjectMarketing::culturalCouncils()->where('project_id', $project_id)->get();
        $freeWords = ProjectMarketing::freeWords()->where('project_id', $project_id)->get();
        $agreementOnTimeRegistration = ProjectMarketing::agreementOnTimeRegistration()->where('project_id', $project_id)->get();
        $printEBooks = ProjectMarketing::printEbooks()->where('project_id', $project_id)->get();
        $sampleBookApproved = ProjectMarketing::sampleBookApproved()->where('project_id', $project_id)->get();
        $pdfPrintIsApproved = ProjectMarketing::pdfPrintIsApproved()->where('project_id', $project_id)->get();
        $numberOfAuthorBooks = ProjectMarketing::numberOfAuthorBooks()->where('project_id', $project_id)->get();
        $updateTheBookBase = ProjectMarketing::updateTheBookBase()->where('project_id', $project_id)->get();
        $ebookOrdered = ProjectMarketing::ebookOrdered()->where('project_id', $project_id)->get();
        $ebookReceived = ProjectMarketing::ebookReceived()->where('project_id', $project_id)->get();

        return view('frontend.learner.self-publishing.project.marketing', compact('project', 'emailBookstores',
            'emailLibraries', 'emailPresses', 'reviewCopiesSent', 'setupOnlineStore', 'setupFacebook', 'advertisementFacebook',
            'manuscriptSentToPrint', 'culturalCouncils', 'freeWords', 'agreementOnTimeRegistration', 'printEBooks',
            'sampleBookApproved', 'pdfPrintIsApproved', 'numberOfAuthorBooks', 'updateTheBookBase', 'ebookOrdered',
            'ebookReceived'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function projectMarketingPlan($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $marketingPlans = MarketingPlan::with(['questions.answers' => function ($query) use ($project_id) {
            $query->where('marketing_plan_question_answers.project_id', $project_id);
        }])->get();

        return view('frontend.learner.self-publishing.project.marketing-plan', compact('project', 'marketingPlans'));
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function saveMarketingPlanQA($project_id, Request $request): RedirectResponse
    {
        foreach ($request->arr as $input) {
            $answer = MarketingPlanQuestionAnswer::firstOrNew([
                'project_id' => $project_id,
                'question_id' => $input['main_question_id'],
            ]);
            $answer->main_answer = $input['main_answer'];
            $answer->sub_answer = isset($input['sub_answer'])
                ? json_encode($input['sub_answer'], JSON_UNESCAPED_UNICODE)
                : null;
            $answer->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Answer saved.'),
            'alert_type' => 'success',
        ]);
    }

    public function projectContract($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $contracts = Contract::where('project_id', $project_id)->paginate(10);

        return view('frontend.learner.self-publishing.project.contract', compact('project', 'contracts'));
    }

    public function projectInvoice($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $invoices = ProjectInvoice::where('project_id', $project_id)->get();

        return view('frontend.learner.self-publishing.project.invoice', compact('project', 'invoices'));
    }

    public function projectStorage($project_id): View
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $projectBook = $project->book;
        $projectCentralDistributions = $project->registrations()
            ->where([
                'field' => 'central-distribution',
                'in_storage' => 1,
            ])
            ->get();

        return view('frontend.learner.self-publishing.project.storage', compact('project', 'projectBook',
            'projectCentralDistributions'));
    }

    public function projectStorageDetails($project_id, $registration_id)
    {
        $project = FrontendHelpers::userProject(Auth::user()->id, $project_id);
        $projectBook = $project->book;
        $projectUserBook = ProjectRegistration::find($registration_id);

        $totalBookSold = 0;
        $totalBookSale = 0;
        $currentYear = Carbon::now()->format('Y');
        $years = [];
        $quantitySoldList = [];
        $turnedOverList = [];

        if ($projectBook && $projectBook->sales) {
            $totalBookSold = $projectBook->sales()->sum('quantity');
            $totalBookSale = $projectBook->sales()->sum('amount');

            $years = range($currentYear, $currentYear - 1);
        }

        $inventorySalesGroup = StorageSale::where('project_book_id', $projectUserBook->id)
            ->where('type', 'like', 'inventory_%')
            ->select('type', DB::raw('SUM(value) as total_sales'))
            ->groupBy('type')
            ->get();

        $inventoryPhysicalItems = 0;
        $inventoryDelivered = 0;
        $inventoryReturns = 0;

        foreach ($inventorySalesGroup as $sale) {
            switch ($sale->type) {
                case 'inventory_physical_items':
                    $inventoryPhysicalItems = $sale->total_sales;
                    break;
                case 'inventory_delivered':
                    $inventoryDelivered = $sale->total_sales;
                    break;
                case 'inventory_returns':
                    $inventoryReturns = $sale->total_sales;
                    break;
                    // Add more cases as needed for other types
            }
        }

        $types = [
            'quantity-sold' => 'Quantity Sold',
            'turned-over' => 'Turned Over',
            'free' => 'Free',
            'commission' => 'Commission',
            'shredded' => 'Shredded',
            'defective' => 'Defective',
            'corrections' => 'Corrections',
            'counts' => 'Counts',
            'returns' => 'Returns',
        ];

        $yearlyData = array_map(function ($key, $name) use ($projectUserBook) {
            return [
                'name' => $name,
                'value' => $projectUserBook ? $this->storageSalesByType($projectUserBook->id, $key) : 0,
            ];
        }, array_keys($types), $types);

        $inventorySales = StorageSale::where('project_book_id', $projectUserBook->id)
            ->where('type', 'like', 'inventory_%')->get();

        $inventorySalesGroup = StorageSale::where('project_book_id', $projectUserBook->id)
            ->where('type', 'like', 'inventory_%')
            ->select('type', DB::raw('SUM(value) as total_sales'))
            ->groupBy('type')
            ->get();

        $project_book_id = $projectUserBook->id;
        $categories = ['quantity-sold', 'turned-over', 'free', 'commission', 'shredded'];

        $categories = ['quantitySoldCount' => 'quantity-sold', 'turnedOverCount' => 'turned-over',
            'freeCount' => 'free', 'commissionCount' => 'commission', 'shreddedCount' => 'shredded',
            'defectiveCount' => 'defective', 'correctionsCount' => 'corrections', 'countsCount' => 'counts',
            'returnsCount' => 'returns'];

        $counts = array_map(function ($label) use ($project_book_id) {
            return $this->salesReportCounter($project_book_id, $label);
        }, $categories);

        extract($counts);

        return view('frontend.learner.self-publishing.project.storage-details', compact('project', 'projectBook',
            'projectUserBook', 'totalBookSold', 'totalBookSale', 'inventoryPhysicalItems', 'inventoryDelivered',
            'inventoryReturns', 'years', 'yearlyData', 'inventorySales', array_keys($categories)));
    }

    public function countFileCharacters(Request $request)
    {
        $compute = null;
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $compute = FrontendHelpers::countFileWords(0, $request);
        }

        return $compute;
    }

    public function uploadSelfPublishingManuscript($id, Request $request): RedirectResponse
    {
        $request->validate(['manuscript' => 'required']);

        $publishing = SelfPublishing::find($id);

        // $destinationPath = 'storage/self-publishing-manuscript/'; // upload path
        $destinationPath = 'Forfatterskolen_app/self-publishing-manuscript/';

        if ($publishing->project_id) {
            $destinationPath = 'Forfatterskolen_app/project/project-'.$publishing->project_id.'/self-publishing-manuscript/';
        }

        if ($request->hasFile('manuscript')) {

            $filesWithPath = '';
            $word_count = 0;
            $requestFilename = 'manuscript';

            foreach (\request()->file($requestFilename) as $k => $file) {
                $extension = pathinfo($_FILES[$requestFilename]['name'][$k], PATHINFO_EXTENSION);
                $original_filename = $file->getClientOriginalName();
                $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

                $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension); // rename document
                $expFileName = explode('/', $fileName);
                $dropboxFileName = end($expFileName);

                // Store the file in Dropbox
                $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

                // Full file path for reference
                $filesWithPath .= '/'.$destinationPath.$fileName.', ';

                // File path in Dropbox
                $filePath = $destinationPath.$dropboxFileName;

                // Download the file locally from Dropbox (temporarily)
                $tempFile = Storage::disk('dropbox')->get($filePath);
                $localPath = storage_path('app/temp/'.$dropboxFileName); // Define temporary local path

                file_put_contents($localPath, $tempFile); // Save it locally temporarily

                // Word counting logic based on the file extension
                if ($extension == 'pdf') {
                    $pdf = new \PdfToText($localPath);
                    $pdfContent = $pdf->Text;
                    $word_count += FrontendHelpers::get_num_of_words($pdfContent);
                } elseif ($extension == 'docx') {
                    $docObj = new \Docx2Text($localPath);
                    $docText = $docObj->convertToText();
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'doc') {
                    $docText = FrontendHelpers::readWord($localPath);
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'odt') {
                    $doc = odt2text($localPath);
                    $word_count += FrontendHelpers::get_num_of_words($doc);
                }

                // Delete the temporary local file after processing
                unlink($localPath);
            }

            /* foreach ($request->file('manuscript') as $k => $file) {
                $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $expFileName = explode('/', $fileName);
                $filePath = "/".$destinationPath.end($expFileName);
                $file->move($destinationPath, end($expFileName));

                $filesWithPath .= $filePath.", ";

                // count words
                if($extension == "pdf") :
                    $pdf  =  new \PdfToText( $destinationPath.end($expFileName) ) ;
                    $pdf_content = $pdf->Text;
                    $word_count += FrontendHelpers::get_num_of_words($pdf_content);
                elseif($extension == "docx") :
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText= $docObj->convertToText();
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                elseif($extension == "doc") :
                    $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                elseif($extension == "odt") :
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count += FrontendHelpers::get_num_of_words($doc);
                endif;
            } */

            $publishing->manuscript = $filesWithPath = trim($filesWithPath, ', ');
            $publishing->word_count = $word_count;

            if($publishing->editor_id) {
                $emailTemplate = AdminHelpers::emailTemplate('Manuscript Uploaded');
                $email_content = str_replace([
                    ':manuscript_from',
                    ':learner',
                ], [
                    "<em>" . $publishing->title . "</em>",
                    "<b>" . Auth::user()->full_name . "</b>",
                ], $emailTemplate->email_content);

                $editor = User::find($publishing->editor_id);
                $to = $editor->email;
                $emailData = [
                    'email_subject' => $emailTemplate->subject,
                    'email_message' => $email_content,
                    'from_name' => '',
                    'from_email' => $emailTemplate->from_email,
                    'attach_file' => null,
                ];
                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            }
        }
        $publishing->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
            'alert_type' => 'success',
        ]);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function uploadOtherServiceManuscript($id, $type, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->validate(['manuscript' => 'required']);

        if (in_array($type, [1, 2])) {
            $data = $type == 1 ? CopyEditingManuscript::find($id) : CorrectionManuscript::find($id);
            $request->merge(['type' => $type]);

            $folderName = $type == 1 ? 'copy-editing-manuscripts' : 'correction-manuscripts';
            $destinationPath = 'Forfatterskolen_app/'.($data->project_id ? 'project/project-'.$data->project_id.'/' : '')
                .$folderName.'/';

            $requestFilename = 'manuscript';
            $file = \request()->file($requestFilename);

            $extension = pathinfo($_FILES[$requestFilename]['name'], PATHINFO_EXTENSION);
            $original_filename = $file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension); // rename document
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            // Store the file in Dropbox
            $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

            // File path in Dropbox
            $filePath = $destinationPath.$dropboxFileName;
            $calculatedPrice = $projectService->calculateFileTextPrice($filePath, $type);

            // $file = $projectService->saveFile($data->project_id, $request);
            $data->file = '/'.$filePath;
            $data->payment_price = $calculatedPrice;
            $data->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag(trans('site.learner.upload-manuscript-success')),
            'alert_type' => 'success',
        ]);
    }

    public function downloadTimeRegisterInvoice($id): BinaryFileResponse
    {
        $timeRegister = TimeRegister::find($id);

        return response()->download($timeRegister->invoice_file);
    }

    public function profile(): View
    {
        // get course certificates based on users course taken
        $certificates = DB::table('course_certificates')
            ->leftJoin('courses', 'course_certificates.course_id', '=', 'courses.id')
            ->leftJoin('packages', 'packages.id', '=', 'course_certificates.package_id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->select('course_certificates.*', 'courses.title as course_title')
            ->where('courses.completed_date', '<=', Carbon::now())
            ->whereNotNull('courses.issue_date')
            ->whereNotNull('course_certificates.package_id')
            ->where('courses_taken.user_id', \Auth::user()->id)
            // ->whereNull('courses_taken.deleted_at') //remove this to not show deleted courses_taken
            ->groupBy('course_certificates.id')
            ->get();

        return view('frontend.learner.profile', compact('certificates'));
    }

    public function profileUpdate(ProfileUpdateRequest $request): RedirectResponse
    {
        if (! empty($request->new_password)) {
            if (Hash::check($request->old_password, Auth::user()->password)) {
                Auth::user()->password = bcrypt($request->new_password);
                \Session::forget('new_user_social');
            } else {
                return redirect()->back()->withErrors('Invalid old password.');
            }
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = substr(Auth::user()->profile_image, 1);
            if (Auth::user()->hasProfileImage && File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/profile-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            Auth::user()->profile_image = '/'.$destinationPath.$fileName;
        }

        Auth::user()->first_name = $request->first_name;
        Auth::user()->last_name = $request->last_name;
        Auth::user()->save();

        // User Address
        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->street = $request->street;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->phone = $request->phone;
        $address->save();

        $learner = Auth::user();

        if (! $learner->fiken_contact_id || $learner->fiken_contact_id == 'none'
            && $learner->activePaidCoursesTakenNotExpired()->count()) {
            CheckFikenContactCommand::updateFikenContactId($learner);
        }

        if ($learner->fiken_contact_id && $learner->fiken_contact_id != 'none' 
            && $learner->activePaidCoursesTakenNotExpired()->count()) {
            dispatch(new UpdateFikenContactDetailsJob($learner));
        }

        // User Social
        $social = UserSocial::firstOrNew(['user_id' => Auth::user()->id]);
        $social->facebook = $request->facebook;
        $social->instagram = $request->instagram;
        $social->save();

        return redirect()->back()->with('profile_success', 'Profile successfully updated.');
    }

    /**
     * Update the profile image of user
     */
    public function profileUpdatePhoto(Request $request): RedirectResponse
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = substr(Auth::user()->profile_image, 1);
            if (Auth::user()->hasProfileImage && File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/profile-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            Auth::user()->profile_image = '/'.$destinationPath.$fileName;
            Auth::user()->save();
        }

        return redirect()->back();
    }

    public function passwordUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required',
        ]);

        Auth::user()->password = bcrypt($request->password);
        Auth::user()->need_pass_update = 0;
        Auth::user()->save();

        return redirect()->back()->with(['passUpdated' => 1]);
    }

    public function terms(): View
    {
        return view('frontend.learner.terms');
    }

    public function lesson($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);

        $lesson_content = $lesson->lessonContent;

        if (! FrontendHelpers::checkIfLearnerHasAccessToLesson(Auth::user()->id, $course_id, $id)) {
            abort(404);
        }

        if ($request->exists('search_replay') && $lesson->id == 191) {
            $lesson_content = LessonContent::where('title', 'like', '%'.$request->search_replay.'%')
                ->get();
        }

        $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)
            ->whereIn('package_id', $course->allPackages->pluck('id')->toArray()) // $course->packages->pluck('id')
            ->first();

        if ($courseTaken->isDisabled || Auth::user()->isDisabled) {
            return redirect()->route('learner.course');
        }

        $lessons = $courseTaken->package->course->lessons;
        if ($courseTaken || FrontendHelpers::hasLessonAccess($courseTaken, $lesson)) {
            return view('frontend.learner.lesson_show', compact('lesson', 'course', 'courseTaken', 'lesson_content', 'lessons'));
        }

        return redirect()->route('learner.dashboard');
        // return abort('503');
    }

    /**
     * Download lesson as pdf file
     *
     * @return \Illuminate\Http\RedirectResponse | mixed
     */
    public function downloadLesson($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);

        if (! FrontendHelpers::checkIfLearnerHasAccessToLesson(Auth::user()->id, $course_id, $id)) {
            abort(404);
        }

        $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course->packages->pluck('id')->toArray())->first();

        // set a cookie to re-enable download button
        $cookie_name = '_lesson_dl';
        $cookie_value = 1;
        setcookie($cookie_name, $cookie_value, time() + 60, '/'); // 86400 = 1 day

        if ($courseTaken || FrontendHelpers::hasLessonAccess($courseTaken, $lesson)) {
            // replace the laravel-filemanager with the actual file path location
            $content = str_replace('/laravel-filemanager', public_path(), $lesson->content);
            $title = $lesson->title;
            $pdf = GlobalPdf::loadView('frontend.pdf.lesson', compact('content', 'title'));

            // set a cookie to re-enable download button
            $cookie_name = '_lesson_dl';
            $cookie_value = 1;
            setcookie($cookie_name, $cookie_value, time() + 600, '/'); // 86400 = 1 day

            return $pdf->download($lesson->title.'.pdf');
        }

        return redirect()->back();
    }

    public function uploadManuscript($id, Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $coursesTaken_ids = Auth::user()->coursesTaken->pluck('id')->toArray();
        $extensions = ['pdf', 'docx', 'odt'];

        if ($courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count && in_array($courseTaken->id, $coursesTaken_ids)) {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                if (Auth::user()->can('participateCourse', $courseTaken) &&
                    ! $courseTaken->hasEnded
                ) {
                    $time = time();
                    $destinationPath = 'storage/manuscripts/'; // upload path
                    $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); // getting document extension
                    $fileName = $time.'.'.$extension; // rename document
                    $request->file->move($destinationPath, $fileName);

                    if (! in_array($extension, $extensions)) {
                        return redirect()->back();
                    }

                    // count words
                    if ($extension == 'pdf') {
                        $pdf = new \PdfToText($destinationPath.$fileName);
                        $pdf_content = $pdf->Text;
                        $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                    } elseif ($extension == 'docx') {
                        $docObj = new \Docx2Text($destinationPath.$fileName);
                        $docText = $docObj->convertToText();
                        $word_count = FrontendHelpers::get_num_of_words($docText);
                    } elseif ($extension == 'odt') {
                        $doc = odt2text($destinationPath.$fileName);
                        $word_count = FrontendHelpers::get_num_of_words($doc);
                    }

                    Manuscript::create([
                        'coursetaken_id' => $courseTaken->id,
                        'filename' => '/'.$destinationPath.$fileName,
                        'word_count' => $word_count,
                    ]);
                    Log::create([
                        'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for course '.$courseTaken->package->course->title,
                    ]);
                    // Admin notification
                    $message = Auth::user()->full_name.' submitted a manuscript for course '.$courseTaken->package->course->title;
                    // mail('post@forfatterskolen.no', 'New manuscript submitted for course', $message);
                    /*AdminHelpers::send_email('New manuscript submitted for course',
                        'post@forfatterskolen.no','post@forfatterskolen.no', $message);*/
                    $to = 'post@forfatterskolen.no'; //
                    $emailData = [
                        'email_subject' => 'New manuscript submitted for course',
                        'email_message' => $message,
                        'from_name' => '',
                        'from_email' => 'post@forfatterskolen.no',
                        'attach_file' => null,
                    ];
                    \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                } else {
                    return abort('503');
                }
            }
        }

        return redirect()->back()->with('success', true);
    }

    public function manuscriptShow($id)
    {
        $manuscript = Manuscript::findOrFail($id);
        if (Auth::user()->id == $manuscript->courseTaken->user_id) {
            return view('frontend.learner.manuscriptShow', compact('manuscript'));
        } else {
            return abort('503');
        }
    }

    public function shopManuscriptPostComment($id, Request $request)
    {
        $shopManuscriptsTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        if (! empty($request->comment) && $shopManuscriptsTaken->is_active) {
            $ShopManuscriptComment = new ShopManuscriptComment;
            $ShopManuscriptComment->shop_manuscript_taken_id = $shopManuscriptsTaken->id;
            $ShopManuscriptComment->user_id = Auth::user()->id;
            $ShopManuscriptComment->comment = $request->comment;
            $ShopManuscriptComment->save();

            // send email to head editor. change - head editor is moved from settings to user table.
            $headEditor = User::where('head_editor', 1)->first();
            $editor = user::where('id', $shopManuscriptsTaken->feedback_user_id)->first();
            // $headEditor = Settings::headEditor();

            $user = Auth::user();
            $emailTemplate = AdminHelpers::emailTemplate('Shop Manuscript Comment');
            $link = route('shop_manuscript_taken', [$user->id, $id]);
            $search_string = [
                ':firstname',
                ':link',
            ];
            $replace_string = [
                $user->first_name,
                "<a href='".$link."'>".$link.'</a>',
            ];
            $email_content = str_replace($search_string, $replace_string, $emailTemplate->email_content);

            if ($headEditor) {
                AdminHelpers::queue_mail($headEditor->email, $emailTemplate->subject, $email_content, $emailTemplate->from_email);
            }
            /*if($editor){
                AdminHelpers::queue_mail($editor->email, $emailTemplate->subject, $email_content, $emailTemplate->from_email);
            }*/

            return redirect()->back();
        } else {
            return abort('503');
        }
    }

    public function search(Request $request): View
    {
        $courses = Auth::user()->coursesTaken()->whereHas('package', function ($query) use ($request) {
            $query->whereHas('course', function ($query) use ($request) {
                $query->where('title', 'LIKE', '%'.$request->search.'%');
            });
        })->get();

        $assignments = Auth::user()->coursesTaken()->whereHas('package', function ($query) use ($request) {
            $query->whereHas('course', function ($query) use ($request) {
                $query->whereHas('assignments', function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%'.$request->search.'%');
                });
            });
        })->get();

        $webinars = Auth::user()->coursesTaken()->whereHas('package', function ($query) use ($request) {
            $query->whereHas('course', function ($query) use ($request) {
                $query->whereHas('webinars', function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%'.$request->search.'%');
                });
            });
        })->get();

        $workshops = Auth::user()->workshopsTaken()->whereHas('workshop', function ($query) use ($request) {
            $query->where('title', 'LIKE', '%'.$request->search.'%');
        })->get();

        return view('frontend.learner.search', compact('courses', 'assignments', 'webinars', 'workshops'));
    }

    /**
     * Renew specific course
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function courseRenew(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $courseTaken = CoursesTaken::find($request->course_id);
        if ($courseTaken) {
            $user = User::find($courseTaken->user_id);
            $package = Package::findOrFail($courseTaken->package_id);
            $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
            $price = (int) 1490 * 100;
            $product_ID = $package->full_price_product;
            $send_to = $user->email;
            $dueDate = date('Y-m-d');

            $payment_mode = $paymentMode->mode;
            if ($payment_mode == 'Faktura') {
                $payment_mode = 'Bankoverføring';
            }

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
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);

            $courseTaken->sent_renew_email = 0;
            $courseTaken->end_date = Carbon::now()->addYear(1);
            $courseTaken->save();

            // Email to support
            // mail('support@forfatterskolen.no', 'Course Renewed', Auth::user()->first_name . ' has renewed the course ' . $package->course->title);
            /*AdminHelpers::send_email('Course Renewed',
                'post@forfatterskolen.no', 'support@forfatterskolen.no',
                Auth::user()->first_name . ' has renewed the course ' . $package->course->title);*/
            $to = 'support@forfatterskolen.no'; //
            $emailData = [
                'email_subject' => 'Course Renewed',
                'email_message' => Auth::user()->first_name.' has renewed the course '.$package->course->title,
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            // Send course email
            $actionText = 'Mine Kurs';
            $actionUrl = 'http://www.forfatterskolen.no/account/course';
            $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_content = $package->course->email;
            // mail($send_to, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
            /*AdminHelpers::send_email($package->course->title, 'post@forfatterskolen.no', $send_to,
                view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));*/
            $to = $send_to; //
            $emailData = [
                'email_subject' => $package->course->title,
                'email_message' => view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content'))->render(),
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            if ($paymentMode->mode == 'Paypal') {
                $paypalForm = '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                    <input type="hidden" name="currency_code" value="NOK">
                    <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                    <input type="hidden" name="item_name" value="Course Order Invoice">
                    <input type="hidden" name="amount" value="'.($price / 100).'">
                    <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                    <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
                </form>
                <script>document.getElementById("paypal_form").submit();</script>';

                return new Response($paypalForm);
            }

            return redirect(route('front.shop.thankyou'));
        }

        return redirect()->back();
    }

    public function courseRenewAllDisabled($course_id): RedirectResponse
    {
        $courseTaken = CoursesTaken::find($course_id);

        if ($courseTaken) {
            $user = Auth::user();
            $package = Package::findOrFail($courseTaken->package_id);
            $paymentMode = PaymentMode::findOrFail(3); // hardcoded faktura payment
            $payment_mode = 'Bankoverføring';
            $price = (int) 1490 * 100;
            $product_ID = $package->full_price_product;
            $send_to = $user->email;
            $dueDate = date('Y-m-d');

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
                'comment' => $comment, 'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);

            // update all the started at of each courses taken
            // Auth::user()->coursesTaken()->update(['started_at' => Carbon::now()]); -- original code

            foreach (Auth::user()->coursesTaken as $coursesTaken) {
                // check if course taken have set end date and add one year to it
                if ($coursesTaken->end_date) {
                    $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                    $coursesTaken->end_date = $addYear;
                }

                $coursesTaken->started_at = Carbon::now();
                $coursesTaken->save();
            }

            // add to automation
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);

            // Email to support
            // mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
            /*AdminHelpers::send_email('All Courses Renewed',
                'post@forfatterskolen.no', 'support@forfatterskolen.no',
                Auth::user()->first_name . ' has renewed all the courses');*/
            $to = 'support@forfatterskolen.no'; //
            $emailData = [
                'email_subject' => 'All Courses Renewed',
                'email_message' => Auth::user()->first_name.' has renewed all the courses',
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            return redirect(route('front.shop.thankyou'));
        }

        return redirect()->back();
    }

    /**
     * Set value of auto renew courses field
     */
    public function setAutoRenewCourses(Request $request): RedirectResponse
    {
        $user = User::find(Auth::user()->id);
        $user->auto_renew_courses = $request->auto_renew;
        $user->save();

        if (! $request->auto_renew) {
            return redirect()->back();
        }

        // check if webinar-pakke is already expired and renew it

        $monthDate = \Carbon\Carbon::now()->format('Y-m-d');
        // get courses taken by end date
        $coursesTaken = Auth::user()->coursesTaken()->whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })->whereNotNull('end_date')->where('end_date', '<=', $monthDate)->get();

        // get courses taken by started at field
        $coursesTakenByStartDate = Auth::user()->coursesTaken()->whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })
            ->whereNotNull('started_at')
            ->whereNull('end_date')
            ->whereDate('started_at', $monthDate)
            ->get();

        // webinar-pakke is expired
        $user_name = Auth::user()->first_name;
        if (count($coursesTaken)) {
            $user_email = Auth::user()->email;
            $automation_id = 73;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate)->all();
        foreach ($coursesTaken as $courseTaken) {
            $user = $courseTaken->user;
            $package = Package::findOrFail($courseTaken->package_id);
            $payment_mode = 'Bankoverføring';
            $price = (int) 1490 * 100;
            $product_ID = $package->full_price_product;
            $send_to = $user->email;
            $end_date = $courseTaken->end_date ? $courseTaken->end_date : date('Y-m-d');
            // add 10 days from today
            // $dueDate        = date('Y-m-d', strtotime(date("Y-m-d") . " +10 days"));
            $dueDate = date('Y-m-d', strtotime($end_date));

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
            $extraText = ' and other course.';
            $courseCounter = 0;
            foreach ($courseTaken->user->coursesTaken as $coursesTaken) {
                $notExpiredCourses = $courseTaken->user->coursesTakenNotExpired()->pluck('id')->toArray();
                // check if there's other course that's not expired yet and update it
                if (! in_array($coursesTaken->id, $notExpiredCourses)) {
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
                        $coursesTaken->renewed_at = Carbon::now();
                    }

                    // $coursesTaken->started_at = Carbon::now();
                    $coursesTaken->save();
                    $courseCounter++;
                }
            }

            // create order record
            $newOrder['user_id'] = $courseTaken->user->id;
            $newOrder['item_id'] = $package->course_id;
            $newOrder['type'] = Order::COURSE_TYPE;
            $newOrder['package_id'] = $package->id;
            $newOrder['plan_id'] = 8; // Full payment
            $newOrder['price'] = $price / 100;
            $newOrder['discount'] = 0;
            $newOrder['payment_mode_id'] = 3; // Faktura
            $newOrder['is_processed'] = 1;
            $order = Order::create($newOrder);

            // Email to support
            $from = 'post@forfatterskolen.no';
            $to = 'support@forfatterskolen.no';
            $messageText = $user_name.' has renewed webinar-pakke';
            $message = $courseCounter > 1 ? $messageText.$extraText : $messageText;
            /*AdminHelpers::send_email('Webinar-pakke Course Renewed',
                $from, $to, $message);*/
            $emailData = [
                'email_subject' => 'Webinar-pakke Course Renewed',
                'email_message' => $message,
                'from_name' => '',
                'from_email' => $from,
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        }

        if ($request->auto_renew) {
            return redirect()->back()->with('success', 'You successfully renew!');
        }

        return redirect()->back();
    }

    /**
     * Renew all courses from the upgrade page
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function renewLearnerCourses(): RedirectResponse
    {
        $coursesTaken = Auth::user()->coursesTaken;
        foreach ($coursesTaken as $courseTaken) {
            $package = Package::find($courseTaken->package_id);
            if ($package && $package->course_id == 17) { // check if webinar pakke
                $course_id = $courseTaken->id;
                $webinarPakkeCourse = CoursesTaken::find($course_id);

                if ($webinarPakkeCourse) {
                    $expiredDate = $courseTaken->end_date;
                    $now = new \DateTime;
                    $checkDate = date('m/Y', strtotime($expiredDate));
                    $input = \DateTime::createFromFormat('m/Y', $checkDate);
                    $diff = $input->diff($now); // Returns DateInterval

                    $withinAMonth = $diff->y === 0 && $diff->m <= 1;

                    // check if this is really expired
                    if (! $withinAMonth) {
                        return redirect()->back();
                    }

                    $user = Auth::user();
                    $package = Package::findOrFail($webinarPakkeCourse->package_id);
                    $payment_mode = 'Bankoverføring';
                    $price = (int) 1490 * 100;
                    $product_ID = $package->full_price_product;
                    $send_to = $user->email;
                    $end_date = $courseTaken->end_date ? $courseTaken->end_date : date('Y-m-d');
                    // $dueDate        = date("Y-m-d", strtotime(date("Y-m-d", strtotime($end_date)) . " + 1 year"));
                    // $dueDate        = date("Y-m-d", strtotime($end_date));
                    $today = Carbon::today();
                    $parseEndDate = Carbon::parse($end_date);
                    $dueDate = $parseEndDate->addDays(14)->format('Y-m-d');

                    if ($parseEndDate->format('Y-m-d') < $today->format('Y-m-d')) {
                        $dueDate = $today->addDays(14)->format('Y-m-d');
                    }

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
                    foreach (Auth::user()->coursesTaken as $coursesTaken) {
                        $formerCourse = Auth::user()->coursesTakenOld()->pluck('id')->toArray();
                        // check if course is not former course
                        if (! in_array($coursesTaken->id, $formerCourse)) {
                            // check if course taken have set end date and add one year to it
                            if ($coursesTaken->end_date) {
                                $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                                $coursesTaken->end_date = $addYear;
                            }

                            $coursesTaken->renewed_at = Carbon::now();
                            // $coursesTaken->started_at = Carbon::now();
                            $coursesTaken->save();
                        }
                    }

                    // create order record
                    $newOrder['user_id'] = $courseTaken->user->id;
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
                    $user_email = Auth::user()->email;
                    $automation_id = 73;
                    $user_name = Auth::user()->first_name;

                    // disable the adding to automation, instead save to db
                    // AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

                    // add user that renew the course
                    UserRenewedCourse::firstOrCreate([
                        'user_id' => Auth::user()->id,
                        'course_id' => $package->course_id,
                    ]);

                    // Email to support
                    // mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
                    /*AdminHelpers::send_email('All Courses Renewed',
                        'post@forfatterskolen.no', 'support@forfatterskolen.no',
                        Auth::user()->first_name . ' has renewed all the courses');*/
                    $to = 'support@forfatterskolen.no'; //
                    $emailData = [
                        'email_subject' => 'All Courses Renewed',
                        'email_message' => Auth::user()->first_name.' has renewed all the courses',
                        'from_name' => '',
                        'from_email' => 'post@forfatterskolen.no',
                        'attach_file' => null,
                    ];
                    \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

                    return redirect(route('front.shop.thankyou'));
                }
            }
        }

        return redirect()->route('learner.upgrade');
    }

    /**
     * Display the course upgrade page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getUpgradeCourse($courseTakenId, $package_id)
    {
        $courseTaken = CoursesTaken::where('id', $courseTakenId)
            ->where('user_id', Auth::user()->id)
            ->first();
        if (! $courseTaken) {
            return redirect()->route('learner.upgrade');
        }

        $currentPackage = Package::where('id', $package_id)->where('course_id', $courseTaken->package->course->id)->first();
        if (! $currentPackage) {
            return redirect()->route('learner.upgrade');
        }

        $currentUser = Auth::user();

        return view('frontend.learner.upgrade-course', compact('courseTaken', 'currentPackage', 'package_id', 'currentUser'));
    }

    public function validateUpgradeCourseForm($courseTakenId, Request $request, CourseService $courseService): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        $request->merge([
            'parent' => 'course-taken', 
            'parent_id' => $courseTakenId,
            'is_pay_later' => 0
        ]);

        return response()->json($courseService->generateSveaCheckout($request));
    }

    /**
     * Upgrade the course of the learner
     * this is using the place_order function on ShopController
     */
    public function upgradeCourse($courseTakenId, Request $request)
    {
        $hasPaidCourse = false;
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                $hasPaidCourse = true;
                break;
            }
        }

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $courseTaken = CoursesTaken::find($courseTakenId);
        $currentCourseType = $courseTaken->package->course_type;
        $add_to_automation = 0;

        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        $dueDate = date('Y-m-d', strtotime($package->issue_date ? $package->issue_date : date('Y-m-d')));
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        if ($payment_plan == 'Hele beløpet') {
            /*$price = $isBetweenFull && $package->full_payment_sale_price
                ? (int)$package->full_payment_sale_price*100
                : (int)$package->full_payment_upgrade_price*100;*/
            $price = $package->full_payment_upgrade_price * 100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenFull && $package->full_payment_sale_price
                    ? (int)$package->full_payment_sale_price*100
                    : (int)$package->full_payment_standard_upgrade_price*100;*/
                $price = $package->full_payment_standard_upgrade_price * 100;
            }

            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        } elseif ($payment_plan == '3 måneder') {
            /*$price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int)$package->months_3_sale_price*100
                : (int)$package->months_3_upgrade_price*100;*/
            $price = $package->months_3_upgrade_price * 100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths3 && $package->months_3_sale_price
                    ? (int)$package->months_3_sale_price*100
                    : (int)$package->months_3_standard_upgrade_price*100;*/
                $price = $package->months_3_standard_upgrade_price * 100;
            }

            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        } elseif ($payment_plan == '6 måneder') {
            /*$price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int)$package->months_6_sale_price*100
                : (int)$package->months_6_upgrade_price*100;*/
            $price = $package->months_6_upgrade_price * 100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths6 && $package->months_6_sale_price
                    ? (int)$package->months_6_sale_price*100
                    : (int)$package->months_6_standard_upgrade_price*100;*/
                $price = $package->months_6_standard_upgrade_price * 100;
            }

            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        } elseif ($payment_plan == '12 måneder') {
            /*$price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int)$package->months_12_sale_price*100
                : (int)$package->months_12_upgrade_price*100;*/
            $price = $package->months_12_upgrade_price * 100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths12 && $package->months_12_sale_price
                    ? (int)$package->months_12_sale_price*100
                    : (int)$package->months_12_standard_upgrade_price*100;*/
                $price = $package->months_12_standard_upgrade_price * 100;
            }

            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        }
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if ($payment_mode == 'Faktura') {
            $payment_mode = 'Bankoverføring';
        }

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $discount = 0;

        $course_id = $package->course->id;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon) {
                $discount = ((int) $discountCoupon->discount);
                $price = $price - ((int) $discount * 100);
            }

        }

        /*if( $hasPaidCourse && $package->course->type == 'Group' && $package->has_student_discount) {
            $groupDiscount = 1000;

            if ($groupDiscount > $discount) {
                $discount = $groupDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
        }

        if( $hasPaidCourse && $package->course->type == 'Single' && $package->has_student_discount) {

            $singleDiscount = 500;

            if ($singleDiscount > $discount) {
                $discount = $singleDiscount;
            }

            $comment .= ' - Discount: Kr '.number_format($discount, 2,',','.');
            $price = $price - ( (int)$discount*100 );
        }*/

        // check if the customer wants to split the invoice
        if (isset($request->split_invoice) && $request->split_invoice) {
            $division = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            for ($i = 1; $i <= $paymentPlan->division; $i++) { // loop based on the split count
                $issue_date = $package->issue_date ? $package->issue_date : date('Y-m-d');
                $dueDate = Carbon::parse($issue_date)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
                $invoice_fields = [
                    'user_id' => Auth::user()->id,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'netAmount' => $price,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $product_ID,
                    'email' => Auth::user()->email,
                    'telephone' => Auth::user()->address->phone,
                    'address' => Auth::user()->address->street,
                    'postalPlace' => Auth::user()->address->city,
                    'postalCode' => Auth::user()->address->zip,
                    'comment' => $comment,
                    'payment_mode' => $paymentMode->mode,
                    'index' => $i,
                ];

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields);
            }

        } else {
            // this is the original code without the split
            $invoice_fields = [
                'user_id' => Auth::user()->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $product_ID,
                'email' => Auth::user()->email,
                'telephone' => Auth::user()->address->phone,
                'address' => Auth::user()->address->street,
                'postalPlace' => Auth::user()->address->city,
                'postalCode' => Auth::user()->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);
        }

        $courseTaken->package_id = $package->id;
        $courseTaken->save();

        // Check for shop manuscripts
        if ($package->shop_manuscripts->count() > 0) {
            foreach ($package->shop_manuscripts as $shop_manuscript) {
                $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->save();
            }
        }

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }
            }
        }

        if ($package->course->id == 17) { // check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
        }

        // Email to support
        // mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);
        /*AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no',
            Auth::user()->first_name . ' has ordered the course ' . $package->course->title);*/
        $to = 'support@forfatterskolen.no'; //
        $emailData = [
            'email_subject' => 'New Course Order',
            'email_message' => Auth::user()->first_name.' has ordered the course '.$package->course->title,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = 'http://www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();
        $user_email = $user->email;
        $email_content = $package->course->email;
        // mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        /*AdminHelpers::send_email($package->course->title,
            'post@forfatterskolen.no', $user->email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));*/
        dispatch(new CourseOrderJob($user_email, $package->course->title, $email_content,
            'postmail@forfatterskolen.no', 'Forfatterskolen', null, 'courses-taken-upgrade',
            $courseTaken->id, $actionText, $actionUrl, $user, $package->id));

        if ($paymentMode->mode == 'Paypal') {
            $paypal = new Paypal;
            $response = $paypal->purchase([
                'amount' => ($price / 100),
                'transactionId' => $invoice->invoiceID,
                'currency' => 'NOK',
                'cancelUrl' => $paypal->getCancelUrl($invoice->invoiceID),
                'returnUrl' => $paypal->getReturnUrl($invoice->invoiceID, 'course'),
            ]);

            if ($response->isRedirect()) {
                $response->redirect();
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($response->getMessage()),
            ]);
            /*echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price/100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'?gateway=Paypal">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';
            return;*/
        }

        // check if vipps payment mode and the current user id is 4
        if ($paymentMode->mode == 'Vipps') {
            // $orderId = $invoice->invoice_number;
            $orderId = $invoice->fiken_invoice_id;
            $transactionText = $package->course->title;
            $vippsData = [
                'amount' => $price,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
                'fallbackUrl' => 'https://www.forfatterskolen.no/thankyou?page=vipps',
            ];

            return $this->vippsInitiatePayment($vippsData);
        }

        return redirect(route('front.shop.thankyou'));
    }

    /**
     * Display the upgrade page of manuscript
     *
     * @param  $shopManuscriptTakenId  ShopManuscriptsTaken
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getUpgradeManuscript($shopManuscriptTakenId)
    {
        /* $shopManuscriptTaken = ShopManuscriptsTaken::where('id',$shopManuscriptTakenId)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($shopManuscriptTaken && $shopManuscriptTaken->status == 'Not started') {
            $shopManuscriptId = $shopManuscriptTaken->shop_manuscript->id;
            $shopManuscriptUpgrades = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptId)->get();
            $currentUser = $this->currentUser();
            $shopManuscript = $shopManuscriptTaken->shop_manuscript;
            return view('frontend.learner.upgrade-manuscript',
                compact('shopManuscriptTaken', 'shopManuscriptUpgrades', 'currentUser', 'shopManuscript'));
        } */

        $shopManuscriptTaken = DB::table('shop_manuscripts_taken')
            ->leftJoin('shop_manuscripts', 'shop_manuscripts_taken.shop_manuscript_id', '=', 'shop_manuscripts.id')
            ->select('shop_manuscripts_taken.*', 'shop_manuscripts.title as manuscript_title')
            ->where('shop_manuscripts_taken.id', $shopManuscriptTakenId)
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($shopManuscriptTaken) {
            $is_active = $shopManuscriptTaken->is_active ?? false;
            $file = $shopManuscriptTaken->file ?? null;

            $feedbacks = DB::table('shop_manuscript_taken_feedbacks')
                ->where('shop_manuscript_taken_id', $shopManuscriptTakenId)
                ->get();

            $feedbackCount = $feedbacks->count();
            $approved = $feedbacks->first()->approved ?? 0;

            // Apply status logic
            $status = 'Not started'; // Default
            if (! $is_active) {
                $status = 'Not started';
            } elseif ($file && $feedbackCount > 0 && $approved == 1) {
                $status = 'Finished';
            } elseif ($file && $feedbackCount > 0 && $approved == 0) {
                $status = 'Pending';
            } elseif ($file && $feedbackCount == 0) {
                $status = 'Started';
            }

            // Attach status to the object (if needed for further use)
            $shopManuscriptTaken->status = $status;

            if ($status == 'Not started') {
                $excessPerWordAmount = FrontendHelpers::manuscriptExcessPerWordPrice();
                $shopManuscriptId = $shopManuscriptTaken->shop_manuscript_id;
                $shopManuscript = ShopManuscript::find($shopManuscriptId);
                $shopManuscriptUpgrades = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptId)->get()
                    ->each(function ($upgrade) use ($shopManuscript, $excessPerWordAmount) {
                        // check if the one being upgraded is Manuscript Start
                        if ($shopManuscript->id == 9 && $upgrade->upgrade_manuscript->id == 3) {
                            $upgrade->price = $upgrade->upgrade_manuscript->full_payment_price - $shopManuscript->full_payment_price;
                        } else {
                            $excessWords = $upgrade->upgrade_manuscript->max_words - $shopManuscript->max_words;
                            $excessWordAmount = $excessWords * $excessPerWordAmount;
                            $upgrade->price = $excessWordAmount;
                        }

                        return $upgrade;
                    });
                $currentUser = $this->currentUser();

                return view('frontend.learner.upgrade-manuscript',
                    compact('shopManuscriptTaken', 'shopManuscriptUpgrades', 'currentUser', 'shopManuscript'));
            }
        }

        return redirect()->route('learner.upgrade');
    }

    public function validateUpgradeManuscriptForm($manuscriptTakenId, Request $request, ShopManuscriptService $shopManuscriptService): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        $request->merge(['parent' => 'manuscript-taken', 'parent_id' => $manuscriptTakenId]);

        if ($request->additional > 0) {
            $request->merge([
                'is_pay_later' => true,
            ]);

            return response()->json($shopManuscriptService->processPayLaterOrder($request));
        }

        return response()->json($shopManuscriptService->generateSveaCheckout($request));
    }

    /**
     * Upgrade the learners manuscript
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function upgradeManuscript($shopManuscriptTakenId, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($shopManuscriptTakenId);
        $shopManuscriptUpgrade = ShopManuscriptUpgrade::find($request->manuscript_upgrade_id);
        if ($shopManuscriptTaken && $shopManuscriptUpgrade) {

            $oldManuscript = $shopManuscriptTaken->shop_manuscript->title;
            $shopManuscript = $shopManuscriptUpgrade->upgrade_manuscript;

            // change the manuscript plan/package
            $shopManuscriptTaken->shop_manuscript_id = $shopManuscriptUpgrade->upgrade_shop_manuscript_id;
            $shopManuscriptTaken->save();

            $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
            $paymentPlan = PaymentPlan::findOrFail(8); // default to full payment $request->payment_plan_id
            $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

            $comment = '(Manuskript: Oppgradering fra '.$oldManuscript.' til '.$shopManuscript->title.', ';
            $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
            $comment .= 'Betalingsplan: '.$payment_plan.')';

            $dueDate = date('Y-m-d');
            $dueDate = Carbon::parse($dueDate);
            $dueDate->addDays(14);
            $dueDate = date_format(date_create($dueDate), 'Y-m-d');
            $price = (int) $shopManuscriptUpgrade->price * 100;

            $invoice_fields = [
                'user_id' => Auth::user()->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $shopManuscript->fiken_product,
                'email' => Auth::user()->email,
                'telephone' => Auth::user()->address->phone,
                'address' => Auth::user()->address->street,
                'postalPlace' => Auth::user()->address->city,
                'postalCode' => Auth::user()->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);

            if ($paymentMode->mode == 'Paypal') {
                $paypal = new Paypal;
                $response = $paypal->purchase([
                    'amount' => ($price / 100),
                    'transactionId' => $invoice->invoiceID,
                    'currency' => 'NOK',
                    'cancelUrl' => $paypal->getCancelUrl($invoice->invoiceID),
                    'returnUrl' => $paypal->getReturnUrl($invoice->invoiceID, 'course'),
                ]);

                if ($response->isRedirect()) {
                    $response->redirect();
                }

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag($response->getMessage()),
                ]);
                /*echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price/100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
                echo '<script>document.getElementById("paypal_form").submit();</script>';
                return;*/
            }

            if ($paymentMode->mode == 'Vipps') {
                // $orderId = $invoice->invoice_number;
                $orderId = $invoice->fiken_invoice_id;
                $transactionText = $shopManuscript->title;
                $vippsData = [
                    'amount' => $price,
                    'orderId' => $orderId,
                    'transactionText' => $transactionText,
                    'fallbackUrl' => 'https://www.forfatterskolen.no/thankyou?page=vipps',
                ];

                return $this->vippsInitiatePayment($vippsData);
            }

            // return redirect(route('front.shop.thankyou'));
        }

        return redirect()->route('learner.upgrade');
    }

    /**
     * Display the Buy/Upgrade Assignment Page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getUpgradeAssignment($assignment_id)
    {
        $assignment = Assignment::find($assignment_id);
        if ($assignment) {
            return view('frontend.learner.upgrade-assignment', compact('assignment'));
        }

        return redirect()->route('learner.upgrade');
    }

    public function validateUpgradeAssignmentForm($assignment_id, Request $request, AssignmentService $assignmentService): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        return response()->json($assignmentService->generateSveaCheckout($request));
    }

    /**
     * Upgrade/Buy assignment
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function upgradeAssignment($assignment_id, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $assignment = Assignment::find($assignment_id);
        if ($assignment) {

            AssignmentAddon::create([
                'user_id' => Auth::user()->id,
                'assignment_id' => $assignment_id,
            ]);

            $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
            $paymentPlan = PaymentPlan::findOrFail(8);
            $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

            $comment = '(Assignment: '.$assignment->title.', ';
            $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
            $comment .= 'Betalingsplan: '.$payment_plan.')';

            $dueDate = date('Y-m-d');
            $dueDate = Carbon::parse($dueDate);
            $dueDate->addDays(14);
            $dueDate = date_format(date_create($dueDate), 'Y-m-d');
            $price = (int) $assignment->add_on_price * 100;

            $product_id = 287613124; // default product id

            $invoice_fields = [
                'user_id' => Auth::user()->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Assignment Add On',
                'productID' => $product_id,
                'email' => Auth::user()->email,
                'telephone' => Auth::user()->address->phone,
                'address' => Auth::user()->address->street,
                'postalPlace' => Auth::user()->address->city,
                'postalCode' => Auth::user()->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields);

            if ($paymentMode->mode == 'Paypal') {
                $paypalForm = '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                    <input type="hidden" name="currency_code" value="NOK">
                    <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                    <input type="hidden" name="item_name" value="Course Order Invoice">
                    <input type="hidden" name="amount" value="'.($price / 100).'">
                    <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                    <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
                </form>
                <script>document.getElementById("paypal_form").submit();</script>';

                return new Response($paypalForm);
            }

            return redirect(route('front.shop.thankyou'));

        }

        return redirect()->route('learner.upgrade');
    }

    /**
     * Replace the manuscript from particular assignment
     *
     * @param  $id  int assignment id
     */
    public function replaceAssignmentManuscript($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if ($request->hasFile('filename') && $request->file('filename')->isValid()) {
                $oldManuscript = $assignmentManuscript->filename;
                $time = time();
                $destinationPath = 'storage/assignment-manuscripts/'; // upload path
                $extensions = ['pdf', 'doc', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
                $actual_name = Auth::user()->id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

                $expFileName = explode('/', $fileName);

                $request->filename->move($destinationPath, end($expFileName));

                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->withInput()->with(
                        'manuscript_test_error', 'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT'
                    );
                }

                // count words
                $word_count = 0;
                if ($extension == 'pdf') {
                    $pdf = new \PdfToText($destinationPath.end($expFileName));
                    $pdf_content = $pdf->Text;
                    $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                } elseif ($extension == 'docx') {
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText = $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'doc') {
                    $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                    $word_count = FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'odt') {
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count = FrontendHelpers::get_num_of_words($doc);
                }

                // check if the assignment is for editor only and if it meets the max word
                // $assignmentManuscript->assignment->for_editor &&
                $assignment = $assignmentManuscript->assignment;
                $assignment_max_words = $assignment->allow_up_to > 0 ? $assignment->allow_up_to : $assignment->max_words;

                if ($word_count > $assignment_max_words && $assignment->check_max_words) {
                    return redirect()->back()->with(['errorMaxWord' => true, 'editorMaxWord' => $assignmentManuscript->assignment->max_words]);
                }

                $assignmentManuscript->filename = '/'.$fileName;
                $assignmentManuscript->words = $word_count;
                $assignmentManuscript->save();

                // delete the old file from the server
                if (File::exists(public_path($oldManuscript))) {
                    File::delete(public_path($oldManuscript));
                }

                // notify editor if manuscript is updated
                if ($assignmentManuscript->editor_id) {
                    $emailTemplate = AdminHelpers::emailTemplate('Manuscript Uploaded');
                    $email_content = str_replace([
                        ':manuscript_from',
                        ':learner',
                    ], [
                        "<em>" . $assignmentManuscript->assignment->title . "</em>",
                        "<b>" . Auth::user()->full_name . "</b>",
                    ], $emailTemplate->email_content);

                    $editor = User::find($assignmentManuscript->editor_id);
                    $to = $editor->email;
                    $emailData = [
                        'email_subject' => $emailTemplate->subject,
                        'email_message' => $email_content,
                        'from_name' => '',
                        'from_email' => $emailTemplate->from_email,
                        'attach_file' => null,
                    ];
                    \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                }

                // notify user
                $user_email = Auth::user()->email;
                $confirm_email['email_message'] = 'Oppgaven din er levert, har vi problemer med filen vil vi ta kontakt med med deg.';
                // Mail::to($user_email)->queue(new SendEmailMessageOnly($confirm_email));

                $emailTemplate = AdminHelpers::emailTemplate('Assignment Submitted');
                $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
                    Auth::user()->first_name, '');

                /* dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
                    $emailTemplate->from_email, null, null, 'assignment-manuscripts',
                    $assignmentManuscript->id)); */

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Assignment uploaded successfully.'),
                    'alert_type' => 'success',
                ]);
            }
        }

        return redirect()->back();
    }

    public function replaceAssignmentLetter($id, Request $request): RedirectResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if ($request->hasFile('filename') && $request->file('filename')->isValid()) {
                $oldManuscript = $assignmentManuscript->filename;

                $destinationPath = 'storage/letter-to-editor'; // upload path
                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
                $actual_name = time();
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                $expFileName = explode('/', $fileName);

                $extensions = ['doc', 'docx', 'odt', 'pdf'];
                if (! in_array($extension, $extensions)) {
                    return redirect()->back()->withInput()->with(
                        'manuscript_test_error', 'Invalid file format. Allowed formats are DOC, DOCX, ODT, PDF'
                    );
                }

                $request->filename->move($destinationPath, end($expFileName));

                // delete the old file from the server
                if (File::exists(public_path($oldManuscript))) {
                    File::delete(public_path($oldManuscript));
                }

                $assignmentManuscript->letter_to_editor = '/'.$fileName;
                $assignmentManuscript->save();
            }
        }

        return redirect()->back();
    }

    /**
     * Delete the manuscript from particular assignment
     *
     * @param  $id  int assignment id
     */
    public function deleteAssignmentManuscript($id): RedirectResponse
    {
        $manuscript = AssignmentManuscript::findOrFail($id);

        // delete the file from the server
        $oldManuscript = $manuscript->filename;
        if (File::exists(public_path($oldManuscript))) {
            File::delete(public_path($oldManuscript));
        }

        $manuscript->forceDelete();

        return redirect()->back();
    }

    /**
     * Replace the feedback
     */
    public function replaceFeedback($id, Request $request): RedirectResponse
    {
        $feedback = AssignmentFeedback::find($id);

        if ($feedback) {
            if ($request->hasFile('filename') && $request->file('filename')->isValid()) {
                $time = time();
                $destinationPath = 'storage/assignment-feedbacks/'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $request->filename->move($destinationPath, $fileName);

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $feedback->filenmae = '/'.$destinationPath.$fileName;

            }
        }

        return redirect()->back();
    }

    public function deleteFeedback($id): RedirectResponse
    {
        $feedback = AssignmentFeedback::findOrFail($id);
        $feedback->forceDelete();

        return redirect()->back();
    }

    /**
     * Download assignment group manuscript
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupManuscript($id)
    {
        $manuscript = AssignmentManuscript::find($id);
        if ($manuscript) {
            $filename = $manuscript->filename;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    /**
     * Download assignment feedback
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupFeedback($feedback_id)
    {
        $feedback = AssignmentFeedback::find($feedback_id);
        if ($feedback) {
            $files = explode(',', $feedback->filename);
            if (count($files) > 1) {
                $zipFileName = $feedback->assignment_group_learner->group->title.' Feedbacks.zip';
                $public_dir = public_path('storage');
                $zip = new \ZipArchive;

                // open zip file connection and create the zip
                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                foreach ($files as $feedFile) {
                    if (file_exists(public_path().'/'.trim($feedFile))) {

                        // get the correct filename
                        $expFileName = explode('/', $feedFile);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                    }
                }

                $zip->close(); // close zip connection

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }

            } else {
                return response()->download(public_path($files[0]));
            }
        }

        return redirect()->back();
    }

    /**
     * Download assignment feedback that don't have a group
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentNoGroupFeedback($feedback_id)
    {
        $feedback = AssignmentFeedbackNoGroup::find($feedback_id);
        if ($feedback) {
            $files = explode(',', $feedback->filename);
            if (count($files) > 1) {
                $zipFileName = $feedback->manuscript->assignment->title.' Feedbacks.zip';
                $public_dir = public_path('storage');
                $zip = new \ZipArchive;

                // open zip file connection and create the zip
                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                foreach ($files as $feedFile) {
                    if (file_exists(public_path().'/'.trim($feedFile))) {

                        // get the correct filename
                        $expFileName = explode('/', $feedFile);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                    }
                }

                $zip->close(); // close zip connection

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }

            } else {
                return response()->download(public_path($files[0]));
            }
        }

        return redirect()->back();
    }

    /**
     * Download all assignment group feedback
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupAllFeedback($group_id)
    {
        $group = AssignmentGroup::find($group_id);
        if ($group) {
            $user_id = Auth::user()->id;
            $assignment_group_learner_id = $group->learners()->where('user_id', $user_id)->first()->id;
            // get all feedback for the assignment group
            $feedbacks = AssignmentFeedback::where('assignment_group_learner_id', $assignment_group_learner_id)->get();
            $manuscript = $group->assignment->manuscripts->where('user_id', $user_id)->first();
            if ($feedbacks->count()) {
                $zipFileName = $group->title.' Feedbacks.zip';
                $public_dir = public_path('storage');
                $zip = new \ZipArchive;

                // open zip file connection and create the zip
                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                foreach ($feedbacks as $feedback) {
                    if (($manuscript->editor_id === $feedback->user_id && $manuscript->status) || $manuscript->editor_id !== $feedback->user_id) {
                        $files = explode(',', $feedback->filename);
                        // for multiple files in a feedback
                        if (count($files) > 1) {
                            foreach ($files as $feedFile) {
                                if (file_exists(public_path().'/'.trim($feedFile))) {

                                    // get the correct filename
                                    $expFileName = explode('/', $feedFile);
                                    $file = str_replace('\\', '/', public_path());

                                    // physical file location and name of the file
                                    $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                                }
                            }
                        } else {
                            if (file_exists(public_path().'/'.$feedback->filename)) {
                                // get the correct filename
                                $expFileName = explode('/', $feedback->filename);
                                $file = str_replace('\\', '/', public_path());

                                // physical file location and name of the file
                                $zip->addFile($file.$feedback->filename, end($expFileName));
                            }
                        }
                    }
                }

                $zip->close(); // close zip connection

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }
            }

            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Download all assignment group feedback
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupAllFeedbackOrig($group_id)
    {
        $group = AssignmentGroup::find($group_id);
        if ($group) {
            $learners = $group->learners;
            $assignment_group_learners = []; // array variable where learner group id is stored

            foreach ($learners as $learner) {
                $assignment_group_learners[] = $learner['id']; // store learner group id
            }
            // get all feedback for the assignment group
            $feedbacks = AssignmentFeedback::whereIn('assignment_group_learner_id', $assignment_group_learners)->get();
            if ($feedbacks->count()) {
                $zipFileName = $group->title.' Feedbacks.zip';
                $public_dir = public_path('storage');
                $zip = new \ZipArchive;

                // open zip file connection and create the zip
                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                foreach ($feedbacks as $feedback) {
                    $files = explode(',', $feedback->filename);
                    // for multiple files in a feedback
                    if (count($files) > 1) {
                        foreach ($files as $feedFile) {
                            if (file_exists(public_path().'/'.trim($feedFile))) {

                                // get the correct filename
                                $expFileName = explode('/', $feedFile);
                                $file = str_replace('\\', '/', public_path());

                                // physical file location and name of the file
                                $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                            }
                        }
                    } else {
                        if (file_exists(public_path().'/'.$feedback->filename)) {
                            // get the correct filename
                            $expFileName = explode('/', $feedback->filename);
                            $file = str_replace('\\', '/', public_path());

                            // physical file location and name of the file
                            $zip->addFile($file.$feedback->filename, end($expFileName));
                        }
                    }
                }

                $zip->close(); // close zip connection

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }
            }

            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Display or create word written by logged in learner
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function wordWritten(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            Auth::user()->wordWritten()->create($data); // use the relationship to insert new record

            return redirect()->back();
        }
        $words = Auth::user()->wordWritten()->paginate(15);

        return view('frontend.learner.word-written', compact('words'));
    }

    /**
     * Display or create word written goal by logged in user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function wordWrittenGoals(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            Auth::user()->wordWrittenGoal()->create($data);

            return redirect()->back();
        }
        $wordsGoal = Auth::user()->wordWrittenGoal()->paginate(15);

        return view('frontend.learner.word-written-goals', compact('wordsGoal'));
    }

    /**
     * Edit the goal
     */
    public function wordWrittenGoalsUpdate($id, Request $request): RedirectResponse
    {
        if ($goal = WordWrittenGoal::find($id)) {
            $data = $request->except('_token');
            $goal->update($data);

            return redirect()->back();
        }

        return redirect()->route('learner.word-written-goals');
    }

    /**
     * Delete a goal
     */
    public function wordWrittenGoalsDelete($id): RedirectResponse
    {
        if ($goal = WordWrittenGoal::find($id)) {
            $goal->forceDelete();

            return redirect()->back();
        }

        return redirect()->route('learner.word-written-goals');
    }

    /**
     * Get the statistics
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

    /**
     * Download the document from a lesson
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadLessonDocument($lessonId)
    {
        $document = LessonDocuments::find($lessonId);
        if ($document) {
            $filename = $document->document;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id): JsonResponse
    {
        if ($notification = Notification::find($id)) {
            $notification->is_read = 1;
            $notification->save();

            return response()->json(['success' => 'Notification marked as read.'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    /**
     * Delete a notification
     */
    public function deleteNotification($id): JsonResponse
    {
        if ($notification = Notification::find($id)) {
            $notification->forceDelete();

            return response()->json(['success' => 'Notification deleted successfully.'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    public function addCoachingSession(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $course_taken_id = $data['course_taken_id'];

        if ($courseTaken = CoursesTaken::find($course_taken_id)) {
            /* $suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            } */

            $extensions = ['docx'];
            $file = null;

            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $destinationPath = 'storage/coaching-timer-manuscripts/'; // upload path

                $time = time();
                $fileName = $time.'.'.$extension; // $original_filename; // rename document0
                $file = $destinationPath.$fileName;
                $request->manuscript->move($destinationPath, $fileName);
            }

            CoachingTimerManuscript::create([
                'user_id' => Auth::user()->id,
                'file' => $file,
                'plan_type' => $data['plan_type'],
                //'suggested_date' => json_encode($suggested_dates),
            ]);

            CoachingTimerTaken::create([
                'user_id' => Auth::user()->id,
                'course_taken_id' => $course_taken_id,
            ]);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Coaching Time added.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
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

    public function downloadCourseCertificate($course_id)
    {
        $certificate = CourseCertificate::findOrFail($course_id);
        $course = $certificate->course;

        $courseLearner = Auth::user()->coursesTaken()->withTrashed()->whereIn('package_id', $course->packages()->pluck('id'))
            ->firstOrFail();

        $issueDate = Carbon::parse($course->type === 'Single' ? Carbon::parse($courseLearner->started_at)->addDays(80) : $course->issue_date);
        $template = str_replace([
            '{LEARNERNAME}',
            '{COURSENAME}',
            '{COMPLETEDDATE}',
            '{ISSUEDDATE}',
        ],
            [
                Auth::user()->full_name,
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

    /**
     * Download file
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadOtherServiceDoc($service_id, $service_type)
    {
        if ($service_type == 1 || $service_type == 2) {
            $filename = '';
            if ($service_type == 1 && $copyEditing = CopyEditingManuscript::find($service_id)) {
                $filename = $copyEditing->file;
            }

            if ($service_type == 2 && $correction = CorrectionManuscript::find($service_id)) {
                $filename = $correction->file;
            }

            return response()->download(public_path($filename));
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Download the feedback for other service
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadOtherServiceFeedback($feedback_id)
    {
        if ($feedback = OtherServiceFeedback::find($feedback_id)) {
            $filename = $feedback->manuscript;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    /**
     * Update the help with field of coaching timer
     */
    public function updateHelpWith($id, Request $request): RedirectResponse
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $coachingTimer->help_with = $request->help_with;
            $coachingTimer->save();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Skriv litt her om hva du vil ha hjelp til saved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    public function setCoachingStatus($id, Request $request): RedirectResponse
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $coachingTimer->status = $request->status;
            $coachingTimer->save();
        }

        return redirect()->back();
    }

    /**
     * List all user emails
     */
    public function listEmails(): JsonResponse
    {
        $user = Auth::user();
        $data['primary'] = $user;
        $data['secondary'] = UserEmail::where('user_id', $user->id)->get();

        return response()->json($data);
    }

    /**
     * Send email confirmation to check if user owns the inputted email
     */
    public function sendEmailConfirmation(Request $request): JsonResponse
    {

        $request->validate([
            'email' => 'required|email|unique:users|unique:user_emails',
        ]);

        $email_data = $request->all();
        $email_data['token'] = md5(microtime());
        $email_data['user_id'] = Auth::user()->id;

        $saveData['email'] = $email_data['email'];
        $saveData['user_id'] = Auth::user()->id;
        $saveEmail = EmailConfirmation::firstOrNew($saveData);
        $saveEmail->token = $email_data['token'];

        if (! $saveEmail->save()) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        $user = Auth::user();
        $user_email = $user->email;

        /*AdminHelpers::send_email('Email Confirmation',
            'post@forfatterskolen.no', $email_data['email'], view('emails.email_confirmation', compact('email_data')));*/
        $buttonStyle = 'text-decoration: none; color: #fff; background: #e83945; border-color: #e83945;'.
                    'padding-right: 1.1rem; padding-left: 1.1rem; padding-top: 0.5rem; padding-bottom: 0.5rem;'.
                    '-webkit-text-size-adjust: none;line-height: 1.5;border-radius: .2rem;margin-right: 10px';
        $emailTemplate = AdminHelpers::emailTemplate('Confirm Additional Email');
        $emailContent = str_replace([
            ':firstname',
            ':email',
            ':button',
            ':end_button',
        ], [
            $user->first_name,
            $user->email,
            '<a href="'.route('front.email-confirmation', $email_data['token']).'" style="'.$buttonStyle.'">',
            '</a>',

        ], $emailTemplate->email_content);

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, 'learner', $user->id));

        return response()->json(['success' => 'Email Confirmation Sent.'], 200);
    }

    /**
     * Set Primary Email
     */
    public function setPrimaryEmail(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user = Auth::user();
        $user_emails = UserEmail::find($request->id);
        $primary = $user_emails->email;
        $secondary = $user->email;
        if (! $user->update(['email' => $primary])) {
            DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        if (! $user_emails->update(['email' => $secondary])) {
            DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        DB::commit();

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

        return response()->json(['success' => 'Secondary email set as primary', 'primary_email' => $primary], 200);
    }

    /**
     * Remove a secondary email
     */
    public function removeSecondaryEmail(Request $request): JsonResponse
    {
        if (! UserEmail::destroy($request->id)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Secondary email deleted'], 200);
    }

    public static function dashboardCalendar()
    {
        $events = [];
        $today = Carbon::today();
        foreach (Auth::user()->coursesTaken as $courseTaken) {
            // Course lessons
            foreach ($courseTaken->package->course->lessons as $lesson) {
                $availability = strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)) * 1000;
                $newAvailability = date('Y-m-d', strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)));

                if (Carbon::parse($newAvailability)->gte($today)) {
                    $events[] = [
                        'id' => $lesson->course->id,
                        'title' => 'Leksjon: '.$lesson->title.' from '.$lesson->course->title,
                        'class' => 'event-important',
                        'start' => $newAvailability, // $availability,
                        'end' => $newAvailability, // $availability,
                        'color' => '#d95e66',
                    ];
                }
            }

            // Course webinars
            foreach ($courseTaken->package->course->webinars as $webinar) {
                $start = date('Y-m-d', strtotime($webinar->start_date));
                $end = date('Y-m-d', strtotime($webinar->start_date));

                if (Carbon::parse($start)->gte($today)) {
                    $events[] = [
                        'id' => $webinar->course->id,
                        'title' => 'Webinar: '.$webinar->title.' from '.$webinar->course->title,
                        'class' => 'event-warning',
                        'start' => $start, // strtotime($webinar->start_date) * 1000,
                        'end' => $end, // strtotime($webinar->start_date) * 1000,
                        'color' => '#ff9c00',
                    ];
                }
            }

            // manuscripts
            foreach ($courseTaken->manuscripts as $manuscript) {
                $start = date('Y-m-d', strtotime($manuscript->expected_finish));
                $end = date('Y-m-d', strtotime($manuscript->expected_finish));

                if (Carbon::parse($start)->gte($today)) {
                    $events[] = [
                        'id' => $courseTaken->package->course->id,
                        'title' => 'Manus: '.basename($manuscript->filename).' from '.$courseTaken->package->course->title,
                        'class' => 'event-info',
                        'start' => $start, // strtotime($manuscript->expected_finish) * 1000,
                        'end' => $end, // strtotime($manuscript->expected_finish) * 1000,
                        'color' => '#29b5f5',
                    ];
                }
            }

            // assignments
            foreach ($courseTaken->package->course->assignments as $assignment) {
                $start = date('Y-m-d', strtotime($assignment->submission_date));
                $end = date('Y-m-d', strtotime($assignment->submission_date));

                if (Carbon::parse($start)->gte($today)) {
                    $events[] = [
                        'id' => $assignment->course->id,
                        'title' => 'Oppgaver: '.$assignment->title.' from '.$assignment->course->title,
                        'class' => 'event-success-new',
                        'start' => $start, // strtotime($assignment->submission_date) * 1000,
                        'end' => $end, // strtotime($assignment->submission_date) * 1000,
                        'color' => '#44af5e',
                    ];
                }
            }

            // get the calendar notes created by admin for certain course only
            foreach ($courseTaken->package->course->notes as $note) {
                $start = date('Y-m-d', strtotime($note->from_date));
                $end = date('Y-m-d', strtotime($note->to_date));

                if (Carbon::parse($start)->gte($today)) {
                    $events[] = [
                        'id' => $note->id,
                        'title' => $note->note,
                        'class' => 'event-inverse',
                        'start' => $start, // strtotime($note->date) * 1000,
                        'end' => $end, // strtotime($note->date) * 1000,
                        'color' => '#1b1b1b', // for full calendar
                    ];
                }
            }

        }

        return $events;
    }

    public function dashboardAssignment()
    {
        $assignments = [];
        $coursesTaken = Auth::user()->coursesTaken;
        $addOns = AssignmentAddon::where('user_id', \Auth::user()->id)->pluck('assignment_id')->toArray();

        foreach ($coursesTaken as $course) {
            foreach ($course->package->course->activeAssignments as $assignment) {
                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $course->package->id;
                // check if the assignment is allowed on the learners package or there's no set package allowed
                if ((! is_null($allowed_package) && in_array($package_id, $allowed_package)) || is_null($allowed_package) || in_array($assignment->id, $addOns)) {
                    // $assignments[] = $assignment;

                    if (! AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                        if (\Carbon\Carbon::parse($course->started_at)->addDays((int) $assignment->submission_date)
                            ->gt(Carbon::now())) {
                            $assignments[] = $assignment;
                        }
                    } else {
                        if (\Carbon\Carbon::parse($assignment->submission_date)->gt(Carbon::now())) {
                            $assignments[] = $assignment;
                        }
                    }
                }
            }
        }

        $userAssignments = Auth::user()->activeAssignments;
        foreach ($userAssignments as $assignment) {
            if (\Carbon\Carbon::parse((int) $assignment->submission_date)->gt(Carbon::now())) {
                $assignments[] = $assignment;
            }

        }

        return $assignments;
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoice($url)
    {
        $invoice = Invoice::find($url); // this is invoice id
        $exp_pdf = count(explode('.pdf', $invoice->pdf_url));

        // check if the pdf url is for version 2
        if (strpos($invoice->pdf_url, 'v2')) {

            $pdf_url = $invoice->pdf_url;

            if ($exp_pdf == 1) {
                $pdf_url = $pdf_url.'.pdf';
            }
            $expFile = explode('/', $pdf_url);

            $filename = 'fiken-invoice/'.end($expFile);

            // write file on the server
            $out = fopen($filename, 'wb');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FILE, $out);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            curl_setopt($ch, CURLOPT_URL, $pdf_url);
            curl_exec($ch);
            curl_close($ch);
            fclose($out);

            return response()->download($filename);

        }

        return $this->downloadInvoiceV1($url);
    }

    /**
     * Get the invoice pdf from the url with login credentials
     */
    public function downloadInvoiceV1($url): BinaryFileResponse
    {
        $invoice = Invoice::find($url); // this is invoice id
        $exp_pdf = count(explode('.pdf', $invoice->pdf_url));
        $pdf_url = str_replace('https://fiken.no/filer/', 'https://fiken.no/api/v1/files/', $invoice->pdf_url);
        if ($exp_pdf == 1) {
            $pdf_url = $pdf_url.'.pdf';
        }
        $expFile = explode('/', $pdf_url);

        // $filename = 'fiken-invoice/'.end($expFile);
        $serverFilename = explode('.', end($expFile));
        $filename = AdminHelpers::checkFileName('fiken-invoice', $serverFilename[0], 'pdf');

        // write file on the server
        $out = fopen($filename, 'wb');

        // download the file from external link with login credentials
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $pdf_url);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_exec($ch);
        curl_close($ch);
        fclose($out);

        return response()->download($filename);
    }

    public function downloadCreditNote($invoice_id): BinaryFileResponse
    {
        $invoice = Invoice::find($invoice_id); // this is invoice id
        $pdf_url = $invoice->credit_note_url;

        $expFile = explode('/', $pdf_url);

        $filename = 'fiken-invoice/'.end($expFile);

        // write file on the server
        $out = fopen($filename, 'wb');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_URL, $pdf_url);
        curl_exec($ch);
        curl_close($ch);
        fclose($out);

        return response()->download($filename);
    }

    public function downloadInvoiceOrig($url)
    {
        $check_url = $url;
        $exp_url = explode('https://fiken.no/filer/', $check_url);

        $host = $check_url;
        // check if it contains https://fiken.no/filer/ then change the url
        if (count($exp_url) > 1) {
            $host = 'https://fiken.no/api/v1/files/'.end($exp_url);
        }

        $exp_link = explode('/', $host);
        $get_pdf_name = end($exp_link);
        $remove_ext = explode('.', $get_pdf_name);
        $pdf_name = $remove_ext[0];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.xcontest.org');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD,
            config('services.fiken.username').':'.config('services.fiken.password'));
        $result = curl_exec($ch);
        curl_close($ch);

        // to download the file on the browser
        header('Cache-Control: public');
        header('Content-type:application/pdf');
        header('Content-Disposition: attachment; filename="'.$pdf_name.'.pdf"');
        header('Content-Length: '.strlen($result));

        // readfile($pdf_storage_link);
        return $result;
    }

    /**
     * Redirect to forum page
     */
    public function forum(): JsonResponse
    {
        $token = $this->createUserToken();
        $redirect_url = 'https://forum.forfatterskolen.no/auth/sso?ssoToken='.$token.'&redirect=/';

        return response()->json([
            'redirect_url' => $redirect_url,
        ]);
    }

    public function autoRegisterCourseWebinar(Request $request)
    {
        $user = Auth::user();
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

    /**
     * Generate a user token
     *
     * @return string
     */
    public function createUserToken()
    {
        $user = Auth::user();
        $privateKey = config('services.jwt.private_key');

        $userData = [
            'email' => $user->email,
            'id' => $user->id,
            'name' => $user->fullname,
        ];

        return JWT::encode($userData, $privateKey, 'HS256');
    }

    /**
     * Login to pilotleser
     */
    public function pilotleserLogin(): JsonResponse
    {
        $user = Auth::user();
        // create token
        $token = JWT::encode([
            'sub' => $user->id,
            'iat' => Carbon::now()->timestamp,
            'jti' => \Illuminate\Support\Str::limit(md5(Carbon::now()->timestamp + $user->id), 16),
            'exp' => Carbon::now()->timestamp * 60,
        ], config('services.jwt.secret'));

        $base_url = config('services.cross-domain.url').'/get-token';
        $header = [];
        $header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Accept: application/json';
        $header[] = 'Authorization: Bearer '.$token;

        $body = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $decode = json_decode($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // check if an access token is generated
        if ($httpcode === 200) {
            $request_url = config('services.cross-domain.url').'/login';
            $login_header = [];
            $login_header[] = 'Content-type: application/x-www-form-urlencoded';
            $login_header[] = 'Accept: application/json';

            $login_body = [
                'jti' => $decode[0]->jti,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $user->password ?: bcrypt(123),
            ];

            $login_ch = curl_init();
            curl_setopt($login_ch, CURLOPT_URL, $request_url);
            curl_setopt($login_ch, CURLOPT_POST, 1);
            curl_setopt($login_ch, CURLOPT_POSTFIELDS, http_build_query($login_body));
            curl_setopt($login_ch, CURLOPT_HTTPHEADER, $login_header);
            curl_setopt($login_ch, CURLOPT_RETURNTRANSFER, 1);

            $login_response = curl_exec($login_ch);
            $login_decode = json_decode($login_response);
            $login_httpcode = curl_getinfo($login_ch, CURLINFO_HTTP_CODE);

            // check for error
            if ($login_httpcode !== 200) {
                return response()->json([
                    'message' => $login_decode->message,
                ], $login_httpcode);
            }

            return response()->json([
                'redirect_url' => $login_decode->redirect_url,
            ]);
        }

        // added fiter for $decode->message: this causes error : cron fix
        return response()->json([
            'message' => $decode ? $decode->message : '',
        ], $httpcode);
    }

    public function coachingTime(Request $request)
    {
        $coachingTimers = CoachingTimerManuscript::where('user_id', Auth::id())
            ->whereNull('editor_id')
            ->get();

        $now = Carbon::now('UTC');

        $editors = EditorTimeSlot::with('editor')
            ->whereDoesntHave('requests', function ($q) {
                $q->where('status', 'accepted');
            })
            ->where(function ($q) use ($now) {
                $q->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('start_time', '>=', $now->toTimeString());
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('editor_id');

        $bookedEditorsCount = CoachingTimerManuscript::where('user_id', Auth::id())
            ->whereNotNull('editor_id')
            ->distinct('editor_id')
            ->count('editor_id');

        $bookedSessions = CoachingTimerManuscript::where('user_id', Auth::id())
            ->whereNotNull('editor_time_slot_id')
            ->where(function ($q) {
                $q->where('status', 0)
                    ->whereHas('timeSlot', function ($q) {
                        $q->where('date', '>=', now()->toDateString());
                    });
            })
            ->with(['editor', 'timeSlot'])
            ->get()
            ->sortBy(function ($session) {
                return $session->timeSlot->date . ' ' . $session->timeSlot->start_time;
            });

        $bookedSessionsThisMonth = $bookedSessions->filter(function ($session) {
            $dt = Carbon::parse(
                $session->timeSlot->date . ' ' . $session->timeSlot->start_time,
                'UTC'
            )->setTimezone(config('app.timezone'));

            return $dt->isSameMonth(Carbon::now(config('app.timezone')));
        })->count();

        return view('frontend.learner.coaching-time', compact(
            'editors',
            'coachingTimers',
            'bookedEditorsCount',
            'bookedSessions',
            'bookedSessionsThisMonth'
        ));
    }

    public function availableCoachingTime(Request $request)
    {
        $coachingTimers = CoachingTimerManuscript::where('user_id', Auth::id())
            ->whereNull('editor_id')
            ->with(['requests' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->get();

        $coachingTimer = null;
        if ($request->filled('coaching_timer_id')) {
            $coachingTimer = $coachingTimers->firstWhere('id', $request->input('coaching_timer_id'));

            if (!$coachingTimer) {
                return redirect()->route('learner.coaching-time');
            }
        } elseif ($coachingTimers->count() === 1) {
            $coachingTimer = $coachingTimers->first();
        }

        $now = Carbon::now('UTC');

        $editors = EditorTimeSlot::with(['editor', 'requests'])
            ->whereDoesntHave('requests', function ($q) {
                $q->where('status', 'accepted');
            })
            ->where(function ($q) use ($now) {
                $q->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('start_time', '>=', $now->toTimeString());
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('editor_id');

        return view('frontend.learner.coaching-time-available', compact('editors', 'coachingTimer', 'coachingTimers'));
    }

    public function requestCoachingTime(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'coaching_timer_id'   => 'required|exists:coaching_timer_manuscripts,id',
            'editor_time_slot_id' => 'required|exists:editor_time_slots,id',
            'help_with'           => 'nullable|string',
        ]);

        $timer = CoachingTimerManuscript::find($data['coaching_timer_id']);
        $slot  = EditorTimeSlot::find($data['editor_time_slot_id']);

        $requiredDuration = $timer->plan_type == 1 ? 60 : 30;
        if ($slot->duration != $requiredDuration) {
            return redirect()->back()->with('error', 'Selected time slot duration does not match your plan.');
        }

        try {
            DB::transaction(function () use ($data, $timer, $slot) {
                $exists = CoachingTimeRequest::where('editor_time_slot_id', $data['editor_time_slot_id'])
                    ->where('status', 'accepted')
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \RuntimeException('Slot already booked');
                }

                $requestRecord = CoachingTimeRequest::create([
                    'coaching_timer_manuscript_id' => $data['coaching_timer_id'],
                    'editor_time_slot_id'          => $data['editor_time_slot_id'],
                    'status'                       => 'accepted',
                ]);

                CoachingTimeRequest::where('editor_time_slot_id', $data['editor_time_slot_id'])
                    ->where('id', '!=', $requestRecord->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'declined']);

                $timer->help_with = $data['help_with'] ?? null;
                $timer->editor_id = $slot->editor_id;
                $timer->editor_time_slot_id = $slot->id;
                $timer->save();
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', 'This time slot has already been booked.');
        }

        $timer->refresh()->load(['user', 'editor', 'timeSlot.editor']);
        $slotModel = $timer->timeSlot ?: $slot;

        if ($slotModel) {
            $emailContext = $this->coachingTimeBookingEmailContext($timer, $slotModel);

            if ($timer->user) {
                $learnerTemplate = AdminHelpers::emailTemplate('Learner Coaching Time Reservation Confirmed');

                if ($learnerTemplate) {
                    $learnerContent =str_replace([
                        ':first_name',
                        ':coaching_session',
                        ':booking_details'
                    ], [
                        $emailContext['learner_first_name'],
                        $emailContext['coaching_session'],
                        $emailContext['booking_details']
                    ], $learnerTemplate->email_content);

                    $to = $timer->user->email;

                    dispatch(new AddMailToQueueJob($to, $learnerTemplate->subject, $learnerContent,
                        $learnerTemplate->from_email, null, null, 'coaching-time-booking', $timer->id));
                }
            }

            if ($timer->editor && $timer->editor->email) {
                $editorTemplate = AdminHelpers::emailTemplate('Editor New Coaching Time Booking Received');

                if ($editorTemplate) {
                    $editorContent = str_replace([
                        ':editor',
                        ':learner',
                        ':coaching_session',
                        ':booking_details'
                    ], [
                        $emailContext['editor_first_name'],
                        $emailContext['learner_name'],
                        $emailContext['coaching_session'],
                        $emailContext['booking_details']
                    ], $editorTemplate->email_content);

                    $emailData = [
                        'email_subject' => $editorTemplate->subject,
                        'email_message' => $editorContent,
                        'from_name' => '',
                        'from_email' => $editorTemplate->from_email ?: 'post@forfatterskolen.no',
                        'attach_file' => null,
                    ];
                    $toEditor = $timer->editor->email;

                    Mail::to($toEditor)->queue(new SubjectBodyEmail($emailData));
                }
            }
        }

        return redirect()->route('learner.coaching-time')->with('success', 'Time slot booked.');
    }

    public function currentUser()
    {
        $user = Auth::user();
        $user['address'] = $user->address;

        return $user;
    }

    private function salesReportCounter($project_book_id, $type)
    {
        return StorageSale::where('project_book_id', $project_book_id)
            ->where('type', $type)
            ->sum('value');
    }

    private function storageSalesByType($user_book_for_sale_id, $type)
    {
        return StorageSale::where('project_book_id', $user_book_for_sale_id)
            ->where('type', $type)
            ->when(request()->filled('year') && request('year') != 'all', function ($query) {
                $query->whereYear('date', request('year'));
            })
            ->when(request()->filled('month') && request('month') != 'all', function ($query) {
                $query->whereMonth('date', request('month'));
            })->sum('value');
    }

    private function storageSalesByTypeArray($user_book_for_sale_id, $type)
    {
        $baseQuery = StorageSale::where('project_book_id', $user_book_for_sale_id)
            ->where('type', $type);

        $sales = (clone $baseQuery)
            ->when(request()->filled('year') && request('year') != 'all', function ($query) {
                $query->whereYear('date', request('year'));
            })
            ->when(request()->filled('month') && request('month') != 'all', function ($query) {
                $query->whereMonth('date', request('month'));
            })
            ->sum('value');

        $overallSales = (clone $baseQuery)->sum('value');

        return [
            'yearly' => $sales,
            'overall' => $overallSales,
        ];
    }

    protected function cleanConvertedText($content): string
    {
        if (! is_string($content)) {
            return '';
        }

        $content = preg_replace("/[\x00-\x08\x0B\x0C\x0E-\x1F]/u", '', $content) ?? '';
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = preg_replace("/\n{3,}/", "\n\n", $content) ?? '';
        $content = preg_replace("/[ \t]+\n/", "\n", $content) ?? '';
        $content = trim($content);

        if ($content === '') {
            return '';
        }

        $lowerContent = strtolower($content);
        if (in_array($lowerContent, ['invalid file type', 'file not exists'], true)) {
            return '';
        }

        return $content;
    }

    protected function extractTextFromDocument(string $path, string $extension): string
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['doc', 'docx'], true)) {
            $docxToText = new \Docx2Text($path);

            return $this->cleanConvertedText($docxToText->convertToText());
        }

        if ($extension === 'pdf') {
            return $this->extractTextFromPdf($path);
        }

        if ($extension === 'pages') {
            return $this->cleanConvertedText($this->extractTextFromPagesFile($path));
        }

        return '';
    }

    protected function extractTextFromPdf(string $path): string
    {
        $text = $this->cleanConvertedText($this->extractTextUsingPdfLibrary($path));

        if ($text !== '') {
            return $text;
        }

        return $this->cleanConvertedText($this->extractTextUsingPdftotextBinary($path));
    }

    protected function extractTextUsingPdfLibrary(string $path): string
    {
        try {
            $pdfToText = new \PdfToText($path, \PdfToText::PDFOPT_NONE);
            $pdfToText->Separator = "\n";
            $pdfToText->BlockSeparator = "\n";
            $pdfToText->PageSeparator = "\n\n";

            return (string) $pdfToText;
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::warning('PdfToText library could not extract text', [
                'user_id' => Auth::id(),
                'message' => $exception->getMessage(),
            ]);

            return '';
        }
    }

    protected function extractTextUsingPdftotextBinary(string $path): string
    {
        if (! function_exists('exec')) {
            return '';
        }

        $binaryPath = $this->findSystemExecutable('pdftotext');
        if ($binaryPath === null) {
            return '';
        }

        $temporaryTextPath = $this->createTemporaryConversionPath('txt');
        $command = escapeshellarg($binaryPath).' -layout -nopgbrk -enc UTF-8 '
            .escapeshellarg($path).' '.escapeshellarg($temporaryTextPath).' 2>&1';

        $output = [];
        $exitCode = null;
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            \Illuminate\Support\Facades\Log::warning('pdftotext command failed', [
                'user_id' => Auth::id(),
                'exit_code' => $exitCode,
                'output' => implode("\n", $output),
            ]);

            if (is_file($temporaryTextPath)) {
                @unlink($temporaryTextPath);
            }

            return '';
        }

        $content = is_file($temporaryTextPath) ? file_get_contents($temporaryTextPath) : '';
        if (is_file($temporaryTextPath)) {
            @unlink($temporaryTextPath);
        }

        return is_string($content) ? $content : '';
    }

    protected function extractTextFromPagesFile(string $path): string
    {
        $zip = new \ZipArchive();
        $text = '';

        if ($zip->open($path) === true) {
            $text = $this->extractTextFromPagesPreview($zip);

            if ($this->cleanConvertedText($text) === '') {
                $xmlContent = $this->getPagesIndexXml($zip);
                if ($xmlContent !== '') {
                    $text = $this->convertPagesXmlToText($xmlContent);
                }
            }

            $zip->close();
        }

        return $text;
    }

    protected function extractTextFromPagesPreview(\ZipArchive $zip): string
    {
        $previewContent = $this->getZipEntryContent($zip, 'QuickLook/Preview.pdf');

        if ($previewContent === null) {
            return '';
        }

        $temporaryPdfPath = $this->createTemporaryConversionPath('pdf');

        file_put_contents($temporaryPdfPath, $previewContent);

        try {
            return $this->extractTextFromPdf($temporaryPdfPath);
        } finally {
            if (is_file($temporaryPdfPath)) {
                @unlink($temporaryPdfPath);
            }
        }
    }

    protected function getPagesIndexXml(\ZipArchive $zip): string
    {
        $xmlContent = $this->getZipEntryContent($zip, 'index.xml');

        if ($xmlContent !== null) {
            return $xmlContent;
        }

        $compressedIndex = $this->getZipEntryContent($zip, 'Index.zip');
        if ($compressedIndex === null) {
            return '';
        }

        $temporaryZipPath = $this->createTemporaryConversionPath('zip');
        file_put_contents($temporaryZipPath, $compressedIndex);

        $innerZip = new \ZipArchive();
        $innerContent = '';

        if ($innerZip->open($temporaryZipPath) === true) {
            $innerXml = $this->getZipEntryContent($innerZip, 'Index.xml');
            if ($innerXml !== null) {
                $innerContent = $innerXml;
            }
            $innerZip->close();
        }

        if (is_file($temporaryZipPath)) {
            @unlink($temporaryZipPath);
        }

        return $innerContent;
    }

    protected function convertPagesXmlToText(string $xmlContent): string
    {
        $xmlContent = preg_replace('/<\/((p|br|li|table|tr|td|para)[^>]*)>/i', "\n", $xmlContent) ?? $xmlContent;
        $xmlContent = strip_tags($xmlContent);
        $xmlContent = html_entity_decode($xmlContent, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return $xmlContent;
    }

    protected function getZipEntryContent(\ZipArchive $zip, string $name): ?string
    {
        $flags = defined('ZipArchive::FL_NOCASE') ? \ZipArchive::FL_NOCASE : 0;
        $index = $zip->locateName($name, $flags);

        if ($index === false) {
            return null;
        }

        $content = $zip->getFromIndex($index);

        return is_string($content) ? $content : null;
    }

    protected function createTemporaryConversionPath(string $extension): string
    {
        $directory = storage_path('app/temp-conversions');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.Str::uuid()->toString().'.'.$extension;
    }

    protected function findSystemExecutable(string $binary): ?string
    {
        if (! function_exists('exec')) {
            return null;
        }

        $output = [];
        $exitCode = null;
        exec('command -v '.escapeshellarg($binary), $output, $exitCode);

        if ($exitCode === 0 && isset($output[0])) {
            $path = trim($output[0]);

            return $path !== '' ? $path : null;
        }

        return null;
    }

    protected function coachingTimeBookingEmailContext(CoachingTimerManuscript $timer, EditorTimeSlot $slot): array
    {
        $learner = $timer->user;
        $editor = $timer->editor ?: $slot->editor;

        $timezone = config('app.timezone', 'UTC');
        $startUtc = Carbon::parse($slot->date.' '.$slot->start_time, 'UTC');
        $startLocal = $startUtc->copy()->setTimezone($timezone);
        $endLocal = $startLocal->copy()->addMinutes($slot->duration);

        $helpWith = $timer->help_with ?? '';
        $helpWith = trim($helpWith);

        $coachingSession = $startLocal->format('d.m.Y').' '.$startLocal->format('H:i')
            .' - '.$endLocal->format('H:i');
        if (!empty($timezone)) {
            $coachingSession .= ' ('.$timezone.')';
        }

        return [
            'learner_name' => $learner ? $learner->full_name : '',
            'learner_first_name' => $learner ? $learner->first_name : '',
            'editor_name' => $editor ? $editor->full_name : '',
            'editor_first_name' => $editor ? $editor->first_name : '',
            'slot_date' => $startLocal->format('d.m.Y'),
            'slot_time' => $startLocal->format('H:i'),
            'slot_end_time' => $endLocal->format('H:i'),
            'slot_time_range' => $startLocal->format('H:i').' - '.$endLocal->format('H:i'),
            'slot_date_time' => $startLocal->format('d.m.Y H:i'),
            'slot_timezone' => $timezone,
            'coaching_session' => FrontendHelpers::getCoachingTimerPlanType($timer->plan_type),
            'booking_details' => $coachingSession,
            'help_with' => $helpWith,
        ];
    }
}
