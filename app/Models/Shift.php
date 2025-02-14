<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';
    protected $fillable = ['namaShift', 'waktuMulai', 'waktuSelesai'];

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class);
    }
}
