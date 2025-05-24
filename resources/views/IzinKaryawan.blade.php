<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cuti Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Data Cuti Karyawan</h3>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table id="data-table" class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Nama</th>
                                <th class="border px-4 py-2">Jenis Cuti</th>
                                <th class="border px-4 py-2">Tangal Mulai</th>
                                <th class="border px-4 py-2">Tanggal Selesai</th>
                                <th class="border px-4 py-2">Jumlah Hari</th>
                                <th class="border px-4 py-2">Dokumen Pengajuan</th>
                                <th class="border px-4 py-2">Status</th>
                                {{-- <th class="border px-4 py-2">Alasan Penolakan</th> --}}
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse ($employees as $employee)
                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->nama }}</td>
                                <td class="border px-4 py-2 text-center">
                                    {{ $employee->jenisCuti ?? '-' }}
                                </td>
                                <td class="border px-4 py-2 text-center">{{ $employee->tanggalMulai ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->tanggalSelesai ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->jumlahHari ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->dokumen ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->statusCuti ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="border px-4 py-2 text-center text-gray-500">
                                    Tidak ada data izin karyawan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pencarian Izin
        const searchInput = document.querySelector('input[type="text"]');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.toLowerCase();

            tableRows.forEach(function (row) {
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let i = 0; i < cells.length; i++) {
                    const cell = cells[i];
                    if (cell.textContent.toLowerCase().includes(query)) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>