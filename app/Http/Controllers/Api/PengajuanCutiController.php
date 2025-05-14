<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'jenisCuti' => 'required|in:Cuti Panjang,Cuti Tahunan',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'file_surat_cuti' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Cek jika ada pengajuan cuti yang masih diproses
        $pengajuanCutiDiproses = PengajuanCuti::where('karyawan_id', $user->id)
                                            ->where('statusCuti', 'Diproses')
                                            ->exists();

        if ($pengajuanCutiDiproses) {
            return response()->json(['message' => 'Anda masih memiliki pengajuan cuti yang sedang diproses'], 400);
        }

        // Hitung jumlah hari cuti
        $tanggalMulai = Carbon::parse($validated['tanggalMulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggalSelesai']);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $user->id)->first();

        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Validasi sisa cuti
        if ($validated['jenisCuti'] === 'Cuti Tahunan') {
            if ($cuti->cutiTahun < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 400);
            }
        } elseif ($validated['jenisCuti'] === 'Cuti Panjang') {
            if ($cuti->cutiPanjang < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 400);
            }
        }

        // Persiapan data pengajuan cuti
        $data = [
            'karyawan_id' => $user->id,
            'jenisCuti' => $validated['jenisCuti'],
            'tanggalMulai' => $validated['tanggalMulai'],
            'tanggalSelesai' => $validated['tanggalSelesai'],
            'jumlahHari' => $jumlahHari,
            'statusCuti' => 'Diproses',
        ];

        // Simpan file jika dikirim
        if ($request->hasFile('file_surat_cuti')) {
            $file = $request->file('file_surat_cuti');

            // Logging nama file untuk debug
            Log::info('File surat cuti ditemukan: ' . $file->getClientOriginalName());

            // Simpan file ke folder storage/app/public/surat_cuti
            $path = $file->store('surat_cuti', 'public');
            $data['file_surat_cuti'] = $path;
        } else {
            Log::warning('File surat cuti tidak ditemukan dalam request.');
        }

        // Simpan ke database
        PengajuanCuti::create($data);

        return response()->json(['message' => 'Pengajuan cuti berhasil dikirim dan sedang diproses'], 201);
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
