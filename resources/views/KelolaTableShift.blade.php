<x-app-layout>
    <x-slot name="header">
        {{-- Header kosong --}}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Data Shift Karyawan</h3>
                <div>
                    <div class="flex justify-end mb-4">
                        <button id="tombol-tambah-akun"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
                            + Shift
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
                            <thead class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                <tr>
                                    <th class="border px-4 py-2">No</th>
                                    <th class="border px-4 py-2">Nama Shift</th>
                                    <th class="border px-4 py-2">Jam Mulai</th>
                                    <th class="border px-4 py-2">Jam Selesai</th>
                                    <th class="border px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-200">
                                @forelse($shifts as $index => $shift)
                                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 text-center"
                                        data-id="{{ $shift->id }}">
                                        <td class="border px-4 py-2">{{ $shifts->firstItem() + $index }}</td>
                                        <td class="border px-4 py-2">{{ $shift->namaShift }}</td>
                                        <td class="border px-4 py-2">{{ $shift->waktuMulai }}</td>
                                        <td class="border px-4 py-2">{{ $shift->waktuSelesai }}</td>
                                        <td class="border px-4 py-2">
                                            <button
                                                class="tombol-edit-shift bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-pencil">
                                                    <path
                                                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg"
                                                onclick="konfirmasiHapus({{ $shift->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-trash-2">
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
                                        <td colspan="5" class="text-center py-4">Tidak ada data shift.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Hapus Tersembunyi -->
        <form id="form-hapus-shift" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- Modal Tambah Shift -->
        <dialog id="tambah-shift" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Tambah Shift Baru</h3>
            <form id="form-tambah-shift" action="{{ route('shift.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Nama Shift</label>
                    <input type="text" name="namaShift"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Jam Mulai</label>
                    <input type="time" name="waktuMulai"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Jam Selesai</label>
                    <input type="time" name="waktuSelesai"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="close-tambah-shift"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </dialog>

        <!-- Modal Edit Shift -->
        <dialog id="edit-shift" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Edit Shift</h3>
            <form id="form-edit-shift" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-shift-id">
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Nama Shift</label>
                    <input type="text" name="namaShift" id="edit-nama-shift"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Jam Mulai</label>
                    <input type="time" name="waktuMulai" id="edit-waktu-mulai"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Jam Selesai</label>
                    <input type="time" name="waktuSelesai" id="edit-waktu-selesai"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="close-edit-shift"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg">Update</button>
                </div>
            </form>
        </dialog>

        <!-- SCRIPT: Tambah Shift -->
        <script>
            const dialogTambah = document.getElementById("tambah-shift");
            const tombolTambah = document.getElementById("tombol-tambah-akun");
            const closeTambah = document.getElementById("close-tambah-shift");

            tombolTambah.addEventListener("click", () => {
                document.getElementById("form-tambah-shift").reset();
                dialogTambah.showModal();
            });

            closeTambah.addEventListener("click", () => dialogTambah.close());
        </script>

        <!-- SCRIPT: Edit Shift -->
        <script>
            const dialogEdit = document.getElementById("edit-shift");
            const btnEdits = document.querySelectorAll(".tombol-edit-shift");
            const closeEdit = document.getElementById("close-edit-shift");

            btnEdits.forEach(button => {
                button.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const id = row.dataset.id;
                    const namaShift = row.cells[1].innerText;
                    const waktuMulai = row.cells[2].innerText.substring(0, 5);
                    const waktuSelesai = row.cells[3].innerText.substring(0, 5);

                    document.getElementById("edit-shift-id").value = id;
                    document.getElementById("edit-nama-shift").value = namaShift;
                    document.getElementById("edit-waktu-mulai").value = waktuMulai;
                    document.getElementById("edit-waktu-selesai").value = waktuSelesai;
                    document.getElementById("form-edit-shift").action = `/kelola-table-shift/${id}`;

                    dialogEdit.showModal();
                });
            });

            closeEdit.addEventListener("click", () => dialogEdit.close());
        </script>

        <!-- SCRIPT: Hapus Shift -->
        <script>
            function konfirmasiHapus(id) {
                if (confirm("Apakah Anda yakin ingin menghapus shift ini?")) {
                    const form = document.getElementById("form-hapus-shift");
                    form.action = `/kelola-table-shift/${id}`;
                    form.submit();
                }
            }
        </script>
    </div>
</x-app-layout>