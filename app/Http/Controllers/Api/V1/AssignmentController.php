<?php

namespace App\Http\Controllers\Api\V1;

use App\Assignment;
use App\AssignmentAddon;
use App\AssignmentFeedback;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentLearnerConfiguration;
use App\AssignmentLearnerSubmissionDate;
use App\AssignmentManuscript;
use App\CoursesTaken;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Services\FileIntegrityService;
use Carbon\Carbon;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

include_once base_path('Docx2Text.php');
include_once base_path('Pdf2Text.php');
include_once base_path('Odt2Text.php');

class AssignmentController extends ApiController
{
    protected FileIntegrityService $fileIntegrityService;

    public function __construct(FileIntegrityService $fileIntegrityService)
    {
        $this->fileIntegrityService = $fileIntegrityService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($user->isDisabled) {
            return response()->json(['data' => []]);
        }

        $assignments = [];
        $addOns = AssignmentAddon::where('user_id', $user->id)->pluck('assignment_id')->toArray();
        $assignmentSubmissionDates = AssignmentLearnerSubmissionDate::where('user_id', $user->id)
            ->pluck('submission_date', 'assignment_id');

        $coursesTaken = $user->coursesTaken()->whereNotNull('end_date')->get()
            ->filter(function (CoursesTaken $courseTaken) {
                return ! $courseTaken->is_disabled;
            });

        foreach ($coursesTaken as $courseTaken) {
            $course = $courseTaken->package->course;
            $packageId = $courseTaken->package->id;

            foreach ($course->activeAssignments as $assignment) {
                $allowedPackage = json_decode($assignment->allowed_package, true);
                $assignmentDisabledLearners = $assignment->disabledLearners()->pluck('user_id')->toArray();
                $assignmentsWithUserManuscript = $assignment->manuscripts()
                    ->where('user_id', $user->id)
                    ->pluck('assignment_id')
                    ->toArray();

                if (((! is_null($allowedPackage) && in_array($packageId, $allowedPackage)) || is_null($allowedPackage)
                        || in_array($assignment->id, $addOns) || in_array($assignment->id, $assignmentsWithUserManuscript))
                    && ! in_array($courseTaken->user_id, $assignmentDisabledLearners)) {
                    $assignmentManuscript = AssignmentManuscript::where('user_id', $user->id)
                        ->where('assignment_id', $assignment->id)
                        ->first();

                    if (! $assignmentManuscript || ($assignmentManuscript && ! $assignmentManuscript->locked
                        && ! $assignmentManuscript->has_feedback)) {
                        if (! AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                            if ($course->type == 'Single' && $assignment->submission_date == '365') {
                                if (Carbon::parse($courseTaken->end_date)->gt(Carbon::now())) {
                                    $assignments[] = $this->formatAssignment($assignment, $user, $courseTaken);
                                }
                            } else {
                                if (Carbon::parse($courseTaken->started_at)->addDays((int) $assignment->submission_date)
                                    ->gt(Carbon::now())) {
                                    $assignments[] = $this->formatAssignment($assignment, $user, $courseTaken);
                                }
                            }
                        } else {
                            $assignmentSubmissionDate = $assignmentSubmissionDates[$assignment->id]
                                ?? $assignment->submission_date;

                            if (Carbon::parse($assignmentSubmissionDate)->gt(Carbon::now()->subDay())
                                && Carbon::parse($courseTaken->end_date)->gt(Carbon::now())) {
                                $assignments[] = $this->formatAssignment($assignment, $user, $courseTaken);
                            }
                        }
                    }
                }
            }
        }

        foreach ($user->activeAssignments as $assignment) {
            $manuscript = $assignment->manuscripts->first();
            $feedback = null;

            if ($manuscript) {
                $feedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript->id)
                    ->where('is_active', 1)
                    ->first();
            }

            if (! $feedback && (! $manuscript || ! $manuscript->locked)) {
                if (Carbon::parse($assignment->submission_date)->gt(Carbon::now())) {
                    $assignments[] = $this->formatAssignment($assignment, $user, null);
                }
            }
        }

        return response()->json(['data' => $assignments]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Assignment not found.', 'not_found', 404);
        }

