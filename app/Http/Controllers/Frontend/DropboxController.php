<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Dropbox\Client as DropboxClient;
use Symfony\Component\HttpFoundation\StreamedResponse;

/* include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php'; */
include_once base_path('Docx2Text.php');

class DropboxController extends Controller
{
    public function redirectToDropbox()
    {
        $appKey = config('filesystems.disks.dropbox.key');
        $redirectUri = route('dropbox.callback');

        return redirect()->away("https://www.dropbox.com/oauth2/authorize?client_id={$appKey}&token_access_type=offline&response_type=code&redirect_uri={$redirectUri}");
    }

    /**
     * Dropbox redirecter hit etter at bruker har godkjent i OAuth-flyten.
     *
     * SIKKERHET: Den gamle versjonen av denne metoden forårsaket
     * "env-katastrofen" fordi den brukte preg_replace() med Dropbox-tokenet
     * som replacement-streng (tokens kan inneholde $1, $2 som PHP tolker
     * som back-references → tom/korrupt .env) OG file_put_contents()
     * uten backup/atomisk rename (prosessen kan dø midt i skriving →
     * halvskrevet .env).
     *
     * Denne versjonen bruker samme defensive mønster som env:update
     * og dropbox:authorize CLI-kommandoene:
     *   1. Backup .env til .env.backup-YYYYmmdd-HHMMSS
     *   2. Line-by-line replacement (ingen regex)
     *   3. Sanity checks: ingen kritiske nøkler forsvant, ny content ikke tom
     *   4. Atomisk rename via .env.tmp-<uniqid>
     *   5. Abort ved enhver feil — .env urørt, backup peker på forrige
     */
    public function handleDropboxCallback(Request $request)
    {
        $code = $request->get('code');
        $appKey = config('filesystems.disks.dropbox.key');
        $appSecret = config('filesystems.disks.dropbox.secret');
        $redirectUri = route('dropbox.callback');

        if (empty($code)) {
            return response()->json(['error' => 'Ingen code-parameter i callback'], 400);
        }

        try {
            $client = new Client;
            $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
                'form_params' => [
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'client_id' => $appKey,
                    'client_secret' => $appSecret,
                    'redirect_uri' => $redirectUri,
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            Log::error('Dropbox callback: token exchange failed: ' . $body);
            return response()->json([
                'error' => 'Dropbox avviste kodeutvekslingen',
                'details' => $body,
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Dropbox callback: uventet feil: ' . $e->getMessage());
            return response()->json(['error' => 'Uventet feil: ' . $e->getMessage()], 500);
        }

        $data = json_decode((string) $response->getBody(), true);
        $accessToken = $data['access_token'] ?? null;
        $refreshToken = $data['refresh_token'] ?? null;

        if (!$accessToken) {
            Log::error('Dropbox callback: response mangler access_token', ['data' => $data]);
            return response()->json(['error' => 'Dropbox returnerte ikke access_token'], 400);
        }

        // Sanity på tokens — ingen newlines
        foreach ([$accessToken, $refreshToken] as $tok) {
            if ($tok !== null && (str_contains($tok, "\n") || str_contains($tok, "\r"))) {
                Log::error('Dropbox callback: token inneholder newline — avbryter');
                return response()->json(['error' => 'Uventet tokenformat fra Dropbox'], 500);
            }
        }

        // --- LES OG VALIDER .env ---
        $path = base_path('.env');
        if (!file_exists($path)) {
            Log::error('Dropbox callback: .env ikke funnet på ' . $path);
            return response()->json(['error' => '.env ikke funnet — avbryter'], 500);
        }

        $original = file_get_contents($path);
        if ($original === false || strlen($original) < 50) {
            Log::error('Dropbox callback: .env uleselig eller mistenkelig kort');
            return response()->json(['error' => '.env er uleselig eller mistenkelig kort — avbryter'], 500);
        }

        // --- BACKUP ---
        $backupPath = $path . '.backup-' . date('Ymd-His');
        if (!copy($path, $backupPath)) {
            Log::error('Dropbox callback: backup av .env feilet');
            return response()->json(['error' => 'Kunne ikke ta backup av .env — avbryter'], 500);
        }
        Log::info('Dropbox callback: backup lagret til ' . $backupPath);

        // --- LINE-BY-LINE REPLACEMENT (ingen regex) ---
        $lines = preg_split('/(\r\n|\n|\r)/', $original);
        if ($lines === false) {
            Log::error('Dropbox callback: kunne ikke parse .env i linjer');
            return response()->json(['error' => 'Kunne ikke parse .env — avbryter (backup: ' . basename($backupPath) . ')'], 500);
        }

        $updates = ['DROPBOX_TOKEN' => $accessToken];
        if ($refreshToken) {
            $updates['DROPBOX_REFRESH_TOKEN'] = $refreshToken;
        }

        foreach ($updates as $key => $value) {
            $found = false;
            foreach ($lines as $i => $line) {
                if (str_starts_with(ltrim($line), $key . '=')) {
                    $lines[$i] = $key . '=' . $value;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $lines[] = $key . '=' . $value;
            }
        }

        $newContent = implode("\n", $lines);

        // --- SANITY CHECKS ---
        if (strlen($newContent) < 50) {
            Log::error('Dropbox callback: ny content mistenkelig kort — avbryter');
            return response()->json([
                'error' => 'Ny .env-content mistenkelig kort — avbryter',
                'backup' => basename($backupPath),
            ], 500);
        }

        foreach ($updates as $key => $value) {
            if (!str_contains($newContent, $key . '=' . $value)) {
                Log::error('Dropbox callback: ny verdi for ' . $key . ' mangler — avbryter');
                return response()->json([
                    'error' => 'Ny verdi manglet fra ny content — avbryter',
                    'backup' => basename($backupPath),
                ], 500);
            }
        }

        // Refuser å skrive hvis kritiske nøkler har forsvunnet
        $criticalKeys = ['APP_KEY=', 'DB_DATABASE=', 'DB_USERNAME='];
        foreach ($criticalKeys as $needle) {
            if (str_contains($original, $needle) && !str_contains($newContent, $needle)) {
                Log::error('Dropbox callback: kritisk nøkkel ' . $needle . ' forsvant — avbryter');
                return response()->json([
                    'error' => 'Kritisk nøkkel ' . $needle . ' forsvant — avbryter',
                    'backup' => basename($backupPath),
                ], 500);
            }
        }

        // --- ATOMISK SKRIV ---
        $tmpPath = $path . '.tmp-' . uniqid();
        if (file_put_contents($tmpPath, $newContent) === false) {
            @unlink($tmpPath);
            Log::error('Dropbox callback: kunne ikke skrive tmp-fil');
            return response()->json([
                'error' => 'Kunne ikke skrive tmp-fil — avbryter',
                'backup' => basename($backupPath),
            ], 500);
        }

        if (!rename($tmpPath, $path)) {
            @unlink($tmpPath);
            Log::error('Dropbox callback: atomisk rename feilet');
            return response()->json([
                'error' => 'Atomisk rename feilet — avbryter',
                'backup' => basename($backupPath),
            ], 500);
        }

        // --- CLEAR CACHES ---
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');

        Log::info('Dropbox callback: nye tokens lagret trygt. Backup: ' . $backupPath);

        // Returner en vennlig HTML-side istedenfor rå JSON, så Sven kan
        // se tydelig i nettleseren at alt gikk bra.
        $maskedAccess = substr($accessToken, 0, 8) . '...' . substr($accessToken, -6);
        $maskedRefresh = $refreshToken ? (substr($refreshToken, 0, 8) . '...' . substr($refreshToken, -6)) : '(ingen)';

        return response(<<<HTML
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Dropbox koblet til</title>
    <style>
        body { font-family: -apple-system, sans-serif; max-width: 640px; margin: 60px auto; padding: 0 20px; color: #333; }
        .card { background: #f0f9f0; border-left: 4px solid #2e7d32; padding: 24px; border-radius: 8px; }
        h1 { color: #2e7d32; margin-top: 0; }
        code { background: #fff; padding: 2px 6px; border-radius: 3px; font-size: 13px; }
        .backup { margin-top: 16px; padding: 12px; background: #fffbea; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 13px; }
        .next { margin-top: 20px; }
        a { color: #862736; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✓ Dropbox koblet til</h1>
        <p>Nye tokens er skrevet trygt til <code>.env</code>:</p>
        <ul>
            <li><code>DROPBOX_TOKEN</code> = <code>{$maskedAccess}</code></li>
            <li><code>DROPBOX_REFRESH_TOKEN</code> = <code>{$maskedRefresh}</code></li>
        </ul>
        <div class="backup">
            <strong>Backup tatt:</strong> <code>.env.backup-{date('Ymd-His')}</code><br>
            Hvis noe likevel går galt kan du rulle tilbake med:
            <code>cp .env.backup-* .env</code>
        </div>
    </div>
    <div class="next">
        <p><strong>Neste steg:</strong></p>
        <ol>
            <li>Gå til <a href="https://admin.forfatterskolen.no/self-publishing">admin.forfatterskolen.no/self-publishing</a></li>
            <li>Last opp et testmanus med et prosjekt valgt</li>
            <li>Verifiser at det går gjennom uten 500-feil</li>
        </ol>
    </div>
</body>
</html>
HTML, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    public function refreshDropboxAccessToken(): JsonResponse
    {
        $appKey = config('filesystems.disks.dropbox.key');
        $appSecret = config('filesystems.disks.dropbox.secret');
        $refreshToken = config('filesystems.disks.dropbox.refresh_token'); // Add this to your .env

        // Initialize Guzzle client
        $client = new Client;

        try {
            $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => $appKey,
                    'client_secret' => $appSecret,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['access_token'])) {
                $accessToken = $data['access_token'];

                // Save the access token securely, e.g., in the database or session
                // session(['dropbox_token' => $accessToken]);

                return response()->json(['success' => 'Access token refreshed successfully!', 'access_token' => $accessToken]);
            } else {
                return response()->json(['error' => 'Failed to refresh access token.']);
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh access token: '.$e->getMessage());

            return response()->json(['error' => 'Failed to refresh access token: '.$e->getMessage()]);
        }
    }

    public function dropboxUpload(): View
    {
        return view('frontend.test');
    }

    public function dropboxPostUpload(Request $request)
    {
        $destinationPath = 'Forfatterskolen_app/assignment-manuscripts/'; // upload path
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); // getting document extension
        $actual_name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $fileName = AdminHelpers::getUniqueFilename('dropbox', 'Forfatterskolen_app/assignment-manuscripts', $actual_name.'.'.$extension);
        $file = $request->file('file');
        $expFileName = explode('/', $fileName);
        $dropboxFileName = end($expFileName);

        $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

        // Path to the uploaded file in Dropbox
        $dropboxFilePath = $destinationPath.$dropboxFileName;

        try {
            // Create Dropbox client
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));

            // Download the file from Dropbox
            $response = $dropbox->download($dropboxFilePath);

            // Ensure the temp directory exists
            $tempDirectory = storage_path('app/temp');
            if (! is_dir($tempDirectory)) {
                mkdir($tempDirectory, 0755, true);
            }

            // Save the downloaded content to a temporary file
            $tempFilePath = $tempDirectory.'/'.$dropboxFileName;
            file_put_contents($tempFilePath, stream_get_contents($response));

            $docObj = new \Docx2Text($tempFilePath);
            $docText = $docObj->convertToText();
            $word_count = FrontendHelpers::get_num_of_words($docText);

            // Clean up the local temporary file
            unlink($tempFilePath);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Uploaded'),
                'alert_type' => 'success',
                'word_count' => $word_count,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Failed to upload or download the file from Dropbox: '.$e->getMessage()),
                'alert_type' => 'danger',
            ]);
        }
    }

    public function createSharedLink($path)
    {
        try {
            $client = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            // Check for existing shared links
            $response = $client->listSharedLinks($path);

            if (isset($response[0]['url'])) {
                // Use the first existing shared link
                $sharedLink = str_replace('?dl=0', '?raw=1', $response[0]['url']);
            } else {
                // Create a new shared link if none exists
                $response = $client->createSharedLinkWithSettings($path, [
                    'requested_visibility' => 'public',
                ]);
                $sharedLink = str_replace('?dl=0', '?raw=1', $response['url']);
            }

            if (request()->isJson()) {
                return response()->json(['shared_link' => $sharedLink]);
            }

            return redirect()->to($sharedLink);

        } catch (\Exception $e) {
            Log::error('Failed to create shared link: '.$e->getMessage());
            if (request()->isJson()) {
                return response()->json(['error' => 'Failed to create shared link: '.$e->getMessage()], 500);
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Failed to create shared link: '.$e->getMessage()),
                'alert_type' => 'danger',
            ]);
        }
    }

    public function downloadFile($path)
    {
        try {
            // Create Dropbox client
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            $dropboxFilePath = $path;
            // Download the file from Dropbox
            $response = $dropbox->download($dropboxFilePath);

            return new StreamedResponse(function () use ($response) {
                $chunkSize = 1024 * 1024; // 1MB per chunk

                while (! feof($response)) {
                    echo fread($response, $chunkSize);
                    flush(); // Flush system output buffer to prevent memory issues
                }
            }, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.basename($path).'"',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Failed to download the file from Dropbox: '.$e->getMessage()),
                'alert_type' => 'danger',
            ]);
        }
    }
}
