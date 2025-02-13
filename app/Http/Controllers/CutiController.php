<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function updateExpiredCuti()
    {
        // Ambil data karyawan beserta data cuti
        $karyawan = Karyawan::with('cutis')->get();

        foreach ($karyawan as $k) {
            $cuti = $k->cutis;

            // Update expiredTahun setiap tahun
            if ($cuti && $cuti->expiredTahun <= now()) {
                $cuti->expiredTahun = now()->addYear(); // Reset expiredTahun
                $cuti->cutiTahun = 12; // Reset cutiTahun menjadi 12 hari
            }

            // Update expiredPanjang setiap 5 tahun
            if ($cuti && $cuti->expiredPanjang <= now()) {
                $cuti->expiredPanjang = now()->addYears(5); // Reset expiredPanjang
                $cuti->cutiPanjang = 60; // Reset cutiPanjang menjadi 60 hari
            }

            // Simpan perubahan
            if ($cuti) {
                $cuti->save();
            }
        }

        return response()->json(['message' => 'Cuti berhasil diperbarui!']);
    }
}
