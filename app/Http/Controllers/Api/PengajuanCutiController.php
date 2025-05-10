<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\PengajuanCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanCutiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Ambil data karyawan yang sedang login
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        // 2. Ambil data cuti karyawan berdasarkan ID
        $karyawan_id = $user->id;
        $cuti = Cuti::where('karyawan_id', $karyawan_id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // 3. Ambil tanggal mulai dan tanggal selesai cuti yang diajukan
        $tanggalMulai = Carbon::parse($request->tanggalMulai);
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1; // +1 untuk hitung hari terakhir

        // Debug: Cek perhitungan jumlah hari
        dd($tanggalMulai, $tanggalSelesai, $jumlahHari);

        // 4. Validasi tanggal cuti
        if ($tanggalSelesai->lt($tanggalMulai)) {
            return response()->json(['message' => 'Tanggal selesai harus setelah tanggal mulai'], 422);
        }

        // 5. Validasi jenis dan sisa cuti
        if ($request->jenisCuti === 'Cuti Tahunan') {
            // Debug: Cek sisa cuti tahunan
            dd($cuti->cutiTahunan, $jumlahHari);
            if ($cuti->cutiTahunan < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 422);
            }

            if (Carbon::now()->gt(Carbon::parse($cuti->cutiTahunan_expired))) {
                return response()->json(['message' => 'Cuti tahunan sudah kedaluwarsa'], 422);
            }

        } elseif ($request->jenisCuti === 'Cuti Panjang') {
            if ($cuti->cutiPanjang < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 422);
            }

            if (Carbon::now()->gt(Carbon::parse($cuti->cutiPanjang_expired))) {
                return response()->json(['message' => 'Cuti panjang sudah kedaluwarsa'], 422);
            }
        } else {
            return response()->json(['message' => 'Jenis cuti tidak valid'], 422);
        }

        // 6. Upload file surat cuti jika ada
        $filePath = null;
        if ($request->hasFile('file_surat_cuti')) {
            $filePath = $request->file('file_surat_cuti')->store('cuti_files', 'public');
        }

        // 7. Simpan pengajuan cuti
        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $karyawan_id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'jumlahHari' => $jumlahHari,
            'statusCuti' => 'Diproses', // Status pengajuan cuti awal
            'file_surat_cuti' => $filePath,
        ]);

        // 8. Kurangi jumlah cuti tahunan jika jenis cuti adalah cuti tahunan
        if ($request->jenisCuti === 'Cuti Tahunan') {
            $cuti->cutiTahunan -= $jumlahHari;
            $cuti->save(); // Simpan perubahan sisa cuti tahunan
        }

        // 9. Return response success
        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim',
            'data' => $pengajuan
        ], 201);
    }
}
