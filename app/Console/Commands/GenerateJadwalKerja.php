<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateJadwalKerja extends Command
{
    protected $signature = 'jadwal:generate';
    protected $description = 'Generate jadwal kerja harian untuk semua karyawan';

    public function handle()
    {
        $tanggalHariIni = Carbon::now()->toDateString();
        $defaultShift = Shift::first();

        if (!$defaultShift) {
            $this->error('Shift belum ada di database!');
            return;
        }

        $karyawanList = Karyawan::all();

        foreach ($karyawanList as $karyawan) {
            // Cek apakah karyawan sedang cuti
            $sedangCuti = DB::table('pengajuan_cuti')
                ->where('karyawan_id', $karyawan->id)
                ->where('statusCuti', 'Disetujui')
                ->whereDate('tanggalMulai', '<=', $tanggalHariIni)
                ->whereDate('tanggalSelesai', '>=', $tanggalHariIni)
                ->exists();

            // Tentukan status kerja
            $statusKerja = $sedangCuti ? 'Cuti' : 'Kerja';

            // Buat jadwal kerja
            JadwalKerja::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tanggalKerja' => $tanggalHariIni,
                ],
                [
                    'shift_id' => $defaultShift->id,
                    'statusKerja' => $statusKerja,
                ]
            );
        }

        $this->info('Jadwal kerja berhasil dibuat!');
    }
}

