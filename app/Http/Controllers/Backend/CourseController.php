<?php
namespace App\Http\Controllers\Backend;

use App\CoursesTaken;
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
            'start_date' => '',
            'end_date' => '',
            'display_order' => '',
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
        $course->display_order = is_numeric($request->display_order) ? $request->display_order : 0;

        if ($request->hasFile('course_image') && $request->file('course_image')->isValid()) :
            $image = substr($course->course_image, 1);
            if( File::exists($image) ) :
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

    public function sendEmailToLearners($id, Request $request)
    {
        $course = Course::find($id);
        if ($course) {

            $learners   = $course->learners->get();
            $subject    = $request->subject;
            $message    = nl2br($request->message);
            $from       = 'post@forfatterskolen.no';

            foreach($learners as $learner) {
                $email = $learner->user->email;
                //AdminHelpers::send_mail($email, $subject, $message, $from);
                AdminHelpers::send_email($subject,
                    'post@forfatterskolen.no', $email, $message);
            }
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
