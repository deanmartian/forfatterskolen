<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\LessonDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Lesson;
use App\Http\Requests\LessonCreateRequest;
use App\Http\Requests\LessonUpdateRequest;

class LessonController extends Controller
{
   

    public function index($course_id)
    {
        $course = Course::findOrFail($course_id);
        $section = NULL;

        return view('backend.lesson.index', compact('course', 'section'));
    }

    public function edit($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id)->toArray();
        $videos = Lesson::findOrFail($id)->videos;
        $documents = Lesson::findOrFail($id)->documents;
        $section = NULL;

    	return view('backend.lesson.edit', compact('course', 'lesson', 'videos', 'section', 'documents'));
    }

    public function create($id)
    {
    	$course = Course::findOrFail($id);
    	$section = NULL;
        $lesson = [
            'title' => old('title'),
            'content' => old('content'),
            'delay' => old('delay'),
        ];
        $documents = [];

    	return view('backend.lesson.create', compact('course', 'lesson', 'section', 'documents'));
    }

    public function store($course_id, LessonCreateRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = new Lesson();
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->delay = $request->delay;
        $lesson->save();

        $destinationPath = 'storage/lesson-documents'; // upload path

        // allowed extensions
        $extensions = ['pdf', 'docx', 'xlsx'];

        if($request->hasFile('documents'))
        {
            $documents = $request->file('documents');
            foreach ($documents as $key => $document) {
                $document_name  = $document->getClientOriginalName();
                $extension      = pathinfo($document_name,PATHINFO_EXTENSION);

                if( in_array($extension, $extensions) ) {
                    $actual_name    = pathinfo($document_name, PATHINFO_FILENAME);
                    $fileName       = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
                    $expFileName    = explode('/', $fileName);
                    $document->move($destinationPath, end($expFileName));

                    $lesson_document            = new LessonDocuments();
                    $lesson_document->lesson_id = $lesson->id;
                    $lesson_document->name      = end($expFileName);
                    $lesson_document->document  = $fileName;
                    $lesson_document->save();
                }
            }
        }

        return redirect(route('admin.lesson.edit', ['course_id' => $lesson->id, 'id' => $course->id]));
    }


    public function update($course_id, $id, LessonUpdateRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->delay = $request->delay;
        $lesson->save();

        $destinationPath = 'storage/lesson-documents'; // upload path

        // allowed extensions
        $extensions = ['pdf', 'docx', 'xlsx'];

        if($request->hasFile('documents'))
        {
            $documents = $request->file('documents');
            foreach ($documents as $key => $document) {
                $document_name  = $document->getClientOriginalName();
                $extension      = pathinfo($document_name,PATHINFO_EXTENSION);

                if( in_array($extension, $extensions) ) {
                    $actual_name    = pathinfo($document_name, PATHINFO_FILENAME);
                    $fileName       = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
                    $expFileName    = explode('/', $fileName);
                    $document->move($destinationPath, end($expFileName));

                    $lesson_document            = new LessonDocuments();
                    $lesson_document->lesson_id = $lesson->id;
                    $lesson_document->name      = end($expFileName);
                    $lesson_document->document  = $fileName;
                    $lesson_document->save();
                }
            }
        }

        return redirect(route('admin.lesson.edit', ['course_id' => $lesson->id, 'id' => $course->id]));
    }

    public function destroy($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->forceDelete();

        return redirect(route('admin.course.show', $course->id).'?section=lessons');
    }


    public function save_order(Request $request)
    {
        $lessons = explode(',', $request->lesson_order);
        $i = 0;

        foreach( $lessons as $lesson ) :
            $lesson = Lesson::find($lesson);
            if( $lesson ) :
                $lesson->order = $i;
                $lesson->save();
                $i++;
            endif;
        endforeach;

        return redirect()->back();
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
     * Delete the lesson document
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLessonDocument($id)
    {
        $document = LessonDocuments::find($id);
        if ($document) {
            $document->forceDelete();
        }
        return redirect()->back();
    }
}
