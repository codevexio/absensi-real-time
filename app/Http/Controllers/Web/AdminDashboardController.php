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
            ->whereIn('statusMasuk', ['Tepat Waktu', 'Terlambat'])
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $totalTerlambatHariIni = Presensi::whereDate('tanggalPresensi', $today)
            ->where('statusMasuk', 'Terlambat')
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        // Data 7 hari terakhir (untuk line chart mingguan)
        $labels = [];
        $dataHadir = [];
        $dataTerlambat = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $labels[] = $tanggal->format('l'); // Nama hari

            $jumlahHadir = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->whereIn('statusMasuk', ['Tepat Waktu', 'Terlambat'])
                ->distinct('karyawan_id')
                ->count('karyawan_id');

            $jumlahTerlambat = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->where('statusMasuk', 'Terlambat')
                ->distinct('karyawan_id')
                ->count('karyawan_id');

            $dataHadir[] = $jumlahHadir;
            $dataTerlambat[] = $jumlahTerlambat;
        }

        // Status Hari Ini (Pie Chart)
        $hadirHariIni = $totalAbsenHariIni - $totalTerlambatHariIni;
        $terlambatHariIni = $totalTerlambatHariIni;

        // Statistik Bulanan (30 hari terakhir)
        $tanggalAwal = Carbon::today()->subDays(29);

        $totalCutiBulanan = PengajuanCuti::where('statusCuti', 'Disetujui')
            ->whereDate('tanggalMulai', '<=', Carbon::today())
            ->whereDate('tanggalSelesai', '>=', $tanggalAwal)
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $totalHadirBulanan = Presensi::whereBetween('tanggalPresensi', [$tanggalAwal, Carbon::today()])
            ->whereIn('statusMasuk', ['Tepat Waktu', 'Terlambat'])
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        $totalTerlambatBulanan = Presensi::whereBetween('tanggalPresensi', [$tanggalAwal, Carbon::today()])
            ->where('statusMasuk', 'Terlambat')
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        return view('dashboard', compact(
            'totalKaryawan',
            'totalCuti',
            'totalAbsenHariIni',
            'totalTerlambatHariIni',
            'labels',
            'dataHadir',
            'dataTerlambat',
            'hadirHariIni',
            'terlambatHariIni',
            'totalCutiBulanan',
            'totalHadirBulanan',
            'totalTerlambatBulanan'
        ));
    }
}
