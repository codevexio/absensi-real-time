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
                    <div
                        class="flex gap-1 px-2 border rounded-lg dark:bg-gray-700 dark:text-white items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-search">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="text" placeholder="Search." class="border-none focus:outline-none focus:ring-0">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                            <tr>
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Nama Karyawan</th>
                                <th class="border px-4 py-2">Shift</th>
                                <th class="border px-4 py-2">Username</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            {{-- @forelse($akun as $index => $akuns)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 text-center" data-id="{{ $akuns->id }}">
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->nama }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->golongan }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->divisi ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->username }}</td>
                                    <td class="border px-4 py-2 text-center"> --}}
                                        {{-- <button
                                            class="tombol-edit-akun bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                                <path
                                                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                <path d="m15 5 4 4" />
                                            </svg>
                                        </button> --}}
                                        {{-- <form action="{{ route('web/kelola-akun-del', $akuns->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg"
                                                type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                                                    <path d="M3 6h18" />
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                    <line x1="10" x2="10" y1="11" y2="17" />
                                                    <line x1="14" x2="14" y1="11" y2="17" />
                                                </svg>
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            {{-- @empty --}}
                                <tr>
                                    <td colspan="6" class="border px-4 py-2 text-center">Tidak ada data</td>
                                </tr>
                            {{-- @endforelse --}}
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-4">
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        ⬅️
                    </button>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg mx-1">1</button>
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        2
                    </button>
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        3
                    </button>
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        ➡️
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Akun -->
    <dialog id="edit-akun" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Edit Akun Karyawan</h3>
        <form id="form-edit-akun" action="" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-akun-id">

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Nama Karyawan</label>
                <input type="text" name="nama" id="edit-nama" required class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Shift</label>
                <select name="golongan" id="edit-golongan" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="A">Reguler</option>
                    <option value="B">Pagi</option>
                    <option value="C">Sore</option>
                    <option value="D">Malam</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Username</label>
                <input type="text" name="username" id="edit-username" required
                    class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Password (Kosongkan jika tidak ingin
                    mengubah)</label>
                <input type="password" name="password" id="edit-password" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="close-edit-akun"
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg">Update</button>
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
        const btnEdits = document.querySelectorAll(".tombol-edit-akun");
        const closeEdit = document.querySelector("#close-edit-akun");

        // Form Edit Akun
        const formEditAkun = document.getElementById("form-edit-akun");

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

                // Update action form edit
                document.getElementById("form-edit-akun").action = `/web/kelola-akun/${id}`;

                dialogEdit.showModal();
            });
        });

        closeEdit.addEventListener("click", () => dialogEdit.close());

        // Pencarian Akun
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