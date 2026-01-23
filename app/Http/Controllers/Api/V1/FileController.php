<?php

namespace App\Http\Controllers\Api\V1;

use App\ApiFile;
use App\Http\Requests\Api\V1\SignedUploadRequest;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FileController extends ApiController
{
    private const UPLOAD_EXPIRY_MINUTES = 10;
    private const DOWNLOAD_EXPIRY_MINUTES = 10;

    public function signedUpload(SignedUploadRequest $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $fileRecord = ApiFile::create([
            'user_id' => $user->id,
            'original_filename' => $request->input('filename'),
            'mime_type' => $request->input('mime_type'),
            'size' => $request->input('size'),
        ]);

        $uploadUrl = URL::temporarySignedRoute(
            'api.v1.files.upload',
            now()->addMinutes(self::UPLOAD_EXPIRY_MINUTES),
            ['file' => $fileRecord->id]
        );

        return response()->json([
            'file_id' => $fileRecord->id,
            'upload' => [
                'method' => 'POST',
                'url' => $uploadUrl,
                'headers' => [
                    'Authorization' => 'Bearer <access_token>',
                ],
                'expires_in' => self::UPLOAD_EXPIRY_MINUTES * 60,
            ],
        ]);
    }

    public function upload(Request $request, ApiFile $file): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $this->canAccessFile($user, $file)) {
            return $this->errorResponse('Forbidden.', 'forbidden', 403);
        }

        $uploadedFile = $request->file('file');

        if (! $uploadedFile || ! $uploadedFile->isValid()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, [
                'file' => ['A valid file is required.'],
            ]);
        }

        if ($uploadedFile->getSize() > $file->size) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, [
                'file' => ['Uploaded file exceeds the expected size.'],
            ]);
        }

        if ($uploadedFile->getClientMimeType() !== $file->mime_type) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, [
                'file' => ['Uploaded file mime type does not match the expected mime type.'],
            ]);
        }

        $storagePath = $this->storeFile($uploadedFile, $file->user_id);

        $file->update([
            'storage_path' => $storagePath,
        ]);

        return response()->json([
            'uploaded' => true,
            'file_id' => $file->id,
        ]);
    }

    public function signedDownload(Request $request, ApiFile $file): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $this->canAccessFile($user, $file)) {
            return $this->errorResponse('Forbidden.', 'forbidden', 403);
        }

        if (! $file->storage_path) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        $downloadUrl = URL::temporarySignedRoute(
            'api.v1.files.download',
            now()->addMinutes(self::DOWNLOAD_EXPIRY_MINUTES),
            ['file' => $file->id]
        );

        return response()->json([
            'file_id' => $file->id,
            'download_url' => $downloadUrl,
            'expires_in' => self::DOWNLOAD_EXPIRY_MINUTES * 60,
        ]);
    }

    public function download(ApiFile $file): BinaryFileResponse|JsonResponse
    {
        if (! $file->storage_path) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        if (! Storage::disk('public')->exists($file->storage_path)) {
            return $this->errorResponse('File not found.', 'not_found', 404);
        }

        $absolutePath = Storage::disk('public')->path($file->storage_path);

        return response()->download($absolutePath, $file->original_filename, [
            'Content-Type' => $file->mime_type,
        ]);
    }

    private function storeFile($uploadedFile, int $userId): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        $filename = Str::uuid()->toString();
        $path = "api-files/{$userId}";
        $fullName = $extension ? "{$filename}.{$extension}" : $filename;

        return $uploadedFile->storeAs($path, $fullName, 'public');
    }

    private function canAccessFile(User $user, ApiFile $file): bool
    {
        if ($user->id === $file->user_id) {
            return true;
        }

        return in_array($user->role, [User::AdminRole, User::EditorRole, User::GiutbokRole], true);
    }
}
