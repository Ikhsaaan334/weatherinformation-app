<?php

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public endpoints
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Email verification link (signed). No auth: the signature + hash prove identity,
// because the user clicks this from their inbox without a bearer token.
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Authenticated endpoints (Sanctum bearer token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1');

    Route::put('/password', [PasswordController::class, 'update']);
    Route::post('/confirm-password', [PasswordController::class, 'confirm']);

    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Read-only access for any authenticated user (admin or user).
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{city}/weather', [CityController::class, 'weather']);

    // Admin-only management.
    Route::middleware('role:admin')->group(function () {
        Route::post('/cities', [CityController::class, 'store']);
        Route::match(['put', 'patch'], '/cities/{city}', [CityController::class, 'update']);
        Route::delete('/cities/{city}', [CityController::class, 'destroy']);

        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
    });
});
