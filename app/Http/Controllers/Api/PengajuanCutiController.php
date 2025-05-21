<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\ApprovalCuti;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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

        // Validasi input
        $validated = Validator::make($request->all(), [
            'jenisCuti' => 'required|in:Cuti Panjang,Cuti Tahunan',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'file_surat_cuti' => 'nullable|file|mimes:pdf|max:5128',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validated->errors()
            ], 422);
        }

        $data = $validated->validated(); // Using validated() to get the data

        // Cek cuti yang masih diproses
        $pengajuanCutiDiproses = PengajuanCuti::where('karyawan_id', $user->id)
                                                ->where('statusCuti', 'Diproses')
                                                ->exists();

        if ($pengajuanCutiDiproses) {
            return response()->json(['message' => 'Anda masih memiliki pengajuan cuti yang sedang diproses'], 400);
        }

        // Hitung jumlah hari cuti
        $tanggalMulai = Carbon::parse($data['tanggalMulai']);
        $tanggalSelesai = Carbon::parse($data['tanggalSelesai']);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Ambil jatah cuti
        $cuti = Cuti::where('karyawan_id', $user->id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Cek sisa cuti
        if ($data['jenisCuti'] === 'Cuti Tahunan') {
            if ($cuti->cutiTahun < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 400);
            }
        } else {
            if ($cuti->cutiPanjang < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 400);
            }
        }

        // Simpan file kalau ada
        $path = null;
        if ($request->hasFile('file_surat_cuti')) {
            $file = $request->file('file_surat_cuti');
            $filename = 'surat_cuti_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat_cuti', $filename, 'public');
            Log::info('File cuti disimpan di: ' . $path);
        } else {
            Log::warning('Tidak ada file surat cuti');
        }

        // Simpan ke DB
        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $user->id,
            'jenisCuti' => $data['jenisCuti'], // Use $data to access validated values
            'tanggalMulai' => $data['tanggalMulai'],
            'tanggalSelesai' => $data['tanggalSelesai'],
            'jumlahHari' => $jumlahHari,
            'statusCuti' => 'Diproses',
            'file_surat_cuti' => $path,
        ]);

        // Kalau direksi, langsung setujui otomatis
        if ($golongan == 'Direksi') {
            $pengajuan->update(['statusCuti' => 'Disetujui']);
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim dan sedang diproses',
            'data' => $pengajuan
        ], 201);
    }
    
}
