<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\IzinKaryawanController;
use App\Http\Controllers\Web\KelolaAkunController;
use App\Http\Controllers\Web\KelolaPresensiController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Web\KelolaShiftController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\KelolaTableShift;

Route::view('/', 'login');


Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/login/store', [AuthController::class, 'login'])->name('login');

//Kelola Presensi
Route::get('/presensi', [KelolaPresensiController::class,'index'])->name('web/presensi');
Route::get('/kelola-presensi', [KelolaPresensiController::class, 'index'])->name('kelola-presensi.index');
Route::get('/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');
Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
Route::put('/kelola-presensi/{id}/update-status', [KelolaPresensiController::class, 'updateStatus'])->name('web/kelola-presensi-updateStatus');


//Kelola Akun 
Route::get('/kelola-akun', [KelolaAkunController::class,'index'])->name('web/kelola-akun');
Route::post('/kelola-akun', [KelolaAkunController::class,'store'])->name('web/kelola-akun-post');
Route::get('/kelola-akun/search', [KelolaAkunController::class, 'search'])->name('web/kelola-akun-search');
Route::put('/web/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('web/kelola-akun-put');
Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('web/kelola-akun-del');

//Kelola Izin
Route::get('/izinkaryawan', [IzinKaryawanController::class,'index'])->name('web/izinkaryawan');
Route::get('/izinkaryawan/search', [IzinKaryawanController::class, 'search'])->name('web/izinkaryawan-search');
Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('<Api>karyawan.store');

//Kelola Shift
// Kelola Shift
Route::get('/kelolashift', [KelolaShiftController::class,'index'])->name('web/kelola-shift');
Route::put('/kelolashift/{id}', [KelolaShiftController::class, 'update'])->name('web/kelola-shift-update');

//Kelola Tabel Shift
Route::get('/kelola-table-shift', [KelolaTableShift::class, 'index'])->name('shift.index');
Route::put('/kelola-table-shift/{id}', [KelolaTableShift::class, 'update'])->name('shift.update');
Route::post('/kelola-table-shift', [KelolaTableShift::class, 'store'])->name('shift.store');
Route::delete('/kelola-table-shift/{id}', [KelolaTableShift::class, 'destroy'])->name('shift.destroy');

// Dashboard
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
