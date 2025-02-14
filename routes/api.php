<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\JadwalKerjaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KeterlambatanController;
use App\Http\Controllers\PengajuanCutiController;
use App\Http\Controllers\ApprovalCutiController;

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
