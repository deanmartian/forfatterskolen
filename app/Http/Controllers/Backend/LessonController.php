<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\LessonContent;
use App\LessonDocuments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function index($course_id): View
    {
        $course = Course::findOrFail($course_id);
        $section = null;

        return view('backend.lesson.index', compact('course', 'section'));
    }

    public function edit($course_id, $id): View
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id)->toArray();
        $videos = Lesson::findOrFail($id)->videos;
        $documents = Lesson::findOrFail($id)->documents;
        $section = null;

        return view('backend.lesson.edit', compact('course', 'lesson', 'videos', 'section', 'documents'));
    }

    public function create($id): View
    {
        $course = Course::findOrFail($id);
        $section = null;
        $lesson = [
            'id' => '',
            'title' => old('title'),
            'content' => old('content'),
            'delay' => old('delay'),
            'whole_lesson_file' => '',
            'allow_lesson_download' => true,
        ];
        $documents = [];

        return view('backend.lesson.create', compact('course', 'lesson', 'section', 'documents'));
    }

    public function store($course_id, Request $request): RedirectResponse
    {

        $otherCourseReqFields = [
            'title' => 'required',
            'content' => 'required',
            'delay' => 'required|string|max:50',
        ];

        $webinarPakkeReqFields = [
            'title' => 'required',
            'delay' => 'required|string|max:50',
        ];

        $reqFields = $otherCourseReqFields;

        if ($course_id == 17) {
            $reqFields = $webinarPakkeReqFields;
        }

        $request->validate($reqFields);
        $wholeLessonFile = $this->uploadWholeFile($request);

        $course = Course::findOrFail($course_id);
        $lesson = new Lesson;
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->whole_lesson_file = $wholeLessonFile;
        $lesson->delay = $request->delay;
        $lesson->allow_lesson_download = $request->has('allow_lesson_download') && $request->allow_lesson_download ? 1 : 0;
        $lesson->save();

        $destinationPath = 'storage/lesson-documents'; // upload path

        // allowed extensions
        $extensions = ['pdf', 'docx', 'xlsx'];

        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            foreach ($documents as $key => $document) {
                $document_name = $document->getClientOriginalName();
                $extension = pathinfo($document_name, PATHINFO_EXTENSION);

                if (in_array($extension, $extensions)) {
                    $actual_name = pathinfo($document_name, PATHINFO_FILENAME);
                    $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                    $expFileName = explode('/', $fileName);
                    $document->move($destinationPath, end($expFileName));

                    $lesson_document = new LessonDocuments;
                    $lesson_document->lesson_id = $lesson->id;
                    $lesson_document->name = end($expFileName);
                    $lesson_document->document = $fileName;
                    $lesson_document->save();
                }
            }
        }

        return redirect(route('admin.lesson.edit', ['course_id' => $course->id, 'lesson' => $lesson->id]));
    }

    public function update($course_id, $id, Request $request): RedirectResponse
    {

        $otherCourseReqFields = [
            'title' => 'required',
            'content' => 'required',
            'delay' => 'required|string|max:50',
        ];

        $webinarPakkeReqFields = [
            'title' => 'required',
            'delay' => 'required|string|max:50',
        ];

        $reqFields = $otherCourseReqFields;

        if ($course_id == 17 && $id > 169) {
            $reqFields = $webinarPakkeReqFields;
        }

        $request->validate($reqFields);

        if ($request->has('whole_lesson_file')) {
            $wholeLessonFile = $this->uploadWholeFile($request);
        }

        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;

        if ($request->has('whole_lesson_file')) {
            $lesson->whole_lesson_file = $wholeLessonFile;
        }
        
        $lesson->delay = $request->delay;
        $lesson->allow_lesson_download = $request->has('allow_lesson_download') && $request->allow_lesson_download ? 1 : 0;
        $lesson->save();

        $destinationPath = 'storage/lesson-documents'; // upload path

        // allowed extensions
        $extensions = ['pdf', 'docx', 'xlsx'];

        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            foreach ($documents as $key => $document) {
                $document_name = $document->getClientOriginalName();
                $extension = pathinfo($document_name, PATHINFO_EXTENSION);

                if (in_array($extension, $extensions)) {
                    $actual_name = pathinfo($document_name, PATHINFO_FILENAME);
                    $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                    $expFileName = explode('/', $fileName);
                    $document->move($destinationPath, end($expFileName));

                    $lesson_document = new LessonDocuments;
                    $lesson_document->lesson_id = $lesson->id;
                    $lesson_document->name = end($expFileName);
                    $lesson_document->document = $fileName;
                    $lesson_document->save();
                }
            }
        }

        return redirect(route('admin.lesson.edit', ['course_id' => $course->id, 'lesson' => $lesson->id]));
    }

    public function destroy($course_id, $id): RedirectResponse
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->forceDelete();

        return redirect(route('admin.course.show', $course->id).'?section=lessons');
    }

    public function save_order(Request $request): RedirectResponse
    {
        $counter = $request->page - 1;
        $multiplier = 25;
        $lessons = explode(',', $request->lesson_order);
        $i = $counter * $multiplier;

        foreach ($lessons as $lesson) {
            $lesson = Lesson::find($lesson);
            if ($lesson) {
                $lesson->order = $i;
                $lesson->save();
                $i++;
            }
        }

        return redirect()->back();
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
     * Delete the lesson document
     */
    public function deleteLessonDocument($id): RedirectResponse
    {
        $document = LessonDocuments::find($id);
        if ($document) {
            $document->forceDelete();
        }

        return redirect()->back();
    }

    public function deleteLessonFile($lessonId): RedirectResponse
    {
        $lesson = Lesson::find($lessonId);

        if ($lesson) {

            $file = public_path($lesson->whole_lesson_file);
            if (\File::isFile($file)) {
                \File::delete($file);
            }

            $lesson->whole_lesson_file = null;
            $lesson->save();
        }

        return redirect()->back();
    }

    /**
     * Get the lesson content of a lesson
     */
    public function getLessonContent($lesson_id): JsonResponse
    {
        $lessonContent = LessonContent::where('lesson_id', $lesson_id)->get();

        return response()->json(['data' => $lessonContent]);
    }

    /**
     * Add a lesson content for a lesson
     */
    public function addContent($lesson_id, Request $request): RedirectResponse
    {
        if ($lesson = Lesson::find($lesson_id)) {
            $titles = $request->title;
            $tags = $request->tags;
            $date = $request->date;
            $description = $request->description;
            $videos = $request->lesson_video;
            $idList = $request->content_id;

            // check if title is not empty
            // $lesson->lessonContent()->delete();

            foreach ($titles as $k => $title) {
                if ($title) {
                    $insertContent = [
                        'title' => $title,
                        'tags' => $tags[$k],
                        'date' => $date[$k],
                        'description' => $description[$k],
                        'lesson_content' => $videos[$k],
                    ];

                    // check if ID is not empty then update the record
                    if ($idList[$k]) {
                        $lesson->lessonContent()->where('id', $idList[$k])->first()->update($insertContent);
                    } else {
                        $lesson->lessonContent()->create($insertContent);
                    }
                }
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Lesson content saved.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete a lesson content
     */
    public function deleteLessonContent($content_id): JsonResponse
    {
        if ($lesson_content = LessonContent::find($content_id)) {
            $lesson_content->delete();

            return response()->json(['success' => 'Lesson Content deleted.'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    private function uploadWholeFile(Request $request)
    {
        $wholeLessonFile = null;

        if ($request->hasFile('whole_lesson_file')) {
            $file = $request->file('whole_lesson_file');
            $extension = $file->getClientOriginalExtension();

            if (! in_array($extension, ['pdf'])) {
                $customErrors = ['manuscript' => 'The whole lesson file must be a file of type: pdf'];
                $validator = Validator::make([], []);
                $validator->validate(); // Perform validation without rules
                $validator->errors()->merge($customErrors);

                throw new ValidationException($validator);
            }

            $destinationPath = 'storage/lesson-whole-file'; // upload path
            $document_name = $file->getClientOriginalName();
            $actual_name = pathinfo($document_name, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $expFileName = explode('/', $fileName);
            $file->move($destinationPath, end($expFileName));

            $wholeLessonFile = $fileName;
        }

        return $wholeLessonFile;
    }
}
