<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKerja;
use Illuminate\Http\JsonResponse;

class JadwalKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = JadwalKerja::all();
        if ($jadwal->isEmpty()) {
            return response()->json(['message' => 'Jadwal Kerja tidak ditemukan'], 404);
        }
        return response()->json($jadwal);
    }

    /**
     * Nambahkan data jadwal kerja
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id', // Pastikan karyawan_id ada di tabel karyawan
            'shift_id' => 'required|exists:shift,id', // Pastikan shift_id ada di tabel shift
            'tanggalKerja' => 'required|date', // Pastikan tanggalKerja adalah format tanggal yang valid
            'statusKerja' => 'required|in:Kerja,Cuti', // Status kerja hanya bisa 'Kerja' atau 'Cuti'
        ]);

        // Simpan data ke tabel yang sesuai
        $jadwal = JadwalKerja::create([
            'karyawan_id' => $request->karyawan_id,
            'shift_id' => $request->shift_id,
            'tanggalKerja' => $request->tanggalKerja,
            'statusKerja' => $request->statusKerja,
        ]);

        // Kembalikan respons dengan data jadwal yang baru disimpan
        return response()->json($jadwal, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $jadwal = JadwalKerja::find($id);
        if (!$jadwal) {
            return response()->json(['message' => 'JadwalKerja tidak ditemukan'], 404);
        }
        return response()->json($jadwal);
    }

    /**
     * Update data Jadwal kerja
     */
    public function update(Request $request, $id): JsonResponse
    {
        $jadwal = JadwalKerja::find($id);
        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id', 
            'shift_id' => 'required|exists:shift,id',
            'tanggalKerja' => 'required|date',
            'statusKerja' => 'required|in:Kerja,Cuti',
        ]);

        $jadwal->update($request->all());
        return response()->json($jadwal);
    }


    /**
     * Hapus Jadwal Kerja
     */
    public function destroy($id): JsonResponse
    {
        $jadwal = JadwalKerja::find($id);
        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal kerja berhasil dihapus']);
    }
}
