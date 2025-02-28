<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Models\Cuti;  // Menambahkan model Cuti
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PengajuanCutiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data request
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|integer',
            'jenisCuti' => 'required|string|max:255',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'statusCuti' => 'required|string|max:255',
            'alasanPenolakan' => 'nullable|string|max:255',
        ]);

        // Jika validasi gagal, return error
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Menghitung jumlah hari cuti berdasarkan tanggalMulai dan tanggalSelesai
        $tanggalMulai = Carbon::parse($request->tanggalMulai);
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1; // Menambah 1 hari karena perhitungan termasuk tanggal mulai

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $request->karyawan_id)->first();

        if (!$cuti) {
            return response()->json(['error' => 'Cuti untuk karyawan ini tidak ditemukan'], 404);
        }

        // Cek apakah jenis cuti yang diminta ada sisa cutinya
        $sisaCuti = 0;
        if ($request->jenisCuti == 'Cuti Tahunan') {
            // Jika jenis cuti tahunan, cek apakah sisa cutinya mencukupi
            $sisaCuti = $cuti->cutiTahun;
        } elseif ($request->jenisCuti == 'Cuti Panjang') {
            // Jika jenis cuti panjang, cek apakah sisa cutinya mencukupi
            $sisaCuti = $cuti->cutiPanjang;
        }

        // Cek jika jumlah hari cuti melebihi sisa cuti
        if ($sisaCuti < $jumlahHari) {
            return response()->json(['error' => 'Cuti yang diminta tidak mencukupi. Sisa cuti Anda: ' . $sisaCuti . ' hari.'], 400);
        }

        // Menyimpan data pengajuan cuti
        $cutiPengajuan = PengajuanCuti::create([
            'karyawan_id' => $request->karyawan_id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'jumlahHari' => $jumlahHari,
            'statusCuti' => $request->statusCuti,
            'alasanPenolakan' => $request->alasanPenolakan,
        ]);

        // Update sisa cuti pada tabel Cuti (misalnya mengurangi sisa cuti yang digunakan)
        if ($request->jenisCuti == 'Cuti Tahunan') {
            $cuti->cutiTahun -= $jumlahHari; // Kurangi cuti tahunan
        } elseif ($request->jenisCuti == 'Cuti Panjang') {
            $cuti->cutiPanjang -= $jumlahHari; // Kurangi cuti panjang
        }
        $cuti->save();

        // Mengembalikan response dengan status success
        return response()->json($cutiPengajuan, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data request
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|integer',
            'jenisCuti' => 'required|string|max:255',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'statusCuti' => 'required|string|max:255',
            'alasanPenolakan' => 'nullable|string|max:255',
        ]);

        // Jika validasi gagal, return error
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Mencari pengajuan cuti berdasarkan ID
        $cutiPengajuan = PengajuanCuti::find($id);

        if (!$cutiPengajuan) {
            return response()->json(['message' => 'Pengajuan Cuti tidak ditemukan'], 404);
        }

        // Menghitung jumlah hari cuti berdasarkan tanggalMulai dan tanggalSelesai
        $tanggalMulai = Carbon::parse($request->tanggalMulai);
        $tanggalSelesai = Carbon::parse($request->tanggalSelesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1; // Menambah 1 hari karena perhitungan termasuk tanggal mulai

        // Ambil data cuti karyawan
        $cuti = Cuti::where('karyawan_id', $request->karyawan_id)->first();

        if (!$cuti) {
            return response()->json(['error' => 'Cuti untuk karyawan ini tidak ditemukan'], 404);
        }

        // Cek apakah jenis cuti yang diminta ada sisa cutinya
        $sisaCuti = 0;
        if ($request->jenisCuti == 'Cuti Tahunan') {
            // Jika jenis cuti tahunan, cek apakah sisa cutinya mencukupi
            $sisaCuti = $cuti->cutiTahun;
        } elseif ($request->jenisCuti == 'Cuti Panjang') {
            // Jika jenis cuti panjang, cek apakah sisa cutinya mencukupi
            $sisaCuti = $cuti->cutiPanjang;
        }

        // Cek jika jumlah hari cuti melebihi sisa cuti
        if ($sisaCuti < $jumlahHari) {
            return response()->json(['error' => 'Cuti yang diminta tidak mencukupi. Sisa cuti Anda: ' . $sisaCuti . ' hari.'], 400);
        }

        // Update data pengajuan cuti
        $cutiPengajuan->update([
            'karyawan_id' => $request->karyawan_id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'jumlahHari' => $jumlahHari,
            'statusCuti' => $request->statusCuti,
            'alasanPenolakan' => $request->alasanPenolakan,
        ]);

        // Update sisa cuti pada tabel Cuti (misalnya mengurangi sisa cuti yang digunakan)
        if ($request->jenisCuti == 'Cuti Tahunan') {
            $cuti->cutiTahun -= $jumlahHari; // Kurangi cuti tahunan
        } elseif ($request->jenisCuti == 'Cuti Panjang') {
            $cuti->cutiPanjang -= $jumlahHari; // Kurangi cuti panjang
        }
        $cuti->save();

        // Mengembalikan response dengan status success
        return response()->json($cutiPengajuan);
    }
}
