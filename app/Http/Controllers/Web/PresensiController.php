<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Presensi::with('karyawan')->get();
        $employees = Presensi::with('jadwalKerja')->get()::with('shift')->get();
        return view('Presensi', compact('employees'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query', ''); // Ambil query pencarian dari request

        $akun = Presensi::where('nama', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%")
                        ->get();

        return response()->json($akun);
    }
}
