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
                        <!-- Search Input -->
                        <input type="text" placeholder="Search..."
                            class="border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-lg w-1/3">

                        <!-- Export Buttons -->
                        <div>
                            {{-- <a href="{{ route('export.pdf') }}"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Cetak PDF
                            </a>
                            <a href="{{ route('export.excel') }}" --}}
                                {{-- class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Cetak Excel
                            </a> --}}
                            <a 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cetak PDF
                            </a>
                            <a 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Cetak Excel
                            </a>
                        </div>
                    </div>

                    <!-- Tabel Presensi -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300 dark:border-gray-700">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nama</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Tanggal</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Shift </th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Jam Masuk</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Status Masuk</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Jam Keluar</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Status Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Nanti isi dengan data dari database -->
                                <tr>
                                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center"
                                        colspan="10">
                                        Data tidak tersedia
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>