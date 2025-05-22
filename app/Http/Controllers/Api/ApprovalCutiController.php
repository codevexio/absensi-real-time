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
    public function detailPengajuanCuti($id)
    {
        $pengajuan = PengajuanCuti::with('karyawan')->findOrFail($id);

        // Ambil approval group by golongan
        $approvals = PengajuanCutiApproval::where('pengajuan_cuti_id', $id)
            ->orderByRaw("FIELD(status, 'Menunggu', 'Disetujui', 'Ditolak')")
            ->get()
            ->groupBy('golongan');

        $finalApprovalList = [];
        $foundCurrentGolongan = false;

        foreach ($approvals as $golongan => $list) {
            // Kalau sudah ada golongan yang sedang aktif, golongan selanjutnya tidak diproses
            if ($foundCurrentGolongan) {
                foreach ($list as $approval) {
                    $approval->status = 'Terkunci';
                    $finalApprovalList[] = $approval;
                }
                continue;
            }

            // Cek kalau golongan ini sudah disetujui oleh salah satu
            $alreadyApproved = $list->firstWhere('status', 'Disetujui');
            if ($alreadyApproved) {
                // Tandai semua sebagai disetujui
                foreach ($list as $approval) {
                    if ($approval->status == 'Menunggu') {
                        $approval->status = 'Disetujui oleh rekan sejawat';
                    }
                    $finalApprovalList[] = $approval;
                }
            } else {
                // Ini golongan yang aktif untuk approval
                $foundCurrentGolongan = true;
                foreach ($list as $approval) {
                    $finalApprovalList[] = $approval;
                }
            }
        }

        return response()->json([
            'pengajuan' => $pengajuan,
            'approval' => $finalApprovalList
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = auth()->user(); // karyawan login

        $approval = PengajuanCutiApproval::where('pengajuan_cuti_id', $id)
            ->where('karyawan_id', $user->id)
            ->firstOrFail();

        // Cek apakah sudah ada yang approve dari golongan ini
        $sudahApprove = PengajuanCutiApproval::where('pengajuan_cuti_id', $id)
            ->where('golongan', $approval->golongan)
            ->where('status', 'Disetujui')
            ->exists();

        if ($sudahApprove) {
            return response()->json([
                'message' => 'Sudah disetujui oleh rekan sejawat.'
            ], 403);
        }

        $approval->status = 'Disetujui';
        $approval->catatan = $request->catatan;
        $approval->save();

        return response()->json(['message' => 'Pengajuan cuti disetujui.']);
    }

    public function tolak(Request $request, $id)
    {
        $user = auth()->user();

        $approval = PengajuanCutiApproval::where('pengajuan_cuti_id', $id)
            ->where('karyawan_id', $user->id)
            ->firstOrFail();

        $alreadyProcessed = PengajuanCutiApproval::where('pengajuan_cuti_id', $id)
            ->where('golongan', $approval->golongan)
            ->where('status', 'Disetujui')
            ->exists();

        if ($alreadyProcessed) {
            return response()->json([
                'message' => 'Sudah diproses oleh rekan sejawat.'
            ], 403);
        }

        $approval->status = 'Ditolak';
        $approval->catatan = $request->catatan;
        $approval->save();

        // Opsional: set alasan penolakan global
        $pengajuan = PengajuanCuti::find($id);
        $pengajuan->alasan_penolakan = $request->catatan;
        $pengajuan->save();

        return response()->json(['message' => 'Pengajuan cuti ditolak.']);
    }

}
