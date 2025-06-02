<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Shift;
class KelolaShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $employees = JadwalKerja::with(['karyawan', 'shift'])
            ->whereHas('karyawan', function ($query) {
                $query->where('divisi', 'Keamanan'); // hanya ambil yang divisinya "keamanan"
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $shifts = Shift::all();

        return view('KelolaShiftKaryawan', compact('employees', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_id' => 'required|exists:shift,id', // Pastikan shift_id valid
        ]);

        $jadwal = JadwalKerja::findOrFail($id);
        $jadwal->shift_id = $request->shift_id;
        $jadwal->save();

        return redirect()->back()->with('success', 'Shift karyawan berhasil diperbarui.');
    }

    /**
     * Fungsi Pencarian Shift Karyawan.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        // Cari karyawan berdasarkan nama
        $employees = Karyawan::where('nama', 'like', "%{$query}%")
            ->with(['jadwalKerja.shift'])
            ->get();

        return response()->json($employees);
    }
}
