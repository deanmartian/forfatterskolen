<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\AdminHelpers;
use App\Http\Requests\Api\V1\ShopManuscriptWordCountLookupRequest;
use App\Http\Resources\Api\V1\ShopManuscriptPlanResource;
use App\Http\FrontendHelpers;
use App\Mail\SubjectBodyEmail;
use App\Log;
use App\ShopManuscript;
use App\ShopManuscriptComment;
use App\ShopManuscriptTakenFeedback;
use App\ShopManuscriptUpgrade;
use App\ShopManuscriptsTaken;
use App\Services\ShopManuscriptService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ShopManuscriptController extends ApiController
{
    public function byWordCount(ShopManuscriptWordCountLookupRequest $request): JsonResponse
    {
        $wordCount = (int) $request->validated()['word_count'];

        $plan = ShopManuscript::query()
            ->where('max_words', '>=', $wordCount)
            ->orderBy('max_words')
            ->first();

        if (! $plan) {
            return $this->errorResponse('No shop manuscript plan found for this word count.', 'not_found', 404);
        }

        return response()->json([
            'data' => (new ShopManuscriptPlanResource($plan))->resolve(),
        ]);
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

        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;

        $shopManuscripts = $user->shopManuscriptsTaken()
            ->with(['shop_manuscript', 'feedbacks'])
            ->paginate($perPage);

        return response()->json([
            'data' => $shopManuscripts->getCollection()
                ->map(fn (ShopManuscriptsTaken $taken) => $this->formatShopManuscript($taken))
                ->values(),
            'meta' => [
                'current_page' => $shopManuscripts->currentPage(),
                'last_page' => $shopManuscripts->lastPage(),
                'per_page' => $shopManuscripts->perPage(),
                'total' => $shopManuscripts->total(),
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::with([
            'shop_manuscript',
            'feedbacks',
            'comments.user',
        ])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->find((int) $id);

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatShopManuscript($shopManuscriptTaken, true),
        ]);
    }

    public function download(Request $request, $id, string $type): JsonResponse|BinaryFileResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        if (! in_array($type, ['manuscript', 'synopsis'], true)) {
            return $this->errorResponse('Invalid download type.', 'validation_error', 422);
        }

        $file = $type === 'synopsis' ? $shopManuscriptTaken->synopsis : $shopManuscriptTaken->file;

        if (! $file) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        $absolutePath = public_path($file);

        if (! file_exists($absolutePath)) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        $fileInfo = pathinfo($absolutePath);
        $filename = $fileInfo['filename'] ?? 'manuscript';
        $fileExt = $fileInfo['extension'] ?? '';
        $newName = $fileExt !== '' ? $filename.'.'.$fileExt : $filename;

        if ($type === 'synopsis' && $fileExt !== '') {
            $newName = $filename.'-synopsis.'.$fileExt;
        }

        return response()->download($absolutePath, $newName);
    }

    public function downloadSynopsis(Request $request, $id): JsonResponse|BinaryFileResponse
    {
        return $this->download($request, $id, 'synopsis');
    }

    public function downloadFeedback(Request $request, $id, $feedbackId): JsonResponse|BinaryFileResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        $feedback = ShopManuscriptTakenFeedback::where([
            'id' => $feedbackId,
            'shop_manuscript_taken_id' => $shopManuscriptTaken->id,
        ])->first();

        if (! $feedback) {
            return $this->errorResponse('Feedback not found.', 'not_found', 404);
        }

        $files = collect($feedback->filename)
            ->map(fn ($file) => $this->normalizeFeedbackFilePath($file))
            ->filter()
            ->values()
            ->all();

        if (! $files || count($files) === 0) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        if (count($files) === 1) {
            $absolutePath = public_path(Arr::first($files));

            if (! file_exists($absolutePath)) {
                return $this->errorResponse('File not found.', 'not_found', 404);
            }

            return response()->download($absolutePath);
        }

        $zipFileName = $shopManuscriptTaken->shop_manuscript->title.' Feedbacks.zip';
        $publicDir = public_path('storage');
        $zip = new ZipArchive;

        if ($zip->open($publicDir.'/'.$zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return $this->errorResponse('Unable to prepare feedback download.', 'server_error', 500);
        }

        foreach ($files as $feedFile) {
            $absolutePath = public_path($feedFile);

            if (! file_exists($absolutePath)) {
                continue;
            }

            $zip->addFile($absolutePath, basename($feedFile));
        }

        $zip->close();

        $fileToPath = $publicDir.'/'.$zipFileName;

        if (! file_exists($fileToPath)) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        return response()->download($fileToPath, $zipFileName, [
            'Content-Type' => 'application/octet-stream',
        ])->deleteFileAfterSend(true);
    }

    public function postComment(Request $request, $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $shopManuscriptTaken || ! $shopManuscriptTaken->is_active) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $comment = new ShopManuscriptComment;
        $comment->shop_manuscript_taken_id = $shopManuscriptTaken->id;
        $comment->user_id = $user->id;
        $comment->comment = $validator->validated()['comment'];
        $comment->save();

        $headEditor = User::where('head_editor', 1)->first();
        $emailTemplate = AdminHelpers::emailTemplate('Shop Manuscript Comment');
        $link = route('shop_manuscript_taken', [$user->id, $shopManuscriptTaken->id]);
        $searchString = [
            ':firstname',
            ':link',
        ];
        $replaceString = [
            $user->first_name,
            "<a href='".$link."'>".$link.'</a>',
        ];
        $emailContent = str_replace($searchString, $replaceString, $emailTemplate->email_content);

        if ($headEditor) {
            AdminHelpers::queue_mail($headEditor->email, $emailTemplate->subject, $emailContent, $emailTemplate->from_email);
        }

        return response()->json([
            'data' => $this->formatComment($comment->load('user')),
        ]);
    }

    public function upload(Request $request, $id, ShopManuscriptService $shopManuscriptService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        if (! $shopManuscriptTaken->is_active
            || $shopManuscriptTaken->is_manuscript_locked
            || $shopManuscriptTaken->status === 'Finished') {
            return $this->errorResponse('Manuscript upload not allowed.', 'forbidden', 403);
        }

        $validator = Validator::make($request->all(), [
            'manuscript' => ['required', 'file'],
            'genre' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'synopsis' => ['nullable', 'file'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        if ($request->hasFile('manuscript')) {
            $extension = strtolower($request->file('manuscript')->getClientOriginalExtension());

            if (! in_array($extension, $extensions, true)) {
                return $this->errorResponse('Invalid file format.', 'validation_error', 422, [
                    'manuscript' => [trans('site.invalid-file-format')],
                ]);
            }

            $uploadedManuscript = $shopManuscriptService->uploadLearnerManuscript($request, (int) $user->id);
            $wordCount = (int) ($uploadedManuscript['word_count'] ?? 0);
            $manuscriptPath = $uploadedManuscript['manuscript_file'] ?? null;
            $integrityPassed = (bool) ($uploadedManuscript['integrity_passed'] ?? false);

            if (! $manuscriptPath || $wordCount <= 0 || ! $integrityPassed) {
                $this->removeUploadedFile($uploadedManuscript);

                return $this->errorResponse('Validation failed.', 'validation_error', 422, [
                    'manuscript' => [trans('site.could-not-read-file-try-again')],
                ]);
            }

            $shopManuscriptTaken->file = $manuscriptPath;
            $shopManuscriptTaken->words = $wordCount;
        }

        if ($request->hasFile('synopsis')) {
            $extension = strtolower($request->file('synopsis')->getClientOriginalExtension());

            if (! in_array($extension, $extensions, true)) {
                return $this->errorResponse('Invalid file format.', 'validation_error', 422, [
                    'synopsis' => [trans('site.invalid-file-format')],
                ]);
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension;
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        $currentPlanWords = $shopManuscriptTaken->shop_manuscript->max_words;
        $wordCount = (int) $shopManuscriptTaken->words;

        if ($wordCount > $currentPlanWords) {
            $nextPlan = ShopManuscript::where('max_words', '>=', $wordCount)
                ->orderBy('max_words', 'ASC')
                ->first();
            $upgradePlan = $nextPlan
                ? ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken->shop_manuscript_id)
                    ->where('upgrade_shop_manuscript_id', $nextPlan->id)
                    ->first()
                : null;

            if ($upgradePlan && $nextPlan) {
                return $this->errorResponse('Word limit exceeded.', 'word_limit_exceeded', 422, [
                    'price' => $upgradePlan->price,
                    'plan_id' => $nextPlan->id,
                    'max_words' => $nextPlan->max_words,
                ]);
            }

            return $this->errorResponse('Word limit exceeded.', 'word_limit_exceeded', 422);
        }

        $shopManuscriptTaken->genre = $validator->validated()['genre'];
        $shopManuscriptTaken->description = $validator->validated()['description'] ?? null;
        $shopManuscriptTaken->manuscript_uploaded_date = Carbon::now()->toDateTimeString();
        $shopManuscriptTaken->save();

        Log::create([
            'activity' => '<strong>'.$user->full_name.'</strong> leverte manus for manusutvikling  '
                .$shopManuscriptTaken->shop_manuscript->title,
        ]);

        $message = $user->full_name.' leverte manus for manusutvikling '
            .$shopManuscriptTaken->shop_manuscript->title;
        $toMail = 'post@forfatterskolen.no';
        $emailData = [
            'email_subject' => 'New manuscript submitted for shop manuscript',
            'email_message' => $message,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));

        return response()->json([
            'data' => $this->formatShopManuscript($shopManuscriptTaken->load('shop_manuscript', 'feedbacks')),
        ]);
    }

    public function uploadSynopsis(Request $request, $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        if (! $shopManuscriptTaken->is_active
            || $shopManuscriptTaken->is_manuscript_locked
            || $shopManuscriptTaken->status === 'Finished') {
            return $this->errorResponse('Synopsis upload not allowed.', 'forbidden', 403);
        }

        $validator = Validator::make($request->all(), [
            'synopsis' => ['required', 'file'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $extension = strtolower($request->file('synopsis')->getClientOriginalExtension());
        $extensions = ['pdf', 'doc', 'docx', 'odt'];

        if (! in_array($extension, $extensions, true)) {
            return $this->errorResponse('Invalid file format.', 'validation_error', 422, [
                'synopsis' => [trans('site.invalid-file-format')],
            ]);
        }

        $time = time();
        $destinationPath = 'storage/shop-manuscripts-synopsis/';
        $fileName = $time.'.'.$extension;
        $request->synopsis->move($destinationPath, $fileName);
        $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        $shopManuscriptTaken->save();

        return response()->json([
            'data' => $this->formatShopManuscript($shopManuscriptTaken->load('shop_manuscript', 'feedbacks')),
        ]);
    }

    public function updateUploaded(Request $request, $id, ShopManuscriptService $shopManuscriptService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        if (! $shopManuscriptTaken->is_active
            || $shopManuscriptTaken->is_manuscript_locked
            || $shopManuscriptTaken->status === 'Finished') {
            return $this->errorResponse('Manuscript update not allowed.', 'forbidden', 403);
        }

        $validator = Validator::make($request->all(), [
            'manuscript' => ['nullable', 'file'],
            'synopsis' => ['nullable', 'file'],
            'genre' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'coaching_time_later' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $isManuscriptUploaded = false;

        if ($request->hasFile('manuscript')) {
            $extension = strtolower($request->file('manuscript')->getClientOriginalExtension());

            if (! in_array($extension, $extensions, true)) {
                return $this->errorResponse('Invalid file format.', 'validation_error', 422, [
                    'manuscript' => [trans('site.invalid-file-format')],
                ]);
            }

            $uploadedManuscript = $shopManuscriptService->uploadLearnerManuscript($request, (int) $user->id);
            $wordCount = (int) ($uploadedManuscript['word_count'] ?? 0);
            $manuscriptPath = $uploadedManuscript['manuscript_file'] ?? null;
            $integrityPassed = (bool) ($uploadedManuscript['integrity_passed'] ?? false);

            if (! $manuscriptPath || $wordCount <= 0 || ! $integrityPassed) {
                $this->removeUploadedFile($uploadedManuscript);

                return $this->errorResponse('Validation failed.', 'validation_error', 422, [
                    'manuscript' => [trans('site.could-not-read-file-try-again')],
                ]);
            }

            $shopManuscriptTaken->file = $manuscriptPath;
            $shopManuscriptTaken->words = $wordCount;
            $isManuscriptUploaded = true;
        }

        if ($request->hasFile('synopsis')) {
            $extension = strtolower($request->file('synopsis')->getClientOriginalExtension());

            if (! in_array($extension, $extensions, true)) {
                return $this->errorResponse('Invalid file format.', 'validation_error', 422, [
                    'synopsis' => [trans('site.invalid-file-format')],
                ]);
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension;
            $request->synopsis->move($destinationPath, $fileName);
            $shopManuscriptTaken->synopsis = '/'.$destinationPath.$fileName;
        }

        if ($shopManuscriptTaken->words !== null) {
            $currentPlanWords = $shopManuscriptTaken->shop_manuscript->max_words;
            $wordCount = (int) $shopManuscriptTaken->words;

            if ($wordCount > $currentPlanWords) {
                $nextPlan = ShopManuscript::where('max_words', '>=', $wordCount)
                    ->orderBy('max_words', 'ASC')
                    ->first();
                $upgradePlan = $nextPlan
                    ? ShopManuscriptUpgrade::where('shop_manuscript_id', $shopManuscriptTaken->shop_manuscript_id)
                        ->where('upgrade_shop_manuscript_id', $nextPlan->id)
                        ->first()
                    : null;

                if ($upgradePlan && $nextPlan) {
                    return $this->errorResponse('Word limit exceeded.', 'word_limit_exceeded', 422, [
                        'price' => $upgradePlan->price,
                        'plan_id' => $nextPlan->id,
                        'max_words' => $nextPlan->max_words,
                    ]);
                }

                return $this->errorResponse('Word limit exceeded.', 'word_limit_exceeded', 422);
            }
        }

        $validated = $validator->validated();
        if (array_key_exists('genre', $validated)) {
            $shopManuscriptTaken->genre = $validated['genre'];
        }
        if (array_key_exists('description', $validated)) {
            $shopManuscriptTaken->description = $validated['description'];
        }
        if (array_key_exists('coaching_time_later', $validated)) {
            $shopManuscriptTaken->coaching_time_later = $validated['coaching_time_later'] ? 1 : 0;
        }

        $shopManuscriptTaken->save();

        if ($isManuscriptUploaded && $shopManuscriptTaken->feedback_user_id) {
            $emailTemplate = AdminHelpers::emailTemplate('Manuscript Uploaded');
            $emailContent = str_replace([
                ':manuscript_from',
                ':learner',
            ], [
                '<em>'.$shopManuscriptTaken->shop_manuscript->title.'</em>',
                '<b>'.$user->full_name.'</b>',
            ], $emailTemplate->email_content);

            $editor = User::find($shopManuscriptTaken->feedback_user_id);
            if ($editor) {
                $emailData = [
                    'email_subject' => $emailTemplate->subject,
                    'email_message' => $emailContent,
                    'from_name' => '',
                    'from_email' => $emailTemplate->from_email,
                    'attach_file' => null,
                ];
                \Mail::to($editor->email)->queue(new SubjectBodyEmail($emailData));
            }
        }

        Log::create([
            'activity' => '<strong>'.$user->full_name.'</strong> leverte manus for manusutvikling  '
                .$shopManuscriptTaken->shop_manuscript->title,
        ]);

        $message = $user->full_name.' leverte manus for manusutvikling '
            .$shopManuscriptTaken->shop_manuscript->title;
        $toMail = 'post@forfatterskolen.no';
        $emailData = [
            'email_subject' => 'New manuscript submitted for shop manuscript',
            'email_message' => $message,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));

        return response()->json([
            'data' => $this->formatShopManuscript($shopManuscriptTaken->load('shop_manuscript', 'feedbacks')),
        ]);
    }

    public function deleteUploaded(Request $request, $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscriptTaken = ShopManuscriptsTaken::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $shopManuscriptTaken) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        if (! $shopManuscriptTaken->is_active
            || $shopManuscriptTaken->is_manuscript_locked
            || $shopManuscriptTaken->status === 'Finished') {
            return $this->errorResponse('Manuscript delete not allowed.', 'forbidden', 403);
        }

        $shopManuscriptTaken->file = null;
        $shopManuscriptTaken->words = null;
        $shopManuscriptTaken->genre = 0;
        $shopManuscriptTaken->description = null;
        $shopManuscriptTaken->is_manuscript_locked = 0;
        $shopManuscriptTaken->synopsis = null;
        $shopManuscriptTaken->expected_finish = null;
        $shopManuscriptTaken->save();

        return response()->json([
            'data' => $this->formatShopManuscript($shopManuscriptTaken->load('shop_manuscript', 'feedbacks')),
        ]);
    }

    protected function formatShopManuscript(ShopManuscriptsTaken $taken, bool $includeRelations = false): array
    {
        $payload = [
            'id' => $taken->id,
            'shop_manuscript_id' => $taken->shop_manuscript_id,
            'title' => $taken->shop_manuscript->title ?? null,
            'genre' => $taken->genre,
            'genre_label' => $taken->genre !== null ? FrontendHelpers::assignmentType($taken->genre) : null,
            'description' => $taken->description,
            'status' => $taken->status,
            'is_active' => (bool) $taken->is_active,
            'words' => $taken->words,
            'max_words' => $taken->shop_manuscript->max_words ?? null,
            'file' => $taken->file,
            'synopsis' => $taken->synopsis,
            'expected_finish' => $taken->expected_finish,
            'created_at' => $taken->created_at,
            'manuscript_uploaded_date' => $taken->manuscript_uploaded_date,
            'feedback_user_id' => $taken->feedback_user_id,
            'coaching_time_later' => (bool) $taken->coaching_time_later,
        ];

        if ($includeRelations) {
            $payload['feedbacks'] = $taken->feedbacks->map(fn (ShopManuscriptTakenFeedback $feedback) => [
                'id' => $feedback->id,
                'grade' => $feedback->grade,
                'notes' => $feedback->notes,
                'hours_worked' => $feedback->hours_worked,
                'notes_to_head_editor' => $feedback->notes_to_head_editor,
                'approved' => $feedback->approved ?? null,
                'files' => collect($feedback->filename)
                    ->map(fn ($file) => $this->normalizeFeedbackFilePath($file))
                    ->filter()
                    ->values(),
                'created_at' => $feedback->created_at,
            ])->values();

            $payload['comments'] = $taken->comments->map(function (ShopManuscriptComment $comment) {
                return $this->formatComment($comment);
            })->values();
        }

        return $payload;
    }

    protected function formatComment(ShopManuscriptComment $comment): array
    {
        return [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at,
            'user' => $comment->user ? [
                'id' => $comment->user->id,
                'first_name' => $comment->user->first_name,
                'last_name' => $comment->user->last_name,
                'full_name' => $comment->user->full_name,
            ] : null,
        ];
    }

    protected function removeUploadedFile(array $uploadedManuscript): void
    {
        $absolutePath = $uploadedManuscript['absolute_path'] ?? null;

        if ($absolutePath && is_file($absolutePath)) {
            try {
                unlink($absolutePath);
            } catch (\Throwable $throwable) {
                // ignore cleanup failures
            }
        }
    }

    protected function normalizeFeedbackFilePath(?string $file): ?string
    {
        if (! $file) {
            return null;
        }

        $trimmedFile = trim($file);
        if ($trimmedFile === '') {
            return null;
        }

        if (! Str::startsWith($trimmedFile, ['http://', 'https://'])) {
            return parse_url($trimmedFile, PHP_URL_PATH) ?: $trimmedFile;
        }

        $urlPath = parse_url($trimmedFile, PHP_URL_PATH);

        if (! is_string($urlPath) || $urlPath === '') {
            return null;
        }

        return $urlPath;
    }
}
