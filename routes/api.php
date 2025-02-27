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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('update-cuti', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/update-expired', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/ajukan', [CutiController::class, 'ajukanCuti']);
Route::post('/cuti/approve/{id}', [CutiController::class, 'approveCuti']);
Route::apiResource('karyawan', KaryawanController::class);
Route::apiResource('shift', ShiftController::class);
Route::apiResource('cuti', CutiController::class);
Route::apiResource('jadwal-kerja', JadwalKerjaController::class);
Route::apiResource('presensi', PresensiController::class);
Route::apiResource('keterlambatan', KeterlambatanController::class);
Route::apiResource('pengajuan-cuti', PengajuanCutiController::class);
Route::apiResource('approval-cuti', ApprovalCutiController::class);
Route::post('/approval-cuti/{id}/approve', [ApprovalCutiController::class, 'approve']);
Route::post('/approval-cuti/{id}/reject', [ApprovalCutiController::class, 'reject']);
Route::apiResource('password-reset-tokens', PasswordResetTokenController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('sessions', SessionController::class)->only(['index', 'store', 'destroy']);
