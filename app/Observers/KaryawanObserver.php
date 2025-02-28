<?php

namespace App\Observers;

use App\Models\Karyawan;
use App\Models\Cuti;

class KaryawanObserver
{
    /**
     * Handle the Karyawan "created" event.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return void
     */
    public function created(Karyawan $karyawan)
    {
        // Buat entri cuti baru ketika karyawan baru ditambahkan
        Cuti::create([
            'karyawan_id' => $karyawan->id, // ID karyawan
            'cutiTahun' => 12,  // Jumlah cuti tahun, bisa diubah sesuai kebijakan
            'expiredTahun' => now()->addYear(),  // Masa berlaku cuti tahun, misalnya 1 tahun dari sekarang
            'cutiPanjang' => 60,  // Jumlah cuti panjang (misalnya, bisa diubah sesuai kebijakan)
            'expiredPanjang' => null,  // Tidak ada tanggal expired untuk cuti panjang
        ]);
    }

    /**
     * Handle the Karyawan "updated" event.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return void
     */
    public function updated(Karyawan $karyawan)
    {
        // Logika lain jika diperlukan ketika data karyawan diupdate
    }

    /**
     * Handle the Karyawan "deleted" event.
     *
     * @param  \App\Models\Karyawan  $karyawan
     * @return void
     */
    public function deleted(Karyawan $karyawan)
    {
        // Hapus entri cuti jika karyawan dihapus (opsional)
        Cuti::where('karyawan_id', $karyawan->id)->delete();
    }
}
