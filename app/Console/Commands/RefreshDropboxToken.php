<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;
use SebastianBergmann\Environment\Console;

class RefreshDropboxToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dropbox:refresh-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Dropbox access token using refresh token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = new Client;
        $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
                'client_id' => env('DROPBOX_APP_KEY'),
                'client_secret' => env('DROPBOX_APP_SECRET'),
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            $path = base_path('.env');
            Log::info('path = '.$path);
            if (file_exists($path)) {
                echo $accessToken;
                file_put_contents($path, str_replace(
                    'DROPBOX_TOKEN='.env('DROPBOX_TOKEN'),
                    'DROPBOX_TOKEN='.$accessToken,
                    file_get_contents($path)
                ));
            }

            $this->call('config:clear'); // run config clear
            $this->info('Dropbox access token refreshed successfully.');
            Log::info('Dropbox access token refreshed successfully.');
        } else {
            $this->error('Failed to refresh Dropbox access token.');
            Log::info('Failed to refresh Dropbox access token.');
        }
    }
}