        $assignment = Assignment::find((int) $id);

        if (! $assignment) {
            return $this->errorResponse('Assignment not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($assignment->parent === 'users') {
            if ((int) $assignment->parent_id !== (int) $user->id) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }
        } else {
            $course = $assignment->course;

            if (! $course || ! $this->userOwnsCourse($user, $course)) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }
        }

        return response()->json([
            'data' => $this->formatAssignment($assignment, $user, null),
        ]);
    }

    public function submit(Request $request, $id): JsonResponse
    {
        $assignment = Assignment::find($id);

        if (! $assignment) {
            return $this->errorResponse('Assignment not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($assignment->parent === 'users') {
            if ((int) $assignment->parent_id !== (int) $user->id) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }
        } else {
            if ($user->isDisabled) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }

            $course = $assignment->course;

            if (! $course || ! $this->userOwnsCourse($user, $course)) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }

            $courseTaken = $user->coursesTaken()
                ->whereIn('package_id', $course->packages()->pluck('id'))
                ->first();

            if (! $courseTaken || $courseTaken->is_disabled) {
                return $this->errorResponse('You do not have access to this assignment.', 'forbidden', 403);
            }
        }

        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($assignmentManuscript) {
            if ($assignmentManuscript->locked) {
                return $this->errorResponse('Submission is already locked.', 'submission_locked', 409);
            }

            return $this->errorResponse('Submission already exists.', 'submission_exists', 409);
        }

        if (! $request->hasFile('filename') || ! $request->file('filename')->isValid()) {
            return $this->errorResponse('Missing or invalid file.', 'invalid_file', 422);
        }

        $destinationPath = 'storage/assignment-manuscripts/';
        $extensions = ['doc', 'docx', 'odt', 'pdf'];

        if ($assignment->for_editor) {
            $extensions = ['docx', 'doc'];
        }

        $extension = strtolower($request->file('filename')->getClientOriginalExtension());

        if (! in_array($extension, $extensions)) {
            return $this->errorResponse(
                'Invalid file format. Allowed formats are DOC, DOCX, ODT, PDF.',
                'invalid_file_format',
                422
            );
        }

        $actualName = $user->id;
        $fileName = AdminHelpers::checkFileName($destinationPath, $actualName, $extension);
        $expFileName = explode('/', $fileName);
        $storedFileName = end($expFileName);

        $uploadedFiles = [];

        $request->file('filename')->move($destinationPath, $storedFileName);
        $absolutePath = $this->resolveUploadedFilePath($destinationPath, $storedFileName);

        $uploadedFiles[] = ['absolute' => $absolutePath];

        if (! $this->fileIntegrityService->passes($absolutePath, $extension)) {
            $this->cleanupUploadedFiles($uploadedFiles);

            return $this->errorResponse(
                'The uploaded file appears to be invalid or corrupted.',
                'invalid_file',
                422
            );
        }

        $wordCount = $this->extractWordCount($extension, $destinationPath.end($expFileName));
        $wordToDeduct = $wordCount * 0.02;
        $newWordCount = (int) ceil($wordCount - $wordToDeduct);

        $assignmentMaxWords = $assignment->allow_up_to > 0 ? $assignment->allow_up_to : $assignment->max_words;
        $assignmentConfigurator = AssignmentLearnerConfiguration::where('user_id', $user->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        if ($assignmentConfigurator) {
            $assignmentMaxWords = $assignmentConfigurator->max_words;
        }

        if ($newWordCount > $assignmentMaxWords && $assignment->check_max_words) {
            $this->cleanupUploadedFiles($uploadedFiles);

            return $this->errorResponse('Word count exceeds maximum.', 'max_words_exceeded', 422, [
                'max_words' => $assignmentMaxWords,
                'word_count' => $newWordCount,
            ]);
        }

        $joinGroup = 0;
        if ($assignment->show_join_group_question) {
            $joinGroup = $request->boolean('join_group') ? 1 : 0;
        }

        $letterToEditor = null;
        if ($assignment->send_letter_to_editor && $request->hasFile('letter_to_editor')
            && $request->file('letter_to_editor')->isValid()) {
            $destinationPathLetter = 'storage/letter-to-editor';
            $extensionLetter = strtolower($request->file('letter_to_editor')->getClientOriginalExtension());
            $actualNameLetter = time();

            if (! in_array($extensionLetter, $extensions)) {
                $this->cleanupUploadedFiles($uploadedFiles);

                return $this->errorResponse(
                    'Invalid file format. Allowed formats are DOC, DOCX, ODT, PDF.',
                    'invalid_file_format',
                    422
                );
            }

            $fileNameLetter = AdminHelpers::checkFileName($destinationPathLetter, $actualNameLetter, $extensionLetter);
            $expFileNameLetter = explode('/', $fileNameLetter);
            $storedLetterName = end($expFileNameLetter);

            $request->file('letter_to_editor')->move($destinationPathLetter, $storedLetterName);
            $letterAbsolutePath = $this->resolveUploadedFilePath($destinationPathLetter, $storedLetterName);
            $uploadedFiles[] = ['absolute' => $letterAbsolutePath];

            if (! $this->fileIntegrityService->passes($letterAbsolutePath, $extensionLetter)) {
                $this->cleanupUploadedFiles($uploadedFiles);

                return $this->errorResponse(
                    'The uploaded file appears to be invalid or corrupted.',
                    'invalid_file',
                    422
                );
            }

            $letterToEditor = '/'.$fileNameLetter;
        }

        $editorId = $assignment->editor_id ? $assignment->editor_id
            : ($assignment->assigned_editor ? $assignment->assigned_editor : 0);

        $submittedManuscript = AssignmentManuscript::create([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'filename' => '/'.$destinationPath.end($expFileName),
            'words' => $wordCount,
            'type' => $request->input('type'),
            'manu_type' => $request->input('manu_type'),
            'join_group' => $joinGroup,
            'letter_to_editor' => $letterToEditor,
            'editor_id' => $editorId,
            'uploaded_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'id' => $submittedManuscript->id,
                'assignment_id' => $submittedManuscript->assignment_id,
                'uploaded_at' => $submittedManuscript->uploaded_at,
                'word_count' => $wordCount,
            ],
        ], 201);
    }

    public function downloadSubmission(Request $request, $id): JsonResponse|BinaryFileResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $manuscript = AssignmentManuscript::find($id);

        if (! $manuscript) {
            return $this->errorResponse('Submission not found.', 'not_found', 404);
        }

        if ((int) $manuscript->user_id !== (int) $user->id) {
            return $this->errorResponse('You do not have access to this submission.', 'forbidden', 403);
        }

        $filename = $manuscript->filename;
        $path = public_path($filename);

        if (! File::exists($path)) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        return response()->download($path);
    }

    public function downloadFeedback(Request $request, $id): JsonResponse|BinaryFileResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $today = Carbon::today();

        $feedback = AssignmentFeedbackNoGroup::find($id);
        if ($feedback) {
            if ((int) $feedback->learner_id !== (int) $user->id) {
                return $this->errorResponse('You do not have access to this feedback.', 'forbidden', 403);
            }

            if (! $feedback->is_active) {
                return $this->errorResponse('Feedback not available.', 'forbidden', 403);
            }

            if ($feedback->availability && Carbon::parse($feedback->availability)->gt($today)) {
                return $this->errorResponse('Feedback not available.', 'forbidden', 403);
            }

            $zipName = optional($feedback->manuscript->assignment)->title.' Feedbacks.zip';

            return $this->downloadFeedbackFiles($feedback->filename, $zipName);
        }

        $groupFeedback = AssignmentFeedback::find($id);
        if ($groupFeedback) {
            $groupLearner = $groupFeedback->assignment_group_learner;
            if (! $groupLearner || (int) $groupLearner->user_id !== (int) $user->id) {
                return $this->errorResponse('You do not have access to this feedback.', 'forbidden', 403);
            }

            if (! $groupFeedback->is_active) {
                return $this->errorResponse('Feedback not available.', 'forbidden', 403);
            }

            if ($groupFeedback->availability && Carbon::parse($groupFeedback->availability)->gt($today)) {
                return $this->errorResponse('Feedback not available.', 'forbidden', 403);
            }

            $zipName = optional($groupFeedback->assignment_group_learner->group)->title.' Feedbacks.zip';

            return $this->downloadFeedbackFiles($groupFeedback->filename, $zipName);
        }

        return $this->errorResponse('Feedback not found.', 'not_found', 404);
    }

    protected function formatAssignment(Assignment $assignment, $user, ?CoursesTaken $courseTaken): array
    {
        $course = $assignment->course;

        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'course' => $course ? [
                'id' => $course->id,
                'title' => $course->title,
            ] : null,
            'parent' => $assignment->parent,
            'parent_id' => $assignment->parent_id,
            'submission_date' => $assignment->getRawOriginal('submission_date'),
            'available_date' => $assignment->getRawOriginal('available_date'),
            'max_words' => $assignment->max_words,
            'allow_up_to' => $assignment->allow_up_to,
            'check_max_words' => (bool) $assignment->check_max_words,
            'for_editor' => (bool) $assignment->for_editor,
            'course_taken_end_date' => $courseTaken ? $courseTaken->end_date : null,
            'submission' => $this->assignmentSubmissionSummary($assignment, $user),
        ];
    }

    protected function assignmentSubmissionSummary(Assignment $assignment, $user): ?array
    {
        $manuscript = AssignmentManuscript::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (! $manuscript) {
            return null;
        }

        return [
            'id' => $manuscript->id,
            'locked' => (bool) $manuscript->locked,
            'has_feedback' => (bool) $manuscript->has_feedback,
            'status' => $manuscript->status,
            'words' => $manuscript->words,
            'uploaded_at' => $manuscript->uploaded_at,
        ];
    }

    protected function resolveUploadedFilePath(string $destinationPath, string $fileName): string
    {
        $relativeDirectory = trim($destinationPath, '/');
        $relativePath = $relativeDirectory === '' ? $fileName : $relativeDirectory.'/'.$fileName;

        return public_path($relativePath);
    }

    protected function cleanupUploadedFiles(array $files): void
    {
        foreach ($files as $file) {
            if (isset($file['absolute']) && $file['absolute'] && File::exists($file['absolute'])) {
                File::delete($file['absolute']);
            }
        }
    }

    protected function extractWordCount(string $extension, string $path): int
    {
        $wordCount = 0;

        if ($extension === 'pdf') {
            $pdf = new \PdfToText($path);
            $wordCount = FrontendHelpers::get_num_of_words($pdf->Text);
        } elseif ($extension === 'docx') {
            $docObj = new \Docx2Text($path);
            $docText = $docObj->convertToText();
            $wordCount = FrontendHelpers::get_num_of_words($docText);
        } elseif ($extension === 'doc') {
            $docText = FrontendHelpers::readWord($path);
            $wordCount = FrontendHelpers::get_num_of_words($docText);
        } elseif ($extension === 'odt') {
            $doc = odt2text($path);
            $wordCount = FrontendHelpers::get_num_of_words($doc);
        }

        return $wordCount;
    }

    protected function downloadFeedbackFiles(string $filename, string $zipFileName): JsonResponse|BinaryFileResponse
    {
        $files = array_values(array_filter(array_map('trim', explode(',', $filename))));

        if (count($files) <= 1) {
            $singleFile = Arr::first($files);

            if (! $singleFile) {
                return $this->errorResponse('File not found.', 'not_found', 404);
            }

            $path = public_path($singleFile);
            if (! File::exists($path)) {
                return $this->errorResponse('File not found.', 'not_found', 404);
            }

            return response()->download($path);
        }

        $publicDir = public_path('storage');
        $zip = new ZipArchive;

        if ($zip->open($publicDir.'/'.$zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return $this->errorResponse('Unable to create zip.', 'zip_error', 500);
        }

        foreach ($files as $file) {
            $path = public_path($file);
            if (File::exists($path)) {
                $expFileName = explode('/', $file);
                $zip->addFile($path, end($expFileName));
            }
        }

        $zip->close();
        $fileToPath = $publicDir.'/'.$zipFileName;

        if (! File::exists($fileToPath)) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        return response()->download($fileToPath, $zipFileName, [
            'Content-Type' => 'application/octet-stream',
        ])->deleteFileAfterSend(true);
    }
}
