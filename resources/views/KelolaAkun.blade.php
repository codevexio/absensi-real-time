<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kelola Akun Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <div class="flex justify-between mb-4">
                    <button id="tombol-tambah-akun"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        + AKUN KARYAWAN
                    </button>


                    <div class="flex gap-1 px-2 border rounded-lg dark:bg-gray-700 dark:text-white items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="text" placeholder="Search."
                            class="border-none focus:outline-none focus:ring-0">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Nama Karyawan</th>
                                <th class="border px-4 py-2">Golongan</th>
                                <th class="border px-4 py-2">Divisi</th>
                                <th class="border px-4 py-2">Username</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($akun as $index => $akuns)
                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 text-center">
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2">{{ $akuns->nama}}</td>
                                <td class="border px-4 py-2">{{ $akuns->golongan}}</td>
                                <td class="border px-4 py-2">{{ $akuns->divisi ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $akuns->username}}</td>
                                <td class="border px-4 py-2 text-center">
                                    <button id="tombol-edit-akun" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </button>
                                    <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-2 text-center">Tidak ada data</td>
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

    <dialog id="tambah-akun" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
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
                <label class="block text-gray-700 dark:text-gray-200">Golongan</label>
                <select name="jabatan" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                    <option value="">Pilih Golongan</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Divisi</label>
                <select name="jabatan" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                    <option value="">Pilih Divisi</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
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
                <button type="button" id="close-tambah-akun"
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                    Batal
                </button>
                <type="submit"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Tambah
                    </button>
            </div>
        </form>
    </dialog>

    <dialog id="edit-akun" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Edit Akun Karyawan</h3>

        <!-- Form Edit Akun -->
        <form id="form-edit-akun" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-akun-id">

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Nama Karyawan</label>
                <input type="text" name="nama" id="edit-nama" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Golongan</label>
                <select name="golongan" id="edit-golongan" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                <option value="">Pilih Divisi</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Divisi</label>
                <select name="divisi" id="edit-divisi" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                <option value="">Pilih Divisi</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Username</label>
                <input type="text" name="username" id="edit-username" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" id="edit-password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="close-edit-akun" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Update</button>
            </div>
        </form>
    </dialog>

    <script>
    // Modal Tambah Akun
    const dialogTambah = document.getElementById("tambah-akun");
    const btnTambah = document.querySelector("#tombol-tambah-akun");
    const closeTambah = document.querySelector("#close-tambah-akun");

    btnTambah.addEventListener("click", () => dialogTambah.showModal());
    closeTambah.addEventListener("click", () => dialogTambah.close());

    // Modal Edit Akun
    const dialogEdit = document.getElementById("edit-akun");
    const btnEdits = document.querySelectorAll("#tombol-edit-akun");
    const closeEdit = document.querySelector("#close-edit-akun");

    btnEdits.forEach(button => {
        button.addEventListener("click", function () {
            const row = this.closest("tr");
            const id = row.dataset.id;
            const nama = row.cells[1].innerText;
            const golongan = row.cells[2].innerText;
            const divisi = row.cells[3].innerText;
            const username = row.cells[4].innerText;

            document.getElementById("edit-akun-id").value = id;
            document.getElementById("edit-nama").value = nama;
            document.getElementById("edit-golongan").value = golongan;
            document.getElementById("edit-divisi").value = divisi;
            document.getElementById("edit-username").value = username;

            dialogEdit.showModal();
        });
    });

    closeEdit.addEventListener("click", () => dialogEdit.close());
</script>

</x-app-layout>