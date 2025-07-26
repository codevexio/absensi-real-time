<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\ApprovalCuti;
use App\Models\PengajuanCuti;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanCutiController extends Controller
{
    // Menampilkan sisa cuti karyawan yang sudah login
    public function getSisaCuti()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $cuti = Cuti::where('karyawan_id', $user->id)->first();

        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        return response()->json([
            'sisaCutiTahun' => $cuti->cutiTahun,
            'sisaCutiPanjang' => $cuti->cutiPanjang,
        ]);
    }

    // Mengajukan cuti
    public function ajukanCuti(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        // Validasi input
        $validated = Validator::make($request->all(), [
            'jenisCuti' => 'required|in:Cuti Panjang,Cuti Tahunan',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'file_surat_cuti' => 'nullable|file|mimes:pdf|max:5128',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validated->errors()
            ], 422);
        }

        $data = $validated->validated();

        // Cek apakah masih ada cuti diproses
        $pengajuanCutiDiproses = PengajuanCuti::where('karyawan_id', $user->id)
            ->where('statusCuti', 'Diproses')
            ->exists();

        if ($pengajuanCutiDiproses) {
            return response()->json(['message' => 'Anda masih memiliki pengajuan cuti yang sedang diproses'], 400);
        }

        // Validasi tanggal mulai tidak boleh sebelum hari ini
        $tanggalMulai = Carbon::parse($data['tanggalMulai']);
        $tanggalSelesai = Carbon::parse($data['tanggalSelesai']);

        if ($tanggalMulai->isBefore(Carbon::today())) {
            return response()->json([
                'message' => 'Tanggal mulai cuti tidak boleh lebih kecil dari hari ini.'
            ], 400);
        }

        // Ambil tanggal merah dari API
        $liburNasional = [];
        try {
            $response = Http::get("https://api-harilibur.vercel.app/api");
            if ($response->successful()) {
                $liburNasional = collect($response->json())->pluck('holiday_date')->toArray();
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil data tanggal merah: " . $e->getMessage());
        }

        // Hitung jumlah hari kerja (tanpa weekend & tanggal merah)
        $jumlahHari = 0;
        $periode = $tanggalMulai->daysUntil($tanggalSelesai);

        foreach ($periode as $tanggal) {
            $tglStr = $tanggal->toDateString();

            if ($tanggal->isWeekend() || in_array($tglStr, $liburNasional)) {
                continue;
            }

            $jumlahHari++;
        }

        if ($jumlahHari <= 0) {
            return response()->json(['message' => 'Tidak ada hari kerja dalam rentang tanggal cuti yang dipilih'], 400);
        }

        // Ambil jatah cuti
        $cuti = Cuti::where('karyawan_id', $user->id)->first();
        if (!$cuti) {
            return response()->json(['message' => 'Data cuti tidak ditemukan'], 404);
        }

        // Cek sisa cuti
        if ($data['jenisCuti'] === 'Cuti Tahunan') {
            if ($cuti->cutiTahun < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti tahunan tidak mencukupi'], 400);
            }
        } else {
            if ($cuti->cutiPanjang < $jumlahHari) {
                return response()->json(['message' => 'Sisa cuti panjang tidak mencukupi'], 400);
            }
        }

        // Simpan file kalau ada
        $path = null;
        if ($request->hasFile('file_surat_cuti')) {
            $file = $request->file('file_surat_cuti');
            $filename = 'surat_cuti_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat_cuti', $filename, 'public');
            Log::info('File cuti disimpan di: ' . $path);
        } else {
            Log::warning('Tidak ada file surat cuti');
        }

        // Simpan pengajuan cuti ke DB
        $pengajuan = PengajuanCuti::create([
            'karyawan_id' => $user->id,
            'jenisCuti' => $data['jenisCuti'],
            'tanggalMulai' => $data['tanggalMulai'],
            'tanggalSelesai' => $data['tanggalSelesai'],
            'jumlahHari' => $jumlahHari,
            'statusCuti' => 'Diproses',
            'file_surat_cuti' => $path,
        ]);

        // Urutan golongan approver dari bawah ke atas
        $urutanGolongan = ['Staff', 'Asisten', 'Kepala SubBagian', 'Kepala Bagian', 'Direksi'];
        $golonganUser = $user->golongan;
        $posisiUser = array_search($golonganUser, $urutanGolongan);

        if ($posisiUser === false) {
            return response()->json(['message' => 'Golongan tidak valid'], 400);
        }

        // Jika Direksi, langsung disetujui dan potong cuti
        if ($golonganUser === 'Direksi') {
            $pengajuan->update(['statusCuti' => 'Disetujui']);

            if ($data['jenisCuti'] === 'Cuti Tahunan') {
                $cuti->cutiTahun -= $jumlahHari;
            } else {
                $cuti->cutiPanjang -= $jumlahHari;
            }

            $cuti->save();
        } else {
            // Tentukan batas maksimal approval
            $batasApproval = ($golonganUser === 'Kepala Bagian') ? 'Direksi' : 'Kepala Bagian';

            for ($i = $posisiUser + 1; $i < count($urutanGolongan); $i++) {
                $golonganApprover = $urutanGolongan[$i];

                // Stop jika sudah melebihi batas approval
                if ($golonganApprover === 'Direksi' && $batasApproval !== 'Direksi') {
                    break;
                }

                // Ambil approver dari golongan ini
                $approvers = \App\Models\Karyawan::where('golongan', $golonganApprover)->get();

                if ($approvers->isEmpty()) {
                    Log::warning("Tidak ada approver ditemukan untuk golongan: $golonganApprover, proses approval di-skip.");
                    continue;
                }

                foreach ($approvers as $approver) {
                    ApprovalCuti::create([
                        'pengajuan_cuti_id' => $pengajuan->id,
                        'approver_id' => $approver->id,
                        'approver_golongan' => $golonganApprover,
                        'status' => 'Menunggu',
                        'catatan' => null,
                    ]);
                }

                if ($golonganApprover === $batasApproval) {
                    break;
                }
            }
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim dan sedang diproses',
            'data' => $pengajuan
        ], 201);
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $riwayat = PengajuanCuti::where('karyawan_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id, // opsional, untuk klik detail
                    'tanggal_pengajuan' => $item->created_at->format('Y-m-d'),
                    'jenis_cuti' => $item->jenisCuti,
                ];
            });

        return response()->json($riwayat);
    }

    public function riwayatDetail($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $cuti = PengajuanCuti::with(['karyawan', 'cutiApprovals.user'])->findOrFail($id);

        if ($cuti->karyawan_id !== $user->id) {
            return response()->json(['message' => 'Tidak diizinkan mengakses cuti ini'], 403);
        }

        // Filter dan susun data approval
        $approvalData = [];

        // Golongan-golongan tetap yang harus ditampilkan
        $roles = ['Asisten', 'Kepala SubBagian', 'Kepala Bagian', 'Direksi'];

        foreach ($roles as $role) {
            // Ambil semua approval untuk role ini
            $approvalsForRole = $cuti->cutiApprovals->where('approver_golongan', $role);

            if ($role === 'Asisten') {
                // Khusus Asisten, ambil hanya satu yang statusnya Disetujui / Ditolak
                $asistenApproval = $approvalsForRole->firstWhere('status', 'Disetujui')
                                    ?? $approvalsForRole->firstWhere('status', 'Ditolak');

                if ($asistenApproval) {
                    $approvalData[] = [
                        'role' => $role,
                        'nama' => $asistenApproval->user->nama,
                        'status' => $asistenApproval->status,
                    ];
                } else {
                    // Jika tidak ada yg menyetujui/menolak
                    $approvalData[] = [
                        'role' => $role,
                        'nama' => '-',
                        'status' => '-',
                    ];
                }
            } else {
                // Untuk role lain, tampilkan semua approval meskipun status Menunggu
                foreach ($approvalsForRole as $approval) {
                    $approvalData[] = [
                        'role' => $role,
                        'nama' => $approval->user->nama,
                        'status' => $approval->status,
                    ];
                }
            }
        }

        return response()->json([
            'nama_pengaju' => $cuti->karyawan->nama,
            'jenis_cuti' => $cuti->jenisCuti,
            'jumlah_cuti_diambil' => $cuti->jumlahHari . ' hari',
            'status_approval' => $approvalData,
            'alasan_penolakan' => $cuti->alasanPenolakan,
        ]);
    }

}
