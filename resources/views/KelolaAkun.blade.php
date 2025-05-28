<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kelola Akun Karyawan
        </h2>
    </x-slot>

    <div class="py-6">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                <div class="flex mb-4">
                    <button id="tombol-tambah-akun"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        + AKUN KARYAWAN
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table id="data-table"
                        class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
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
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 text-center" data-id="{{ $akuns->id }}">
                                    <td class="border px-4 py-2 text-center">{{ $akun->firstItem() + $index }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->nama }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->golongan }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->divisi ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $akuns->username }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        <button
                                            class="tombol-edit-akun bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                                <path
                                                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                <path d="m15 5 4 4" />
                                            </svg>
                                        </button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-lg"
                                            type="submit" onclick="konfirmasiHapus({{ $akuns->id }})">
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
                                        <script>
                                            function konfirmasiHapus(id) {
                                                Swal.fire({
                                                    title: "Yakin ingin menghapus akun?",
                                                    text: "Aksi ini tidak bisa dibatalkan!",
                                                    icon: "warning",
                                                    showCancelButton: true,
                                                    confirmButtonColor: "#d33",
                                                    cancelButtonColor: "#6c757d",
                                                    confirmButtonText: "Ya, hapus!",
                                                    cancelButtonText: "Batal"
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        fetch(`/kelola-akun/${id}`, {
                                                            method: "POST", // HARUS POST karena Laravel hanya menerima spoofed method
                                                            headers: {
                                                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                                                "Content-Type": "application/json",
                                                                "Accept": "application/json"
                                                            },
                                                            body: JSON.stringify({
                                                                _method: "DELETE"
                                                            })
                                                        }).then(response => {
                                                            if (response.ok) {
                                                                Swal.fire(
                                                                    "Dihapus!",
                                                                    "Akun berhasil dihapus.",
                                                                    "success"
                                                                ).then(() => {
                                                                    location.reload();
                                                                });
                                                            } else {
                                                                Swal.fire(
                                                                    "Gagal!",
                                                                    "Akun gagal dihapus.",
                                                                    "error"
                                                                );
                                                            }
                                                        }).catch(() => {
                                                            Swal.fire(
                                                                "Gagal!",
                                                                "Terjadi kesalahan saat menghapus data.",
                                                                "error"
                                                            );
                                                        });
                                                    }
                                                });
                                            }
                                        </script>
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
                {{ $akun->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Akun -->
    <dialog id="tambah-akun" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Tambah Akun Karyawan</h3>
        <form action="{{ route('web/kelola-akun-post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Nama Karyawan</label>
                <input type="text" name="nama" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Golongan</label>
                <select name="golongan" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                    <option value="">Pilih Golongan</option>
                    <option value="Direksi">Direksi</option>
                    <option value="Kepala Bagian">Kepala Bagian (Kabag)</option>
                    <option value="Kepala SubBagian">Kepala SubBagian (Kasubag)</option>
                    <option value="Asisten">Asisten</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Divisi</label>
                <select name="divisi" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                    <option value="Bag.Sekper">Bag.Sekper</option>
                    <option value="Bag.SPI">Bag.SPI</option>
                    <option value="Bag.SDM">Bag.SDM</option>
                    <option value="Bag.Tanaman">Bag.Tanaman</option>
                    <option value="Bag.Teknik & Pengolahan">Bag.Teknik & Pengolahan</option>
                    <option value="Bag.Keuangan">Bag.Keuangan</option>
                    <option value="Bag.Pemasaran & P.Baku">Bag.Pemasaran & P.Baku</option>
                    <option value="Bag.Perencanaa Strategis">Bag.Perencanaa Strategis</option>
                    <option value="Bag.Hukum">Bag.Hukum</option>
                    <option value="Bag.Pengadaan & TI">Bag.Pengadaan & TI</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Papam">Papam</option>
                    <option value="Bag.Percepetan Transformasi Teknologi">Bag.Percepetan Transformasi Teknologi</option>
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
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Tambah
                </button>
            </div>
        </form>
    </dialog>

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
                <label class="block text-gray-700 dark:text-gray-200">Golongan</label>
                <select name="golongan" id="edit-golongan" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Pilih Golongan</option>
                    <option value="Direksi">Direksi</option>
                    <option value="Kepala Bagian">Kepala Bagian (Kabag)</option>
                    <option value="Kepala SubBagian">Kepala SubBagian (Kasubag)</option>
                    <option value="Asisten">Asisten</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Divisi</label>
                <select name="divisi" id="edit-divisi" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="Bag.Sekper">Bag.Sekper</option>
                    <option value="Bag.SPI">Bag.SPI</option>
                    <option value="Bag.SDM">Bag.SDM</option>
                    <option value="Bag.Tanaman">Bag.Tanaman</option>
                    <option value="Bag.Teknik & Pengolahan">Bag.Teknik & Pengolahan</option>
                    <option value="Bag.Keuangan">Bag.Keuangan</option>
                    <option value="Bag.Pemasaran & P.Baku">Bag.Pemasaran & P.Baku</option>
                    <option value="Bag.Perencanaa Strategis">Bag.Perencanaa Strategis</option>
                    <option value="Bag.Hukum">Bag.Hukum</option>
                    <option value="Bag.Pengadaan & TI">Bag.Pengadaan & TI</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Papam">Papam</option>
                    <option value="Bag.Percepetan Transformasi Teknologi">Bag.Percepetan Transformasi Teknologi</option>
                    <option value="Bag.Teknik & Pengolahan">Bag.Teknik & Pengolahan</option>
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