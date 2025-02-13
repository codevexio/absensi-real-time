<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;

Route::view('/', 'login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('update-cuti', [CutiController::class, 'updateExpiredCuti']);

require __DIR__.'/auth.php';
