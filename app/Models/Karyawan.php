<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $fillable = ['nama', 'username', 'password', 'golongan', 'divisi'];

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function keterlambatan()
    {
        return $this->hasMany(Keterlambatan::class);
    }

    public function pengajuanCuti()
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class);
    }

    public function cuti()
    {
        return $this->hasOne(Cuti::class);
    }

}
