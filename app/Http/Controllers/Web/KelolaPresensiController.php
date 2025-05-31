<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class KelolaPresensiController extends Controller
{
    /**
     * Tampilkan daftar presensi karyawan.
     */
    public function index(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $query = Presensi::with(['karyawan', 'jadwalKerja.shift']);

        if ($month && $year) {
            $query->whereMonth('tanggalPresensi', $month)
                  ->whereYear('tanggalPresensi', $year);
        }
        // Ambil data presensi dengan relasi karyawan, jadwal kerja, dan shift
        $employees = Presensi::with(['karyawan', 'jadwalKerja.shift'])->orderBy('created_at','desc')->paginate(10);
        
        // Pass data ke view
        return view('KelolaPresensi', compact('employees'));
    }

    /**
     * Fungsi Pencarian Presensi.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        // Cari presensi berdasarkan nama karyawan atau status
        $presensi = Presensi::whereHas('karyawan', function ($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%");
            })
            ->orWhere('statusMasuk', 'like', "%{$query}%")
            ->orWhere('statusPulang', 'like', "%{$query}%")
            ->with(['jadwalKerja.shift']) // Pastikan relasi shift juga di-load
            ->get();

        return response()->json($presensi);
    }
}

