<?php
namespace App\Http\Controllers\Backend;

use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailOutLog;
use App\Mail\SubjectBodyEmail;
use App\Package;
use App\PackageCourse;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\SimilarCourse;
use App\Http\AdminHelpers;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use File;
use Maatwebsite\Excel\Excel;

class CourseController extends Controller
{

    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:1');
    }

    public function index(Request $request)
    {
        if( $request->search && !empty($request->search) ) :
            $courses = Course::where('title', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        else :
            // display 0 value last
            $courses = Course::orderByRaw('display_order = 0, display_order')->orderBy('created_at', 'desc')->paginate(25);
        endif;
    	return view('backend.course.index', compact('courses'));
    }



    public function show(Request $request, $id)
    {
    	$section = isset( $request->section ) ? $request->section : "overview";
    	AdminHelpers::validateCourseSubpage($section);

    	$course = Course::findOrFail($id);
    	return $this->showSection($section, $course);
    }



    public function showSection($section, $course)
    {
    	return view('backend.course.' . $section, compact('course', 'section'));
    }




    public function create()
    {
        $course = [
            'title' => old('title'),
            'description' => old('description'),
            'description_simplemde' => old('description_simplemde'),
            'course_image' => '',
            'type' => '',
            'course_plan' => '',
            'course_plan_data' => '',
            'start_date' => '',
            'end_date' => '',
            'display_order' => '',
            'is_free' => '',
            'instructor' => '',
            'auto_list_id' => '',
            'photographer' => '',
            'hide_price' => ''
        ];
        return view('backend.course.create', compact('course'));
    }



    public function store(CourseCreateRequest $request)
    {
        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->display_order = is_numeric($request->display_order) ? $request->display_order : 0;

        if ($request->hasFile('course_image')) :
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $course->course_image = '/'.$destinationPath.$fileName;
        endif;

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->is_free = isset($request->is_free) ? 1 : 0;
        $course->instructor = $request->instructor;
        $course->auto_list_id = $request->auto_list_id ?: 0;
        $course->photographer = $request->photographer;
        $course->hide_price = isset($request->hide_price) ? 1 : 0;
        $course->save();
        return redirect(route('admin.course.show', $course->id));
    }




    public function edit($id)
    {
        $course = Course::findOrFail($id)->toArray();
        return view('backend.course.edit', compact('course'));
    }





    public function update($id, CourseUpdateRequest $request)
    {
        $course = Course::findOrFail($id);
        $course->title = $request->title;
        $course->description = $request->description;
        $course->course_plan = $request->course_plan;
        $course->course_plan_data = $request->course_plan_data;
        $course->display_order = is_numeric($request->display_order) ? $request->display_order : 0;

        if ($request->hasFile('course_image') && $request->file('course_image')->isValid()) :
            $checkImageExistCount = Course::where('course_image', 'LIKE', '%'.$course->course_image.'%')
                ->get()->count();
            $image = substr($course->course_image, 1);
            if( File::exists($image) && $checkImageExistCount < 2) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $course->course_image = '/'.$destinationPath.$fileName;
        endif;

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->instructor = $request->instructor;
        $course->auto_list_id = $request->auto_list_id ?: 0;
        $course->photographer = $request->photographer;
        $course->is_free = isset($request->is_free) ? 1 : 0;
        $course->hide_price = isset($request->hide_price) ? 1 : 0;
        $course->save();
        return redirect(route('admin.course.show', $course->id));
    }





    public function destroy($id){
        $course = Course::findOrFail($id);
        $image = substr($course->course_image, 1);
        if( File::exists($image) ) :
            File::delete($image);
        endif;
        $course->forceDelete();
        return redirect(route('admin.course.index'));
    }





    public function update_email($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $course->email = $request->email;
        $course->save();
        return redirect()->back();
    }




    public function clone_course($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $clone_course = $course->replicate();
        $clone_course->push();

        foreach( $course->lessons as $lesson ) :
            $clone_lesson = $lesson->replicate();
            $clone_lesson->course_id = $clone_course->id;
            $clone_lesson->push();
        endforeach;

        foreach( $course->packages as $package ):
            $clone_package = $package->replicate();
            $clone_package->course_id = $clone_course->id;
            $clone_package->push();
        endforeach;

        foreach( $course->webinars as $webinar ):
            $clone_webinar = $webinar->replicate();
            $clone_webinar->course_id = $clone_course->id;
            $clone_webinar->push();
        endforeach;

        return redirect(route('admin.course.show', $clone_course->id));
    }





    public function add_similar_course($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $similar_course_id = Course::findOrFail($request->similar_course_id);

        $similar_course = new SimilarCourse();
        $similar_course->course_id = $course->id;
        $similar_course->similar_course_id = $similar_course_id->id;
        $similar_course->save();
        return redirect()->back();
    }



    public function remove_similar_course($similar_course_id)
    {
        $similar_course = SimilarCourse::findOrFail($similar_course_id);
        $similar_course->forceDelete();
        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {

        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->status = $request->status;
            $course->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public function updateForSaleStatus(Request $request)
    {
        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->for_sale = $request->for_sale;
            $course->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    /**
     * Update is free field
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIsFreeStatus(Request $request)
    {
        $course = Course::find($request->course_id);
        $success = false;

        if ($course) {
            $course->is_free = $request->is_free;
            $course->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public function sendEmailToLearners($id, Request $request)
    {
        $course = Course::find($id);
        if ($course) {

            $this->validate($request,
                [
                    'subject' => 'required',
                    'message' => 'required'
                ]
            );

            $learners   = isset($request->check_all) || isset($request->learners) ?
                $course->learners->whereIn('user_id', $request->learners)->get()
                : $course->learners->get();
            $subject    = $request->subject;
            $message    = $request->message;
            $from_email = $request->from_email ?: 'post@forfatterskolen.no';
            $from_name  = $request->from_name ?: 'Forfatterskolen';

            // check for attachment
            // save the file first before attaching it on email
            $attachment = NULL;
            $attachmentText = '';
            if ($request->hasFile('attachment')) :
                $destinationPath = 'storage/email_attachments'; // upload path

                if (!\File::exists($destinationPath)) {
                    \File::makeDirectory($destinationPath);
                }

                $extension = $request->attachment->extension(); // getting image extension
                $uploadedFile = $request->attachment->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                //remove spaces to avoid error on attachment
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
                $request->attachment->move($destinationPath, $fileName);

                $attachment = '/'.$fileName;
                $emailAttach['filename'] =  $attachment;
                $emailAttach['hash'] = substr(md5(microtime()), 0, 6);
                $emailAttachment = EmailAttachment::create($emailAttach);
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($attachment)."</a></p>";
            endif;

            foreach($learners as $learner) {
                /*$email = $learner->user->email;
                AdminHelpers::send_email($subject,
                    $from_email, $email, $message, $from_name);*/
                $email = $learner->user->email;
                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $message.$attachmentText;
                $emailData['from_name'] = $from_name;
                $emailData['from_email'] = $from_email;
                $emailData['attach_file'] = NULL;
                \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
            }

            $selected_learners = NULL;
            if (isset($request->check_all) || isset($request->learners)) {
                $selected_learners = json_encode($request->learners);
            }

            $emailOutLog = [
                'course_id' => $id,
                'subject' => $subject,
                'message' => $message,
                'learners' => $selected_learners,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'attachment' => $attachment
            ];
            EmailOutLog::create($emailOutLog);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Mail sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Send email to the learners that haven't started the free course yet
     * @param $course_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notStartedCourseReminder($course_id, Request $request)
    {
        $course = Course::find($course_id);
        if ($course) {

            $this->validate($request,
                [
                    'subject' => 'required',
                    'message' => 'required'
                ]
            );

            // check courses taken that's not yet started with the specified course id
            $coursesTaken = CoursesTaken::whereHas('package', function($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })
            ->whereNull('started_at')
            ->get();

            $learners = [];

            // check if send to has value or if it's for testing purpose
            if ($request->send_to) {
                $email = $request->send_to;
                $user = User::where('email', $email)->first();

                if ($user) {
                    $encode_email = encrypt($email);
                    $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                    $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                    $search_string = [
                        '[login_link]', '[username]', '[password]'
                    ];
                    $replace_string = [
                        $loginLink, $email, $password
                    ];
                    $convert_message = str_replace($search_string, $replace_string, $request->message);

                    $emailData['email_subject'] = $request->subject;
                    $emailData['email_message'] = $convert_message;
                    $emailData['from_name'] = NULL;
                    $emailData['from_email'] = NULL;
                    $emailData['attach_file'] = NULL;
                    \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
                }
            } else {
                foreach($coursesTaken as $courseTaken) {
                    $encode_email = encrypt($courseTaken->user->email);
                    $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                    $convert_message = str_replace('[login_link]', $loginLink, $request->message);

                    $email = $courseTaken->user->email;
                    $emailData['email_subject'] = $request->subject;
                    $emailData['email_message'] = $convert_message;
                    $emailData['from_name'] = NULL;
                    $emailData['from_email'] = NULL;
                    $emailData['attach_file'] = NULL;
                    \Mail::to($email)->queue(new SubjectBodyEmail($emailData));

                    array_push($learners, $courseTaken->user->id);
                }

                $emailOutLog = [
                    'course_id' => $course_id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'learners' => json_encode($learners)
                ];
                EmailOutLog::create($emailOutLog);
            }
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Mail sent successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Export the learners to excel
     * @param $course_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function learnerListExcel($course_id)
    {
        $course = Course::find($course_id);
        if ($course) {
            $excel          = \App::make('excel');
            $learners       = $course->learners->get();
            $learnerList    = [];
            $learnerList[]  = ['id', 'learner', 'email']; // first row in excel

            // loop all the learners
            foreach ($learners as $learner) {
                $learnerList[] = [$learner->user->id, $learner->user->full_name, $learner->user->email];
            }

            $excel->create($course->title.' Learners', function($excel) use ($learnerList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                // prevent inserting an empty first row
                $sheet->fromArray($learnerList, null, 'A1', false, false);
            });
            })->download('xlsx');
        }
        return redirect()->back();
    }

    /**
     * Course with learners from included package
     * @param $course_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function learnerActiveListExcel($course_id)
    {
        $course = Course::find($course_id);
        if ($course) {
            $excel          = \App::make('excel');
            $packageIdsOfCourse = $course->packages()->pluck('id')->toArray();
            $packageCourses = PackageCourse::whereIn('included_package_id', $packageIdsOfCourse)->get()
                ->pluck('package_id')
                ->toArray();
            $packageCourses[] = 29; // add the actual package id of webinar-pakke

            $learnerWithCourse = CoursesTaken::whereIn('package_id', $packageCourses)
                ->where('end_date','>=', Carbon::now())
                ->groupBy('user_id')
                ->orderBy('updated_at', 'desc')
                ->get();

            $learnerList    = [];
            $learnerList[]  = ['id', 'learner', 'email']; // first row in excel

            // loop all the learners that have the course (included from other course)
            foreach ($learnerWithCourse as $learner) {
                $learnerList[] = [$learner->user->id, $learner->user->full_name, $learner->user->email];
            }

            $excel->create($course->title.' Active Learners', function($excel) use ($learnerList) {

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                    // prevent inserting an empty first row
                    $sheet->fromArray($learnerList, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

        return redirect()->back();
    }

}
