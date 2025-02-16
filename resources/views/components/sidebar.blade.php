<div class="w-64 min-h-screen bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-3">
    <div class="p-5 text-xl font-semibold border-b dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="images/Logo_PTPN4.png" alt="Logo PTPN4" class="logo w-28 mx-auto">
        </a>
    </div>
    <nav class="mt-10">
        <ul>
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex gap-1 block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard">
                        <rect width="7" height="9" x="3" y="3" rx="1" />
                        <rect width="7" height="5" x="14" y="3" rx="1" />
                        <rect width="7" height="9" x="14" y="12" rx="1" />
                        <rect width="7" height="5" x="3" y="16" rx="1" />
                    </svg>
                    <div>Dashboard</div>
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
                    class="flex gap-1 block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round">
                        <path d="M18 21a8 8 0 0 0-16 0" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                    </svg>
                    <div>Akun Karyawan</div>
                </a>
            </li>
            <li>
                <a href="{{ route('web/izinkaryawan') }}"
                    class="flex gap-1 block px-5 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-pen">
                        <rect width="8" height="4" x="8" y="2" rx="1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5.5" />
                        <path d="M4 13.5V6a2 2 0 0 1 2-2h2" />
                        <path d="M13.378 15.626a1 1 0 1 0-3.004-3.004l-5.01 5.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                    </svg>
                    <div>Kelola Izin Karyawan</div>
                </a>
            </li>
        </ul>
    </nav>
</div>