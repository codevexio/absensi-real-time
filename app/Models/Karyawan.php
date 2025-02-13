<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $fillable = ['nama', 'golongan', 'divisi'];

    public function cuti()
    {
        return $this->hasOne(Cuti::class); // Setiap karyawan punya 1 data cuti
    }
}
