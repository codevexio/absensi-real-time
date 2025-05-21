<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'karyawan';
    protected $fillable = ['nama', 'username', 'password', 'golongan', 'divisi'];

    protected $hidden = [
        'password',
    ];

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
        return $this->hasMany(JadwalKerja::class,'karyawan_id', 'id');
    }

    public function cuti()
    {
        return $this->hasOne(Cuti::class);
    }

    public function approvalCuti()
    {
        return $this->hasMany(ApprovalCuti::class, 'approver_id');
    }

}
