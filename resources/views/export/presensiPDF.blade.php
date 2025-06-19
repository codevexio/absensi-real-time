<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Karyawan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
        }
        .title {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #555;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #ddd;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="title">
        <h2>Rekap Presensi Karyawan</h2>
        <h4>Bulan: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Jam Pulang</th>
                <th>Status Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $employee->karyawan->nama ?? '-' }}</td>
                    <td>{{ $employee->tanggalPresensi ?? '-' }}</td>
                    <td>{{ $employee->jadwalKerja->shift->namaShift ?? '-' }}</td>
                    <td>{{ $employee->waktuMasuk ?? '-' }}</td>
                    <td>{{ $employee->statusMasuk ?? '-' }}</td>
                    <td>{{ $employee->waktuPulang ?? '-' }}</td>
                    <td>{{ $employee->statusPulang ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Data tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
