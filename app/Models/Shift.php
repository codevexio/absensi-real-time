<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    protected $fillable = ['namaShift','waktuMulai','waktuSelesai'];

    public function jadwalKerja()
    {
        return $this->hasOne(jadwalKerja::class); // Setiap shift mempunyai 1 data jadwalKerja
    }
}
