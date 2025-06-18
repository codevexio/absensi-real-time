<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { margin-bottom: 20px; }
        .info p { display: flex; align-items: center; margin: 3px 0; }
        .info .label { min-width: 130px; font-weight: bold; text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <div class="info">
        <p><span class="label">Nama Karyawan</span>: {{ $user->nama }}</p>
        <p><span class="label">Golongan</span>: {{ $user->golongan }}</p>
        <p><span class="label">Divisi</span>: {{ $user->divisi }}</p>
        <p><span class="label">Bulan</span>: {{ $bulan }}</p>
        <p><span class="label">Total Data</span>: {{ count($presensi) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Waktu Masuk</th>
                <th>Status Masuk</th>
                <th>Waktu Pulang</th>
                <th>Status Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($presensi as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggalPresensi)->locale('id')->translatedFormat('d F Y') }}</td>
                    <td>{{ $p->waktuMasuk ?? '-' }}</td>
                    <td>{{ $p->statusMasuk ?? '-' }}</td>
                    <td>{{ $p->waktuPulang ?? '-' }}</td>
                    <td>{{ $p->statusPulang ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data presensi untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
