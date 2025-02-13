<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanCuti extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_cuti';
    protected $fillable = [
        'karyawan_id', 'jenisCuti', 'tanggalMulai', 'tanggalSelesai',
        'jumlahHari', 'statusCuti', 'alasanPenolakan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
