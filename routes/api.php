<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('update-cuti', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/update-expired', [CutiController::class, 'updateExpiredCuti']);
Route::post('/cuti/ajukan', [CutiController::class, 'ajukanCuti']);
Route::post('/cuti/approve/{id}', [CutiController::class, 'approveCuti']);
