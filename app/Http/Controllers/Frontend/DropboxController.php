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

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';

class DropboxController extends Controller
{
    public function redirectToDropbox()
    {
        $appKey = config('filesystems.disks.dropbox.key');
        $redirectUri = route('dropbox.callback');

        return "https://www.dropbox.com/oauth2/authorize?client_id={$appKey}&token_access_type=offline&response_type=code&redirect_uri={$redirectUri}";

        return redirect()->away("https://www.dropbox.com/oauth2/authorize?client_id={$appKey}&token_access_type=offline&response_type=code&redirect_uri={$redirectUri}");
    }

    public function handleDropboxCallback(Request $request)
    {
        $code = $request->get('code');
        $appKey = config('filesystems.disks.dropbox.key');
        $appSecret = config('filesystems.disks.dropbox.secret');
        $redirectUri = route('dropbox.callback');

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

        $data = json_decode($response->getBody(), true);

        return $data;
        $accessToken = $data['access_token'];

        // Save the access token securely, e.g., in the database or session
        // session(['dropbox_token' => $accessToken]);
        // file_put_contents(base_path('.env'), "\nDROPBOX_TOKEN={$accessToken}", FILE_APPEND);

        return $accessToken;
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

            return $dropboxFilePath;

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Uploaded'),
                'alert_type' => 'success',
                'word_count' => $word_count, // Include word count in the response
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Failed to upload or download the file from Dropbox: '.$e->getMessage()),
                'alert_type' => 'danger',
            ]);
        }
        /* return $dropboxFilePath;

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Uploaded'),
        'alert_type' => 'success']); */
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
            if (! request()->isJson()) {
                return AdminHelpers::createMessageBag('Failed to create shared link: '.$e->getMessage());

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Failed to create shared link: '.$e->getMessage()),
                    'alert_type' => 'danger',
                ]);
            }

            return null;
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
