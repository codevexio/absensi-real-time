<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\IzinKaryawanController;
use App\Http\Controllers\Web\KelolaAkunController;
use App\Http\Controllers\Web\PresensiController;
use App\Http\Controllers\Api\KaryawanController;

Route::view('/', 'login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/login/store', [AuthController::class, 'login'])->name('login');
Route::get('/presensi', [PresensiController::class,'index'])->name('web/presensi');
Route::get('/kelola-akun', [KelolaAkunController::class,'index'])->name('web/kelola-akun');
Route::post('/kelola-akun', [KelolaAkunController::class,'store'])->name('web/kelola-akun-post');
Route::get('/kelola-akun/search', [KelolaAkunController::class, 'search'])->name('web/kelola-akun-search');
Route::put('/web/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('web/kelola-akun-put');
Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('web/kelola-akun-del');
Route::get('/izinkaryawan', [IzinKaryawanController::class,'index'])->name('web/izinkaryawan');
Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('<Api>karyawan.store');
require __DIR__.'/auth.php';
