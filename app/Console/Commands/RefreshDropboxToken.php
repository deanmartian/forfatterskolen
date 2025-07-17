<?php

namespace App\Console\Commands;

use Artisan;
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
                'refresh_token' => config('services.dropbox.refresh_token'),
                'client_id' => config('services.dropbox.key'),
                'client_secret' => config('services.dropbox.secret'),
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            $path = base_path('.env');
            Log::info('Updating Dropbox token at: '.$path);

            if (file_exists($path)) {
                $envContent = file_get_contents($path);

                if (str_contains($envContent, 'DROPBOX_TOKEN=')) {
                    // Replace existing
                    $envContent = preg_replace(
                        '/^DROPBOX_TOKEN=.*$/m',
                        'DROPBOX_TOKEN=' . $accessToken,
                        $envContent
                    );
                } else {
                    // Append if not found
                    $envContent .= "\nDROPBOX_TOKEN=" . $accessToken . "\n";
                }

                file_put_contents($path, $envContent);
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            $this->info('Dropbox access token refreshed successfully.');
            Log::info('Dropbox access token refreshed successfully.');
        } else {
            $this->error('Failed to refresh Dropbox access token.');
            Log::error('Failed to refresh Dropbox access token.');
        }
    }

}
