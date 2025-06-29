<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GenerateJadwalKerja extends Command
{
    protected $signature = 'jadwal:generate';
    protected $description = 'Generate jadwal kerja harian dan data presensi default untuk semua karyawan, kecuali hari libur dan weekend';

    public function handle()
    {
        $tanggalHariIni = Carbon::now();
        $hariIni = $tanggalHariIni->format('l'); // Saturday, Sunday, etc
        $tanggalString = $tanggalHariIni->toDateString(); // Y-m-d

        // // ✅ 1. Skip weekend
        // if ($hariIni === 'Saturday' || $hariIni === 'Sunday') {
        //     $this->info("Hari ini ($hariIni) adalah weekend. Jadwal tidak dibuat.");
        //     return;
        // }

        // ✅ 2. Cek tanggal merah via API
        try {
            $response = Http::get("https://api-harilibur.vercel.app/api");
            if ($response->successful()) {
                $liburNasional = collect($response->json());
                $isTanggalMerah = $liburNasional->contains('holiday_date', $tanggalString);

                if ($isTanggalMerah) {
                    $this->info("Hari ini ($tanggalString) adalah tanggal merah. Jadwal tidak dibuat.");
                    return;
                }
            } else {
                $this->warn("Gagal memeriksa tanggal merah. Lanjutkan proses.");
            }
        } catch (\Exception $e) {
            $this->warn("Terjadi error saat cek tanggal merah: " . $e->getMessage());
        }

        // ✅ 3. Ambil shift default
        $defaultShift = Shift::first();
        if (!$defaultShift) {
            $this->error('Shift belum tersedia di database!');
            return;
        }

        $karyawanList = Karyawan::all();

        foreach ($karyawanList as $karyawan) {
            // ✅ 4. Cek apakah karyawan sedang cuti hari ini
            $sedangCuti = DB::table('pengajuan_cuti')
                ->where('karyawan_id', $karyawan->id)
                ->where('statusCuti', 'Disetujui')
                ->whereDate('tanggalMulai', '<=', $tanggalString)
                ->whereDate('tanggalSelesai', '>=', $tanggalString)
                ->exists();

            if ($sedangCuti) {
                $statusKerja = 'Cuti';
                $statusMasuk = 'Cuti';
                $statusPulang = 'Cuti';
            } else {
                $statusKerja = 'Kerja';
                $statusMasuk = 'Tidak Presensi Masuk';
                $statusPulang = 'Tidak Presensi Pulang';
            }

            // ✅ 5. Cek apakah jadwal kerja hari ini sudah ada
            $jadwal = JadwalKerja::firstOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tanggalKerja' => $tanggalString,
                ],
                [
                    'shift_id' => $defaultShift->id,
                    'statusKerja' => $statusKerja,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // ✅ 6. Buat / update presensi default
            Presensi::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'jadwal_kerja_id' => $jadwal->id,
                    'tanggalPresensi' => $tanggalString,
                ],
                [
                    'statusMasuk' => $statusMasuk,
                    'statusPulang' => $statusPulang,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info("✅ Jadwal kerja dan presensi berhasil dibuat untuk tanggal $tanggalString.");
    }
}
