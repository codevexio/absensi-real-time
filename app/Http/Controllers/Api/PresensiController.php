<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\JadwalKerja;
use App\Models\Keterlambatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * Presensi Masuk
     */
    public function presensiMasuk(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today()->toDateString();

        // Cek jadwal kerja karyawan hari ini
        $jadwalKerja = JadwalKerja::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $tanggalHariIni)
            ->first();

        if (!$jadwalKerja) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }

        // Cek apakah sudah ada presensi hari ini
        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if ($presensi && $presensi->waktuMasuk) {
            return response()->json(['message' => 'Anda sudah melakukan presensi masuk hari ini.'], 400);
        }

        // Validasi input
        $request->validate([
            'imageMasuk' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiMasuk.latitude' => 'required|numeric',
            'lokasiMasuk.longitude' => 'required|numeric',
        ]);

        // Hapus foto lama jika ada
        if ($presensi && $presensi->imageMasuk) {
            Storage::disk('public')->delete($presensi->imageMasuk);
        }

        // Simpan gambar masuk
        $imagePath = $request->file('imageMasuk')->store('uploads/presensi', 'public');

        // Tentukan status masuk (Tepat Waktu / Terlambat)
        $waktuSekarang = now()->format('H:i:s');
        $statusMasuk = ($waktuSekarang > $jadwalKerja->shift->waktu_masuk) ? 'Terlambat' : 'Tepat Waktu';

        // Jika belum ada presensi, buat data baru
        if (!$presensi) {
            $presensi = new Presensi();
            $presensi->karyawan_id = $karyawan_id;
            $presensi->jadwal_kerja_id = $jadwalKerja->id;
            $presensi->tanggalPresensi = $tanggalHariIni;
        }

        // Update data presensi masuk
        $presensi->waktuMasuk = $waktuSekarang;
        $presensi->statusMasuk = $statusMasuk;
        $presensi->imageMasuk = $imagePath;
        $presensi->lokasiMasuk = json_encode([
            'latitude' => $request->input('lokasiMasuk.latitude'),
            'longitude' => $request->input('lokasiMasuk.longitude'),
        ]);
        $presensi->save();

        // Jika terlambat, masukkan ke tabel keterlambatan
        if ($statusMasuk === 'Terlambat') {
            Keterlambatan::firstOrCreate([
                'karyawan_id' => $karyawan_id,
                'presensi_id' => $presensi->id,
            ]);
        }

        return response()->json([
            'message' => 'Presensi masuk berhasil dicatat',
            'data' => $presensi
        ], 200);
    }

    /**
     * Presensi Pulang
     */
    public function presensiPulang(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today()->toDateString();

        // Cek presensi masuk
        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if (!$presensi || !$presensi->waktuMasuk) {
            return response()->json(['message' => 'Anda belum presensi masuk.'], 400);
        }

        // Cek apakah sudah melakukan presensi pulang
        if ($presensi->waktuPulang) {
            return response()->json(['message' => 'Anda sudah melakukan presensi pulang.'], 400);
        }

        // Validasi input
        $request->validate([
            'imagePulang' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiPulang.latitude' => 'required|numeric',
            'lokasiPulang.longitude' => 'required|numeric',
        ]);

        // Hapus foto lama jika ada
        if ($presensi->imagePulang) {
            Storage::disk('public')->delete($presensi->imagePulang);
        }

        // Simpan gambar pulang
        $imagePath = $request->file('imagePulang')->store('uploads/presensi', 'public');

        // Update data presensi pulang
        $presensi->waktuPulang = now()->format('H:i:s');
        $presensi->statusPulang = 'Tepat Waktu';
        $presensi->imagePulang = $imagePath;
        $presensi->lokasiPulang = json_encode([
            'latitude' => $request->input('lokasiPulang.latitude'),
            'longitude' => $request->input('lokasiPulang.longitude'),
        ]);
        $presensi->save();

        return response()->json([
            'message' => 'Presensi pulang berhasil dicatat',
            'data' => $presensi
        ], 200);
    }
}
