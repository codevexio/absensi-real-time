<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tanggalAwal = $today->copy()->subDays(29);

        // Data untuk ringkasan (tidak diubah)
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

        // Data 7 hari terakhir (hari kerja saja: Senin - Jumat)
        $labels = [];
        $dataHadir = [];
        $dataTerlambat = [];

        $tanggal = Carbon::today();
        $hariKerjaTerkumpul = 0;

        while ($hariKerjaTerkumpul < 5) {
            if (!in_array($tanggal->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $labels[] = $tanggal->translatedFormat('l'); // "Senin", "Selasa", dst

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

                $hariKerjaTerkumpul++;
            }

            $tanggal->subDay(); // mundur 1 hari
        }
        // Balik urutan agar urut dari Senin ke Jumat
        $labels = array_reverse($labels);
        $dataHadir = array_reverse($dataHadir);
        $dataTerlambat = array_reverse($dataTerlambat);


        // Pie Chart Hari Ini
        $hadirHariIni = $totalAbsenHariIni - $totalTerlambatHariIni;
        $terlambatHariIni = $totalTerlambatHariIni;

        // 🔽 Tambahan: Statistik Bulanan Harian (30 hari terakhir)
        $periode = CarbonPeriod::create($tanggalAwal, $today);
        $bulananLabels = [];
        $bulananHadir = [];
        $bulananCuti = [];
        $bulananTerlambat = [];

        foreach ($periode as $tanggal) {
            $label = $tanggal->format('d M');
            $bulananLabels[] = $label;

            $hadir = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->whereIn('statusMasuk', ['Tepat Waktu', 'Terlambat'])
                ->distinct('karyawan_id')
                ->count('karyawan_id');
            $terlambat = Presensi::whereDate('tanggalPresensi', $tanggal)
                ->where('statusMasuk', 'Terlambat')
                ->distinct('karyawan_id')
                ->count('karyawan_id');
            $cuti = PengajuanCuti::where('statusCuti', 'Disetujui')
                ->whereDate('tanggalMulai', '<=', $tanggal)
                ->whereDate('tanggalSelesai', '>=', $tanggal)
                ->distinct('karyawan_id')
                ->count('karyawan_id');

            $bulananHadir[] = $hadir;
            $bulananCuti[] = $cuti;
            $bulananTerlambat[] = $terlambat;
        }

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
            'bulananLabels',
            'bulananHadir',
            'bulananCuti',
            'bulananTerlambat'
        ));
    }
}
