<div class="w-64 min-h-screen bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="p-5 text-xl font-semibold border-b dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="images/Logo_PTPN4.png" alt="Logo PTPN4" class="logo w-28 mx-auto">
        </a>
    </div>
    <nav class="mt-5">
        <ul>
            <li>
                <a href="{{ route('dashboard') }}"
                    class="block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    📊 Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('web/presensi') }}"
                    class="flex gap-1 px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-list">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                        <path d="M12 11h4" />
                        <path d="M12 16h4" />
                        <path d="M8 11h.01" />
                        <path d="M8 16h.01" />
                    </svg>
                    <div>Kelola Data Presensi </div>
                </a>
            </li>
            <li>
                <a href="{{ route('web/kelola-akun') }}"
                    class="block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    📝 Kelola Akun Karyawan
                </a>
            </li>
            <li>
                <a href="{{ route('web/izinkaryawan') }}"
                    class="block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    📝 Kelola Izin Karyawan
                </a>
            </li>
        </ul>
    </nav>
</div>