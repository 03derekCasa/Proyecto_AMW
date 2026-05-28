<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'storage/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:8080',
        'http://127.0.0.1:8080',

        'http://localhost:8081',
        'http://127.0.0.1:8081',

        'http://localhost:8082',
        'http://127.0.0.1:8082',

        'http://localhost:5173',
        'http://127.0.0.1:5173',

        'http://127.0.0.1:8080',
        'http://172.20.10.14:8080',

        env('FRONTEND_URL'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
