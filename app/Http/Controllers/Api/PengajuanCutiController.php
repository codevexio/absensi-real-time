<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanCutiController extends Controller
{
    // Menampilkan sisa cuti karyawan yang sudah login
    public function getSisaCuti()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $cuti = Cuti::where('karyawan_id', $user->id)->first();

        if (!$cuti) {
            return response()->json(['message' => 'Cuti tidak ditemukan'], 404);
        }

        // Menghitung jumlah cuti tahunan yang sudah digunakan
        $cutiTahunDipakai = PengajuanCuti::where('karyawan_id', $user->id)
                                        ->where('jenisCuti', 'Cuti Tahunan')
                                        ->where('statusCuti', 'Disetujui')
                                        ->sum('jumlahHari');

        // Menghitung jumlah cuti panjang yang sudah digunakan
        $cutiPanjangDipakai = PengajuanCuti::where('karyawan_id', $user->id)
                                          ->where('jenisCuti', 'Cuti Panjang')
                                          ->where('statusCuti', 'Disetujui')
                                          ->sum('jumlahHari');

        // Menghitung sisa cuti tahunan dan panjang
        $sisaCutiTahun = $cuti->cutiTahun - $cutiTahunDipakai;
        $sisaCutiPanjang = $cuti->cutiPanjang - $cutiPanjangDipakai;

        return response()->json([
            'sisaCutiTahun' => $sisaCutiTahun,
            'sisaCutiPanjang' => $sisaCutiPanjang
        ]);
    }

    // Mengajukan cuti
    public function ajukanCuti(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $validated = $request->validate([
            'jenisCuti' => 'required|in:Cuti Panjang,Cuti Tahunan',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'jumlahHari' => 'required|integer|min:1',
            'file_surat_cuti' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Menyimpan pengajuan cuti
        $validated['karyawan_id'] = $user->id; // Ambil karyawan_id dari user yang sudah login
        $pengajuanCuti = PengajuanCuti::create($validated);

        // Mengupdate sisa cuti karyawan jika pengajuan disetujui
        if ($pengajuanCuti->statusCuti == 'Disetujui') {
            $cuti = Cuti::where('karyawan_id', $user->id)->first();

            if ($pengajuanCuti->jenisCuti == 'Cuti Tahunan') {
                $cuti->cutiTahun -= $pengajuanCuti->jumlahHari;
            } elseif ($pengajuanCuti->jenisCuti == 'Cuti Panjang') {
                $cuti->cutiPanjang -= $pengajuanCuti->jumlahHari;
            }

            $cuti->save();
        }

        return response()->json(['message' => 'Pengajuan cuti berhasil'], 201);
    }

    // Menampilkan semua pengajuan cuti dari karyawan yang sudah login
    public function getPengajuanCuti()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $pengajuanCuti = PengajuanCuti::where('karyawan_id', $user->id)->get();

        if ($pengajuanCuti->isEmpty()) {
            return response()->json(['message' => 'Tidak ada pengajuan cuti'], 404);
        }

        return response()->json($pengajuanCuti);
    }
}
