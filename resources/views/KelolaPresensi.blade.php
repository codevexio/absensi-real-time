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

                    <!-- Filter Bulan dan Tahun -->
                    <form method="GET" action="{{ route('kelola-presensi.index') }}" class="flex flex-wrap gap-4 mb-6">
                        <div>
                            <label for="month" class="text-sm font-medium text-gray-700 dark:text-gray-200">Pilih Bulan</label>
                            <select name="month" id="month"
                                class="form-select rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="year" class="text-sm font-medium text-gray-700 dark:text-gray-200">Pilih Tahun</label>
                            <select name="year" id="year"
                                class="form-select rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                                @php
                                    $currentYear = now()->year;
                                    $startYear = $currentYear - 5;
                                @endphp
                                @for ($y = $startYear; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mt-0">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                Filter
                            </button>
                        </div>
                    </form>

                    <!-- Tombol Ekspor -->
                    <div class="flex justify-between mb-4">
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
                                    <th class="border px-4 py-2">No</th>
                                    <th class="border px-4 py-2">Nama</th>
                                    <th class="border px-4 py-2">Tanggal</th>
                                    <th class="border px-4 py-2">Shift</th>
                                    <th class="border px-4 py-2">Jam Masuk</th>
                                    <th class="border px-4 py-2">Status Masuk</th>
                                    <th class="border px-4 py-2">Jam Keluar</th>
                                    <th class="border px-4 py-2">Status Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->karyawan->nama ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->tanggalPresensi ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->jadwalKerja->shift->namaShift ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->waktuMasuk ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->statusMasuk }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->waktuPulang ?? '-' }}</td>
                                        <td class="border px-4 py-2 text-center">{{ $employee->statusPulang }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
p