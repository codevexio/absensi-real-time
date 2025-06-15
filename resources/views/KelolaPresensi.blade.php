    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-black-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Data Presensi Karyawan</h3>

                        <!-- Tombol Ekspor dan Filter -->
                        <div class="flex justify-between h-10">
                            <form method="GET" action="{{ route('kelola-presensi.index') }}"
                                class="flex flex-wrap gap-4 mb-6">
                                <div>
                                    <label for="month" class="text-sm font-medium text-gray-700 dark:text-gray-200">Pilih
                                        Bulan</label>
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
                                    <label for="year" class="text-sm font-medium text-gray-700 dark:text-gray-200">Pilih
                                        Tahun</label>
                                    <select name="year" id="year"
                                        class="form-select rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                                        @php $currentYear = now()->year;
                                        $startYear = 2025; @endphp
                                        @for ($y = $startYear; $y <= $currentYear; $y++)
                                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mt-0">
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
                                </div>
                            </form>

                            <div class="flex item-center gap-2 [&_a]:max-h-10">
                                <a href="{{route("export.pdf")}}"
                                    class="bg-red-500 hover:bg-red-700 h-fit text-white font-bold ms-auto py-2 px-4 rounded-lg mr-2 flex item-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-file-text">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                        <path d="M10 9H8" />
                                        <path d="M16 13H8" />
                                        <path d="M16 17H8" />
                                    </svg>
                                    <span class="text-nowrap">Cetak PDF</span>
                                </a>
                                <a href="{{route("export.excel")}}"
                                    class="bg-green-500 hover:bg-green-700 h-fit text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-sheet">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                        <line x1="3" x2="21" y1="9" y2="9" />
                                        <line x1="3" x2="21" y1="15" y2="15" />
                                        <line x1="9" x2="9" y1="9" y2="21" />
                                        <line x1="15" x2="15" y1="9" y2="21" />
                                    </svg>
                                    <span class="text-nowrap">Cetak Excel</span>
                                </a>
                            </div>
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
                                        <th class="border px-4 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->karyawan->nama ?? '-' }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->tanggalPresensi ?? '-' }}
                                            </td>
                                            <td class="border px-4 py-2 text-center">
                                                {{ $employee->jadwalKerja->shift->namaShift ?? '-' }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->waktuMasuk ?? '-' }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->statusMasuk }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->waktuPulang ?? '-' }}</td>
                                            <td class="border px-4 py-2 text-center">{{ $employee->statusPulang }}</td>
                                            <td class="border px-4 py-2 text-center">
                                                <button data-id="{{ $employee->id }}"
                                                    data-status-masuk="{{ $employee->statusMasuk }}"
                                                    data-status-pulang="{{ $employee->statusPulang }}"
                                                    class="tombol-edit-akun bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-2 py-1 rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-pencil">
                                                        <path
                                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                            </td>
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

        <!-- Modal Edit Status -->
        <div id="editModal" class="fixed z-50 inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Edit Status Presensi</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-akun-id">

                <label>Status Masuk:</label>
                <select name="statusMasuk" id="edit-statusMasuk" class="mb-4 w-full rounded border px-2 py-1">
                    <option value="Tepat Waktu">Tepat Waktu</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Cuti">Cuti</option>
                </select>

                <label>Status Pulang:</label>
                <select name="statusPulang" id="edit-statusPulang" class="mb-4 w-full rounded border px-2 py-1">
                    <option value="Tepat Waktu">Tepat Waktu</option>
                    <option value="Tidak Presensi Pulang">Tidak Presensi Pulang</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
            </div>
        </div>


        <!-- Script Modal -->
        <script>
            function openModal(id, statusMasuk, statusPulang) {
                const form = document.getElementById('editForm');
                // Update action URL dengan ID presensi yang dipilih
                form.action = `/kelola-presensi/${id}/update-status`; // sesuaikan dengan route kamu

                // Set nilai select sesuai data
                document.getElementById('edit-statusMasuk').value = statusMasuk;
                document.getElementById('edit-statusPulang').value = statusPulang;

                // Tampilkan modal
                document.getElementById('editModal').classList.remove('hidden');
                document.getElementById('editModal').classList.add('flex');
            }

            function closeModal() {
                document.getElementById('editModal').classList.remove('flex');
                document.getElementById('editModal').classList.add('hidden');
            }

            document.querySelectorAll('.tombol-edit-akun').forEach(button => {
                button.addEventListener('click', function () {
                    openModal(
                        this.dataset.id,
                        this.dataset.statusMasuk,
                        this.dataset.statusPulang
                    );
                });
            });
        </script>
    </x-app-layout>