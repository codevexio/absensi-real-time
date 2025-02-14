<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalCuti extends Model
{
    use HasFactory;

    protected $table = 'approval_cuti';
    protected $fillable = ['pengajuan_cuti_id', 'approver_id', 'status'];

    public function pengajuanCuti()
    {
        return $this->belongsTo(PengajuanCuti::class);
    }

    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approver_id');
    }
}
