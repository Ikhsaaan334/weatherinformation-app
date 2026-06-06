<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Read-only access for any authenticated user (admin or user).
    Route::get('cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('cities/{city}/weather', [CityController::class, 'weather'])->name('cities.weather');

    // Admin-only management.
    Route::middleware('role:admin')->group(function () {
        Route::post('cities', [CityController::class, 'store'])->name('cities.store');
        Route::match(['put', 'patch'], 'cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');

        Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::patch('admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
