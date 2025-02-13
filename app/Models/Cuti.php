<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $fillable = ['karyawan_id', 'cutiTahun', 'expiredTahun', 'cutiPanjang', 'expiredPanjang'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class); // Cuti milik satu karyawan
    }
}
