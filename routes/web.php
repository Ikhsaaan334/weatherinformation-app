<?php

use Illuminate\Support\Facades\Route;

/*
| This application is an API-only backend. The user interface lives in the
| decoupled Vue SPA (see the frontend/ directory) which talks to routes/api.php.
*/

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'message' => 'SkyCast API. The frontend lives at '.config('app.frontend_url').'. See /api for endpoints.',
    ]);
});
