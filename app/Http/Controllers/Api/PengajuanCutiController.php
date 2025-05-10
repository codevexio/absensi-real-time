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
        $user = Auth::user();  // Menggunakan Auth::user() untuk mengambil data user berdasarkan token
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id; // Ambil id karyawan dari user yang terautentikasi
        $tanggalMulai = Carbon::parse($request->tanggalMulai);
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $cuti = Cuti::where('karyawan_id', $karyawan_id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Cek sisa cuti
        if ($request->jenisCuti === 'Cuti Tahunan') {
            if ($jumlahHari > $cuti->cutiTahunan) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 422);
            }
        } else if ($request->jenisCuti === 'Cuti Panjang') {
            if ($jumlahHari > $cuti->cutiPanjang) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 422);
            }
        }

        // Upload file jika ada
        $filePath = null;
        if ($request->hasFile('file_surat_cuti')) {
            $filePath = $request->file('file_surat_cuti')->store('cuti_files', 'public');
        }

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
