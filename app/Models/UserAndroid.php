<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAndroid extends Model
{
    use HasFactory;

    protected $table = 'user_android';

    protected $fillable = [
        'karyawan_id',
        'username',
        'password',
    ];

    // Relasi dengan Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
