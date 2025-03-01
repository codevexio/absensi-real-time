<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $fillable = [
        'karyawan_id', 'jadwal_kerja_id', 'tanggalPresensi',
        'waktuMasuk', 'statusMasuk', 'waktuPulang', 'statusPulang',
        'imageMasuk', 'imagePulang', 'lokasiMasuk', 'lokasiPulang'
    ];

    protected $casts = [
        'lokasiMasuk' => 'array',  // Mengonversi lokasiMasuk ke array (untuk format JSON)
        'lokasiPulang' => 'array', // Mengonversi lokasiPulang ke array (untuk format JSON)
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jadwalKerja()
    {
        return $this->belongsTo(JadwalKerja::class, 'jadwal_kerja_id');
    }
}
