<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The Vue SPA frontend runs on a different origin (http://localhost:5173)
    | from this Laravel API (http://localhost:8000). These settings allow the
    | browser to call the API across origins. Auth is token-based (Bearer), so
    | cookies/credentials are not required.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter([
        env('FRONTEND_URL'),
    ])),

    // Accept the SPA on any local port/host alias during development. Vite falls
    // back to 5174+ when 5173 is taken, and the dev URL may be opened as either
    // localhost or 127.0.0.1 — all of which must echo back as a matching origin
    // or the browser blocks the response (even though the API itself responds).
    'allowed_origins_patterns' => [
        '#^http://(localhost|127\.0\.0\.1)(:\d+)?$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
