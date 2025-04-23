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
        $defaultShift = Shift::first();

        if (!$defaultShift) {
            $this->error('Shift belum ada di database!');
            return;
        }

        $karyawanList = Karyawan::all();

        foreach ($karyawanList as $karyawan) {
            DB::disconnect(); // force reconnect to prevent invalid prepared statement
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

            // Cek apakah sudah ada jadwal kerja untuk hari ini
            $jadwal = JadwalKerja::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggalKerja', $tanggalHariIni)
                ->first();

            if ($jadwal) {
                // Jika sudah ada, update status kerja
                $jadwal->update([
                    'statusKerja' => $statusKerja,
                    'updated_at' => now(),
                ]);
            } else {
                // Jika belum ada, buat baru
                $jadwal = JadwalKerja::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggalKerja' => $tanggalHariIni,
                    'shift_id' => $defaultShift->id,
                    'statusKerja' => $statusKerja,
                ]);
            }

            // *** Generate Data Presensi Default ***
            Presensi::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'jadwal_kerja_id' => $jadwal->id,
                    'tanggalPresensi' => $tanggalHariIni,
                ],
                [
                    'statusMasuk' => $statusMasuk,
                    'statusPulang' => $statusPulang,
                ]
            );
        }

        $this->info('Jadwal kerja dan presensi berhasil diperbarui atau dibuat!');
    }
}
