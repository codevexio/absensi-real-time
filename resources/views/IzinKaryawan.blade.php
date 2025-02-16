<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <h2 class="font-semibold text-xl text-black-800  leading-tight">
            Kelola Cuti
        </h2>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                
                <!-- Search Bar -->
                <div class="flex justify-end mb-4">
                    <input type="text" placeholder="🔍 Search..." 
                           class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Full Name</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Divisi</th>
                                <th class="border px-4 py-2">Tanggal dan Waktu</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @php
                                $statuses = ['✅ Disetujui' => 'text-green-500', '⏳ Diproses' => 'text-gray-500', '❌ Tidak Disetujui' => 'text-red-500'];
                                $employees = [
                                    ['name' => 'Alyvia Kelley', 'status' => '✅ Disetujui', 'email' => 'a.kelley@gmail.com', 'date' => '06/18/1978'],
                                    ['name' => 'Jaiden Nixon', 'status' => '✅ Disetujui', 'email' => 'jaiden.n@gmail.com', 'date' => '09/30/1963'],
                                    ['name' => 'Ace Foley', 'status' => '⏳ Diproses', 'email' => 'ace.fo@yahoo.com', 'date' => '12/09/1985'],
                                    ['name' => 'Nikolai Schmidt', 'status' => '❌ Tidak Disetujui', 'email' => 'nikolai.schmidt1964@outlook.com', 'date' => '03/22/1956'],
                                    ['name' => 'Clayton Charles', 'status' => '✅ Disetujui', 'email' => 'me@clayton.com', 'date' => '10/14/1971'],
                                    ['name' => 'Prince Chen', 'status' => '✅ Disetujui', 'email' => 'prince.chen1997@gmail.com', 'date' => '07/05/1992'],
                                    ['name' => 'Reece Duran', 'status' => '✅ Disetujui', 'email' => 'reece@yahoo.com', 'date' => '05/26/1980'],
                                    ['name' => 'Anastasia Mcdaniel', 'status' => '❌ Tidak Disetujui', 'email' => 'anastasia.spring@mcdaniel12.com', 'date' => '02/11/1968'],
                                    ['name' => 'Melvin Boyle', 'status' => '✅ Disetujui', 'email' => 'Me.boyle@gmail.com', 'date' => '08/03/1974'],
                                    ['name' => 'Kailee Thomas', 'status' => '⏳ Diproses', 'email' => 'Kailee.thomas@gmail.com', 'date' => '11/28/1954'],
                                ];
                            @endphp

                            @foreach ($employees as $index => $employee)
                                @php
                                    $statusColor = $statuses[$employee['status']] ?? 'text-gray-500';
                                @endphp
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                    <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2">{{ $employee['name'] }}</td>
                                    <td class="border px-4 py-2">
                                        <span class="{{ $statusColor }}">{{ $employee['status'] }}</span>
                                    </td>
                                    <td class="border px-4 py-2">{{ $employee['email'] }}</td>
                                    <td class="border px-4 py-2">{{ $employee['date'] }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        <button class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                            ✏️
                                        </button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
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
