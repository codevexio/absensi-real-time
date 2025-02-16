<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shift = Shift::all();
        if ($shift->isEmpty()) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 404);
        }
        return response()->json($shift);
    }

    /**
     * Tambah shift baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'namaShift' => 'required|string|max:255',
            'waktuMulai' => 'required|date_format:H:i',
            'waktuSelesai' => 'required|date_format:H:i'
        ]);

        // Menyimpan shift baru
        $shift = Shift::create($request->all());
        
        return response()->json($shift, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 404);
        }
        return response()->json($shift);
    }

    /**
     * Update data shift
     */
    public function update(Request $request, $id): JsonResponse
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 404);
        }

        $request->validate([
            'namaShift' => 'required|string|max:255',
            'waktuMulai' => 'required|date_format:H:i',
            'waktuSelesai' => 'required|date_format:H:i'
        ]);

        $shift->update($request->all());
        return response()->json($shift);
    }

   /**
     * Hapus shift
     */
    public function destroy($id): JsonResponse
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 404);
        }
        $shift->delete();
        return response()->json(['message' => 'Shift berhasil dihapus']);
    }
}
