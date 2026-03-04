<?php

namespace App\Http\Controllers\Api\V1;

use App\Assignment;
use App\AssignmentAddon;
use App\AssignmentFeedback;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentGroup;
use App\AssignmentGroupLearner;
use App\AssignmentLearnerConfiguration;
use App\AssignmentLearnerSubmissionDate;
use App\AssignmentManuscript;
use App\CoursesTaken;
use App\Genre;
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

        $assignments = [];
        $expiredAssignments = [];
        $upcomingAssignments = [];
        $waitingForResponse = [];
        $waitingForResponseIds = [];
        $noWordLimitAssignments = [];

        $addOns = AssignmentAddon::where('user_id', $user->id)->pluck('assignment_id')->toArray();
        $assignmentGroupLearners = AssignmentGroupLearner::with(['group.assignment.course'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function (AssignmentGroupLearner $assignmentGroupLearner) use ($user) {
                $payload = $assignmentGroupLearner->toArray();
                $assignment = optional($assignmentGroupLearner->group)->assignment;

                $payload['submission'] = $assignment
                    ? $this->assignmentSubmissionSummary($assignment, $user)
                    : null;

                return $payload;
            })
            ->values();
        $assignmentSubmissionDates = AssignmentLearnerSubmissionDate::where('user_id', $user->id)
            ->pluck('submission_date', 'assignment_id');
        $assignmentMaxWords = AssignmentLearnerConfiguration::where('user_id', $user->id)
            ->pluck('max_words', 'assignment_id');
        $noGroupWithFeedback = AssignmentFeedbackNoGroup::with(['manuscript.assignment.course'])
            ->where('learner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (AssignmentFeedbackNoGroup $feedback) {
                $manuscript = $feedback->manuscript;
                $assignment = $manuscript ? $manuscript->assignment : null;
                $course = $assignment ? $assignment->course : null;

                return [
                    'id' => $feedback->id,
                    'filename' => $feedback->filename,
                    'is_admin' => (bool) $feedback->is_admin,
                    'is_active' => (bool) $feedback->is_active,
                    'availability' => $feedback->availability,
                    'manuscript' => $manuscript ? [
                        'id' => $manuscript->id,
                        'status' => $manuscript->status,
                        'file_link_with_download' => $manuscript->file_link_with_download,
                        'assignment' => $assignment ? [
                            'id' => $assignment->id,
                            'title' => $assignment->title,
                            'course' => $course ? [
                                'id' => $course->id,
                                'title' => $course->title,
                            ] : null,
                        ] : null,
                    ] : null,
                ];
            })
            ->values();

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
                                    $payload = $this->formatAssignment($assignment, $user, $courseTaken, false);
                                    if ($assignment->max_words === 0) {
                                        $noWordLimitAssignments[] = $payload;
                                    } else {
                                        $assignments[] = $payload;
                                    }
                                }
                            } else {
                                if (Carbon::parse($courseTaken->started_at)->addDays((int) $assignment->submission_date)
                                    ->gt(Carbon::now())) {
                                    $payload = $this->formatAssignment($assignment, $user, $courseTaken, false);
                                    if ($assignment->max_words === 0) {
                                        $noWordLimitAssignments[] = $payload;
                                    } else {
                                        $assignments[] = $payload;
                                    }
                                }
                            }
                        } else {
                            $assignmentSubmissionDate = $assignmentSubmissionDates[$assignment->id]
                                ?? $assignment->submission_date;

                            if (Carbon::parse($assignmentSubmissionDate)->gt(Carbon::now()->subDay())
                                && Carbon::parse($courseTaken->end_date)->gt(Carbon::now())) {
                                $payload = $this->formatAssignment($assignment, $user, $courseTaken, false);
                                if ($assignment->max_words === 0) {
                                    $noWordLimitAssignments[] = $payload;
                                } else {
                                    $assignments[] = $payload;
                                }
                            }
                        }
                    }

                    if ($assignmentManuscript && $assignmentManuscript->locked && ! $assignmentManuscript->has_feedback
                        && ! $assignment->for_editor) {
                        $waitingForResponse[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                        $waitingForResponseIds[] = $assignment->id;
                    }
                }
            }

            foreach ($course->expiredAssignments as $assignment) {
                $allowedPackage = json_decode($assignment->allowed_package, true);
                if ((! is_null($allowedPackage) && in_array($packageId, $allowedPackage))
                    || is_null($allowedPackage)
                    || in_array($assignment->id, $addOns)) {
                    $waitingForResponseManuscript = AssignmentManuscript::where('user_id', $user->id)
                        ->where('editor_id', '!=', 0)
                        ->where('locked', 1)
                        ->where('status', 0)
                        ->where('assignment_id', $assignment->id)
                        ->first();

                    if ($waitingForResponseManuscript && ! in_array($assignment->id, $waitingForResponseIds)) {
                        $waitingForResponse[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                    }

                    if (! AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                        if ($course->type == 'Single' && $assignment->submission_date == '365') {
                            if (Carbon::parse($courseTaken->end_date)->lt(Carbon::now())) {
                                $expiredAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                            }
                        } else {
                            if (Carbon::parse($courseTaken->started_at)->addDays((int) $assignment->submission_date)
                                ->lt(Carbon::now())) {
                                $expiredAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                            }
                        }
                    } else {
                        $assignmentManuscript = AssignmentManuscript::where('user_id', $user->id)
                            ->where('assignment_id', $assignment->id)
                            ->first();

                        if (Carbon::parse($assignment->submission_date)->lt(Carbon::now())) {
                            if ($course->type == 'Group') {
                                if ($assignmentManuscript) {
                                    $assignmentFeedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $assignmentManuscript->id)->first();
                                    $assignmentGroups = $assignment->groups()->pluck('id')->toArray();
                                    $userAssignmentGroupLearner = AssignmentGroupLearner::where('user_id', $user->id)
                                        ->whereIn('assignment_group_id', $assignmentGroups)
                                        ->first();

                                    if (($assignmentFeedback && $assignmentManuscript->status > 0) || $userAssignmentGroupLearner) {
                                        $expiredAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                                    }
                                } else {
                                    $expiredAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                                }
                            } else {
                                $expiredAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
                            }
                        }

                        if (! $assignmentManuscript
                            && Carbon::parse($assignment->submission_date)->gt(Carbon::now())
                            && Carbon::parse($assignment->available_date)->gt(Carbon::now())) {
                            $upcomingAssignments[] = $this->formatAssignment($assignment, $user, $courseTaken, false);
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

            if (! $feedback) {
                if ($manuscript && $manuscript->locked) {
                    $waitingForResponse[] = $this->formatAssignment($assignment, $user, null, false);
                } elseif (Carbon::parse($assignment->submission_date)->gt(Carbon::now())) {
                    $assignments[] = $this->formatAssignment($assignment, $user, null, false);
                }
            }
        }

        foreach ($user->expiredAssignments as $assignment) {
            $manuscript = $assignment->manuscripts->first();
            $feedback = null;

            if ($manuscript) {
                $feedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript->id)
                    ->where('is_active', 1)
                    ->first();
            }

            if ($feedback) {
                $expiredAssignments[] = $this->formatAssignment($assignment, $user, null, false);
            }
        }

        $upcomingPersonalAssignments = Assignment::where('parent', 'users')
            ->where('parent_id', $user->id)
            ->where('submission_date', '>=', Carbon::now())
            ->where('available_date', '>', Carbon::now())
            ->oldest('submission_date')
            ->get();

        foreach ($upcomingPersonalAssignments as $assignment) {
            $upcomingAssignments[] = $this->formatAssignment($assignment, $user, null, false);
        }

        $expiredAssignments = collect($expiredAssignments)
            ->sortByDesc('created_at')
            ->unique('id')
            ->values()
            ->all();

        $expiredById = collect($expiredAssignments)->pluck('id')->flip();
        $waitingForResponse = collect($waitingForResponse)
            ->reject(function ($assignment) use ($expiredById) {
                return $expiredById->has($assignment['id']);
            })
            ->unique('id')
            ->values()
            ->all();

        $upcomingAssignments = collect($upcomingAssignments)
            ->sortBy('submission_date')
            ->unique('id')
            ->values()
            ->all();

        $assignments = $user->isDisabled ? [] : collect($assignments)->unique('id')->values()->all();
        $noWordLimitAssignments = $user->isDisabled ? [] : collect($noWordLimitAssignments)->unique('id')->values()->all();

        return response()->json([
            'data' => [
                'assignments' => $assignments,
                'expiredAssignments' => $expiredAssignments,
                'upcomingAssignments' => $upcomingAssignments,
                'waitingForResponse' => $waitingForResponse,
                'assignmentGroupLearners' => $assignmentGroupLearners,
                'noWordLimitAssignments' => $noWordLimitAssignments,
                'assignmentSubmissionDates' => $assignmentSubmissionDates,
                'assignmentMaxWords' => $assignmentMaxWords,
                'noGroupWithFeedback' => $noGroupWithFeedback,
            ],
        ]);
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
            'data' => $this->formatAssignment($assignment, $user, null, true),
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

        $type = $request->input('type');

        if ($type === null || $type === '') {
            return $this->errorResponse('Type is required.', 'validation_error', 422, [
                'field' => 'type',
            ]);
        }

        if (! is_numeric($type) || ! Genre::find((int) $type)) {
            return $this->errorResponse('Invalid type.', 'validation_error', 422, [
                'field' => 'type',
            ]);
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
            'type' => (int) $type,
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

    public function groupShowDetails(Request $request, $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $group = AssignmentGroup::where('id', $id)
            ->whereHas('learners', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (! $group) {
            return $this->errorResponse('Assignment group not found.', 'not_found', 404);
        }

        $groupLearners = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', '!=', $user->id);

        $groupLearner = AssignmentGroupLearner::where('assignment_group_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $groupLearner) {
            return $this->errorResponse('Assignment group membership not found.', 'not_found', 404);
        }

        $otherLearnersIdList = $groupLearners->pluck('id')->toArray();
        $couldSendFeedbackTo = $groupLearner->could_send_feedback_to_id_list ?: $otherLearnersIdList;
        $assignmentManuscript = AssignmentManuscript::where('assignment_id', $group->assignment_id)
            ->where('user_id', $user->id)
            ->first();

        $couldSendFeedbackTo[] = $groupLearner->id;

        $groupLearners = AssignmentGroupLearner::with('user:id,first_name,last_name')
            ->where('assignment_group_id', $id)
            ->whereIn('id', $couldSendFeedbackTo)
            ->orderBy('created_at', 'desc')
            ->get();

        $manuscriptsByUserId = AssignmentManuscript::where('assignment_id', $group->assignment_id)
            ->whereIn('user_id', $groupLearners->pluck('user_id')->all())
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($manuscripts) {
                $manuscript = $manuscripts->first();

                return [
                    'id' => $manuscript->id,
                    'assignment_id' => $manuscript->assignment_id,
                    'user_id' => $manuscript->user_id,
                    'filename' => $manuscript->filename,
                    'status' => $manuscript->status,
                    'locked' => (bool) $manuscript->locked,
                    'has_feedback' => (bool) $manuscript->has_feedback,
                    'words' => $manuscript->words,
                    'type' => $manuscript->type,
                    'type_label' => FrontendHelpers::assignmentType($manuscript->type),
                    'manu_type' => $manuscript->manu_type,
                    'manu_type_label' => $manuscript->manu_type > 0 
                        ? FrontendHelpers::manuscriptType($manuscript->manu_type) : 'None',
                    'uploaded_at' => $manuscript->uploaded_at,
                ];
            });

        $feedbackByGroupLearnerId = AssignmentFeedback::where('user_id', $user->id)
            ->whereIn('assignment_group_learner_id', $groupLearners->pluck('id')->all())
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('assignment_group_learner_id')
            ->map(function ($feedbacks) {
                $feedback = $feedbacks->first();

                return [
                    'id' => $feedback->id,
                    'assignment_group_learner_id' => $feedback->assignment_group_learner_id,
                    'assignment_manuscript_id' => $feedback->assignment_manuscript_id,
                    'user_id' => $feedback->user_id,
                    'filename' => $feedback->filename,
                    'is_active' => (bool) $feedback->is_active,
                    'availability' => $feedback->availability,
                    'created_at' => $feedback->created_at,
                    'updated_at' => $feedback->updated_at,
                ];
            });

        $groupLearnerList = $groupLearners
            ->map(function (AssignmentGroupLearner $learner) use ($feedbackByGroupLearnerId, $manuscriptsByUserId) {
                return [
                    'id' => $learner->id,
                    'assignment_group_id' => $learner->assignment_group_id,
                    'user_id' => $learner->user_id,
                    'could_send_feedback_to' => $learner->could_send_feedback_to,
                    'could_send_feedback_to_id_list' => $learner->could_send_feedback_to_id_list,
                    'created_at' => $learner->created_at,
                    'updated_at' => $learner->updated_at,
                    'user' => $learner->user ? [
                        'id' => $learner->user->id,
                        'first_name' => $learner->user->first_name,
                        'last_name' => $learner->user->last_name,
                    ] : null,
                    'assignmentManuscript' => $manuscriptsByUserId->get($learner->user_id),
                    'feedback' => $feedbackByGroupLearnerId->get($learner->id),
                ];
            })
            ->values();

        return response()->json([
            'data' => [
                'group' => [
                    'id' => $group->id,
                    'assignment_id' => $group->assignment_id,
                    'title' => $group->title,
                    'submission_date' => $group->submission_date,
                    'allow_feedback_download' => (bool) $group->allow_feedback_download,
                ],
                'otherLearnersIdList' => $otherLearnersIdList,
                'couldSendFeedbackTo' => array_values(array_unique($couldSendFeedbackTo)),
                'groupLearnerList' => $groupLearnerList,
                'assignmentManuscript' => $assignmentManuscript ? [
                    'id' => $assignmentManuscript->id,
                    'assignment_id' => $assignmentManuscript->assignment_id,
                    'user_id' => $assignmentManuscript->user_id,
                    'filename' => $assignmentManuscript->filename,
                    'status' => $assignmentManuscript->status,
                    'locked' => (bool) $assignmentManuscript->locked,
                    'has_feedback' => (bool) $assignmentManuscript->has_feedback,
                    'words' => $assignmentManuscript->words,
                    'uploaded_at' => $assignmentManuscript->uploaded_at,
                ] : null,
            ],
        ]);
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

    public function replaceSubmission(Request $request, $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $assignmentManuscript = AssignmentManuscript::find($id);

        if (! $assignmentManuscript) {
            return $this->errorResponse('Submission not found.', 'not_found', 404);
        }

        if ((int) $assignmentManuscript->user_id !== (int) $user->id) {
            return $this->errorResponse('You do not have access to this submission.', 'forbidden', 403);
        }

        if (! $request->hasFile('filename') || ! $request->file('filename')->isValid()) {
            return $this->errorResponse('Missing or invalid file.', 'invalid_file', 422);
        }

        $oldManuscript = $assignmentManuscript->filename;
        $destinationPath = 'storage/assignment-manuscripts/';
        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $extension = strtolower($request->file('filename')->getClientOriginalExtension());

        if (! in_array($extension, $extensions)) {
            return $this->errorResponse(
                'Invalid file format. Allowed formats are PDF, DOC, DOCX, ODT.',
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
        $assignment = $assignmentManuscript->assignment;
        $assignmentMaxWords = $assignment->allow_up_to > 0 ? $assignment->allow_up_to : $assignment->max_words;

        $assignmentConfigurator = AssignmentLearnerConfiguration::where('user_id', $user->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        if ($assignmentConfigurator) {
            $assignmentMaxWords = $assignmentConfigurator->max_words;
        }

        if ($wordCount > $assignmentMaxWords && $assignment->check_max_words) {
            $this->cleanupUploadedFiles($uploadedFiles);

            return $this->errorResponse('Word count exceeds maximum.', 'max_words_exceeded', 422, [
                'max_words' => $assignmentMaxWords,
                'word_count' => $wordCount,
            ]);
        }

        $relativePath = trim($destinationPath, '/').'/'.$storedFileName;
        $assignmentManuscript->filename = '/'.$relativePath;
        $assignmentManuscript->words = $wordCount;
        $assignmentManuscript->save();

        if ($oldManuscript && File::exists(public_path($oldManuscript))) {
            File::delete(public_path($oldManuscript));
        }

        return response()->json([
            'data' => [
                'id' => $assignmentManuscript->id,
                'assignment_id' => $assignmentManuscript->assignment_id,
                'word_count' => $wordCount,
            ],
        ]);
    }

    public function deleteSubmission(Request $request, $id): JsonResponse
    {
        $assignmentManuscript = AssignmentManuscript::find($id);

        if (! $assignmentManuscript) {
            return $this->errorResponse('Submission not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ((int) $assignmentManuscript->user_id !== (int) $user->id) {
            return $this->errorResponse('You do not have access to this submission.', 'forbidden', 403);
        }

        $oldManuscript = $assignmentManuscript->filename;
        $assignmentManuscript->forceDelete();

        if ($oldManuscript && File::exists(public_path($oldManuscript))) {
            File::delete(public_path($oldManuscript));
        }

        return response()->json([
            'data' => [
                'id' => (int) $id,
                'deleted' => true,
            ],
        ]);
    }

    protected function formatAssignment(Assignment $assignment, $user, ?CoursesTaken $courseTaken, bool $includeFeedbackSummary): array
    {
        $course = $assignment->course;

        $payload = [
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

        if ($includeFeedbackSummary) {
            $payload['feedback_summary'] = $this->assignmentFeedbackSummary($assignment, $user);
        }

        return $payload;
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
            'filename' => $manuscript->filename,
            'filename_display' => $manuscript->filename ? basename($manuscript->filename) : null,
            'locked' => (bool) $manuscript->locked,
            'has_feedback' => (bool) $manuscript->has_feedback,
            'status' => $manuscript->status,
            'words' => $manuscript->words,
            'uploaded_at' => $manuscript->uploaded_at,
        ];
    }

    protected function assignmentFeedbackSummary(Assignment $assignment, $user): ?array
    {
        $manuscript = AssignmentManuscript::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $noGroupFeedback = null;
        if ($manuscript) {
            $noGroupFeedback = AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript->id)
                ->where('is_active', 1)
                ->latest('id')
                ->first();
        }

        $groupLearnerIds = AssignmentGroupLearner::where('user_id', $user->id)
            ->whereHas('group', function ($query) use ($assignment) {
                $query->where('assignment_id', $assignment->id);
            })
            ->pluck('id');

        $groupFeedback = null;
        if ($groupLearnerIds->isNotEmpty()) {
            $groupFeedback = AssignmentFeedback::whereIn('assignment_group_learner_id', $groupLearnerIds)
                ->where('is_active', 1)
                ->latest('id')
                ->first();
        }

        $feedback = $noGroupFeedback ?: $groupFeedback;

        if (! $feedback) {
            return null;
        }

        return [
            'id' => $feedback->id,
            'is_active' => (bool) $feedback->is_active,
            'availability' => $feedback->availability,
            'filename' => $feedback->filename,
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
