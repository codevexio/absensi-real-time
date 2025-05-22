<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use Illuminate\Support\Facades\DB;

class ApprovalCutiController extends Controller
{
    /**
     * Tampilkan daftar pengajuan cuti yang harus di-approve oleh golongan user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $golongan = $user->golongan;

        // Cari pengajuan cuti dengan status 'Menunggu' yang masih ada approval 'Menunggu' di golongan user
        $pengajuan = PengajuanCuti::where('status', 'Menunggu')
            ->whereHas('cutiApprovals', function ($query) use ($golongan) {
                $query->where('golongan', $golongan)
                    ->where('status', 'Menunggu');
            })
            ->with(['cutiApprovals' => function ($query) use ($golongan) {
                // Hanya ambil approval golongan ini yg status menunggu supaya bisa dilihat
                $query->where('golongan', $golongan);
            }, 'karyawan'])
            ->get();

        return response()->json($pengajuan);
    }

    /**
     * Detail pengajuan cuti lengkap dengan history approval
     */
    public function show($id)
    {
        $pengajuan = PengajuanCuti::with(['cutiApprovals.user', 'karyawan'])->findOrFail($id);

        return response()->json($pengajuan);
    }

    /**
     * Proses approve atau tolak pengajuan cuti oleh user
     */
    public function prosesApproval(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $golongan = $user->golongan;

        // Ambil pengajuan cuti dan relasi approval untuk golongan user yang statusnya menunggu
        $pengajuan = PengajuanCuti::with('cutiApprovals')->findOrFail($id);

        $currentApproval = $pengajuan->cutiApprovals()
            ->where('golongan', $golongan)
            ->where('status', 'Menunggu')
            ->first();

        // Validasi hak approve: harus ada approval menunggu di golongan user dan approver harus sama dengan user ini
        if (!$currentApproval) {
            return response()->json(['message' => 'Tidak ada approval yang harus Anda proses'], 403);
        }

        if ($currentApproval->karyawan_id != $user->id) {
            return response()->json(['message' => 'Anda tidak berhak melakukan approval ini'], 403);
        }

        DB::beginTransaction();
        try {
            // Update status approval yang sedang diproses
            $currentApproval->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
            ]);

            if ($request->status === 'Ditolak') {
                // Jika ditolak, langsung update pengajuan cuti jadi ditolak
                $pengajuan->update([
                    'status' => 'Ditolak',
                    'alasan_penolakan' => $request->catatan,
                ]);

                // Tandai semua approval yang masih menunggu sebagai "Diabaikan"
                $pengajuan->cutiApprovals()
                    ->where('status', 'Menunggu')
                    ->update(['status' => 'Diabaikan']);
            } else {
                // Jika disetujui, update approval lain yang masih menunggu di golongan yang sama jadi "Diabaikan"
                $pengajuan->cutiApprovals()
                    ->where('golongan', $golongan)
                    ->where('status', 'Menunggu')
                    ->where('id', '!=', $currentApproval->id)
                    ->update(['status' => 'Diabaikan']);

                // Cek ada approval menunggu selanjutnya?
                $nextApproval = $pengajuan->cutiApprovals()
                    ->where('status', 'Menunggu')
                    ->orderBy('id')
                    ->first();

                if (!$nextApproval) {
                    // Kalau gak ada lagi, update pengajuan cuti jadi Disetujui
                    $pengajuan->update(['status' => 'Disetujui']);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Approval berhasil']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses approval', 'error' => $e->getMessage()], 500);
        }
    }
}
