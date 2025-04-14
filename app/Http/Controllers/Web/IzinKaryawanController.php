<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use Illuminate\Http\Request;

class IzinKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = PengajuanCuti::with('karyawan'); 
        return view('IzinKaryawan', compact('employees'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query', ''); // Ambil query pencarian dari request

        $akun = PengajuanCuti::where('nama', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%")
                        ->get();

        return response()->json($akun);
    }

    
}
