<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getTanggalPresensiAttribute($value)
    {
        return Carbon::parse($value)->translatedFormat("l, j F Y");
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jadwalKerja()
    {
        return $this->belongsTo(JadwalKerja::class, 'jadwal_kerja_id');
    }

    public function keterlambatan()
    {
        return $this->hasOne(Keterlambatan::class);
    }
}
