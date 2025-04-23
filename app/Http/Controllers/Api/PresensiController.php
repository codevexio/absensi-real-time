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
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PresensiController extends Controller
{
    /**
     * Pengaturan Lokasi
     */
    private $officeLatitude = -0.364388; // Latitude kantor
    private $officeLongitude = 100.066725; // Longitude kantor
    private $radiusAllowed = 500; // Radius dalam meter

    public function pengaturanLokasi($lat, $lng)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat - $this->officeLatitude);
        $dLng = deg2rad($lng - $this->officeLongitude);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($this->officeLatitude)) * cos(deg2rad($lat)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return $distance <= $this->radiusAllowed;
    }

    /**
     * Cek Waktu Presensi
     */
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

        $jadwalKerja = JadwalKerja::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $tanggalHariIni)
            ->first();

        if (!$jadwalKerja) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Jadwal kerja tidak ditemukan'
            ], 404);
        }

        $waktuSekarang = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $waktuMasukShift = Carbon::parse($jadwalKerja->shift->waktu_masuk, 'UTC')->setTimezone('Asia/Jakarta');
        $waktuPulangShift = Carbon::parse($jadwalKerja->shift->waktu_pulang, 'UTC')->setTimezone('Asia/Jakarta');

        // Presensi time validation
        $waktuBukaMasuk = $waktuMasukShift->copy()->subMinutes(480)->format('H:i:s');
        $waktuTutupMasuk = $waktuMasukShift->copy()->addMinutes(90)->format('H:i:s');
        $waktuBukaPulang = $waktuPulangShift->copy()->subMinutes(1000)->format('H:i:s');
        $waktuTutupPulang = $waktuPulangShift->copy()->addHours(5)->format('H:i:s');

        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if ($waktuSekarang < $waktuBukaMasuk) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Belum saatnya presensi masuk'
            ]);
        }

        if ($waktuSekarang > $waktuTutupMasuk && (!$presensi || !$presensi->waktuMasuk)) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Waktu presensi masuk sudah ditutup'
            ]);
        }

        if ($waktuSekarang < $waktuBukaPulang) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Belum saatnya presensi pulang'
            ]);
        }

        if ($waktuSekarang > $waktuTutupPulang) {
            return response()->json([
                'bisaPresensiMasuk' => false,
                'bisaPresensiPulang' => false,
                'message' => 'Waktu presensi pulang sudah ditutup'
            ]);
        }

        return response()->json([
            'bisaPresensiMasuk' => true,
            'bisaPresensiPulang' => true,
            'message' => 'Silakan lakukan presensi'
        ]);
    }

    /**
     * Presensi Masuk
     */
    public function presensiMasuk(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        $jadwalKerja = JadwalKerja::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalKerja', $tanggalHariIni)
            ->first();

        if (!$jadwalKerja) {
            return response()->json(['message' => 'Jadwal kerja tidak ditemukan'], 404);
        }

        $request->validate([
            'imageMasuk' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiMasuk.latitude' => 'required|numeric',
            'lokasiMasuk.longitude' => 'required|numeric',
        ]);

        $latitude = $request->input('lokasiMasuk.latitude');
        $longitude = $request->input('lokasiMasuk.longitude');

        if (!$this->pengaturanLokasi($latitude, $longitude)) {
            return response()->json(['message' => 'Anda berada di luar lokasi yang diizinkan untuk presensi.'], 400);
        }

        $waktuSekarang = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $waktuMasukShift = Carbon::parse($jadwalKerja->waktu_masuk, 'Asia/Jakarta')->format('H:i:s');
        $statusMasuk = ($waktuSekarang > $waktuMasukShift) ? 'Terlambat' : 'Tepat Waktu';

        $imagePath = $request->file('imageMasuk')->store('uploads/presensi', 'public');

        $presensi = Presensi::updateOrCreate(
            ['karyawan_id' => $karyawan_id, 'tanggalPresensi' => $tanggalHariIni],
            [
                'jadwal_kerja_id' => $jadwalKerja->id,
                'waktuMasuk' => $waktuSekarang,
                'statusMasuk' => $statusMasuk,
                'imageMasuk' => $imagePath,
                'lokasiMasuk' => json_encode(['latitude' => $latitude, 'longitude' => $longitude]),
            ]
        );

        if ($statusMasuk === 'Terlambat') {
            Keterlambatan::firstOrCreate([
                'karyawan_id' => $karyawan_id,
                'presensi_id' => $presensi->id,
            ]);
        }

        return response()->json(['message' => 'Presensi masuk berhasil dicatat', 'data' => $presensi], 200);
    }

    /**
     * Presensi Pulang
     */
    public function presensiPulang(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;
        $tanggalHariIni = Carbon::today()->toDateString();

        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereDate('tanggalPresensi', $tanggalHariIni)
            ->first();

        if (!$presensi || !$presensi->waktuMasuk) {
            return response()->json(['message' => 'Anda belum presensi masuk.'], 400);
        }

        $request->validate([
            'imagePulang' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'lokasiPulang.latitude' => 'required|numeric',
            'lokasiPulang.longitude' => 'required|numeric',
        ]);

        $latitude = $request->input('lokasiPulang.latitude');
        $longitude = $request->input('lokasiPulang.longitude');

        if (!$this->pengaturanLokasi($latitude, $longitude)) {
            return response()->json(['message' => 'Anda berada di luar lokasi yang diizinkan untuk presensi.'], 400);
        }

        $imagePath = $request->file('imagePulang')->store('uploads/presensi', 'public');

        $presensi->update([
            'waktuPulang' => now()->format('H:i:s'),
            'statusPulang' => 'Tepat Waktu',
            'imagePulang' => $imagePath,
            'lokasiPulang' => json_encode(['latitude' => $latitude, 'longitude' => $longitude]),
        ]);

        return response()->json(['message' => 'Presensi pulang berhasil dicatat', 'data' => $presensi], 200);
    }

    /**
     * List Rekap Presensi Bulanan
     */
    public function listRekapPresensi()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $rekap = Presensi::selectRaw('TO_CHAR("tanggalPresensi", \'YYYY-MM\') as bulan')
            ->where('karyawan_id', $user->id)
            ->groupBy('bulan')
            ->orderBy('bulan', 'desc')
            ->get();

        return response()->json([
            'karyawan_id' => $user->id,
            'nama_karyawan' => $user->nama,
            'rekap_presensi' => $rekap
        ]);
    }

    /**
     * Download PDF Rekapan Presensi Bulanan Karyawan
     */
    public function rekapPresensiPDF($bulan)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Karyawan belum login'], 401);
        }

        $karyawan_id = $user->id;

        try {
            // Perbaikan parsing tanggal
            $tanggal_awal = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $tanggal_akhir = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format bulan tidak valid, gunakan format YYYY-MM'], 400);
        }

        // Ambil data presensi
        $presensi = Presensi::where('karyawan_id', $karyawan_id)
            ->whereBetween('tanggalPresensi', [$tanggal_awal, $tanggal_akhir])
            ->get();

        $data = [
            'user' => $user, // Nama, golongan, divisi
            'bulan' => $bulan,
            'presensi' => $presensi,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.rekap_presensi', $data);
        return $pdf->download("rekap-presensi-$bulan.pdf");
    }
}
