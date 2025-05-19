<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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
            'golongan' => 'required|in:Direksi,Kepala Bagian,Kepala SubBagian,Asisten,Staff',
            'divisi' => 'required|in:Bag.Sekper,Bag.SPI,Bag.SDM,Bag.Tanaman,Bag.Teknik & Pengolahan, 
                                     Bag.Keuangan,Bag.Pemasaran & P.Baku,Bag.Perencana Strategis, 
                                     Bag.Hukum,Bag.Pengadaan & TI,Keamanan,Papam, 
                                     Bag.Percepetan Transformasi Teknologi,Bag.Teknik & Pengolahan'
        ]);

        $karyawan = Karyawan::create($request->all());

        // Buat data cuti baru untuk karyawan ini
        Cuti::create([
            'karyawan_id' => $karyawan->id,
            'cutiTahun' => 12, // Default cuti tahunan
            'cutiPanjang' => 60, // Default cuti panjang
            'expiredTahun' => Carbon::now()->addYear(), // Berlaku 1 tahun dari sekarang
            'expiredPanjang' => Carbon::now()->addYears(5) // Berlaku 5 tahun dari sekarang
        ]);

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
            'golongan' => 'in:Direksi,Kepala Bagian,Kepala SubBagian,Asisten,Staff',
            'divisi' => 'in:Bag.Sekper,Bag.SPI,Bag.SDM,Bag.Tanaman,Bag.Teknik & Pengolahan, 
                            Bag.Keuangan,Bag.Pemasaran & P.Baku,Bag.Perencana Strategis, 
                            Bag.Hukum,Bag.Pengadaan & TI,Keamanan,Papam, 
                            Bag.Percepetan Transformasi Teknologi,Bag.Teknik & Pengolahan'
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
