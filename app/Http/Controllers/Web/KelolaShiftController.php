<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;

class KelolaShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data presensi dengan relasi karyawan, jadwal kerja, dan shift
        $employees = Karyawan::with(['jadwalKerja.shift'])->get();
        
        // Pass data ke view
        return view('KelolaShiftKaryawan', compact('employees'));
    }

    /**
     * Fungsi Pencarian Presensi.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        // Cari presensi berdasarkan nama karyawan atau status
        $shift = Karyawan::whereHas('karyawan', function ($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%");
            })
            ->with(['jadwalKerja.shift']) // Pastikan relasi shift juga di-load
            ->get();

        return response()->json($shift);
    }
}
