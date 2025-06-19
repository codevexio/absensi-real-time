<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .data-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="title">Laporan Data Presensi Karyawan</div>

    <table class="info-table">
        <tr>
            <td><strong>Nama Karyawan</strong></td>
            <td>: {{ $karyawan->nama }}</td>
            <td><strong>Divisi</strong></td>
            <td>: {{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Golongan</strong></td>
            <td>: {{ $karyawan->golongan ?? '-' }}</td>
            <td><strong>Bulan</strong></td>
            <td>: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Jam Pulang</th>
                <th>Status Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($employee->tanggalPresensi)->translatedFormat('d M Y') }}</td>
                    <td>{{ $employee->jadwalKerja->shift->namaShift ?? '-' }}</td>
                    <td>{{ $employee->waktuMasuk ?? '-' }}</td>
                    <td>{{ $employee->statusMasuk ?? '-' }}</td>
                    <td>{{ $employee->waktuPulang ?? '-' }}</td>
                    <td>{{ $employee->statusPulang ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
