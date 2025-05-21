<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalCuti extends Model
{
    use HasFactory;

    protected $table = 'approval_cuti';

    protected $fillable = [
        'pengajuan_cuti_id',
        'approver_id',
        'approver_golongan',
        'status',
        'catatan',
    ];

    // Relasi ke pengajuan cuti
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanCuti::class, 'pengajuan_cuti_id');
    }

    // Relasi ke karyawan yang menyetujui
    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }
}
