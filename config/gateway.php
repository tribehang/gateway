<?php


return [
    'services' => [
        'provider' => [
            'url' => env('APP_MS_PROVIDER_HOST') . '/api',
            'cache' => [
                'provider/locations' => 2,
            ]
        ]
    ],
];
