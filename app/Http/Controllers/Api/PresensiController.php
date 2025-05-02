<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\JadwalKerja;
use App\Models\Keterlambatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PresensiController extends Controller
{
    // Koordinat lokasi kantor dan radius yang diperbolehkan (dalam meter)
    private $officeLatitude = 0.5267822;
    private $officeLongitude = 101.4276879;
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

        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $waktuMasuk = Carbon::parse($jadwalKerja->shift->waktu_masuk, 'UTC')->setTimezone('Asia/Jakarta');
        $waktuPulang = Carbon::parse($jadwalKerja->shift->waktu_pulang, 'UTC')->setTimezone('Asia/Jakarta');

        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        $bisaPresensiMasuk = $waktuSekarang->between($waktuMasuk->copy()->subMinutes(120), $waktuMasuk->copy()->addMinutes(600))
            && (!$presensi || !$presensi->waktuMasuk);

        $bisaPresensiPulang = $presensi && $presensi->waktuMasuk &&
            $waktuSekarang->between($waktuPulang, $waktuPulang->copy()->addHours(5)) &&
            !$presensi->waktuPulang;

        return response()->json([
            'bisaPresensiMasuk' => $bisaPresensiMasuk,
            'bisaPresensiPulang' => $bisaPresensiPulang,
            'message' => 'Status presensi berhasil diambil'
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

        $path = $request->file('imageMasuk')->store('uploads/presensi', 'public');

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
                'lokasiMasuk' => $presensi->lokasiMasuk, // Tidak perlu json_decode
            ],
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

        $path = $request->file('imagePulang')->store('uploads/presensi', 'public');

        $presensi->update([
            'waktuPulang' => Carbon::now('Asia/Jakarta'),
            'statusPulang' => 'Tepat Waktu',
            'imagePulang' => $path,
            'lokasiPulang' => json_encode(['latitude' => $latitude, 'longitude' => $longitude]),
        ]);

        return response()->json(['message' => 'Presensi pulang berhasil', 'data' => $presensi], 200);
    }

    public function listRekapPresensi()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

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
    }

    public function rekapPresensiPDF($bulan)
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

        $presensi = Presensi::where('karyawan_id', $user->id)
            ->whereBetween('tanggalPresensi', [$start, $end])
            ->get();

        $data = [
            'user' => $user,
            'bulan' => $bulan,
            'presensi' => $presensi,
        ];

        $pdf = Pdf::loadView('pdf.rekap_presensi', $data);
        return $pdf->download("rekap-presensi-$bulan.pdf");
    }
}
