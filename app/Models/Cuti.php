<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
    protected $fillable = ['karyawan_id', 'cutiTahun', 'expiredTahun', 'cutiPanjang', 'expiredPanjang'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
