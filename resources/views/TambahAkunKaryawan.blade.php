<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tambah Akun Karyawan
        </h2>
    </x-slot>

    <!-- Modal Alert -->
    <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Tambah Akun Karyawan</h3>

            <!-- Form Tambah Akun -->
            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Nama Karyawan</label>
                    <input type="text" name="nama" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Jabatan</label>
                    <select name="jabatan" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih Jabatan</option>
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Staff">Staff</option>
                        <option value="Admin">Admin</option>
                        <option value="Operator">Operator</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
