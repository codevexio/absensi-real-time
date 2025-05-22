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
    // GET: Menampilkan pengajuan cuti yang perlu diverifikasi oleh approver
    public function listPengajuanUntukDisetujui()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Anda belum login'], 401);
        }

        $list = ApprovalCuti::with('pengajuan.karyawan')
            ->where('approver_id', $user->id)
            ->where('status', 'Menunggu')
            ->get()
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
            'message' => 'Daftar pengajuan cuti menunggu persetujuan',
            'data' => $list
        ]);
    }

    // POST: Menyetujui atau menolak pengajuan cuti
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

        // Update status dan catatan
        $approval->update([
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        $pengajuan = $approval->pengajuan;

        // Jika ditolak, langsung ubah status pengajuan jadi Ditolak
        if ($data['status'] === 'Ditolak') {
            $pengajuan->update(['statusCuti' => 'Ditolak']);
        } else {
            // Jika semua approval sudah disetujui, set status pengajuan jadi Disetujui
            $jumlahMenunggu = $pengajuan->approvals->where('status', 'Menunggu')->count();
            $jumlahDitolak = $pengajuan->approvals->where('status', 'Ditolak')->count();

            if ($jumlahMenunggu == 0 && $jumlahDitolak == 0) {
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
