<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\LessonContent;
use App\LessonAssignment;
use App\LessonQuiz;
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
        $lessonModel = Lesson::findOrFail($id);
        $lesson = $lessonModel->toArray();
        $videos = $lessonModel->videos;
        $documents = $lessonModel->documents;
        $quizzes = $lessonModel->quizzes()->get();
        $lessonAssignments = $lessonModel->lessonAssignments()->get();
        $section = null;

        return view('backend.lesson.edit', compact('course', 'lesson', 'videos', 'section', 'documents', 'quizzes', 'lessonAssignments'));
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

    public function saveQuiz($id, Request $request): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:1000',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string|max:500',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        $quiz = LessonQuiz::create([
            'lesson_id' => $lesson->id,
            'question' => $request->question,
            'options' => $request->options,
            'correct_option' => $request->correct_option,
            'order' => LessonQuiz::where('lesson_id', $lesson->id)->count(),
        ]);

        return response()->json(['success' => true, 'quiz' => $quiz]);
    }

    public function deleteQuiz($id): JsonResponse
    {
        $quiz = LessonQuiz::findOrFail($id);
        $quiz->answers()->delete();
        $quiz->delete();

        return response()->json(['success' => true]);
    }

    public function aiReview($id): JsonResponse
    {
        set_time_limit(120); // AI API can take 30-60 seconds

        $lesson = Lesson::findOrFail($id);
        $content = strip_tags(html_entity_decode($lesson->content ?? ''));

        if (mb_strlen($content) < 50) {
            return response()->json(['error' => 'Leksjonen har for lite innhold til å analysere'], 422);
        }

        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            return response()->json(['error' => 'ANTHROPIC_API_KEY er ikke konfigurert'], 500);
        }

        $excerpt = mb_substr($content, 0, 5000);
        $wordCount = str_word_count($content);

        // Check existing assignments and quizzes
        $lessonModel = Lesson::find($id);
        $existingAssignments = $lessonModel->lessonAssignments()->pluck('question_text')->toArray();
        $existingQuizzes = $lessonModel->quizzes()->pluck('question')->toArray();

        $assignmentInfo = count($existingAssignments) > 0
            ? "Eksisterende oppgaver i systemet:\n" . implode("\n", array_map(fn($a) => "- {$a}", $existingAssignments))
            : "Ingen oppgaver er lagt inn i systemet ennå.";

        $quizInfo = count($existingQuizzes) > 0
            ? "Eksisterende quiz-spørsmål i systemet:\n" . implode("\n", array_map(fn($q) => "- {$q}", $existingQuizzes))
            : "Ingen quiz-spørsmål er lagt inn i systemet ennå.";

        $systemPrompt = "Du er en erfaren redaktør og pedagogisk konsulent for Forfatterskolen (norsk skrivelærer-portal). "
            . "Analyser leksjonsteksten og gi KONKRETE endringsforslag.\n\n"
            . "Returner KUN JSON (ingen annen tekst) med dette formatet:\n"
            . "{\n"
            . "  \"score\": 7,\n"
            . "  \"summary\": \"Kort vurdering av kvaliteten (2-3 setninger)\",\n"
            . "  \"changes\": [\n"
            . "    {\n"
            . "      \"type\": \"replace\",\n"
            . "      \"original\": \"Den eksakte originalteksten som bør endres (kopier ordrett fra teksten)\",\n"
            . "      \"suggested\": \"Foreslått ny tekst\",\n"
            . "      \"reason\": \"Kort forklaring på hvorfor\"\n"
            . "    },\n"
            . "    {\n"
            . "      \"type\": \"add\",\n"
            . "      \"after\": \"Teksten dette skal legges til etter\",\n"
            . "      \"suggested\": \"Ny tekst som bør legges til\",\n"
            . "      \"reason\": \"Kort forklaring\"\n"
            . "    },\n"
            . "    {\n"
            . "      \"type\": \"delete\",\n"
            . "      \"original\": \"Tekst som bør fjernes\",\n"
            . "      \"reason\": \"Kort forklaring\"\n"
            . "    }\n"
            . "  ]\n"
            . "}\n\n"
            . "Regler:\n"
            . "- Gi MINST 4-6 konkrete endringsforslag — du MÅ finne noe å forbedre\n"
            . "- «original»-feltet MÅ være eksakt tekst fra leksjonen (kopier ordrett 1-3 setninger)\n"
            . "- «suggested»-feltet MÅ være den forbedrede versjonen av HELE den kopierte teksten\n"
            . "- Fokuser på: skrivefeil, uklare formuleringer, bedre pedagogisk flyt, manglende overganger, engasjement, bedre eksempler\n"
            . "- Foreslå også nye avsnitt der det mangler (type: add)\n"
            . "- Ikke endre faglig innhold eller meninger, bare språk og struktur\n"
            . "- Alt på norsk. Vær konkret og konstruktiv.\n\n"
            . "I tillegg: sjekk om det finnes oppgaver i leksjonsteksten (ofte markert med «Oppgaver:» eller nummerert liste). "
            . "Sammenlign med oppgavene og quizene som allerede er lagt inn i systemet (listet under). "
            . "Rapporter dette i et eget «tasks»-felt i JSON:\n"
            . "\"tasks\": {\n"
            . "  \"found_in_text\": [\"Oppgave funnet i teksten\", ...],\n"
            . "  \"missing_in_system\": [\"Oppgave som finnes i teksten men IKKE i systemet\", ...],\n"
            . "  \"has_quiz\": true/false,\n"
            . "  \"suggestion\": \"Anbefaling om oppgaver/quiz\"\n"
            . "}";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(45)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 3000,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => "Leksjonstekst ({$wordCount} ord):\n\n{$excerpt}\n\n---\n\n{$assignmentInfo}\n\n{$quizInfo}"],
                ],
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'AI API feilet: ' . $response->status()], 500);
            }

            $aiText = $response->json('content.0.text', '');

            if (preg_match('/\{[\s\S]*\}/m', $aiText, $matches)) {
                $parsed = json_decode($matches[0], true);
                if ($parsed) {
                    return response()->json(['success' => true, 'review' => $parsed]);
                }
            }

            return response()->json(['error' => 'Kunne ikke parse AI-respons'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Feil: ' . $e->getMessage()], 500);
        }
    }

    public function aiGenerate($id, Request $request): JsonResponse
    {
        set_time_limit(120);

        $lesson = Lesson::findOrFail($id);
        $content = strip_tags(html_entity_decode($lesson->content ?? ''));

        if (mb_strlen($content) < 50) {
            return response()->json(['error' => 'Leksjonen har for lite innhold til å generere oppgaver'], 422);
        }

        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            return response()->json(['error' => 'ANTHROPIC_API_KEY er ikke konfigurert'], 500);
        }

        $excerpt = mb_substr($content, 0, 4000);

        $systemPrompt = "Du er en erfaren skrivelærer ved Forfatterskolen. "
            . "Analyser leksjonsteksten og generer oppgaver og quiz-spørsmål.\n\n"
            . "Returner JSON med følgende format (kun JSON, ingen annen tekst):\n"
            . "{\n"
            . "  \"assignments\": [\n"
            . "    {\"question_text\": \"Oppgavetekst her (kreativ skriveoppgave)\"}\n"
            . "  ],\n"
            . "  \"quizzes\": [\n"
            . "    {\"question\": \"Spørsmål?\", \"options\": [\"Alt A\", \"Alt B\", \"Alt C\", \"Alt D\"], \"correct_option\": 0}\n"
            . "  ]\n"
            . "}\n\n"
            . "Regler:\n"
            . "- Finn eksisterende oppgaver i teksten (ofte markert med 'Oppgaver:' eller nummerert liste)\n"
            . "- Lag 2-3 kreative skriveoppgaver basert på leksjonsinnholdet\n"
            . "- Lag 2-3 quiz-spørsmål (flervalg) som tester forståelse av stoffet\n"
            . "- Alt på norsk\n"
            . "- correct_option er 0-indeksert (0=A, 1=B, 2=C, 3=D)";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(45)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 2000,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => "Leksjonstekst:\n\n{$excerpt}"],
                ],
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'AI API feilet: ' . $response->status()], 500);
            }

            $aiText = $response->json('content.0.text', '');

            // Extract JSON from response (might be wrapped in ```json...```)
            if (preg_match('/\{[\s\S]*\}/m', $aiText, $matches)) {
                $parsed = json_decode($matches[0], true);
                if ($parsed) {
                    return response()->json(['success' => true, 'data' => $parsed]);
                }
            }

            return response()->json(['error' => 'Kunne ikke parse AI-respons'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Feil: ' . $e->getMessage()], 500);
        }
    }

    public function saveLessonAssignment($id, Request $request): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'question_text' => 'required|string|max:2000',
        ]);

        $assignment = LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'question_text' => $request->question_text,
            'order' => LessonAssignment::where('lesson_id', $lesson->id)->count(),
        ]);

        return response()->json(['success' => true, 'assignment' => $assignment]);
    }

    public function deleteLessonAssignment($id): JsonResponse
    {
        $assignment = LessonAssignment::findOrFail($id);
        $assignment->submissions()->delete();
        $assignment->delete();

        return response()->json(['success' => true]);
    }
}
