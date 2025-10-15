<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentConversionService
{
    public function convertUploadedFileToDocx(UploadedFile $uploadedFile, string $tag = 'document-converter', ?int $userId = null): ?array
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $allowedExtensions = ['doc', 'docx', 'pdf', 'pages', 'odt'];

        if (! in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        if (! Storage::exists('temp-conversions')) {
            Storage::makeDirectory('temp-conversions');
        }

        $temporaryPath = $uploadedFile->storeAs(
            'temp-conversions',
            Str::uuid()->toString().'.'.$extension
        );

        $fullPath = storage_path('app/'.$temporaryPath);
        $docxRelativePath = 'temp-conversions/'.Str::uuid()->toString().'.docx';
        $docxStoragePath = storage_path('app/'.$docxRelativePath);
        $downloadName = $this->determineDownloadName($uploadedFile->getClientOriginalName());

        $conversionSucceeded = false;

        try {
            if ($extension === 'docx') {
                if (Storage::exists($docxRelativePath)) {
                    Storage::delete($docxRelativePath);
                }

                $conversionSucceeded = Storage::copy($temporaryPath, $docxRelativePath);

                if (! $conversionSucceeded) {
                    Log::error('Document conversion copy failed', [
                        'user_id' => $userId,
                        'source' => $temporaryPath,
                        'destination' => $docxRelativePath,
                    ]);
                }
            } else {
                $conversionSucceeded = $this->convertDocumentWithCloudConvert(
                    $fullPath,
                    $extension,
                    $docxStoragePath,
                    $uploadedFile->getClientOriginalName(),
                    $tag,
                    $userId
                );
            }
        } finally {
            Storage::delete($temporaryPath);
        }

        if (! $conversionSucceeded) {
            if (Storage::exists($docxRelativePath)) {
                Storage::delete($docxRelativePath);
            }

            return null;
        }

        return [
            'relative_path' => $docxRelativePath,
            'full_path' => $docxStoragePath,
            'download_name' => $downloadName,
        ];
    }

    protected function determineDownloadName(?string $originalName): string
    {
        $originalName = $originalName ?? '';

        if ($originalName === '') {
            return 'document.docx';
        }

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        if ($extension !== '') {
            if (strtolower($extension) === 'docx') {
                return $originalName;
            }

            $baseName = substr($originalName, 0, - (strlen($extension) + 1));

            if ($baseName === '') {
                return 'document.docx';
            }

            return $baseName.'.docx';
        }

        return $originalName.'.docx';
    }

    protected function convertDocumentWithCloudConvert(
        string $sourcePath,
        string $extension,
        string $destinationPath,
        string $originalName,
        string $tag,
        ?int $userId
    ): bool {
        $apiKey = config('services.cloudconvert.api_key');

        if (empty($apiKey)) {
            Log::error('CloudConvert API key missing for conversion', [
                'user_id' => $userId,
                'tag' => $tag,
            ]);

            return false;
        }

        $outputFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'converted-document';

        $jobResponse = Http::withToken($apiKey)
            ->acceptJson()
            ->post('https://api.cloudconvert.com/v2/jobs', [
                'tasks' => [
                    'import-file' => [
                        'operation' => 'import/upload',
                    ],
                    'convert-file' => [
                        'operation' => 'convert',
                        'input' => ['import-file'],
                        'input_format' => $extension,
                        'output_format' => 'docx',
                        'output_filename' => $outputFilename,
                    ],
                    'export-file' => [
                        'operation' => 'export/url',
                        'input' => ['convert-file'],
                    ],
                ],
                'tag' => $tag,
            ]);

        if (! $jobResponse->successful()) {
            Log::error('CloudConvert job creation failed', [
                'user_id' => $userId,
                'tag' => $tag,
                'response' => $jobResponse->json(),
                'status' => $jobResponse->status(),
            ]);

            return false;
        }

        $jobData = $jobResponse->json('data');
        $jobId = is_array($jobData) ? ($jobData['id'] ?? null) : null;

        if (! $jobId) {
            Log::error('CloudConvert response missing job id', [
                'user_id' => $userId,
                'tag' => $tag,
                'response' => $jobResponse->json(),
            ]);

            return false;
        }

        $tasks = $jobData['tasks'] ?? [];
        $importTask = collect($tasks)->firstWhere('operation', 'import/upload');

        $uploadUrl = data_get($importTask, 'result.form.url');
        $uploadParameters = data_get($importTask, 'result.form.parameters', []);

        if (! is_array($uploadParameters)) {
            $uploadParameters = [];
        }

        if (! $uploadUrl) {
            Log::error('CloudConvert upload URL missing', [
                'user_id' => $userId,
                'tag' => $tag,
                'job_id' => $jobId,
                'task' => $importTask,
            ]);

            return false;
        }

        $uploadRequest = Http::asMultipart();

        $fileResource = fopen($sourcePath, 'r');
        if ($fileResource === false) {
            Log::error('Could not open source file for CloudConvert upload', [
                'user_id' => $userId,
                'tag' => $tag,
                'path' => $sourcePath,
            ]);

            return false;
        }

        $uploadResponse = $uploadRequest
            ->attach('file', $fileResource, basename($originalName) ?: basename($sourcePath))
            ->post($uploadUrl, $uploadParameters);

        fclose($fileResource);

        if (! $uploadResponse->successful()) {
            Log::error('CloudConvert upload failed', [
                'user_id' => $userId,
                'tag' => $tag,
                'job_id' => $jobId,
                'status' => $uploadResponse->status(),
                'response' => $uploadResponse->json(),
            ]);

            return false;
        }

        $exportTask = null;
        $maxAttempts = 30;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            sleep(2);

            $jobStatusResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->get('https://api.cloudconvert.com/v2/jobs/'.$jobId);

            if (! $jobStatusResponse->successful()) {
                Log::error('CloudConvert status check failed', [
                    'user_id' => $userId,
                    'tag' => $tag,
                    'job_id' => $jobId,
                    'status' => $jobStatusResponse->status(),
                    'response' => $jobStatusResponse->json(),
                ]);

                return false;
            }

            $jobStatusData = $jobStatusResponse->json('data');
            $jobStatus = data_get($jobStatusData, 'status');
            $exportTask = collect(data_get($jobStatusData, 'tasks', []))
                ->firstWhere('operation', 'export/url');

            if (data_get($exportTask, 'status') === 'finished') {
                break;
            }

            if (data_get($exportTask, 'status') === 'error') {
                Log::error('CloudConvert export task returned error status', [
                    'user_id' => $userId,
                    'tag' => $tag,
                    'job_id' => $jobId,
                    'export_task' => $exportTask,
                ]);

                return false;
            }

            if ($jobStatus === 'error') {
                Log::error('CloudConvert reported job error', [
                    'user_id' => $userId,
                    'tag' => $tag,
                    'job_id' => $jobId,
                    'tasks' => $jobStatusData['tasks'] ?? [],
                ]);

                return false;
            }
        }

        if (data_get($exportTask, 'status') !== 'finished') {
            Log::error('CloudConvert export did not finish in time', [
                'user_id' => $userId,
                'tag' => $tag,
                'job_id' => $jobId,
                'export_task' => $exportTask,
            ]);

            return false;
        }

        $downloadUrl = data_get($exportTask, 'result.files.0.url');

        if (! $downloadUrl) {
            Log::error('CloudConvert export missing download URL', [
                'user_id' => $userId,
                'tag' => $tag,
                'job_id' => $jobId,
                'export_task' => $exportTask,
            ]);

            return false;
        }

        $downloadResponse = Http::timeout(120)->get($downloadUrl);

        if (! $downloadResponse->successful()) {
            Log::error('CloudConvert download failed', [
                'user_id' => $userId,
                'tag' => $tag,
                'job_id' => $jobId,
                'status' => $downloadResponse->status(),
            ]);

            return false;
        }

        if (! is_dir(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0755, true);
        }

        if (is_file($destinationPath)) {
            @unlink($destinationPath);
        }

        $writeResult = @file_put_contents($destinationPath, $downloadResponse->body());

        if ($writeResult === false) {
            Log::error('Could not write converted document to disk', [
                'user_id' => $userId,
                'tag' => $tag,
                'destination' => $destinationPath,
            ]);

            return false;
        }

        return true;
    }
}
