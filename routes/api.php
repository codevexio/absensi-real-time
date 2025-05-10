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
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pengajuan Cuti
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pengajuan-cuti', [PengajuanCutiController::class, 'store']);
});


// Jadwal Kerja
Route::apiResource('jadwal-kerja', JadwalKerjaController::class);
Route::post('/generate-presensi-harian', [JadwalKerjaController::class, 'generatePresensiHarian']);
Route::middleware('auth:sanctum')->get('/shift/hari-ini', [JadwalKerjaController::class, 'getShiftHariIni']);

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

// Keterlambatan
Route::middleware('auth:sanctum')->get('keterlambatan/statistik-bulanan', [KeterlambatanController::class, 'statistikBulananOtomatis']);

// Presensi
Route::middleware('auth:sanctum')->post('/presensi/masuk', [PresensiController::class, 'presensiMasuk']);
Route::middleware('auth:sanctum')->post('/presensi/pulang', [PresensiController::class, 'presensiPulang']);
Route::middleware('auth:sanctum')->get('/cek-waktu-presensi', [PresensiController::class, 'cekWaktuPresensi']);
Route::middleware('auth:sanctum')->get('/list-rekap-presensi', [PresensiController::class, 'listRekapPresensi']);
Route::middleware('auth:sanctum')->get('/rekap-presensi-pdf/{bulan}', [PresensiController::class, 'rekapPresensiPDF']);

// Login Android
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// etc
Route::apiResource('pengajuan-cuti', PengajuanCutiController::class);
Route::apiResource('approval-cuti', ApprovalCutiController::class);
Route::post('/approval-cuti/{id}/approve', [ApprovalCutiController::class, 'approve']);
Route::post('/approval-cuti/{id}/reject', [ApprovalCutiController::class, 'reject']);
Route::apiResource('password-reset-tokens', PasswordResetTokenController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('sessions', SessionController::class)->only(['index', 'store', 'destroy']);