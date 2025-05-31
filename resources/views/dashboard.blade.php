<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4">
        <!-- Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Total Karyawan</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalKaryawan }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Karyawan Sedang Cuti</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalCuti }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Karyawan Absen Hari Ini</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalAbsenHariIni }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700">Terlambat Hari Ini</h3>
                <p class="text-2xl font-bold mt-2">{{ $totalTerlambatHariIni }}</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pie Chart - Status Kehadiran Hari Ini -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-md font-semibold mb-3">Status Kehadiran Hari Ini</h3>
                <canvas id="statusPieChart" height="70"></canvas>
            </div>

            <!-- Line Chart - Gabungan Kehadiran dan Terlambat -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="text-md font-semibold mb-3">Statistik Kehadiran & Keterlambatan Mingguan</h3>
                <canvas id="combinedLineChart" height="90"></canvas>
            </div>

            <!-- Bar Chart - Statistik Bulanan -->
            <div class="bg-white p-4 shadow rounded-lg lg:col-span-2">
                <h3 class="text-md font-semibold mb-3">Statistik Bulanan: Cuti, Hadir, Terlambat</h3>
                <canvas id="monthlySummaryChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $hadirHariIniVal = $hadirHariIni ?? 0;
        $terlambatHariIniVal = $terlambatHariIni ?? 0;
    @endphp

    <script>
        const labels = @json($labels);
        const dataHadir = @json($dataHadir);
        const dataTerlambat = @json($dataTerlambat);
        const statusData = @json([$hadirHariIniVal, $terlambatHariIniVal]);

        // Doughnut Chart - Status Kehadiran Hari Ini
        new Chart(document.getElementById('statusPieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat'],
                datasets: [{
                    label: 'Status Kehadiran Hari Ini',
                    data: statusData,
                    backgroundColor: ['#10B981', '#F59E0B']
                }]
            },
            options: {
                responsive: true,
                cutout: '60%' // Optional: atur seberapa besar lubang tengahnya
            }
        });


        // Line Chart - Gabungan Kehadiran dan Terlambat
        new Chart(document.getElementById('combinedLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Jumlah Hadir',
                        data: dataHadir,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Jumlah Terlambat',
                        data: dataTerlambat,
                        borderColor: '#F87171',
                        backgroundColor: 'rgba(248, 113, 113, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Line Chart - Cuti, Hadir, Terlambat Bulanan
        const ctx = document.getElementById('monthlySummaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Total Karyawan (30 Hari)'],
                datasets: [
                    {
                        label: 'Cuti',
                        data: [{{ $totalCutiBulanan }}],
                        borderColor: 'rgba(251, 191, 36, 1)',
                        backgroundColor: 'rgba(251, 191, 36, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Hadir',
                        data: [{{ $totalHadirBulanan }}],
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Terlambat',
                        data: [{{ $totalTerlambatBulanan }}],
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    </script>
</x-app-layout>