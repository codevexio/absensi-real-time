<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Presensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Data Presensi Karyawan</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Jam Keluar</th>
                <th>Status Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $employee->karyawan->nama ?? '-' }}</td>
                    <td>{{ $employee->tanggalPresensi }}</td>
                    <td>{{ $employee->jadwalKerja->shift->namaShift ?? '-' }}</td>
                    <td>{{ $employee->waktuMasuk }}</td>
                    <td>{{ $employee->statusMasuk }}</td>
                    <td>{{ $employee->waktuPulang ?? '-' }}</td>
                    <td>{{ $employee->statusPulang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
