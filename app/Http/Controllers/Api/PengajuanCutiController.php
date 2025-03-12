<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;

class PengajuanCutiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'jenisCuti' => 'required|in:Cuti Panjang,Cuti Tahunan',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'jumlahHari' => 'required|integer|min:1',
        ]);

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $request->karyawan_id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Validasi jatah cuti
        if ($request->jenisCuti == 'Cuti Tahunan' && $cuti->cutiTahun < $request->jumlahHari) {
            return response()->json(['message' => 'Jatah cuti tahunan tidak mencukupi'], 400);
        }
        if ($request->jenisCuti == 'Cuti Panjang' && $cuti->cutiPanjang < $request->jumlahHari) {
            return response()->json(['message' => 'Jatah cuti panjang tidak mencukupi'], 400);
        }

        // Simpan pengajuan cuti
        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $request->karyawan_id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $request->tanggalMulai,
            'tanggalSelesai' => $request->tanggalSelesai,
            'jumlahHari' => $request->jumlahHari,
            'statusCuti' => 'Diproses',
        ]);

        return response()->json(['message' => 'Pengajuan cuti berhasil dikirim', 'data' => $pengajuan], 201);
    }

    public function updateStatusCuti(Request $request, $id)
    {
        $request->validate([
            'statusCuti' => 'required|in:Disetujui,Ditolak',
            'alasanPenolakan' => 'nullable|string',
        ]);

        $pengajuan = PengajuanCuti::findOrFail($id);

        if ($request->statusCuti == 'Disetujui') {
            $cuti = Cuti::where('karyawan_id', $pengajuan->karyawan_id)->first();

            if ($pengajuan->jenisCuti == 'Cuti Tahunan') {
                $cuti->cutiTahun -= $pengajuan->jumlahHari;
            } else {
                $cuti->cutiPanjang -= $pengajuan->jumlahHari;
            }

            $cuti->save();
        } else {
            $pengajuan->alasanPenolakan = $request->alasanPenolakan;
        }

        $pengajuan->statusCuti = $request->statusCuti;
        $pengajuan->save();

        return response()->json(['message' => 'Status pengajuan cuti diperbarui'], 200);
    }

}
