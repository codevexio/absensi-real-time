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

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jadwalKerja()
    {
        return $this->belongsTo(JadwalKerja::class);
    }
}
