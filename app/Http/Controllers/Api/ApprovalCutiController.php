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

        if ($currentGolonganIndex === false) {
            return response()->json([
                'message' => 'Golongan user tidak valid',
                'data' => []
            ]);
        }

        \Log::info('User Golongan: ' . $golongan);
        \Log::info('Golongan index: ' . $currentGolonganIndex);

        // Ambil pengajuan dengan status Diproses yang ada approval golongan user menunggu
        $pengajuan = PengajuanCuti::with(['karyawan', 'cutiApprovals'])
            ->where('statusCuti', 'Diproses')
            ->whereHas('cutiApprovals', function ($query) use ($golongan) {
                $query->where('approver_golongan', $golongan)
                    ->where('status', 'Menunggu');
            })
            ->get();

        \Log::info('Jumlah pengajuan sebelum filter: ' . $pengajuan->count());

        $filtered = $pengajuan->filter(function($item) use ($golonganUrutan, $currentGolonganIndex) {
            \Log::info("Memeriksa pengajuan ID {$item->id} oleh {$item->karyawan->nama} (golongan {$item->karyawan->golongan})");

            // Cek approval golongan bawah sudah disetujui semua
            for ($i = 0; $i < $currentGolonganIndex; $i++) {
                $golonganBawah = $golonganUrutan[$i];
                $approvalBawah = $item->cutiApprovals->firstWhere('approver_golongan', $golonganBawah);

                if ($approvalBawah) {
                    \Log::info("Approval golongan bawah {$golonganBawah} status: {$approvalBawah->status}");
                } else {
                    \Log::info("Approval golongan bawah {$golonganBawah} tidak ditemukan");
                }

                if ($approvalBawah && $approvalBawah->status != 'Disetujui') {
                    return false;
                }
            }

            $pengajuGolonganIndex = array_search($item->karyawan->golongan, $golonganUrutan);
            if ($pengajuGolonganIndex === false) {
                \Log::info("Golongan pengaju tidak valid: {$item->karyawan->golongan}");
                return false;
            }

            if ($pengajuGolonganIndex >= $currentGolonganIndex) {
                \Log::info("Pengaju golongan index {$pengajuGolonganIndex} >= current golongan index {$currentGolonganIndex}, skip");
                return false;
            }

            return true;
        });

        \Log::info('Jumlah pengajuan setelah filter: ' . $filtered->count());

        $result = $filtered->values()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_karyawan' => $item->karyawan->nama,
                'golongan_karyawan' => $item->karyawan->golongan,
                'status_cuti' => $item->statusCuti,
                'tanggal_pengajuan' => $item->created_at->format('Y-m-d'),
                'approvals' => $item->cutiApprovals->map(function($app) {
                    return [
                        'approver_golongan' => $app->approver_golongan,
                        'status' => $app->status,
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'Daftar pengajuan cuti yang menunggu approval Anda',
            'data' => $result
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
            'file_surat_cuti' => asset('storage/' . $pengajuan->file_surat_cuti),
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
