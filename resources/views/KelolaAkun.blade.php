<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <h2 class="font-semibold text-xl text-black-800  leading-tight">
            Kelola Akun Karyawan
        </h2>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        + AKUN KARYAWAN
                    </button>
                    <input type="text" placeholder="🔍 Search..." 
                           class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Nama Karyawan</th>
                                <th class="border px-4 py-2">Jabatan</th>
                                <th class="border px-4 py-2">Username</th>
                                <th class="border px-4 py-2">Password</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @for ($i = 1; $i <= 10; $i++)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                    <td class="border px-4 py-2">{{ $i }}</td>
                                    <td class="border px-4 py-2">Nama Karyawan {{ $i }}</td>
                                    <td class="border px-4 py-2">Jabatan {{ $i }}</td>
                                    <td class="border px-4 py-2">username{{ $i }}</td>
                                    <td class="border px-4 py-2">******</td>
                                    <td class="border px-4 py-2 text-center">
                                        <button class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                            ✏️
                                        </button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @endfor
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
