<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CutiController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\JadwalKerjaController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\KeterlambatanController;
use App\Http\Controllers\Api\PengajuanCutiController;
use App\Http\Controllers\Api\ApprovalCutiController;
use App\Http\Controllers\Api\PasswordResetTokenController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\GeminiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Pengajuan Cuti
Route::post('/pengajuan-cuti', [PengajuanCutiController::class, 'store']);
Route::put('/pengajuan-cuti/{id}', [PengajuanCutiController::class, 'updateStatusCuti']);
Route::post('/pengajuan-cuti', [PengajuanCutiController::class, 'ajukanCuti']);
Route::put('/pengajuan-cuti/{id}', [PengajuanCutiController::class, 'updateStatusCuti']);

// Jadwal Kerja
Route::apiResource('jadwal-kerja', JadwalKerjaController::class);
Route::post('/generate-presensi-harian', [JadwalKerjaController::class, 'generatePresensiHarian']);

// Cuti
Route::get('update-cuti', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/update-expired', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/ajukan', [CutiController::class, 'ajukanCuti']);
Route::post('/cuti/approve/{id}', [CutiController::class, 'approveCuti']);
Route::apiResource('cuti', CutiController::class);

// Karyawan
Route::apiResource('karyawan', KaryawanController::class);

// Shift
Route::apiResource('shift', ShiftController::class);

// Presensi
Route::apiResource('presensi', PresensiController::class);

// Keterlambatan
Route::apiResource('keterlambatan', KeterlambatanController::class);

// etc
Route::apiResource('pengajuan-cuti', PengajuanCutiController::class);
Route::apiResource('approval-cuti', ApprovalCutiController::class);
Route::post('/approval-cuti/{id}/approve', [ApprovalCutiController::class, 'approve']);
Route::post('/approval-cuti/{id}/reject', [ApprovalCutiController::class, 'reject']);
Route::apiResource('password-reset-tokens', PasswordResetTokenController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('sessions', SessionController::class)->only(['index', 'store', 'destroy']);