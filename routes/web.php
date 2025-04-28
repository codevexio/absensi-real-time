<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\IzinKaryawanController;
use App\Http\Controllers\Web\KelolaAkunController;
use App\Http\Controllers\Web\KelolaPresensiController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Web\KelolaShiftController;

Route::view('/', 'login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/login/store', [AuthController::class, 'login'])->name('login');

//Kelola Presensi
Route::get('/presensi', [KelolaPresensiController::class,'index'])->name('web/presensi');
Route::get('/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');
Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');

//Kelola Akun 
Route::get('/kelola-akun', [KelolaAkunController::class,'index'])->name('web/kelola-akun');
Route::post('/kelola-akun', [KelolaAkunController::class,'store'])->name('web/kelola-akun-post');
Route::get('/kelola-akun/search', [KelolaAkunController::class, 'search'])->name('web/kelola-akun-search');
Route::put('/web/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('web/kelola-akun-put');
Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('web/kelola-akun-del');

//Kelola Izin
Route::get('/izinkaryawan', [IzinKaryawanController::class,'index'])->name('web/izinkaryawan');
Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('<Api>karyawan.store');
Route::get('/izinkaryawan/search', [IzinKaryawanController::class, 'search'])->name('web/izinkaryawan-search');

//Kelola Shift
Route::get('/kelolashift', [KelolaShiftController::class,'index'])->name('web/kelola-shift');
Route::put('/kelola-shift/{id}', [KelolaShiftController::class, 'update'])->name('kelola-shift.update');
require __DIR__.'/auth.php';

use App\Http\Controllers\Web\AdminDashboardController;

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
