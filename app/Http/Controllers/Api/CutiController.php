<?php
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use App\Models\ApprovalCuti;
use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    // Update expired cuti tahunan dan cuti panjang
    public function updateExpiredCuti()
    {
        $karyawan = Karyawan::with('cutis')->get();

        foreach ($karyawan as $k) {
            $cuti = $k->cutis;

            if ($cuti && $cuti->expiredTahun <= now()) {
                $cuti->expiredTahun = now()->addYear(); 
                $cuti->cutiTahun = 12; 
            }

            if ($cuti && $cuti->expiredPanjang <= now()) {
                $cuti->expiredPanjang = now()->addYears(5);
                $cuti->cutiPanjang = 60;
            }

            if ($cuti) {
                $cuti->save();
            }
        }

        return response()->json(['message' => 'Cuti berhasil diperbarui!']);
    }

    // Fungsi untuk mengajukan cuti
    public function ajukanCuti(Request $request)
    {
        $karyawan = Karyawan::find($request->karyawan_id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan!'], 404);
        }

        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $karyawan->id,
            'jenisCuti' => $request->jenisCuti,
            'tanggalMulai' => $request->tanggalMulai,
            'tanggalSelesai' => $request->tanggalSelesai,
            'jumlahHari' => $request->jumlahHari,
            'statusCuti' => 'Diproses'
        ]);

        $approver = Karyawan::where('golongan', $this->getAtasan($karyawan->golongan))->first();
        
        if ($approver) {
            ApprovalCuti::create([
                'pengajuan_cuti_id' => $pengajuan->id,
                'approver_id' => $approver->id,
                'status' => 'Menunggu'
            ]);
        }

        return response()->json(['message' => 'Pengajuan cuti berhasil dibuat, menunggu persetujuan.']);
    }

    // Fungsi untuk menyetujui cuti
    public function approveCuti($approval_id)
    {
        $approval = ApprovalCuti::find($approval_id);

        if (!$approval) {
            return response()->json(['message' => 'Data approval tidak ditemukan!'], 404);
        }

        $approval->update(['status' => 'Disetujui']);

        $pengajuan = PengajuanCuti::find($approval->pengajuan_cuti_id);
        $karyawan = Karyawan::find($pengajuan->karyawan_id);

        $atasanBaru = Karyawan::where('golongan', $this->getAtasan($karyawan->golongan))->first();

        if ($atasanBaru) {
            ApprovalCuti::create([
                'pengajuan_cuti_id' => $pengajuan->id,
                'approver_id' => $atasanBaru->id,
                'status' => 'Menunggu'
            ]);
        } else {
            $pengajuan->update(['statusCuti' => 'Disetujui']);
        }

        return response()->json(['message' => 'Persetujuan berhasil diperbarui.']);
    }

    // Fungsi untuk mencari atasan berdasarkan golongan
    private function getAtasan($golongan)
    {
        $hierarchy = ['E' => 'D', 'D' => 'C', 'C' => 'B', 'B' => 'A', 'A' => null];
        return $hierarchy[$golongan] ?? null;
    }
}
