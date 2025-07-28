<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Shift;

class KelolaTableShift extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('created_at', 'desc')->paginate(10);
        return view('KelolaTableShift', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaShift' => 'required|string|max:255',
            'waktuMulai' => 'required|date_format:H:i',
            'waktuSelesai' => 'required|date_format:H:i',
        ]);

        $shift = Shift::create($request->all());

        return redirect()->route('web/kelola-table-shift')->with('success', 'Shift berhasil ditambahkan.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'namaShift' => 'required|string|max:255',
            'waktuMulai' => 'required|date_format:H:i',
            'waktuSelesai' => 'required|date_format:H:i',
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update([
            'namaShift' => $request->namaShift,
            'waktuMulai' => $request->waktuMulai,
            'waktuSelesai' => $request->waktuSelesai,
        ]);

        return redirect()->route('web/kelola-table-shift')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

       return redirect()->route('web/kelola-table-shift')->with('success', 'Shift berhasil dihapus.');
    }
}
