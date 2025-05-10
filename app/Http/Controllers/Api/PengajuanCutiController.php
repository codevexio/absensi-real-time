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
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalMulai = Carbon::parse($request->tanggalMulai);
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Validasi tanggal cuti
        if ($tanggalSelesai->lt($tanggalMulai)) {
            return response()->json(['message' => 'Tanggal selesai harus setelah tanggal mulai'], 422);
        }

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $karyawan_id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Validasi sisa cuti dan tanggal kadaluarsa
        if ($request->jenisCuti === 'Cuti Tahunan') {
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

        // Upload file surat cuti (jika ada)
        $filePath = null;
        if ($request->hasFile('file_surat_cuti')) {
            $filePath = $request->file('file_surat_cuti')->store('cuti_files', 'public');
        }

        // Simpan pengajuan cuti
        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $karyawan_id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'jumlahHari' => $jumlahHari,
            'statusCuti' => 'Diproses',
            'file_surat_cuti' => $filePath,
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim',
            'data' => $pengajuan
        ], 201);
    }
}
