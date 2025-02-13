<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\Auth\AuthController;

Route::view('/', 'login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/login/store', [AuthController::class, 'login'])->name('login');


require __DIR__.'/auth.php';
