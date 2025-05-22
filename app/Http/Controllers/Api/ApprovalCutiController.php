<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApprovalCutiController extends Controller
{
    // Menampilkan pengajuan cuti yang bisa disetujui oleh user saat ini
    public function listPengajuanUntukApprove()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $golonganUser = $user->golongan;

        // Ambil data ApprovalCuti dengan status "Menunggu" untuk golongan user
        // Approval ini harus terkait pengajuan cuti dengan status "Diproses"
        $approvalList = ApprovalCuti::with('pengajuanCuti.karyawan')
            ->where('approver_id', $user->id)
            ->where('approver_golongan', $golonganUser)
            ->where('status', 'Menunggu')
            ->whereHas('pengajuanCuti', function ($query) {
                $query->where('statusCuti', 'Diproses');
            })
            ->get();

        // Kalau ternyata ada user lain di golongan yang sama, mereka juga bisa lihat pengajuan cuti untuk golongan ini,
        // Jadi kita perlu cari semua approval status Menunggu di golongan user, bukan hanya milik user ini

        // Jadi ubah sedikit query untuk ambil semua approval status Menunggu di golongan user
        $approvalList = ApprovalCuti::with('pengajuanCuti.karyawan')
            ->where('approver_golongan', $golonganUser)
            ->where('status', 'Menunggu')
            ->whereHas('pengajuanCuti', function ($query) {
                $query->where('statusCuti', 'Diproses');
            })
            ->get();

        // Filter approval list yang unik berdasarkan pengajuan_cuti_id supaya satu pengajuan cuti muncul sekali saja
        $pengajuanUnik = $approvalList->unique('pengajuan_cuti_id')->values();

        // Format data agar lebih jelas
        $result = $pengajuanUnik->map(function ($approval) {
            return [
                'pengajuan_cuti_id' => $approval->pengajuan_cuti_id,
                'karyawan_id' => $approval->pengajuanCuti->karyawan->id,
                'nama_karyawan' => $approval->pengajuanCuti->karyawan->nama,
                'jenis_cuti' => $approval->pengajuanCuti->jenisCuti,
                'tanggal_mulai' => $approval->pengajuanCuti->tanggalMulai,
                'tanggal_selesai' => $approval->pengajuanCuti->tanggalSelesai,
                'jumlah_hari' => $approval->pengajuanCuti->jumlahHari,
                'status_cuti' => $approval->pengajuanCuti->statusCuti,
                'status_approval' => $approval->status,
            ];
        });

        return response()->json([
            'message' => 'Daftar pengajuan cuti yang perlu di-approve',
            'data' => $result,
        ]);
    }
}
