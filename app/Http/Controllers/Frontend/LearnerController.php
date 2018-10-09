<?php
namespace App\Http\Controllers\Frontend;

use App\AssignmentAddon;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentGroupLearner;
use App\CalendarNote;
use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Diploma;
use App\EmailConfirmation;
use App\Genre;
use App\Http\AdminHelpers;
use App\Http\Middleware\Admin;
use App\Http\Requests\AddWritingGroupRequest;
use App\LessonContent;
use App\LessonDocuments;
use App\Mail\CoachingSuggestionDateEmail;
use App\Mail\MultipleEmailConfirmation;
use App\Notification;
use App\OtherServiceFeedback;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\PilotReaderReaderProfile;
use App\Repositories\Services\CompetitionService;
use App\Repositories\Services\PublishingService;
use App\Repositories\Services\WritingGroupService;
use App\ShopManuscriptUpgrade;
use App\Survey;
use App\SurveyAnswer;
use App\User;
use App\UserEmail;
use App\WordWritten;
use App\WordWrittenGoal;
use App\WritingGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Course;
use App\Manuscript;
use App\CoursesTaken;
use App\WorkshopsTaken;
use App\Http\FikenInvoice;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptComment;
use App\Lesson;
use App\Invoice;
use App\Address;
use App\Assignment;
use App\AssignmentManuscript;
use App\AssignmentGroup;
use App\AssignmentFeedback;
use App\Log;
use Hash;
use File;
use App\Http\FrontendHelpers;

