<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\JadwalKerja;
use App\Models\Keterlambatan;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function presensiMasuk(Request $request)
    {
        // Ambil karyawan_id dari sesi login
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }
        $karyawan_id = $user->id;

        // Cari jadwal kerja berdasarkan karyawan_id dan tanggal hari ini
        $jadwalKerja = JadwalKerja::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', now()->toDateString())
            ->first();

        if (!$jadwalKerja) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }

        // Ambil waktu sekarang
        $waktuSekarang = now()->format('H:i:s');

        // Tentukan status keterlambatan
        $statusMasuk = ($waktuSekarang > $jadwalKerja->waktu_masuk) ? 'Terlambat' : 'Tepat Waktu';

        // Validasi input
        $request->validate([
            'imageMasuk' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiMasuk.latitude' => 'required|numeric',
            'lokasiMasuk.longitude' => 'required|numeric',
        ]);

        // Simpan gambar masuk
        $imagePath = $request->file('imageMasuk')->store('uploads/presensi', 'public');

        // Simpan data presensi
        $presensi = Presensi::create([
            'karyawan_id' => $karyawan_id,
            'jadwal_kerja_id' => $jadwalKerja->id,
            'tanggalPresensi' => now()->toDateString(),
            'waktuMasuk' => $waktuSekarang,
            'statusMasuk' => $statusMasuk,
            'imageMasuk' => $imagePath,
            'lokasiMasuk' => json_encode([
                'latitude' => $request->input('lokasiMasuk.latitude'),
                'longitude' => $request->input('lokasiMasuk.longitude'),
            ]),
        ]);

        // Jika terlambat, masukkan ke tabel keterlambatan
        if ($statusMasuk === 'Terlambat') {
            Keterlambatan::create([
                'karyawan_id' => $karyawan_id,
                'presensi_id' => $presensi->id,
            ]);
        }

        return response()->json([
            'message' => 'Presensi masuk berhasil dicatat',
            'data' => $presensi
        ], 201);
    }
}
