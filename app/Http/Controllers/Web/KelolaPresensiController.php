<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
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

        // ✅ Validasi agar tahun minimal 2025 dan bulan valid
        if ($year || $month) {
            $request->validate([
                'year' => 'nullable|integer|min:2025|max:' . now()->year,
                'month' => 'nullable|integer|min:1|max:12',
            ]);
        }

        $query = Presensi::with(['karyawan', 'jadwalKerja.shift']);

        if ($month && $year) {
            $query->whereMonth('tanggalPresensi', $month)
                ->whereYear('tanggalPresensi', $year);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('KelolaPresensi', compact('employees'));
    }

    /**
     * Fungsi Pencarian Presensi.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $presensi = Presensi::whereHas('karyawan', function ($q) use ($query) {
            $q->where('nama', 'like', "%{$query}%");
        })
            ->orWhere('statusMasuk', 'like', "%{$query}%")
            ->orWhere('statusPulang', 'like', "%{$query}%")
            ->with(['jadwalKerja.shift'])
            ->get();

        return response()->json($presensi);
    }
    /**
     * Update status presensi karyawan.
     */
    public function updateStatus(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $request->validate([
            'statusMasuk' => 'nullable|string',
            'statusPulang' => 'nullable|string',
        ]);

        $presensi->update([
            'statusMasuk' => $request->statusMasuk ?? $presensi->statusMasuk,
            'statusPulang' => $request->statusPulang ?? $presensi->statusPulang,
        ]);

        return redirect()->route('kelola-presensi.index')->with('success', 'Status presensi berhasil diperbarui');
    }
}
