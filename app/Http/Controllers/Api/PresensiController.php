<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input request
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id', // Pastikan karyawan_id ada di tabel karyawan
            'jadwal_kerja_id' => 'required|exists:jadwal_kerja,id', // Pastikan jadwal_kerja_id ada di tabel jadwal_kerja
            'waktuMasuk' => 'required|date_format:H:i:s', // Pastikan waktuMasuk adalah format waktu yang valid
            'statusMasuk' => 'required|in:Tepat Waktu,Terlambat,Cuti', // Status Masuk harus sesuai pilihan
            'waktuPulang' => 'nullable|date_format:H:i:s', // Waktu pulang bisa kosong
            'statusPulang' => 'required|in:Tepat Waktu,Tidak Presensi Pulang,Cuti', // Status Pulang
            'lokasiMasuk' => 'required|array', // Lokasi masuk harus berupa array dengan latitude dan longitude
            'lokasiPulang' => 'nullable|array', // Lokasi pulang bisa kosong
        ]);

        // Menyesuaikan tanggalPresensi dengan tanggal saat ini
        $tanggalPresensi = Carbon::today()->toDateString();  // Menggunakan hanya tanggal (YYYY-MM-DD)

        // Jika Anda ingin menyimpan waktu juga, bisa gunakan Carbon::now() untuk mendapatkan tanggal dan waktu
        // $tanggalPresensi = Carbon::now()->toDateTimeString(); // Untuk menyimpan dengan waktu juga

        // Buat presensi baru
        $presensi = Presensi::create([
            'karyawan_id' => $request->karyawan_id,
            'jadwal_kerja_id' => $request->jadwal_kerja_id,
            'tanggalPresensi' => $tanggalPresensi,  // Menyimpan tanggal saat ini
            'waktuMasuk' => $request->waktuMasuk,
            'statusMasuk' => $request->statusMasuk,
            'waktuPulang' => $request->waktuPulang,
            'statusPulang' => $request->statusPulang,
            'lokasiMasuk' => $request->lokasiMasuk,  // Lokasi masuk disimpan sebagai array/JSON
            'lokasiPulang' => $request->lokasiPulang,  // Lokasi pulang disimpan sebagai array/JSON
        ]);

        // Mengembalikan response sukses dengan data presensi yang baru
        return response()->json($presensi, 201);
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
