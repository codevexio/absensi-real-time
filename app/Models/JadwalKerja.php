<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKerja extends Model
{
    protected $table = 'jadwalKerja';
    protected $fillable = ['tanggalKerja','statusKerja'];

    public function shift()
    {
        return $this->hasMany(Shift::class); // Setiap jadwalKerja mempunyai banyak data jadwalKerja
    }
}
