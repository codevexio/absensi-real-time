<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateJadwalKerja extends Command
{
    protected $signature = 'jadwal:generate';
    protected $description = 'Generate jadwal kerja harian dan data presensi default untuk semua karyawan';

    public function handle()
    {
        // Reconnect untuk menghindari error prepared statement PostgreSQL
        DB::disconnect(); 
        DB::reconnect();

        $tanggalHariIni = Carbon::now()->toDateString();
        $defaultShift = Shift::first(); // Bisa diganti logika custom per karyawan jika perlu

        if (!$defaultShift) {
            $this->error('Shift belum ada di database!');
            return;
        }

        $karyawanList = Karyawan::all();

        foreach ($karyawanList as $karyawan) {
            DB::disconnect();
            DB::reconnect();

            // Cek apakah karyawan sedang cuti
            $sedangCuti = DB::table('pengajuan_cuti')
                ->where('karyawan_id', $karyawan->id)
                ->where('statusCuti', 'Disetujui')
                ->whereDate('tanggalMulai', '<=', $tanggalHariIni)
                ->whereDate('tanggalSelesai', '>=', $tanggalHariIni)
                ->exists();

            // Tentukan status kerja dan presensi
            if ($sedangCuti) {
                $statusKerja = 'Cuti';
                $statusMasuk = 'Cuti';
                $statusPulang = 'Cuti';
            } else {
                $statusKerja = 'Kerja';
                $statusMasuk = 'Tidak Presensi Masuk';
                $statusPulang = 'Tidak Presensi Pulang';
            }

            // Buat atau update jadwal kerja (update juga shift jika berubah)
            $jadwal = JadwalKerja::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tanggalKerja' => $tanggalHariIni,
                ],
                [
                    'shift_id' => $defaultShift->id,
                    'statusKerja' => $statusKerja,
                    'updated_at' => now(),
                ]
            );

            // Buat atau update data presensi default
            Presensi::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'jadwal_kerja_id' => $jadwal->id,
                    'tanggalPresensi' => $tanggalHariIni,
                ],
                [
                    'statusMasuk' => $statusMasuk,
                    'statusPulang' => $statusPulang,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Jadwal kerja dan presensi berhasil diperbarui atau dibuat!');
    }
}
