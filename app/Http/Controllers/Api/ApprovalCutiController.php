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
    public function listPengajuanUntukDisetujui()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Anda belum login'], 401);
        }

        // Ambil semua approval milik user yang masih Menunggu
        $approvals = ApprovalCuti::with('pengajuan.karyawan', 'pengajuan.approvals')
            ->where('approver_id', $user->id)
            ->where('status', 'Menunggu')
            ->get()
            ->filter(function ($approval) {
                $allApprovals = $approval->pengajuan->approvals;

                // Cari level aktif saat ini (level terkecil yang masih Menunggu)
                $activeLevel = $allApprovals
                    ->where('status', 'Menunggu')
                    ->pluck('level')
                    ->min();

                // Hanya izinkan approval jika level user saat ini adalah level aktif
                return $approval->level == $activeLevel;
            })
            ->map(function ($item) {
                return [
                    'approval_id' => $item->id,
                    'pengajuan_id' => $item->pengajuan->id,
                    'nama_pengaju' => $item->pengajuan->karyawan->nama,
                    'jenis_cuti' => $item->pengajuan->jenisCuti,
                    'tanggal_mulai' => $item->pengajuan->tanggalMulai,
                    'tanggal_selesai' => $item->pengajuan->tanggalSelesai,
                    'jumlah_hari' => $item->pengajuan->jumlahHari,
                    'status_pengajuan' => $item->pengajuan->statusCuti,
                    'file_surat_cuti' => $item->pengajuan->file_surat_cuti,
                ];
            });

        return response()->json([
            'message' => 'Daftar pengajuan cuti menunggu giliran Anda',
            'data' => $approvals->values()
        ]);
    }

    // Menyetujui atau menolak pengajuan cuti
    public function prosesApproval(Request $request, $approvalId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Anda belum login'], 401);
        }

        $approval = ApprovalCuti::with('pengajuan.approvals')->find($approvalId);

        if (!$approval || $approval->approver_id != $user->id) {
            return response()->json(['message' => 'Data approval tidak ditemukan atau bukan milik Anda'], 403);
        }

        if ($approval->status != 'Menunggu') {
            return response()->json(['message' => 'Approval sudah diproses sebelumnya'], 400);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Jika status Ditolak, catatan wajib diisi
        if ($data['status'] === 'Ditolak' && empty($data['catatan'])) {
            return response()->json(['message' => 'Catatan wajib diisi jika cuti ditolak'], 422);
        }

        // Update status dan catatan untuk approval ini
        $approval->update([
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        $pengajuan = $approval->pengajuan;

        if ($data['status'] === 'Ditolak') {
            // Jika ditolak, status pengajuan langsung Ditolak
            $pengajuan->update(['statusCuti' => 'Ditolak']);
        } else {
            // Jika disetujui, tandai approval lain di level yang sama sebagai "Lewati"
            $currentLevel = $approval->level;

            ApprovalCuti::where('pengajuan_cuti_id', $pengajuan->id)
                ->where('level', $currentLevel)
                ->where('id', '!=', $approval->id)
                ->where('status', 'Menunggu')
                ->update(['status' => 'Lewati']);

            // Cek apakah masih ada approval yang Menunggu atau Ditolak
            $stillWaiting = $pengajuan->approvals->where('status', 'Menunggu')->count();
            $rejected = $pengajuan->approvals->where('status', 'Ditolak')->count();

            if ($stillWaiting == 0 && $rejected == 0) {
                $pengajuan->update(['statusCuti' => 'Disetujui']);
            }
        }

        return response()->json([
            'message' => 'Approval berhasil diproses',
            'data' => [
                'approval_id' => $approval->id,
                'status' => $approval->status,
                'catatan' => $approval->catatan,
            ]
        ]);
    }
}
