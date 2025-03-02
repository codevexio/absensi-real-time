<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresensiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return Presensi::with(['karyawan', 'jadwalKerja.shift'])
            ->get()
            ->map(function ($presensi, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $presensi->karyawan->nama ?? '-',
                    'Tanggal' => $presensi->tanggalPresensi,
                    'Shift' => $presensi->jadwalKerja->shift->namaShift ?? '-',
                    'Jam Masuk' => $presensi->waktuMasuk,
                    'Status Masuk' => $presensi->statusMasuk,
                    'Jam Keluar' => $presensi->waktuPulang ?? '-',
                    'Status Keluar' => $presensi->statusPulang,
                ];
            });
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'Tanggal', 'Shift', 'Jam Masuk', 'Status Masuk', 'Jam Keluar', 'Status Keluar'];
    }
}
