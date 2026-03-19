<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshFacebookToken extends Command
{
    protected $signature = 'facebook:refresh-token';

    protected $description = 'Forny Facebook access token til long-lived (60 dager) og oppdater .env';

    public function handle(): int
    {
        $currentToken = config('services.facebook_ads.access_token');
        $appId = config('services.facebook_ads.app_id');
        $appSecret = config('services.facebook_ads.app_secret');

        if (!$currentToken || !$appId || !$appSecret) {
            $this->error('Mangler FACEBOOK_ACCESS_TOKEN, FACEBOOK_APP_ID eller FACEBOOK_APP_SECRET i .env');
            return 1;
        }

        $this->info('Fornyer Facebook access token...');

        // Konverter til long-lived token
        $r = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $currentToken,
        ]);

        if (!$r->successful()) {
            $this->error('Feil: ' . $r->body());
            return 1;
        }

        $newToken = $r->json('access_token');
        $expiresIn = $r->json('expires_in', 0);

        if (!$newToken) {
            $this->error('Ingen token mottatt: ' . $r->body());
            return 1;
        }

        $days = round($expiresIn / 86400);
        $this->info("Ny token mottatt (gyldig i {$days} dager)");

        // Oppdater .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        $envContent = preg_replace(
            '/FACEBOOK_ACCESS_TOKEN=.*/',
            'FACEBOOK_ACCESS_TOKEN=' . $newToken,
            $envContent
        );
        file_put_contents($envPath, $envContent);

        // Clear config cache
        $this->call('config:clear');

        $this->info('✅ .env oppdatert med ny token. Utløper: ' . now()->addSeconds($expiresIn)->format('d.m.Y'));

        Log::info('Facebook token fornyet', ['expires_in' => $expiresIn, 'expires_at' => now()->addSeconds($expiresIn)]);

        return 0;
    }
}
