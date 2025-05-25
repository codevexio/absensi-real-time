<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ringkasan
        $totalKaryawan = Karyawan::count();

        $totalCuti = PengajuanCuti::where('statusCuti', 'Disetujui')
            ->whereDate('tanggalMulai', '<=', $today)
            ->whereDate('tanggalSelesai', '>=', $today)
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $totalAbsenHariIni = Presensi::whereDate('tanggalPresensi', $today)
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $totalTerlambatHariIni = Presensi::whereDate('tanggalPresensi', $today)
            ->where('statusMasuk', 'Terlambat')
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        // Data 7 hari terakhir
        $labels = [];
        $dataHadir = [];
        $dataTerlambat = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $labels[] = $tanggal->format('l'); // Nama hari (Senin, Selasa, dst)

            $jumlahHadir = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->distinct('karyawan_id')
                ->count('karyawan_id');

            $jumlahTerlambat = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->where('statusMasuk', 'Terlambat')
                ->distinct('karyawan_id')
                ->count('karyawan_id');

            $dataHadir[] = $jumlahHadir;
            $dataTerlambat[] = $jumlahTerlambat;
        }

        // Status Hari Ini untuk Pie Chart
        $hadir = $totalAbsenHariIni;
        $cuti = $totalCuti;

        return view('dashboard', compact(
            'totalKaryawan',
            'totalCuti',
            'totalAbsenHariIni',
            'totalTerlambatHariIni',
            'labels',
            'dataHadir',
            'dataTerlambat',
            'hadir',
            'cuti'
        ));
    }
}
