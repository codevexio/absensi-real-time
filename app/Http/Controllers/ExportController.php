<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportPDF(Request $request)
    {
        $bulan = (int) $request->input('month', date('m'));
        $tahun = (int) $request->input('year', date('Y'));

        $employees = Presensi::with(['karyawan', 'jadwalKerja.shift'])
            ->whereMonth('tanggalPresensi', $bulan)
            ->whereYear('tanggalPresensi', $tahun)
            ->get();

        $pdf = Pdf::loadView('export.presensiPDF', [
            'employees' => $employees,
            'bulan' => $bulan, // pastikan ini angka
            'tahun' => $tahun,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('data_presensi.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PresensiExport, 'data_presensi.xlsx');
    }
}
