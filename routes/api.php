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

// Login
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pengajuan Cuti
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cuti/sisa', [PengajuanCutiController::class, 'getSisaCuti']);
    Route::post('cuti/ajukan', [PengajuanCutiController::class, 'ajukanCuti']);
    Route::get('cuti/detail/{id}', [PengajuanCutiController::class, 'detailPengajuanCuti']);
});

// Approval Cuti
Route::middleware('auth:sanctum')->group(function () {
    Route::get('approval-cuti', [ApprovalCutiController::class, 'index']);
    Route::get('approval-cuti/{id}', [ApprovalCutiController::class, 'show']);
    Route::post('approval-cuti/{id}/proses', [ApprovalCutiController::class, 'prosesApproval']);
});

// Jadwal Kerja
Route::apiResource('jadwal-kerja', JadwalKerjaController::class);
Route::post('/generate-presensi-harian', [JadwalKerjaController::class, 'generatePresensiHarian']);
Route::middleware('auth:sanctum')->get('/shift/hari-ini', [JadwalKerjaController::class, 'getShiftHariIni']);

// Karyawan
Route::apiResource('karyawan', KaryawanController::class);

// Shift
Route::apiResource('shift', ShiftController::class);

// Keterlambatan
Route::middleware('auth:sanctum')->get('keterlambatan/statistik-bulanan', [KeterlambatanController::class, 'statistikBulananOtomatis']);
Route::middleware('auth:sanctum')->get('keterlambatan/daftar', [KeterlambatanController::class, 'daftarTerlambat']);

// Presensi
Route::middleware('auth:sanctum')->post('/presensi/masuk', [PresensiController::class, 'presensiMasuk']);
Route::middleware('auth:sanctum')->post('/presensi/pulang', [PresensiController::class, 'presensiPulang']);
Route::middleware('auth:sanctum')->get('/cek-waktu-presensi', [PresensiController::class, 'cekWaktuPresensi']);
Route::middleware('auth:sanctum')->get('/list-rekap-presensi', [PresensiController::class, 'listRekapPresensi']);
Route::middleware('auth:sanctum')->get('/rekap-presensi-pdf/{bulan}', [PresensiController::class, 'rekapPresensiPDF']);
Route::middleware('auth:sanctum')->get('/detail-rekap/{bulan}', [PresensiController::class, 'getRekapDetail']);
Route::middleware('auth:sanctum')->get('/presensi/status-hari-ini', [PresensiController::class, 'getPresensiHariIni']);

// Login Android
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// etc
Route::apiResource('password-reset-tokens', PasswordResetTokenController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('sessions', SessionController::class)->only(['index', 'store', 'destroy']);