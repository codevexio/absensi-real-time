<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cuti;
use Carbon\Carbon;

class ResetCuti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuti:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mereset cuti tahunan dan cuti panjang berdasarkan aturan';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Ambil semua data cuti yang ada
        $cuti = Cuti::all();

        foreach ($cuti as $data) {
            // Cek apakah cuti tahunan perlu direset
            if (Carbon::parse($data->cutiTahun)->addYear()->isPast()) {
                // Reset cuti tahunan jika sudah lebih dari 1 tahun
                $data->cutiTahun = 12; // Misalnya cuti tahunan direset menjadi 12 hari
                $data->save();
            }

            // Cek apakah cuti panjang perlu direset
            if (Carbon::parse($data->expiredPanjang)->addYears(5)->isPast()) {
                // Reset cuti panjang jika sudah lebih dari 5 tahun
                $data->expiredPanjang = 0; // Reset cuti panjang menjadi 0
                $data->save();
            }
        }

        $this->info('Cuti tahunan dan cuti panjang berhasil direset.');
    }
}
