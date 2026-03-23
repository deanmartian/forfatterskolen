<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudConvertService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.cloudconvert.com/v2';

    public function __construct()
    {
        $this->apiKey = config('services.cloudconvert.api_key', env('CLOUDCONVERT_API_KEY'));
    }

    /**
     * Konverter fil til docx via CloudConvert API
     * Returnerer path til den konverterte filen, eller null ved feil
     */
    public function convertToDocx(string $inputPath, ?string $outputDir = null): ?string
    {
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));

        if ($extension === 'docx') {
            return $inputPath; // Allerede docx
        }

        $allowedFormats = ['pdf', 'odt', 'doc', 'pages', 'rtf'];
        if (!in_array($extension, $allowedFormats)) {
            Log::warning('CloudConvert: Ustøttet format', ['extension' => $extension]);
            return null;
        }

        try {
            // 1. Opprett jobb med import + convert + export
            $job = Http::withToken($this->apiKey)->post("{$this->baseUrl}/jobs", [
                'tasks' => [
                    'import-file' => [
                        'operation' => 'import/upload',
                    ],
                    'convert-file' => [
                        'operation' => 'convert',
                        'input' => ['import-file'],
                        'output_format' => 'docx',
                    ],
                    'export-file' => [
                        'operation' => 'export/url',
                        'input' => ['convert-file'],
                    ],
                ],
            ])->json();

            if (empty($job['data']['id'])) {
                Log::error('CloudConvert: Kunne ikke opprette jobb', ['response' => $job]);
                return null;
            }

            // 2. Finn upload-task
            $uploadTask = collect($job['data']['tasks'])->firstWhere('name', 'import-file');
            $uploadUrl = $uploadTask['result']['form']['url'] ?? null;
            $uploadParams = $uploadTask['result']['form']['parameters'] ?? [];

            if (!$uploadUrl) {
                Log::error('CloudConvert: Ingen upload-URL', ['task' => $uploadTask]);
                return null;
            }

            // 3. Last opp filen
            $multipart = [];
            foreach ($uploadParams as $key => $value) {
                $multipart[] = ['name' => $key, 'contents' => $value];
            }
            $multipart[] = [
                'name' => 'file',
                'contents' => fopen($inputPath, 'r'),
                'filename' => basename($inputPath),
            ];

            Http::asMultipart()->post($uploadUrl, $multipart);

            // 4. Vent på at jobben fullføres (poll)
            $jobId = $job['data']['id'];
            $maxAttempts = 60; // 60 × 2s = 2 min
            $attempt = 0;

            do {
                sleep(2);
                $status = Http::withToken($this->apiKey)
                    ->get("{$this->baseUrl}/jobs/{$jobId}")
                    ->json();

                $jobStatus = $status['data']['status'] ?? 'unknown';
                $attempt++;

                if ($jobStatus === 'error') {
                    Log::error('CloudConvert: Jobb feilet', ['job' => $status]);
                    return null;
                }
            } while ($jobStatus !== 'finished' && $attempt < $maxAttempts);

            if ($jobStatus !== 'finished') {
                Log::error('CloudConvert: Timeout', ['jobId' => $jobId]);
                return null;
            }

            // 5. Hent eksportert fil-URL
            $exportTask = collect($status['data']['tasks'])->firstWhere('name', 'export-file');
            $downloadUrl = $exportTask['result']['files'][0]['url'] ?? null;

            if (!$downloadUrl) {
                Log::error('CloudConvert: Ingen download-URL', ['task' => $exportTask]);
                return null;
            }

            // 6. Last ned og lagre konvertert fil
            $outputDir = $outputDir ?: dirname($inputPath);
            $outputFilename = pathinfo($inputPath, PATHINFO_FILENAME) . '.docx';
            $outputPath = $outputDir . '/' . $outputFilename;

            $content = Http::get($downloadUrl)->body();
            file_put_contents($outputPath, $content);

            Log::info('CloudConvert: Konvertert', [
                'from' => basename($inputPath),
                'to' => $outputFilename,
                'size' => strlen($content),
            ]);

            return $outputPath;

        } catch (\Throwable $e) {
            Log::error('CloudConvert: Feil', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
