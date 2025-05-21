<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cuti';
    protected $fillable = [
        'karyawan_id',
        'jenisCuti',
        'tanggalMulai',
        'tanggalSelesai',
        'jumlahHari',
        'statusCuti',
        'alasanPenolakan',
        'file_surat_cuti', // INI WAJIB!
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function approvalCutis()
    {
        return $this->hasMany(ApprovalCuti::class);
    }

    public function penyetuju()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }



}
