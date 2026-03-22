<?php

namespace App\Providers;

use App\Http\Controllers\Frontend\DropboxController;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            $accessToken = $this->getValidAccessToken($config);

            $adapter = new DropboxAdapter(new Client($accessToken));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }

    /**
     * Get a valid Dropbox access token, auto-refreshing if expired.
     * Caches the token for 3.5 hours (tokens expire after 4 hours).
     */
    private function getValidAccessToken(array $config): string
    {
        $cacheKey = 'dropbox_access_token';
        $cached = cache($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Try to refresh using refresh_token
        if (!empty($config['refresh_token']) && !empty($config['key']) && !empty($config['secret'])) {
            try {
                $response = \Illuminate\Support\Facades\Http::asForm()
                    ->post('https://api.dropbox.com/oauth2/token', [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $config['refresh_token'],
                        'client_id' => $config['key'],
                        'client_secret' => $config['secret'],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $token = $data['access_token'];
                    $expiresIn = $data['expires_in'] ?? 14400;

                    // Cache for 90% of expiry time (3.5 hours for 4-hour tokens)
                    cache([$cacheKey => $token], now()->addSeconds((int) ($expiresIn * 0.9)));

                    return $token;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Dropbox token refresh failed: ' . $e->getMessage());
            }
        }

        // Fallback to static token from config
        return $config['authorization_token'] ?? '';
    }
}
