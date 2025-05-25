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

        $golonganUrutan = ['Asisten', 'Kepala SubBagian', 'Kepala Bagian', 'Direksi'];
        $currentGolonganIndex = array_search($golongan, $golonganUrutan);

        $pengajuan = PengajuanCuti::with(['karyawan', 'cutiApprovals'])
            ->where('statusCuti', 'Diproses')
            ->whereHas('cutiApprovals', function ($query) use ($golongan) {
                $query->where('approver_golongan', $golongan)
                    ->where('status', 'Menunggu');
            })
            ->get()
            ->filter(function($item) use ($golonganUrutan, $currentGolonganIndex) {
                for ($i = 0; $i < $currentGolonganIndex; $i++) {
                    $golonganBawah = $golonganUrutan[$i];
                    $approvalBawah = $item->cutiApprovals
                        ->firstWhere('approver_golongan', $golonganBawah);

                    if (!$approvalBawah || $approvalBawah->status != 'Disetujui') {
                        return false;
                    }
                }
                return true;
            })
            ->values()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_karyawan' => $item->karyawan->nama,
                    'tanggal_pengajuan' => $item->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'message' => 'Daftar pengajuan cuti yang menunggu approval Anda',
            'data' => $pengajuan
        ]);
    }

    /**
     * Detail pengajuan cuti lengkap dengan history approval
     */
    public function show($id)
    {
        $pengajuan = PengajuanCuti::with('karyawan')->findOrFail($id);

        return response()->json([
            'nama_karyawan' => $pengajuan->karyawan->nama,
            'tanggal_pengajuan' => $pengajuan->created_at->format('Y-m-d'),
            'tanggal_mulai' => $pengajuan->tanggalMulai,
            'tanggal_selesai' => $pengajuan->tanggalSelesai,
            'file_surat_cuti' => $pengajuan->file_surat_cuti, // misal URL file bisa disesuaikan kalau disimpan di storage
        ]);
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

        if ($request->status === 'Ditolak' && empty($request->catatan)) {
            return response()->json(['message' => 'Catatan wajib diisi saat menolak cuti'], 422);
        }
 
        $user = $request->user();
        $golongan = $user->golongan;

        $pengajuan = PengajuanCuti::with('cutiApprovals')->findOrFail($id);

        $currentApproval = $pengajuan->cutiApprovals()
            ->where('approver_golongan', $golongan)
            ->where('approver_id', $user->id)
            ->where('status', 'Menunggu')
            ->first();

        if (!$currentApproval) {
            return response()->json(['message' => 'Anda tidak memiliki hak untuk meng-approve pengajuan ini atau sudah diproses.'], 403);
        }

        // Update status dan catatan approval saat ini
        $currentApproval->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        // Jika ditolak, langsung set status pengajuan cuti menjadi "Ditolak"
        if ($request->status === 'Ditolak') {
            $pengajuan->update(['statusCuti' => 'Ditolak']);
            return response()->json(['message' => 'Pengajuan cuti telah ditolak.']);
        }

        // Cek apakah semua approval sudah disetujui
        $semuaDisetujui = $pengajuan->cutiApprovals()->where('status', '!=', 'Disetujui')->count() === 0;

        if ($semuaDisetujui) {
            $pengajuan->update(['statusCuti' => 'Disetujui']);

            // Potong jatah cuti HANYA SEKARANG
            $cuti = \App\Models\Cuti::where('karyawan_id', $pengajuan->karyawan_id)->first();
            if ($pengajuan->jenisCuti === 'Cuti Tahunan') {
                $cuti->cutiTahun -= $pengajuan->jumlahHari;
            } else {
                $cuti->cutiPanjang -= $pengajuan->jumlahHari;
            }
            $cuti->save();
        }

        return response()->json(['message' => 'Approval berhasil diproses.']);
    }

}
