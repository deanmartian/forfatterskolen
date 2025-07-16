<?php

return [

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
            'serve' => true,
            'report' => false,
        ],

        'dropbox' => [
            'driver' => 'dropbox',
            'authorization_token' => config('services.dropbox.token'),
            'refresh_token' => config('services.dropbox.refresh_token'),
            'client_id' => config('services.dropbox.key'),
            'client_secret' => config('services.dropbox.secret'),
        ],
    ],

];
