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
            'authorization_token' => env('DROPBOX_TOKEN'),
            'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
            'key' => env('DROPBOX_APP_KEY'),
            'secret' => env('DROPBOX_APP_SECRET'),
        ],
    ],

];
