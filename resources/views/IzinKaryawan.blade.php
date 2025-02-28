<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cuti Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                
                <!-- Search Bar -->
                <div class="flex justify-between mb-4 items-center">
                    <h3 class="text-lg font-semibold">Data Cuti Karyawan</h3>
                    <div class="flex gap-1 px-2 border rounded-lg dark:bg-gray-700 dark:text-white items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="text" placeholder="Search."
                            class="border-none focus:outline-none focus:ring-0">
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Nama</th>
                                <th class="border px-4 py-2">Jenis Cuti</th>
                                <th class="border px-4 py-2">Tangal Mulai</th>
                                <th class="border px-4 py-2">Tanggal Selesai</th>
                                <th class="border px-4 py-2">Jumlah Hari</th>
                                <th class="border px-4 py-2">Status</th>
                                {{-- <th class="border px-4 py-2">Alasan Penolakan</th> --}}
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse ($employees as $index => $employee)
                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->karyawan->nama }}</td>
                                <td class="border px-4 py-2 text-center">
                                    {{ $employee->jenisCuti ?? '-' }}
                                </td>
                                <td class="border px-4 py-2 text-center">{{ $employee->tanggalMulai ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->tanggalSelesai ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->jumlahHari ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $employee->statusCuti ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-2 text-center text-gray-500">
                                    Tidak ada data izin karyawan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-4">
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        ⬅️
                    </button>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg mx-1">1</button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        2
                    </button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        3
                    </button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        ➡️
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>