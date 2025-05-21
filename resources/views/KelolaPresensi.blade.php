<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Data Presensi Karyawan</h3>

                    <!-- Search Bar -->
                    <div class="flex justify-between mb-4">
                        <!-- Export Buttons -->
                        <a href="{{route("export.pdf")}}"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold ms-auto py-2 px-4 rounded-lg mr-2 flex item-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-file-text">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                <path d="M10 9H8" />
                                <path d="M16 13H8" />
                                <path d="M16 17H8" />
                            </svg>Cetak PDF
                        </a>
                        <a href="{{route("export.excel")}}"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-sheet">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                <line x1="3" x2="21" y1="9" y2="9" />
                                <line x1="3" x2="21" y1="15" y2="15" />
                                <line x1="9" x2="9" y1="9" y2="21" />
                                <line x1="15" x2="15" y1="9" y2="21" />
                            </svg>
                            Cetak Excel
                        </a>
                    </div>

                    <!-- Tabel Presensi -->
                    <div class="overflow-x-auto">
                        <table id='data-table'
                            class="w-full border-collapse border border-gray-300 dark:border-gray-700">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nama</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Tanggal</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Shift</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Jam Masuk</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Status Masuk</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Jam Keluar</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Status Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->karyawan->nama ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">
                                            {{ $employee->tanggalPresensi->format("d-m-Y") ?? "-" }}</td>
                                        <td class="border px-4 py-2 text-center">
                                            {{ $employee->jadwalKerja->shift->namaShift ?? '-' }}
                                        </td>
                                        <!-- Ambil nama shift -->
                                        <td class="border px-4 py-2 text-center">
                                            {{ $employee->waktuMasuk->format("H:i") ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->statusMasuk }}</td>
                                        <td class="border px-4 py-2 text-center">
                                            {{ $employee->waktuPulang->format("H:i") ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->statusPulang }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pencarian Presensi
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