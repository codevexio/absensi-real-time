<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4">
        <!-- Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Total Karyawan</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalKaryawan }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Karyawan Sedang Cuti</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalCuti }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-sm font-semibold text-gray-700">Karyawan Absen Hari Ini</h3>
                    <p class="text-2xl font-bold mt-2">{{ $totalAbsenHariIni }}</p>
            </div>
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Terlambat Hari Ini</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalTerlambatHariIni }}</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kehadiran Mingguan -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-md font-semibold mb-3">Statistik Kehadiran Mingguan</h3>
                <canvas id="attendanceChart" height="70"></canvas>
            </div>

            <!-- Status Kehadiran Hari Ini -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-md font-semibold mb-3">Status Kehadiran Hari Ini</h3>
                <canvas id="statusPieChart" height="70"></canvas>
            </div>

            <!-- Trend Terlambat -->
            <div class="bg-white p-4 shadow rounded-lg lg:col-span-2">
                <h3 class="text-md font-semibold mb-3">Jumlah Keterlambatan Mingguan</h3>
                <canvas id="lateTrendChart" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $hadirVal = $hadir ?? 0;
        $cutiVal = $cuti ?? 0;
    @endphp

    <!-- Chart Script -->
    <script>
        const labels = @json($labels);
        const dataHadir = @json($dataHadir);
        const dataTerlambat = @json($dataTerlambat);
        const statusData = @json([$hadirVal, $cutiVal]);

        // Bar Chart
        new Chart(document.getElementById('attendanceChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Hadir',
                    data: dataHadir,
                    backgroundColor: '#4F46E5'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pie Chart
        new Chart(document.getElementById('statusPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Terlambat'],
                datasets: [{
                    label: 'Status Kehadiran',
                    data: statusData,
                    backgroundColor: ['#10B981', '#F59E0B']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Line Chart
        new Chart(document.getElementById('lateTrendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Terlambat',
                    data: dataTerlambat,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