require app_path('/Http/PaypalIPN/PaypalIPN.php');

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PaypalIPN;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class LearnerController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices";
    public $username = "cleidoscope@gmail.com";
    public $password = "moonfang";
    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];

   
    public function dashboard()
    {
        return view('frontend.learner.dashboard');
    }




    public function course()
    {
        $surveys = Survey::all();
        return view('frontend.learner.course', compact('surveys'));
    }

    /**
     * Display the survey page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function survey($id)
    {
        $survey = Survey::find($id);
        if (!$survey) {
            return redirect()->route('learner.course');
        }

        return view('frontend.learner.survey', compact('survey'));
    }


    public function takeSurvey($id, Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            $answer = new SurveyAnswer();
            if (! is_array( $value )) {
                $newValue = $value['answer'];
            } else {
                $newValue = json_encode($value['answer']);
            }
            $answer->answer = $newValue;
            $answer->survey_question_id = $key;
            $answer->user_id = Auth::id();
            $answer->survey_id = $id;

            $answer->save();
        }

        return redirect()->route('learner.course');
    }



    public function shopManuscript()
    {
        return view('frontend.learner.shop-manuscript');
    }



    public function shopManuscriptShow($id)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::where('user_id', Auth::user()->id)->where('id', $id)->where('is_active', true)->first();
        if( $shopManuscriptTaken ) :
            return view('frontend.learner.shopManuscriptShow', compact('shopManuscriptTaken'));
        endif;
        return abort('503');
    }



    public function workshop()
    {
        return view('frontend.learner.workshop');
    }

    /**
     * Approve the coaching date set by admin
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveCoachingDate($id, Request $request)
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suggestCoachingDate($id, Request $request)
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

            $email_data['sender']           = Auth::user()->full_name;
            $email_data['suggested_dates']  = $data['suggested_date'];
            $toMail = 'Camilla@forfatterskolen.no';
            // use queue to send email on background
            Mail::to($toMail)->queue(new CoachingSuggestionDateEmail($email_data));
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Suggested date saved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }


    public function webinar( Request $request )
    {
        $isPost = 0;
        $isReplay = 0;
        $searchResult = [];

        if ($request->exists('search_upcoming')) {
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
        }

        // check if webinar-pakke is replay
        $webinarsRepriser = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
            ->where('user_id',Auth::user()->id)
            ->where('courses.id',17) // just added this line to show all webinar pakke webinars
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
        }

        return view('frontend.learner.webinar', compact('searchResult', 'isPost', 'isReplay'));
    }

    public function courseWebinar()
    {
        return view('frontend.learner.course-webinar');
    }


    public function courseShow($id)
    {
        $courseTaken = CoursesTaken::findOrFail($id);

        if( Auth::user()->can('participateCourse', $courseTaken) ) :
            if ($courseTaken->hasEnded):
                return redirect()->route('learner.course');
            endif;

            return view('frontend.learner.course_show', compact('courseTaken'));
        endif;
        return abort('503');
    }

    public function notifications()
    {
        return view('frontend.learner.notifications');
    }

    public function calendar()
    {
        $events = [];

        foreach( Auth::user()->coursesTaken as $courseTaken ) :
            // Course lessons
            $token = str_random(10);
            foreach( $courseTaken->package->course->lessons as $lesson ) :
                $availability = strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)) * 1000;
                $newAvailability = date('Y-m-d',strtotime(FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)));
                $events[] = [
                    'id' => $lesson->course->id,
                    'title' => 'Lesson: ' . $lesson->title . ' from ' . $lesson->course->title,
                    'class' => 'event-important',
                    'start' => $newAvailability,//$availability,
                    'end' => $newAvailability,//$availability,
                    'color' => '#d95e66'
                ];
            endforeach;

            // Course webinars
            $token = str_random(10);
            foreach( $courseTaken->package->course->webinars as $webinar ) :
                $events[] = [
                    'id' => $webinar->course->id,
                    'title' => 'Webinar: ' . $webinar->title . ' from ' . $webinar->course->title,
                    'class' => 'event-warning',
                    'start' => date('Y-m-d',strtotime($webinar->start_date)),//strtotime($webinar->start_date) * 1000,
                    'end' => date('Y-m-d',strtotime($webinar->start_date)),//strtotime($webinar->start_date) * 1000,
                    'color' => '#ff9c00'
                ];
            endforeach;

            // manuscripts
            foreach ($courseTaken->manuscripts as $manuscript) :
                $events[] = [
                    'id' => $courseTaken->package->course->id,
                    'title' => 'Manus: ' . basename($manuscript->filename). ' from '.$courseTaken->package->course->title,
                    'class' => 'event-info',
                    'start' => date('Y-m-d',strtotime($manuscript->expected_finish)),//strtotime($manuscript->expected_finish) * 1000,
                    'end' => date('Y-m-d',strtotime($manuscript->expected_finish)),//strtotime($manuscript->expected_finish) * 1000,
                    'color' => '#29b5f5'
                ];
            endforeach;

            // assignments
            foreach ($courseTaken->package->course->assignments as $assignment) :
                $events[] = [
                    'id' => $assignment->course->id,
                    'title' => 'Oppgaver: ' . $assignment->title. ' from '.$assignment->course->title,
                    'class' => 'event-success-new',
                    'start' => date('Y-m-d',strtotime($assignment->submission_date)),//strtotime($assignment->submission_date) * 1000,
                    'end' => date('Y-m-d',strtotime($assignment->submission_date)),//strtotime($assignment->submission_date) * 1000,
                    'color' => '#44af5e'
                ];
            endforeach;

            // get the calendar notes created by admin for certain course only
            foreach ($courseTaken->package->course->notes as $note):
                $events[] = [
                    'id' => $note->id,
                    'title' => $note->note,
                    'class' => 'event-inverse',
                    'start' => date('Y-m-d',strtotime($note->from_date)),//strtotime($note->date) * 1000,
                    'end' => date('Y-m-d',strtotime($note->to_date)),//strtotime($note->date) * 1000,
                    'color' => '#1b1b1b' // for full calendar
                ];
            endforeach;

        endforeach;

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
        foreach($approved_coaching as $coaching) {
            $events[] = [
                'id' => $coaching->id,
                'title' => 'Coaching Session at '.date('H:i A',strtotime($coaching->approved_date)),
                'class' => 'event-inverse',
                'start' => date('Y-m-d',strtotime($coaching->approved_date)),//strtotime($note->date) * 1000,
                'end' => date('Y-m-d',strtotime($coaching->approved_date)),//strtotime($note->date) * 1000,
                'color' => '#f00' // for full calendar
            ];
        }


    	$event_1 = [
    		'title' => 'Event 1',
    		'class' => 'event-important',
    		'start' => '1494259200000',
    		'end' => '1494259300000',1503292298
    	];

    	return view('frontend.learner.calendar', compact('events'));
    }



    public function assignment()
    {
        $assignments = [];
        $expiredAssignments = [];
        $coursesTaken = Auth::user()->coursesTaken;
        $addOns = AssignmentAddon::where('user_id', \Auth::user()->id)->pluck('assignment_id')->toArray();

        foreach( $coursesTaken as $course ) :
            foreach( $course->package->course->activeAssignments as $assignment ) :

                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $course->package->id;
                // check if the assignment is allowed on the learners package or there's no set package allowed
                if ((!is_null($allowed_package) && in_array($package_id,$allowed_package)) || is_null($allowed_package) || in_array($assignment->id, $addOns)) {
                    $assignments[] = $assignment;
                }
            endforeach;

            foreach( $course->package->course->expiredAssignments as $assignment ) :

                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $course->package->id;
                // check if the assignment is allowed on the learners package or there's no set package allowed
                if ((!is_null($allowed_package) && in_array($package_id,$allowed_package)) || is_null($allowed_package) || in_array($assignment->id, $addOns)) {
                    $expiredAssignments[] = $assignment;
                }
            endforeach;
        endforeach;
        return view('frontend.learner.assignment', compact('assignments', 'expiredAssignments'));
    }


    public function assignmentManuscriptUpload($id, Request $request)
    {
        $assignment = Assignment::findOrFail($id);
        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $assignment->id)->where('user_id', Auth::user()->id)->first();
        $courseIds = [];
        $coursesTaken = Auth::user()->coursesTaken;
        foreach( $coursesTaken as $course ) :
            foreach( $course->package->course as $course ) :
                $courseIds[] = $course;
            endforeach;
        endforeach;

        if ( $request->hasFile('filename') && 
            $request->file('filename')->isValid() && 
            in_array($assignment->course_id, $courseIds) &&
            !$assignmentManuscript) :
            $time = time();
            $destinationPath = 'storage/assignment-manuscripts/'; // upload path

            $extensions = ['pdf', 'docx', 'odt'];
            if ($assignment->for_editor) {
                $extensions = ['docx'];
            }

            $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
            $actual_name = Auth::user()->id;
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);

            $request->filename->move($destinationPath, end($expFileName));

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            // count words
            if($extension == "pdf") :
              $pdf  =  new \PdfToText ( $destinationPath.end($expFileName) ) ;
              $pdf_content = $pdf->Text; 
              $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
              $docObj = new \Docx2Text($destinationPath.end($expFileName));
              $docText= $docObj->convertToText();
              $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
              $doc = odt2text($destinationPath.end($expFileName));
              $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;

            // check if the assignment is for editor only and if it meets the max word
            if ($assignment->for_editor && $word_count > $assignment->max_words) {
                return redirect()->back()->with(['errorMaxWord' => true, 'editorMaxWord' => $assignment->max_words]);
            }

            AssignmentManuscript::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::user()->id,
                'filename' => '/'.$destinationPath.end($expFileName),
                'words' => $word_count,
                'type' => $request->type,
                'manu_type' => $request->manu_type,
            ]);
            Log::create([
                'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for assignment '.$assignment->title
            ]);
            // Admin notification
            $message = Auth::user()->full_name.' submitted a manuscript for assignment '.$assignment->title;
            $toMail = 'Camilla@forfatterskolen.no'; //post@forfatterskolen.no
            //mail($toMail, 'New manuscript submitted for assignment', $message);
            AdminHelpers::send_email('New manuscript submitted for assignment',
                'post@forfatterskolen.no', $toMail, $message);
        endif;


        return redirect()->back()->with('success', true);
    }




    public function group_show($id)
    {
        $group = AssignmentGroup::where('id', $id)->whereHas('learners', function($query){
            $query->where('user_id', Auth::user()->id);
        })->firstOrFail();
        return view('frontend.learner.groupShow', compact('group'));
    }




    public function submit_feedback($group_id, $id, Request $request)
    {
        $group = AssignmentGroup::where('id', $group_id)->whereHas('learners', function($query) use ($id){
            $query->where('id', $id)->where('user_id', '<>', Auth::user()->id);
        })->firstOrFail();
        if ( $request->hasFile('filename')) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];

            $filesWithPath = '';
            // loop through all the uploaded files
            foreach ($request->file('filename') as $k => $file) {
                $extension = pathinfo($_FILES['filename']['name'][$k],PATHINFO_EXTENSION);
                $actual_name = AssignmentGroupLearner::find($id)->user_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension);
                $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension).", ";

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $file->move($destinationPath, $fileName);

            }

            $filesWithPath = trim($filesWithPath,", ");

            AssignmentFeedback::create([
                'assignment_group_learner_id' => $id,
                'user_id' => Auth::user()->id,
                'filename' => $filesWithPath
            ]);
            return redirect()->back();
        endif;
    }








    public function manuscript()
    {
        return view('frontend.learner.manuscript');
    }


    public function invoice()
    {
        $invoices = Auth::user()->invoices()->paginate(15);
        /*$ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};*/
        return view('frontend.learner.invoice', compact('invoices'));
    }




    public function invoiceShow($id)
    {
        $invoice = Invoice::findOrFail($id);
        if(Auth::user()->can('viewInvoice', $invoice)) :
            $ch = curl_init($this->fikenInvoices); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $data = curl_exec($ch);
            $data = json_decode($data);
            $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};
            return view('frontend.learner.invoiceShow', compact('invoice', 'fikenInvoices'));
        endif;
            return abort('503');
    }

    /**
     * Publishing Page
     * @param Request $request
     * @param PublishingService $publishingService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function publishing(Request $request, PublishingService $publishingService)
    {
        if( $request->search && !empty($request->search) ) :
            $searchFromGenre = Genre::where('name', 'LIKE', '%' . $request->search  . '%')
                ->get(['id'])->toArray();

            $searchCollection = new \Illuminate\Database\Eloquent\Collection();

            // loop through all of the search result from the genre
            // then search it on the field in publishing
            foreach ($searchFromGenre as $searchID) {
                $result = $publishingService->search($searchID['id']);
                $searchCollection = $searchCollection->merge($result); // merge the result found
            }

            $searchCollection   = $searchCollection->toArray(); // convert the collection to array
            $total              = count($searchCollection);
            $page               = Paginator::resolveCurrentPage('page') ?: 1; // get the current page
            $startIndex         = ($page - 1) * 15; // 15 is per page
            $results            = array_slice($searchCollection, $startIndex, 15);

            // create a paginator based on the search result
            $publishingHouses = new LengthAwarePaginator($results, $total, 15, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

        else :
            $publishingHouses = $publishingService->paginate(15);
        endif;
        return view('frontend.learner.publishing', compact('publishingHouses'));
    }

    /**
     * Get the competitions
     * @param CompetitionService $competitionService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function competition(CompetitionService $competitionService)
    {
        $competitions = $competitionService->getActiveRecords();
        return view('frontend.learner.competition', compact('competitions'));
    }

    /**
     * Display all writing groups
     * @param WritingGroupService $writingGroupService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function writingGroups(WritingGroupService $writingGroupService)
    {
        $writingGroups = $writingGroupService->getRecord();
        return view('frontend.learner.writing-groups', compact('writingGroups'));
    }

    /**
     * Get writing group or update the record
     * @param $id
     * @param WritingGroupService $writingGroupService
     * @param Request $request
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

    public function upgrade()
    {
        $assignments = [];
        $coursesTaken = Auth::user()->coursesTaken;
        $today = Carbon::now();

        $addOns = AssignmentAddon::where('user_id', \Auth::user()->id)->pluck('assignment_id')->toArray();

        foreach( $coursesTaken as $course ) :
            foreach( $course->package->course->assignments as $assignment ) :

                $allowed_package = json_decode($assignment->allowed_package);
                $package_id = $course->package->id;
                $submission_date =  Carbon::parse($assignment->submission_date);
                // check if the assignment is allowed on the learners package and the submission date is in future
                // or there's no set package allowed and the submission date is in future
                if ((!is_null($allowed_package) && !in_array($package_id,$allowed_package) && $today->lt($submission_date) && !in_array($assignment->id, $addOns))
                    || (is_null($allowed_package) && $today->lt($submission_date) && !in_array($assignment->id, $addOns))) {
                    $assignments[] = $assignment;
                }
            endforeach;
        endforeach;
        return view('frontend.learner.upgrade', compact('assignments'));
    }

    public function takeCourse(Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($request->courseTakenId);
        if( Auth::user()->can('participateCourse', $courseTaken) && 
            FrontendHelpers::isCourseTakenAvailable($courseTaken) &&
            FrontendHelpers::isCourseAvailable($courseTaken->package->course) ) :
            $courseTaken->started_at = date('Y-m-d h:i:s');
            $courseTaken->save();
            return redirect(route('learner.course.show', ['id' => $courseTaken->id]));
        endif;
        return redirect()->back();
    }





    public function profile()
    {
        return view('frontend.learner.profile');
    }





    public function profileUpdate(ProfileUpdateRequest $request)
    {
        if(! empty($request->new_password) ) :
            if (Hash::check($request->old_password, Auth::user()->password)) :
                Auth::user()->password = bcrypt($request->new_password);
                \Session::forget('new_user_social');
            else :
                return redirect()->back()->withErrors('Invalid old password.');
            endif;
        endif;

        if ($request->hasFile('image') && $request->file('image')->isValid()) :
            $image = substr(Auth::user()->profile_image, 1);
            if( Auth::user()->hasProfileImage && File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/profile-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            Auth::user()->profile_image = '/'.$destinationPath.$fileName;
        endif;

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

        return redirect()->back()->with('profile_success', 'Profile successfully updated.');
    }


    public function terms()
    {
        return view('frontend.learner.terms');
    }

    public function lesson($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);

        $lesson_content = $lesson->lessonContent;

        if ($request->exists('search_replay') && $lesson->id == 191) {
            $lesson_content = LessonContent::where('title', 'like', '%'.$request->search_replay.'%')
                ->get();
        }

        $courseTaken = CoursesTaken::where('user_id', Auth::user()->id)->whereIn('package_id', $course->packages->pluck('id')->toArray())->first();
        if(  $courseTaken || FrontendHelpers::hasLessonAccess($courseTaken, $lesson) ) :
            return view('frontend.learner.lesson_show', compact('lesson', 'course', 'courseTaken', 'lesson_content'));
        endif;
        return abort('503');
    }




    public function uploadManuscript($id, Request $request)
    {
        $courseTaken = CoursesTaken::findOrFail($id);
        $coursesTaken_ids = Auth::user()->coursesTaken->pluck('id')->toArray();
        $extensions = ['pdf', 'docx', 'odt'];
        
        if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count && in_array($courseTaken->id, $coursesTaken_ids) ) :
            if( $request->hasFile('file') &&  $request->file('file')->isValid() ) :
                if( Auth::user()->can('participateCourse', $courseTaken) &&
                    !$courseTaken->hasEnded
                ) :
                    $time = time();
                    $destinationPath = 'storage/manuscripts/'; // upload path
                    $extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION); // getting document extension
                    $fileName = $time.'.'.$extension; // rename document
                    $request->file->move($destinationPath, $fileName);

                    if( !in_array($extension, $extensions) ) :
                        return redirect()->back();
                    endif;

                    // count words
                    if($extension == "pdf") :
                      $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
                      $pdf_content = $pdf->Text; 
                      $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                    elseif($extension == "docx") :
                      $docObj = new \Docx2Text($destinationPath.$fileName);
                      $docText= $docObj->convertToText();
                      $word_count = FrontendHelpers::get_num_of_words($docText);
                    elseif($extension == "odt") :
                      $doc = odt2text($destinationPath.$fileName);
                      $word_count = FrontendHelpers::get_num_of_words($doc);
                    endif;


                    Manuscript::create([
                        'coursetaken_id' => $courseTaken->id,
                        'filename' => '/'.$destinationPath.$fileName,
                        'word_count' => $word_count
                    ]);
                    Log::create([
                        'activity' => '<strong>'.Auth::user()->full_name.'</strong> submitted a manuscript for course '.$courseTaken->package->course->title
                    ]);
                    // Admin notification
                    $message = Auth::user()->full_name.' submitted a manuscript for course '.$courseTaken->package->course->title;
                    //mail('post@forfatterskolen.no', 'New manuscript submitted for course', $message);
                    AdminHelpers::send_email('New manuscript submitted for course',
                        'post@forfatterskolen.no','post@forfatterskolen.no', $message);
                else :
                    return abort('503');
                endif;
            endif;
        endif;

        return redirect()->back()->with('success', true);
    }




    public function manuscriptShow($id)
    {
        $manuscript = Manuscript::findOrFail($id);
        if( Auth::user()->id == $manuscript->courseTaken->user_id ) :
            return view('frontend.learner.manuscriptShow', compact('manuscript'));
        else :
            return abort('503');
        endif;
    }



    public function shopManuscriptPostComment( $id, Request $request )
    {
        $shopManuscriptsTaken = ShopManuscriptsTaken::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        if( !empty($request->comment) && $shopManuscriptsTaken->is_active ) :
            $ShopManuscriptComment = new ShopManuscriptComment();
            $ShopManuscriptComment->shop_manuscript_taken_id = $shopManuscriptsTaken->id;
            $ShopManuscriptComment->user_id = Auth::user()->id;
            $ShopManuscriptComment->comment = $request->comment;
            $ShopManuscriptComment->save();
            return redirect()->back();
        else :
            return abort('503');
        endif;
    }



    public function search(Request $request)
    {   
        $courses = Auth::user()->coursesTaken()->whereHas('package', function($query) use ($request){
            $query->whereHas('course', function($query) use ($request){
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            });
        })->get();


        $assignments = Auth::user()->coursesTaken()->whereHas('package', function($query) use ($request){
            $query->whereHas('course', function($query) use ($request){
                $query->whereHas('assignments', function($query) use ($request){
                    $query->where('title', 'LIKE', '%' . $request->search . '%');
                });
            });
        })->get();


        $webinars = Auth::user()->coursesTaken()->whereHas('package', function($query) use ($request){
            $query->whereHas('course', function($query) use ($request){
                $query->whereHas('webinars', function($query) use ($request){
                    $query->where('title', 'LIKE', '%' . $request->search . '%');
                });
            });
        })->get();


        $workshops = Auth::user()->workshopsTaken()->whereHas('workshop', function($query) use ($request){
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        })->get();


        return view('frontend.learner.search', compact('courses', 'assignments', 'webinars', 'workshops'));
    }

    /**
     * Renew specific course
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function courseRenew(Request $request)
    {
        $courseTaken = CoursesTaken::find($request->course_id);
        if ($courseTaken) {
            $user       = User::find($courseTaken->user_id);
            $package    = Package::findOrFail($courseTaken->package_id);
            $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
            $price      = (int)1490*100;
            $product_ID = $package->full_price_product;
            $send_to    = $user->email;
            $dueDate = date("Y-m-d");

            $payment_mode = $paymentMode->mode;
            if( $payment_mode == 'Faktura' ) {
                $payment_mode = 'Bankoverføring';
            }


            $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
            $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

            $invoice_fields = [
                'user_id'       => $user->id,
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'netAmount'     => $price,
                'dueDate'       => $dueDate,
                'description'   => 'Kursordrefaktura',
                'productID'     => $product_ID,
                'email'         => $send_to,
                'telephone'     => $user->address->phone,
                'address'       => $user->address->street,
                'postalPlace'   => $user->address->city,
                'postalCode'    => $user->address->zip,
                'comment'       => $comment
            ];


            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);

            $courseTaken->sent_renew_email = 0;
            $courseTaken->end_date = Carbon::now()->addYear(1);
            $courseTaken->save();

            // Email to support
            //mail('support@forfatterskolen.no', 'Course Renewed', Auth::user()->first_name . ' has renewed the course ' . $package->course->title);
            AdminHelpers::send_email('Course Renewed',
                'post@forfatterskolen.no', 'support@forfatterskolen.no',
                Auth::user()->first_name . ' has renewed the course ' . $package->course->title);

            // Send course email
            $actionText = 'Mine Kurs';
            $actionUrl = 'http://www.forfatterskolen.no/account/course';
            $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_content = $package->course->email;
            //mail($send_to, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
            AdminHelpers::send_email($package->course->title, 'post@forfatterskolen.no', $send_to,
                view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));

            if( $paymentMode->mode == "Paypal" ) :
                echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
                return;
            endif;


            return redirect(route('front.shop.thankyou'));
        }
        return redirect()->back();
    }

    public function courseRenewAllDisabled($course_id)
    {
        $courseTaken    = CoursesTaken::find($course_id);

        if ($courseTaken) {
            $user           = Auth::user();
            $package        = Package::findOrFail($courseTaken->package_id);
            $paymentMode    = PaymentMode::findOrFail(3); // hardcoded faktura payment
            $payment_mode   = 'Bankoverføring';
            $price          = (int)1490*100;
            $product_ID     = $package->full_price_product;
            $send_to        = $user->email;
            $dueDate = date("Y-m-d");

            $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
            $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

            $invoice_fields = [
                'user_id'       => $user->id,
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'netAmount'     => $price,
                'dueDate'       => $dueDate,
                'description'   => 'Kursordrefaktura',
                'productID'     => $product_ID,
                'email'         => $send_to,
                'telephone'     => $user->address->phone,
                'address'       => $user->address->street,
                'postalPlace'   => $user->address->city,
                'postalCode'    => $user->address->zip,
                'comment'       => $comment
            ];


            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);

            // update all the started at of each courses taken
            //Auth::user()->coursesTaken()->update(['started_at' => Carbon::now()]); -- original code

            foreach (Auth::user()->coursesTaken as $coursesTaken) {
                // check if course taken have set end date and add one year to it
                if ($coursesTaken->end_date) {
                    $addYear = date("Y-m-d", strtotime(date("Y-m-d", strtotime($coursesTaken->end_date)) . " + 1 year"));
                    $coursesTaken->end_date = $addYear;
                }

                $coursesTaken->started_at = Carbon::now();
                $coursesTaken->save();
            }

            // add to automation
            $user_email     = Auth::user()->email;
            $automation_id  = 73;
            $user_name      = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

            // Email to support
            //mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
            AdminHelpers::send_email('All Courses Renewed',
                'post@forfatterskolen.no', 'support@forfatterskolen.no',
                Auth::user()->first_name . ' has renewed all the courses');
            return redirect(route('front.shop.thankyou'));
        }

        return redirect()->back();
    }

    /**
     * Set value of auto renew courses field
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setAutoRenewCourses(Request $request)
    {
        $user                       = User::find(Auth::user()->id);
        $user->auto_renew_courses   = $request->auto_renew;
        $user->save();

        $user_email     = Auth::user()->email;
        $automation_id  = 73;
        $user_name      = Auth::user()->first_name;

        AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
        return redirect()->back();
    }

    /**
     * Renew all courses from the upgrade page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function renewLearnerCourses()
    {
        $coursesTaken = Auth::user()->coursesTaken;
        foreach ($coursesTaken as $courseTaken) {
            $package = Package::find($courseTaken->package_id);
            if ($package && $package->course_id == 17) { // check if webinar pakke
                $course_id = $courseTaken->id;
                $webinarPakkeCourse = CoursesTaken::find($course_id);

                if ($webinarPakkeCourse) {
                    $expiredDate = $courseTaken->end_date;
                    $now = new \DateTime();
                    $checkDate = date('m/Y', strtotime($expiredDate));
                    $input = \DateTime::createFromFormat('m/Y', $checkDate);
                    $diff = $input->diff($now); // Returns DateInterval

                    $withinAMonth = $diff->y === 0 && $diff->m <= 1;

                    // check if this is really expired
                    if (!$withinAMonth) {
                        return redirect()->back();
                    }

                    $user           = Auth::user();
                    $package        = Package::findOrFail($webinarPakkeCourse->package_id);
                    $payment_mode   = 'Bankoverføring';
                    $price          = (int)1490*100;
                    $product_ID     = $package->full_price_product;
                    $send_to        = $user->email;
                    $end_date       = $courseTaken->end_date ? $courseTaken->end_date : date("Y-m-d");
                    $dueDate        = date("Y-m-d", strtotime(date("Y-m-d", strtotime($end_date)) . " + 1 year"));

                    $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
                    $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

                    $invoice_fields = [
                        'user_id'       => $user->id,
                        'first_name'    => $user->first_name,
                        'last_name'     => $user->last_name,
                        'netAmount'     => $price,
                        'dueDate'       => $dueDate,
                        'description'   => 'Kursordrefaktura',
                        'productID'     => $product_ID,
                        'email'         => $send_to,
                        'telephone'     => $user->address->phone,
                        'address'       => $user->address->street,
                        'postalPlace'   => $user->address->city,
                        'postalCode'    => $user->address->zip,
                        'comment'       => $comment
                    ];


                    $invoice = new FikenInvoice();
                    $invoice->create_invoice($invoice_fields);

                    // update all the started at of each courses taken
                    foreach (Auth::user()->coursesTaken as $coursesTaken) {
                        // check if course taken have set end date and add one year to it
                        if ($coursesTaken->end_date) {
                            $addYear = date("Y-m-d", strtotime(date("Y-m-d", strtotime($coursesTaken->end_date)) . " + 1 year"));
                            $coursesTaken->end_date = $addYear;
                        }

                        $coursesTaken->started_at = Carbon::now();
                        $coursesTaken->save();
                    }

                    // add to automation
                    $user_email     = Auth::user()->email;
                    $automation_id  = 73;
                    $user_name      = Auth::user()->first_name;

                    AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

                    // Email to support
                    //mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
                    AdminHelpers::send_email('All Courses Renewed',
                        'post@forfatterskolen.no', 'support@forfatterskolen.no',
                        Auth::user()->first_name . ' has renewed all the courses');
                    return redirect(route('front.shop.thankyou'));
                }
            }
        }

        return redirect()->route('learner.upgrade');
    }

    /**
     * Display the course upgrade page
     * @param $courseTakenId
     * @param $package_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getUpgradeCourse($courseTakenId, $package_id)
    {
        $courseTaken    = CoursesTaken::find($courseTakenId);
        if (!$courseTaken) {
            return redirect()->route('learner.upgrade');
        }

        $currentPackage = Package::where('id',$package_id)->where('course_id', $courseTaken->package->course->id)->first();
        if (!$currentPackage) {
            return redirect()->route('learner.upgrade');
        }

        return view('frontend.learner.upgrade-course', compact('courseTaken', 'currentPackage', 'package_id'));
    }

    /**
     * Upgrade the course of the learner
     * this is using the place_order function on ShopController
     * @param $courseTakenId
     * @param Request $request
     */
    public function upgradeCourse($courseTakenId, Request $request)
    {
        $hasPaidCourse = false;
        foreach( Auth::user()->coursesTaken as $courseTaken ) :
            if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                $hasPaidCourse = true;
                break;
            endif;
        endforeach;

        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $package = Package::findOrFail($request->package_id);
        $courseTaken = CoursesTaken::find($courseTakenId);
        $currentCourseType = $courseTaken->package->course_type;
        $add_to_automation = 0;

        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);
        $payment_plan = trim($payment_plan);

        // this is use to check if the current date is within a sale date
        // for the 3 plans/payments
        $today 			= \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull 	= (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 	= (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 	= (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 	= (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        if( $payment_plan == "Hele beløpet" ) :
            /*$price = $isBetweenFull && $package->full_payment_sale_price
                ? (int)$package->full_payment_sale_price*100
                : (int)$package->full_payment_upgrade_price*100;*/
            $price = $package->full_payment_upgrade_price*100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenFull && $package->full_payment_sale_price
                    ? (int)$package->full_payment_sale_price*100
                    : (int)$package->full_payment_standard_upgrade_price*100;*/
                $price = $package->full_payment_standard_upgrade_price*100;
            }

            $product_ID = $package->full_price_product;
            $dueDate->addDays($package->full_price_due_date);
        elseif( $payment_plan == "3 måneder" ) :
            /*$price = $isBetweenMonths3 && $package->months_3_sale_price
                ? (int)$package->months_3_sale_price*100
                : (int)$package->months_3_upgrade_price*100;*/
            $price = $package->months_3_upgrade_price*100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths3 && $package->months_3_sale_price
                    ? (int)$package->months_3_sale_price*100
                    : (int)$package->months_3_standard_upgrade_price*100;*/
                $price = $package->months_3_standard_upgrade_price*100;
            }

            $product_ID = $package->months_3_product;
            $dueDate->addDays($package->months_3_due_date);
        elseif( $payment_plan == "6 måneder" ) :
            /*$price = $isBetweenMonths6 && $package->months_6_sale_price
                ? (int)$package->months_6_sale_price*100
                : (int)$package->months_6_upgrade_price*100;*/
            $price = $package->months_6_upgrade_price*100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths6 && $package->months_6_sale_price
                    ? (int)$package->months_6_sale_price*100
                    : (int)$package->months_6_standard_upgrade_price*100;*/
                $price = $package->months_6_standard_upgrade_price*100;
            }

            $product_ID = $package->months_6_product;
            $dueDate->addDays($package->months_6_due_date);
        elseif( $payment_plan == "12 måneder" ) :
            /*$price = $isBetweenMonths12 && $package->months_12_sale_price
                ? (int)$package->months_12_sale_price*100
                : (int)$package->months_12_upgrade_price*100;*/
            $price = $package->months_12_upgrade_price*100;

            // check if the current course of learner is standard and is trying to buy pro course
            // then apply this price
            if ($package->course_type == 3 && $currentCourseType == 2) {
                /*$price = $isBetweenMonths12 && $package->months_12_sale_price
                    ? (int)$package->months_12_sale_price*100
                    : (int)$package->months_12_standard_upgrade_price*100;*/
                $price = $package->months_12_standard_upgrade_price*100;
            }

            $product_ID = $package->months_12_product;
            $dueDate->addDays($package->months_12_due_date);
        endif;
        $dueDate = date_format(date_create($dueDate), 'Y-m-d');

        $payment_mode = $paymentMode->mode;
        if( $payment_mode == 'Faktura' ) :
            $payment_mode = 'Bankoverføring';
        endif;

        $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: ' . $payment_mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $discount = 0;

        $course_id = $package->course->id;

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course_id)->first();

            if ($discountCoupon) {
                $discount = ( (int) $discountCoupon->discount);
                $price = $price - ( (int)$discount*100 );
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
            $division   = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price      = round($price/$division, 2); // round the value to the nearest tenths
            $price      = (int)$price*100;
            for ($i=1; $i <= $paymentPlan->division; $i++ ) { // loop based on the split count
                $dueDate =  Carbon::today()->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
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
                    'comment' => $comment
                ];

                $invoice = new FikenInvoice();
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
                'comment' => $comment
            ];

            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);
        }

        $courseTaken->package_id = $package->id;
        $courseTaken->save();

        // Check for shop manuscripts
        if( $package->shop_manuscripts->count() > 0 ) :
            foreach( $package->shop_manuscripts as $shop_manuscript ) :
                $shopManuscriptTaken = ShopManuscriptsTaken::firstOrNew(['user_id' => Auth::user()->id, 'shop_manuscript_id' => $shop_manuscript->shop_manuscript_id]);
                $shopManuscriptTaken->user_id = Auth::user()->id;
                $shopManuscriptTaken->shop_manuscript_id = $shop_manuscript->shop_manuscript_id;
                $shopManuscriptTaken->is_active = false;
                $shopManuscriptTaken->save();
            endforeach;
        endif;

        if ($package->included_courses->count() > 0) {
            foreach ($package->included_courses as $included_course) {
                if ($included_course->included_package_id == 29) { // check if webinar-pakke is included
                    $add_to_automation++;
                }
            }
        }

        if ($package->course->id == 17) { //check if webinar-pakke
            $add_to_automation++;
        }

        if ($add_to_automation > 0) {
            $user_email = Auth::user()->email;
            $automation_id = 73;
            $user_name = Auth::user()->first_name;

            AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);
        }

        // Email to support
        //mail('support@forfatterskolen.no', 'New Course Order', Auth::user()->first_name . ' has ordered the course ' . $package->course->title);
        AdminHelpers::send_email('New Course Order',
            'post@forfatterskolen.no', 'support@forfatterskolen.no',
            Auth::user()->first_name . ' has ordered the course ' . $package->course->title);

        // Send course email
        $actionText = 'Mine Kurs';
        $actionUrl = 'http://www.forfatterskolen.no/account/course';
        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $user = Auth::user();
        $email_content = $package->course->email;
        //mail($user->email, $package->course->title, view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')), $headers);
        AdminHelpers::send_email($package->course->title,
            'post@forfatterskolen.no', $user->email,
            view('emails.course_order', compact('actionText', 'actionUrl', 'user', 'email_content')));

        if( $paymentMode->mode == "Paypal" ) :
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
            return;
        endif;


        return redirect(route('front.shop.thankyou'));
    }

    /**
     * Display the upgrade page of manuscript
     * @param $shopManuscriptTakenId ShopManuscriptsTaken
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getUpgradeManuscript($shopManuscriptTakenId)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::find($shopManuscriptTakenId);
        if ($shopManuscriptTaken) {
            $shopManuscriptId = $shopManuscriptTaken->shop_manuscript->id;
            $shopManuscriptUpgrades = ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptId)->get();
            return view('frontend.learner.upgrade-manuscript', compact('shopManuscriptTaken', 'shopManuscriptUpgrades'));
        }

        return redirect()->route('learner.upgrade');
    }

    /**
     * Upgrade the learners manuscript
     * @param $shopManuscriptTakenId
     * @param Request $request
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
            $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

            $comment = '(Manuskript: Oppgradering fra ' . $oldManuscript. ' til '. $shopManuscript->title . ', ';
            $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
            $comment .= 'Betalingsplan: ' . $payment_plan . ')';

            $dueDate = date("Y-m-d");
            $dueDate = Carbon::parse($dueDate);
            $dueDate->addDays(14);
            $dueDate = date_format(date_create($dueDate), 'Y-m-d');
            $price = (int)$shopManuscriptUpgrade->price*100;

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
            ];

            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);

            if( $paymentMode->mode == "Paypal" ) :
                echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
                return;
            endif;



            return redirect(route('front.shop.thankyou'));
        }
        return redirect()->route('learner.upgrade');
    }

    /**
     * Display the Buy/Upgrade Assignment Page
     * @param $assignment_id
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

    /**
     * Upgrade/Buy assignment
     * @param $assignment_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function upgradeAssignment($assignment_id, Request $request)
    {
        $assignment = Assignment::find($assignment_id);
        if ($assignment) {

            AssignmentAddon::create([
                'user_id' => Auth::user()->id,
                'assignment_id' => $assignment_id
            ]);

            $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
            $paymentPlan = PaymentPlan::findOrFail(8);
            $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

            $comment = '(Assignment: ' . $assignment->title . ', ';
            $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
            $comment .= 'Betalingsplan: ' . $payment_plan . ')';

            $dueDate    = date("Y-m-d");
            $dueDate    = Carbon::parse($dueDate);
            $dueDate->addDays(14);
            $dueDate = date_format(date_create($dueDate), 'Y-m-d');
            $price      = (int)$assignment->add_on_price*100;

            $product_id =  287613124; // default product id

            $invoice_fields = [
                'user_id'       => Auth::user()->id,
                'first_name'    => Auth::user()->first_name,
                'last_name'     => Auth::user()->last_name,
                'netAmount'     => $price,
                'dueDate'       => $dueDate,
                'description'   => 'Assignment Add On',
                'productID'     => $product_id,
                'email'         => Auth::user()->email,
                'telephone'     => Auth::user()->address->phone,
                'address'       => Auth::user()->address->street,
                'postalPlace'   => Auth::user()->address->city,
                'postalCode'    => Auth::user()->address->zip,
                'comment'       => $comment,
            ];

            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);

            if( $paymentMode->mode == "Paypal" ) :
                echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
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
                return;
            endif;

            return redirect(route('front.shop.thankyou'));

        }
        return redirect()->route('learner.upgrade');
    }

    /**
     * Replace the manuscript from particular assignment
     * @param $id int assignment id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function replaceAssignmentManuscript($id, Request $request)
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if ($assignmentManuscript) {
            if ( $request->hasFile('filename') && $request->file('filename')->isValid() ) {
                $time = time();
                $destinationPath = 'storage/assignment-manuscripts/'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
                $actual_name = Auth::user()->id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $expFileName = explode('/', $fileName);

                $request->filename->move($destinationPath, end($expFileName));

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                // count words
                $word_count = 0;
                if($extension == "pdf") :
                    $pdf  =  new \PdfToText ( $destinationPath.end($expFileName) ) ;
                    $pdf_content = $pdf->Text;
                    $word_count = FrontendHelpers::get_num_of_words($pdf_content);
                elseif($extension == "docx") :
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText= $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);
                elseif($extension == "odt") :
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count = FrontendHelpers::get_num_of_words($doc);
                endif;

                // check if the assignment is for editor only and if it meets the max word
                if ($assignmentManuscript->assignment->for_editor && $word_count > $assignmentManuscript->assignment->max_words) {
                    return redirect()->back()->with(['errorMaxWord' => true, 'editorMaxWord' => $assignmentManuscript->assignment->max_words]);
                }

                $assignmentManuscript->filename = '/'.$fileName;
                $assignmentManuscript->words = $word_count;
                $assignmentManuscript->save();
            }
        }

        return redirect()->back();
    }

    /**
     * Delete the manuscript from particular assignment
     * @param $id int assignment id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAssignmentManuscript($id)
    {
        $manuscript = AssignmentManuscript::findOrFail($id);
        $manuscript->forceDelete();
        return redirect()->back();
    }

    /**
     * Replace the feedback
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function replaceFeedback($id, Request $request)
    {
        $feedback = AssignmentFeedback::find($id);

        if ($feedback) {
            if ( $request->hasFile('filename') && $request->file('filename')->isValid() ) {
                $time = time();
                $destinationPath = 'storage/assignment-feedbacks/'; // upload path
                $extensions = ['pdf', 'docx', 'odt'];
                $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $request->filename->move($destinationPath, $fileName);

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $feedback->filenmae = '/'.$destinationPath.$fileName;

            }
        }

        return redirect()->back();
    }

    public function deleteFeedback($id)
    {
        $feedback = AssignmentFeedback::findOrFail($id);
        $feedback->forceDelete();
        return redirect()->back();
    }

    /**
     * Download assignment group manuscript
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupManuscript($id)
    {
        $manuscript = AssignmentManuscript::find($id);
        if ($manuscript) {
            $filename =  $manuscript->filename;
            return response()->download(public_path($filename));
        }
        return redirect()->back();
    }

    /**
     * Download assignment feedback
     * @param $feedback_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupFeedback($feedback_id)
    {
        $feedback = AssignmentFeedback::find($feedback_id);
        if ($feedback) {
            $files =  explode(',', $feedback->filename);
            if (count($files) > 1) {
                $zipFileName    = $feedback->assignment_group_learner->group->title.' Feedbacks.zip';
                $public_dir     = public_path('storage');
                $zip            = new \ZipArchive();

                // open zip file connection and create the zip
                if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die ("An error occurred creating your ZIP file.");
                }

                foreach($files as $feedFile) {
                    if (file_exists(public_path().'/'.trim($feedFile))) {

                        //get the correct filename
                        $expFileName = explode('/', $feedFile);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                    }
                }

                $zip->close(); // close zip connection

                $headers = array(
                    'Content-Type' => 'application/octet-stream',
                );

                $fileToPath = $public_dir.'/'.$zipFileName;

                if(file_exists($fileToPath)){
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
     * @param $feedback_id
     * @return $this|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentNoGroupFeedback($feedback_id)
    {
        $feedback = AssignmentFeedbackNoGroup::find($feedback_id);
        if ($feedback) {
            $files =  explode(',', $feedback->filename);
            if (count($files) > 1) {
                $zipFileName    = $feedback->manuscript->assignment->title.' Feedbacks.zip';
                $public_dir     = public_path('storage');
                $zip            = new \ZipArchive();

                // open zip file connection and create the zip
                if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die ("An error occurred creating your ZIP file.");
                }

                foreach($files as $feedFile) {
                    if (file_exists(public_path().'/'.trim($feedFile))) {

                        //get the correct filename
                        $expFileName = explode('/', $feedFile);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                    }
                }

                $zip->close(); // close zip connection

                $headers = array(
                    'Content-Type' => 'application/octet-stream',
                );

                $fileToPath = $public_dir.'/'.$zipFileName;

                if(file_exists($fileToPath)){
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
     * @param $group_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignmentGroupAllFeedback($group_id)
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
                $zipFileName    = $group->title.' Feedbacks.zip';
                $public_dir     = public_path('storage');
                $zip            = new \ZipArchive();

                // open zip file connection and create the zip
                if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die ("An error occurred creating your ZIP file.");
                }

                foreach($feedbacks as $feedback) {
                    $files = explode(',', $feedback->filename);
                    // for multiple files in a feedback
                    if (count($files) > 1) {
                        foreach($files as $feedFile) {
                            if (file_exists(public_path().'/'.trim($feedFile))) {

                                //get the correct filename
                                $expFileName = explode('/', $feedFile);
                                $file = str_replace('\\', '/', public_path());

                                // physical file location and name of the file
                                $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                            }
                        }
                    } else {
                        if (file_exists(public_path().'/'.$feedback->filename)) {
                            //get the correct filename
                            $expFileName = explode('/', $feedback->filename);
                            $file = str_replace('\\', '/', public_path());

                            // physical file location and name of the file
                            $zip->addFile($file.$feedback->filename, end($expFileName));
                        }
                    }
                }

                $zip->close(); // close zip connection

                $headers = array(
                    'Content-Type' => 'application/octet-stream',
                );

                $fileToPath = $public_dir.'/'.$zipFileName;

                if(file_exists($fileToPath)){
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }
            }
            return redirect()->back();
        }
        return redirect()->back();
    }

    /**
     * Display or create word written by logged in learner
     * @param Request $request
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
     * @param Request $request
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function wordWrittenGoalsUpdate($id, Request $request)
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function wordWrittenGoalsDelete($id)
    {
        if ($goal = WordWrittenGoal::find($id)) {
            $goal->forceDelete();
            return redirect()->back();
        }
        return redirect()->route('learner.word-written-goals');
    }

    /**
     * Get the statistics
     * @param $goal_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function goalStatistic($goal_id)
    {
        $statistics = [];
        $totalStatistic = 0;
        $goal = \App\WordWrittenGoal::find($goal_id);
        $from_ymd = date('Y-m-d', strtotime($goal->from_date));
        $to_ymd = date('Y-m-d', strtotime($goal->to_date));

        $statisticsData = \App\WordWritten::where('user_id',$goal->user_id)
            ->whereBetween('date',[$from_ymd, $to_ymd])
            ->select(\DB::raw('sum(words) as `words`'),  \DB::raw('YEAR(date) year, MONTH(date) month'))
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
            'month' => 'Total Words'
        ];

        return response()->json($statistics);
    }

    /**
     * Download the document from a lesson
     * @param $lessonId
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNotificationAsRead($id)
    {
        if($notification = Notification::find($id)) {
            $notification->is_read = 1;
            $notification->save();
            return response()->json(['success' => 'Notification marked as read.'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    /**
     * Delete a notification
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNotification($id)
    {
        if($notification = Notification::find($id)) {
            $notification->forceDelete();
            return response()->json(['success' => 'Notification deleted successfully.'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    public function addCoachingSession(Request $request)
    {
        $data = $request->except('_token');
        $course_taken_id = $data['course_taken_id'];

        if ($courseTaken = CoursesTaken::find($course_taken_id)) {
            $suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }

            $extensions = ['docx'];
            $file   = NULL;

            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $destinationPath = 'storage/coaching-timer-manuscripts/'; // upload path

                $time = time();
                $fileName = $time.'.'.$extension;//$original_filename; // rename document0
                $file = $destinationPath.$fileName;
                $request->manuscript->move($destinationPath, $fileName);
            endif;

            CoachingTimerManuscript::create([
                'user_id'           => Auth::user()->id,
                'file'              => $file,
                'plan_type'         => $data['plan_type'],
                'suggested_date'    => json_encode($suggested_dates)
            ]);

            CoachingTimerTaken::create([
                'user_id'           => Auth::user()->id,
                'course_taken_id'   => $course_taken_id
            ]);

        }

        return redirect()->back();
    }

    /**
     * Download the diploma
     * @param $id
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
     * Download file
     * @param $service_id
     * @param $service_type
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadOtherServiceDoc($service_id, $service_type)
    {
        if ($service_type == 1 || $service_type == 2) {
            $filename = '';
            if ($service_type == 1 && $copyEditing = CopyEditingManuscript::find($service_id)) {
                $filename = $copyEditing->file;
            }

            if ($service_type == 2 && $correction = CorrectionManuscript::find($service_id)){
                $filename = $correction->file;
            }

            return response()->download(public_path($filename));
        }

        return redirect()->route('admin.learner.index');
    }

    /**
     * Download the feedback for other service
     * @param $feedback_id
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHelpWith($id, Request $request)
    {
        if($coachingTimer = CoachingTimerManuscript::find($id)) {
            $coachingTimer->help_with = $request->help_with;
            $coachingTimer->save();
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Skriv litt her om hva du vil ha hjelp til saved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * List all user emails
     * @return \Illuminate\Http\JsonResponse
     */
    public function listEmails()
    {
        $user = Auth::user();
        $data['primary'] = $user;
        $data['secondary'] = UserEmail::where('user_id', $user->id)->get();
        return response()->json($data);
    }

    /**
     * Send email confirmation to check if user owns the inputted email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailConfirmation(Request $request){

        $this->validate($request, [
            'email' => 'required|email|unique:users|unique:user_emails',
        ]);


        $email_data = $request->all();
        $email_data['token'] = md5(microtime());
        $email_data['user_id'] = Auth::user()->id;

        $saveData['email'] = $email_data['email'];
        $saveData['user_id'] = Auth::user()->id;
        $saveEmail = EmailConfirmation::firstOrNew($saveData);
        $saveEmail->token = $email_data['token'];

        if(! $saveEmail->save())
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        $email_data['name'] = Auth::user()->first_name;

        Mail::to($email_data['email'])->queue(new MultipleEmailConfirmation($email_data));


        return response()->json(['success' => 'Email Confirmation Sent.'], 200);
    }

    /**
     * Set Primary Email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPrimaryEmail(Request $request)
    {
        DB::beginTransaction();
        $user           = Auth::user();
        $user_emails    = UserEmail::find($request->id);
        $primary        = $user_emails->email;
        $secondary      = $user->email;
        if( !$user->update(['email' => $primary]))
        {
            DB::rollback();
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        if(! $user_emails->update(['email' => $secondary]))
        {
            DB::rollback();
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        DB::commit();
        return response()->json(['success' => 'Secondary email set as primary', 'primary_email' => $primary], 200);
    }

    /**
     * Remove a secondary email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSecondaryEmail(Request $request)
    {
        if(! UserEmail::destroy($request->id))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Secondary email deleted'], 200);
    }
}
