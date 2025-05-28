<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;

class KeterlambatanController extends Controller
{

    public function statistikBulananOtomatis()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        // Ambil bulan sekarang
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth()->toDateString();
        $end = $now->copy()->endOfMonth()->toDateString();

        // Total presensi masuk bulan ini
        $totalPresensi = Presensi::where('karyawan_id', $user->id)
            ->whereBetween('tanggalPresensi', [$start, $end])
            ->whereNotNull('waktuMasuk') // pastikan sudah presensi masuk
            ->count();

        // Jumlah keterlambatan bulan ini
        $jumlahTerlambat = Keterlambatan::where('karyawan_id', $user->id)
            ->whereHas('presensi', function ($query) use ($start, $end) {
                $query->whereBetween('tanggalPresensi', [$start, $end]);
            })
            ->count();

        // Hitung jumlah tepat waktu
        $jumlahTepatWaktu = $totalPresensi - $jumlahTerlambat;

        return response()->json([
            'bulan' => $now->format('Y-m'),
            'total_presensi' => $totalPresensi,
            'jumlah_terlambat' => $jumlahTerlambat,
            'jumlah_tepat_waktu' => $jumlahTepatWaktu
        ]);
    }

    public function daftarTerlambat()
    {
        // Ambil tanggal hari ini
        $today = Carbon::today();

        // Ambil semua keterlambatan hari ini + relasi presensi dan karyawan
        $keterlambatan = Keterlambatan::whereHas('presensi', function ($query) use ($today) {
            $query->whereDate('tanggalPresensi', $today);
        })
        ->with(['presensi.karyawan'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Transformasi output sesuai kebutuhan
        $data = $keterlambatan->map(function ($item) {
            return [
                'nama_karyawan' => $item->presensi->karyawan->nama ?? '-',
                'waktuMasuk' => $item->presensi->waktuMasuk ?? '-',
                'divisi_karyawan' => $item->presensi->karyawan->divisi ?? '-',
                'imageMasuk' => $item->presensi->imageMasuk ?? '-',
            ];
        });

        return response()->json($data);
    }

}
