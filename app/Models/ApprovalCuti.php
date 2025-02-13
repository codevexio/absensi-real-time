<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalCuti extends Model
{
    use HasFactory;
    protected $table = 'approval_cuti';
    protected $fillable = ['pengajuan_cuti_id', 'approver_id', 'status'];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanCuti::class, 'pengajuan_cuti_id');
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }
}
