<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index()
    {
        $shift = Cuti::all();
        if ($shift->isEmpty()) {
            return response()->json(['message' => 'Shift tidak ditemukan'], 404);
        }
        return response()->json($shift);
    }
}
