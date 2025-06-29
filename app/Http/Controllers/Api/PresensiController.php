<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\JadwalKerja;
use Illuminate\Support\Facades\Log;
use App\Models\Keterlambatan;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanCuti;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PresensiController extends Controller
{
    // Koordinat lokasi kantor dan radius yang diperbolehkan (dalam meter)
    private $officeLatitude = 0.470293;
    private $officeLongitude = 101.426107;
    private $radiusAllowed = 500;

    private function pengaturanLokasi($lat, $lng)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat - $this->officeLatitude);
        $dLng = deg2rad($lng - $this->officeLongitude);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($this->officeLatitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance <= $this->radiusAllowed;
    }

    public function cekWaktuPresensi()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Karyawan belum login'
            ], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        // Cek apakah karyawan sedang cuti hari ini
        $sedangCuti = PengajuanCuti::where('karyawan_id', $karyawan_id)
            ->where('statusCuti', 'Disetujui')
            ->whereDate('tanggalMulai', '<=', $tanggalHariIni)
            ->whereDate('tanggalSelesai', '>=', $tanggalHariIni)
            ->exists();

        if ($sedangCuti) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Anda sedang cuti hari ini'
            ], 200);
        }

        $jadwalKerja = JadwalKerja::with('shift')
            ->where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $tanggalHariIni)
            ->first();

        if (!$jadwalKerja || !$jadwalKerja->shift) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Jadwal kerja tidak ditemukan'
            ], 404);
        }

        if (!$jadwalKerja->shift->waktuMulai || !$jadwalKerja->shift->waktuSelesai) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Waktu shift belum disetel dengan benar'
            ], 422);
        }

        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $waktuMasuk = Carbon::parse($tanggalHariIni . ' ' . $jadwalKerja->shift->waktuMulai, 'Asia/Jakarta');
        $waktuPulang = Carbon::parse($tanggalHariIni . ' ' . $jadwalKerja->shift->waktuSelesai, 'Asia/Jakarta');

        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        $sudahPresensiMasuk = $presensi && $presensi->waktuMasuk;
        $sudahPresensiPulang = $presensi && $presensi->waktuPulang;

        $windowMasukStart = $waktuMasuk->copy()->subMinutes(60); // 1 jam sebelum masuk
        $windowMasukEnd = $waktuMasuk->copy()->addMinutes(70);   // 1 jam 10 menit setelah masuk
        $windowPulangStart = $waktuPulang;                        // mulai jam pulang
        $windowPulangEnd = $waktuPulang->copy()->addHours(5);     // maksimal 5 jam setelahnya

        $bisaPresensiMasuk = $waktuSekarang->between($windowMasukStart, $windowMasukEnd) && !$sudahPresensiMasuk;
        $bisaPresensiPulang = $sudahPresensiMasuk && !$sudahPresensiPulang && $waktuSekarang->between($windowPulangStart, $windowPulangEnd);

        // Jika belum presensi masuk, maka presensi pulang tidak diizinkan
        if (!$sudahPresensiMasuk) {
            $bisaPresensiPulang = false;
        }

        // Logging untuk debugging
        \Log::info('Cek presensi:', [
            'karyawan_id' => $karyawan_id,
            'waktuSekarang' => $waktuSekarang->toDateTimeString(),
            'windowMasukStart' => $windowMasukStart->toDateTimeString(),
            'windowMasukEnd' => $windowMasukEnd->toDateTimeString(),
            'windowPulangStart' => $windowPulangStart->toDateTimeString(),
            'windowPulangEnd' => $windowPulangEnd->toDateTimeString(),
            'sudahPresensiMasuk' => $sudahPresensiMasuk,
            'sudahPresensiPulang' => $sudahPresensiPulang,
            'bisaPresensiMasuk' => $bisaPresensiMasuk,
            'bisaPresensiPulang' => $bisaPresensiPulang,
        ]);

        // Tentukan pesan status
        if ($sudahPresensiPulang) {
            $message = 'Presensi pulang sudah diterima';
        } elseif ($sudahPresensiMasuk && !$sudahPresensiPulang && $waktuSekarang->between($windowPulangStart, $windowPulangEnd)) {
            $message = 'Silakan presensi pulang';
        } elseif ($sudahPresensiMasuk && !$sudahPresensiPulang && $waktuSekarang->gt($windowPulangEnd)) {
            $message = 'Presensi pulang sudah tutup, hubungi admin';
        } elseif ($bisaPresensiMasuk) {
            $message = 'Silakan presensi masuk';
        } elseif (!$sudahPresensiMasuk && $waktuSekarang->gt($windowMasukEnd)) {
            $message = 'Presensi masuk sudah tutup, hubungi admin';
        } elseif ($sudahPresensiMasuk) {
            $message = 'Presensi masuk sudah diterima';
        } else {
            $message = 'Status presensi berhasil diambil';
        }

        return response()->json([
            'bisaPresensiMasuk' => $bisaPresensiMasuk,
            'bisaPresensiPulang' => $bisaPresensiPulang,
            'message' => $message,
        ]);
    }

    public function presensiMasuk(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        // Ambil jadwal kerja berdasarkan tanggal hari ini dan karyawan
        $jadwalKerja = JadwalKerja::with('shift')
            ->where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $tanggalHariIni)
            ->first();

        if (!$jadwalKerja || !$jadwalKerja->shift) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }

        // Gunakan zona waktu Asia/Jakarta
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $waktuMasuk = Carbon::parse($jadwalKerja->shift->waktuMulai, 'Asia/Jakarta'); // Waktu masuk dari shift

        // Tentukan batas toleransi, misalnya 10 menit
        $batasTerlambat = $waktuMasuk->copy()->addMinutes(10);

        // Tentukan status berdasarkan perbandingan waktu masuk
        $statusMasuk = $waktuSekarang->greaterThan($batasTerlambat) ? 'Terlambat' : 'Tepat Waktu';

        // Cek jika sudah lewat batas waktu masuk tanpa toleransi
        if ($waktuSekarang->greaterThan($waktuMasuk)) {
            $statusMasuk = 'Terlambat'; // Pastikan terlambat jika sudah lewat waktu masuk
        }

        // Validasi data input dari request
        $request->validate([
            'imageMasuk' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiMasuk.latitude' => 'required|numeric',
            'lokasiMasuk.longitude' => 'required|numeric',
        ]);

        $latitude = $request->input('lokasiMasuk.latitude');
        $longitude = $request->input('lokasiMasuk.longitude');

        if (!$this->pengaturanLokasi($latitude, $longitude)) {
            return response()->json(['message' => 'Lokasi di luar area kantor'], 400);
        }

        $path = $request->file('imageMasuk')->store('presensi', 'public');

        // Simpan atau update presensi
        $presensi = Presensi::updateOrCreate(
            ['karyawan_id' => $karyawan_id, 'tanggalPresensi' => $tanggalHariIni],
            [
                'waktuMasuk' => $waktuSekarang,
                'statusMasuk' => $statusMasuk,
                'imageMasuk' => $path,
                'lokasiMasuk' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],

            ]
        );

        // Jika terlambat, simpan ke tabel keterlambatan
        if ($statusMasuk === 'Terlambat') {
            Keterlambatan::updateOrCreate(
                ['karyawan_id' => $karyawan_id, 'presensi_id' => $presensi->id],
                []
            );
        }
        

        return response()->json([
            'message' => 'Presensi masuk berhasil',
            'data' => [
                'id' => $presensi->id,
                'waktuMasuk' => $presensi->waktuMasuk,
                'statusMasuk' => $presensi->statusMasuk,
                'imageMasuk' => $presensi->imageMasuk,
                'lokasiMasuk' => $presensi->lokasiMasuk,
            ]
        ], 200);                
    }

    public function presensiPulang(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if (!$presensi || !$presensi->waktuMasuk) {
            return response()->json(['message' => 'Belum presensi masuk'], 400);
        }

        $request->validate([
            'imagePulang' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiPulang.latitude' => 'required|numeric',
            'lokasiPulang.longitude' => 'required|numeric',
        ]);

        $latitude = $request->input('lokasiPulang.latitude');
        $longitude = $request->input('lokasiPulang.longitude');

        if (!$this->pengaturanLokasi($latitude, $longitude)) {
            return response()->json(['message' => 'Lokasi di luar area kantor'], 400);
        }

        $path = $request->file('imagePulang')->store('presensi', 'public');

        $presensi->update([
            'waktuPulang' => Carbon::now('Asia/Jakarta'),
            'statusPulang' => 'Sudah Presensi Pulang',
            'imagePulang' => $path,
            'lokasiPulang' => json_encode(['latitude' => $latitude, 'longitude' => $longitude]),
        ]);

        return response()->json(['message' => 'Presensi pulang berhasil', 'data' => $presensi], 200);
    }

    public function getPresensiHariIni()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawanId = $user->id;
        $tanggalHariIni = Carbon::now()->format('Y-m-d');

        $presensi = Presensi::where('karyawan_id', $karyawanId)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if (!$presensi) {
            return response()->json([
                'status' => false,
                'message' => 'Belum Presensi Masuk',
            ]);
        }

        $statusMasuk = $presensi->statusMasuk; // "Tepat Waktu", "Terlambat", "Cuti", "Tidak Presensi Masuk"

        if (in_array($statusMasuk, ['Tepat Waktu', 'Terlambat'])) {
            return response()->json([
                'status' => true,
                'message' => 'Sudah Presensi Masuk',
                'code' => 'SUDAH_PRESENSI',
            ]);
        } elseif ($statusMasuk === 'Cuti') {
            return response()->json([
                'status' => false,
                'message' => 'Anda sedang cuti',
                'code' => 'CUTI',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Belum Presensi Masuk',
                'code' => 'BELUM_PRESENSI',
            ]);
        }
    }

    public function listRekapPresensi()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Karyawan belum login'], 401);
            }

            // Ganti TO_CHAR jika pakai MySQL
            $rekap = Presensi::selectRaw("DATE_FORMAT(tanggalPresensi, '%Y-%m') as bulan")
                ->where('karyawan_id', $user->id)
                ->groupBy('bulan')
                ->orderByDesc('bulan')
                ->get();

            return response()->json([
                'karyawan_id' => $user->id,
                'nama_karyawan' => $user->nama,
                'rekap_presensi' => $rekap
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRekapDetail($bulan)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        try {
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format bulan salah, gunakan YYYY-MM'], 400);
        }

        $presensi = Presensi::with(['jadwalKerja.shift'])
            ->where('karyawan_id', $user->id)
            ->whereBetween('tanggalPresensi', [$start, $end])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tanggalPresensi,
                    'jam_masuk' => $item->waktuMasuk,
                    'status_masuk' => $item->statusMasuk,
                    'jam_pulang' => $item->waktuPulang,
                    'status_pulang' => $item->statusPulang,
                    'shift' => $item->jadwalKerja->shift->namaShift ?? '-',
                ];
            });

        return response()->json([
            'karyawan_id' => $user->id,
            'nama_karyawan' => $user->nama,
            'bulan' => $bulan,
            'rekap' => $presensi,
        ]);
    }

    public function rekapPresensiPDF($bulan)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        try {
            // Format: "2025-06"
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format bulan salah, gunakan YYYY-MM'], 400);
        }

        $dataPresensi = Presensi::with(['jadwalKerja.shift', 'karyawan'])
            ->where('karyawan_id', $user->id)
            ->whereBetween('tanggalPresensi', [$start, $end])
            ->orderBy('tanggalPresensi', 'asc')
            ->get();

        $pdf = PDF::loadView('export.presensi', [
            'employees' => $dataPresensi,
            'karyawan' => $user,
            'bulan' => $bulan,
        ])->setPaper('A4', 'portrait');

        $filename = 'presensi_' . $user->nama . '_' . $bulan . '.pdf';
        return $pdf->download($filename);
    }
}
