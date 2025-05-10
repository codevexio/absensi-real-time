<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JadwalKerja;
use App\Models\PengajuanCuti;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class JadwalKerjaController extends Controller
{
    /**
     * Get semua jadwal kerja (dengan filter karyawan & tanggal).
     */
    public function index(Request $request): JsonResponse
    {
        $query = JadwalKerja::query();

        if ($request->has('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        if ($request->has('tanggalKerja')) {
            $query->whereDate('tanggalKerja', $request->tanggalKerja);
        }

        $jadwal = $query->get();

        if ($jadwal->isEmpty()) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }

        return response()->json($jadwal);
    }

    /**
     * Tambah data jadwal kerja dengan status otomatis.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'shift_id' => 'required|exists:shift,id',
            'tanggalKerja' => 'required|date',
        ]);

        // Cek apakah karyawan memiliki pengajuan cuti yang disetujui di tanggal ini
        $isCuti = PengajuanCuti::where('karyawan_id', $request->karyawan_id)
            ->where('statusCuti', 'Disetujui') // Hanya cuti yang sudah disetujui
            ->where('tanggalMulai', '<=', $request->tanggalKerja)
            ->where('tanggalSelesai', '>=', $request->tanggalKerja)
            ->exists();

        // Set status kerja otomatis berdasarkan pengajuan cuti
        $statusKerja = $isCuti ? 'Cuti' : 'Kerja';

        $jadwal = JadwalKerja::create([
            'karyawan_id' => $request->karyawan_id,
            'shift_id' => $request->shift_id,
            'tanggalKerja' => $request->tanggalKerja,
            'statusKerja' => $statusKerja,
        ]);

        return response()->json($jadwal, 201);
    }

    /**
     * Get detail jadwal kerja.
     */
    public function show($id): JsonResponse
    {
        $jadwal = JadwalKerja::find($id);
        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }
        return response()->json($jadwal);
    }

    public function getShiftHariIni(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id; // atau sesuaikan dengan struktur kamu
        $today = Carbon::today()->toDateString();

        $jadwal = JadwalKerja::with('shift')
            ->where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $today)
            ->first();

        if ($jadwal && $jadwal->shift) {
            return response()->json([
                'status' => true,
                'message' => 'Shift ditemukan',
                'shift' => [
                    'nama' => $jadwal->shift->nama_shift,
                    'jam_masuk' => $jadwal->shift->jam_masuk,
                    'jam_pulang' => $jadwal->shift->jam_pulang,
                ]
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tidak ada shift hari ini',
            'shift' => null,
        ]);
    }

}
