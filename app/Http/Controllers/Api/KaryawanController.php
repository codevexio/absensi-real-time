<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Http\JsonResponse;

class KaryawanController extends Controller
{
    /**
     * Ambil semua data karyawan dengan filter dan paginasi
     */
    public function index(Request $request): JsonResponse
    {
        $query = Karyawan::query();

        // Filter berdasarkan golongan atau divisi
        if ($request->has('golongan')) {
            $query->where('golongan', $request->golongan);
        }
        if ($request->has('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        // Paginasi dengan default 10 data per halaman
        $perPage = $request->get('per_page', 10);
        $karyawan = $query->paginate($perPage);

        return response()->json($karyawan);
    }

    /**
     * Ambil detail karyawan
     */
    public function show($id): JsonResponse
    {
        $karyawan = Karyawan::find($id);
        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }
        return response()->json($karyawan);
    }

    /**
     * Tambah karyawan baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'golongan' => 'required|in:A,B,C,D,E',
            'divisi' => 'required|in:A,B,C,D,E'
        ]);

        $karyawan = Karyawan::create($request->all());
        return response()->json($karyawan, 201);
    }

    /**
     * Update data karyawan
     */
    public function update(Request $request, $id): JsonResponse
    {
        $karyawan = Karyawan::find($id);
        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        $request->validate([
            'nama' => 'string|max:255',
            'golongan' => 'in:A,B,C,D,E',
            'divisi' => 'in:A,B,C,D,E'
        ]);

        $karyawan->update($request->all());
        return response()->json($karyawan);
    }

    /**
     * Hapus karyawan
     */
    public function destroy($id): JsonResponse
    {
        $karyawan = Karyawan::find($id);
        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }
        $karyawan->delete();
        return response()->json(['message' => 'Karyawan berhasil dihapus']);
    }
}
