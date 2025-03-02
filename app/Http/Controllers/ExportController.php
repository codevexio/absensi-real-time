<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportPDF()
    {
        // Ambil data presensi karyawan
        $employees = Presensi::with(['karyawan', 'jadwalKerja.shift'])->get();

        // Load view untuk PDF
        $pdf = Pdf::loadView('export.presensiPDF', compact('employees'))
          ->setPaper('a4', 'landscape');

        // Download PDF
        return $pdf->download('data_presensi.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PresensiExport, 'data_presensi.xlsx');
    }
}
