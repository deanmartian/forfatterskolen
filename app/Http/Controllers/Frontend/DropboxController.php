<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DropboxController extends Controller {
    
    public function redirectToDropbox()
    {
        $appKey = config('filesystems.disks.dropbox.key');
        $redirectUri = route('dropbox.callback');

        return redirect()->away("https://www.dropbox.com/oauth2/authorize?client_id={$appKey}&token_access_type=offline&response_type=code&redirect_uri={$redirectUri}");
    }

    public function handleDropboxCallback(Request $request)
    {
        $code = $request->get('code');
        $appKey = config('filesystems.disks.dropbox.key');
        $appSecret = config('filesystems.disks.dropbox.secret');
        $redirectUri = route('dropbox.callback');

        $client = new Client();
        $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
            'form_params' => [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'client_id' => $appKey,
                'client_secret' => $appSecret,
                'redirect_uri' => $redirectUri
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data;
        $accessToken = $data['access_token'];

        // Save the access token securely, e.g., in the database or session
        //session(['dropbox_token' => $accessToken]);
        //file_put_contents(base_path('.env'), "\nDROPBOX_TOKEN={$accessToken}", FILE_APPEND);

        return $accessToken;
    }

    public function refreshDropboxAccessToken()
    {
        $appKey = config('filesystems.disks.dropbox.key');
        $appSecret = config('filesystems.disks.dropbox.secret');
        $refreshToken = config('filesystems.disks.dropbox.refresh_token'); // Add this to your .env

        // Initialize Guzzle client
        $client = new Client();

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
                session(['dropbox_token' => $accessToken]);

                return response()->json(['success' => 'Access token refreshed successfully!', 'access_token' => $accessToken]);
            } else {
                return response()->json(['error' => 'Failed to refresh access token.']);
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh access token: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to refresh access token: ' . $e->getMessage()]);
        }
    }

    public function dropboxUpload()
    {
        return view('frontend.test');
    }

    public function dropboxPostUpload(Request $request)
    {
        $destinationPath = 'assignment-manuscripts/'; // upload path
        $extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION); // getting document extension
        $actual_name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $fileName = AdminHelpers::getUniqueFilename('dropbox', 'assignment-manuscripts', $actual_name . "." . $extension);
        $file = $request->file('file');
        $expFileName = explode('/', $fileName);
        $file->storeAs($destinationPath, end($expFileName), 'dropbox');
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Uploaded'),
        'alert_type' => 'success']);
    }
}