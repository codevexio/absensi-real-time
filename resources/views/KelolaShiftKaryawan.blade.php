<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kelola Shift Karyawan
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
                <h3 class="text-lg font-semibold mb-4">Data Shift Karyawan</h3>
{{-- 
                @if (session('success'))
                    <div class="mb-4 text-green-600 dark:text-green-400">
                        {{ session('success') }}
                    </div>
                @endif --}}

                <div class="overflow-x-auto">
                    <table id="data-table"
                        class="w-full border-collapse border border-gray-300 dark:border-gray-700 rounded-lg">
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
                            @forelse($employees as $index => $employee)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 text-center"
                                    data-id="{{ $employee->id }}">
                                    <td class="border px-4 py-2">{{ $employees->firstItem() + $index }}</td>
                                    <td class="border px-4 py-2">{{ $employee->karyawan->nama }}</td>
                                    <td class="border px-4 py-2">{{ $employee->shift->namaShift ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $employee->karyawan->username }}</td>
                                    <td class="border px-4 py-2">
                                        <button
                                            class="tombol-edit-shift bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                                <path
                                                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                <path d="m15 5 4 4" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">Tidak ada data
                                        shift karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Shift -->
    <dialog id="edit-shift" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Edit Shift Karyawan</h3>
        <form id="form-edit-shift" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-akun-id">

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Nama Karyawan</label>
                <input type="text" id="edit-nama" disabled class="w-full px-3 py-2 border rounded-lg bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Shift</label>
                <select name="shift_id" id="edit-shift-id" required class="w-full px-3 py-2 border rounded-lg">
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->namaShift }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="close-edit-shift"
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg">Update</button>
            </div>
        </form>
    </dialog>

    <script>
        const dialogEdit = document.getElementById("edit-shift");
        const btnEdits = document.querySelectorAll(".tombol-edit-shift");
        const closeEdit = document.getElementById("close-edit-shift");
        const formEditShift = document.getElementById("form-edit-shift");

        btnEdits.forEach(button => {
            button.addEventListener("click", function () {
                const row = this.closest("tr");
                const id = row.dataset.id;
                const nama = row.cells[1].innerText;
                const shift = row.cells[2].innerText;

                document.getElementById("edit-akun-id").value = id;
                document.getElementById("edit-nama").value = nama;

                const shiftSelect = document.getElementById("edit-shift-id");
                for (let option of shiftSelect.options) {
                    option.selected = option.text === shift;
                }

                formEditShift.action = `/kelolashift/${id}`;
                dialogEdit.showModal();
            });
        });

        closeEdit.addEventListener("click", () => dialogEdit.close());
    </script>
</x-app-layout>