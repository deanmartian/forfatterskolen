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
            $adapter = new DropboxAdapter(new Client(
                $config['authorization_token']
            ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
        /* Storage::extend('dropbox', function ($app, $config) {
            $accessToken = session('dropbox_token');

            // If the access token is not found in the session, refresh it
            if (!$accessToken) {
                // Call the refresh token method to get a new access token
                app(DropboxController::class)->refreshDropboxAccessToken();
                $accessToken = session('dropbox_token');
            }

            $client = new Client($accessToken);
            $adapter = new DropboxAdapter($client);

            return new Filesystem($adapter);
        }); */
    }
}
