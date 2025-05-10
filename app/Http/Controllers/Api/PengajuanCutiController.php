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

        return response()->json([
            'guard' => config('auth.defaults.guard'),
            'auth_user' => Auth::user(),
            'token' => $request->bearerToken()
        ]);

        // Cek user login
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        // Validasi input
        $validated = $request->validate([
            'jenisCuti' => 'required|in:Cuti Tahunan,Cuti Panjang',
            'tanggalMulai' => 'required|date|after_or_equal:today',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'file_surat_cuti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $karyawan_id = $user->id;
        $tanggalMulai = Carbon::parse($request->tanggalMulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai)->endOfDay();
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $karyawan_id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Cek tanggal kedaluwarsa cuti
        $hariIni = Carbon::now();
        if ($request->jenisCuti === 'Cuti Tahunan') {
            if ($cuti->cutiTahunan_expired < $hariIni) {
                return response()->json(['message' => 'Cuti tahunan sudah kedaluwarsa'], 422);
            }
            if ($jumlahHari > $cuti->cutiTahunan) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 422);
            }
        } elseif ($request->jenisCuti === 'Cuti Panjang') {
            if ($cuti->cutiPanjang_expired < $hariIni) {
                return response()->json(['message' => 'Cuti panjang sudah kedaluwarsa'], 422);
            }
            if ($jumlahHari > $cuti->cutiPanjang) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 422);
            }
        }

        // Cek pengajuan cuti duplikat (tanggal bertabrakan)
        $duplikat = PengajuanCuti::where('karyawan_id', $karyawan_id)
            ->where('statusCuti', '!=', 'Ditolak')
            ->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('tanggalMulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggalSelesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function ($query2) use ($tanggalMulai, $tanggalSelesai) {
                        $query2->where('tanggalMulai', '<=', $tanggalMulai)
                               ->where('tanggalSelesai', '>=', $tanggalSelesai);
                    });
            })
            ->exists();

        if ($duplikat) {
            return response()->json(['message' => 'Sudah ada pengajuan cuti pada rentang tanggal tersebut'], 422);
        }

        // Upload file jika ada
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
