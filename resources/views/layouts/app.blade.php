<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.min.css">
    <script src="//cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Spinner CSS */
        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 6px solid #ccc;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="font-sans antialiased overflow-hidden">

    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="spinner"></div>
    </div>

    <div class="h-dvh grid grid-cols-[auto_1fr] bg-gray-100 overflow-hidden">

        <x-sidebar />

        <div class="min-h-screen overflow-x-hidden overflow-y-auto flex-auto bg-gray-100">
            <!-- Navbar/Header -->
            <livewire:layout.navigation />
            <!-- Main Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        // Sembunyikan loading screen saat halaman sudah selesai dimuat
        window.addEventListener('load', function () {
            const loader = document.getElementById('loading-screen');
            if (loader) loader.style.display = 'none';
        });

        $(document).ready(function () {
            new DataTable('#data-table', {
                language: {
                    emptyTable: "Tidak ada data tersedia pada tabel"
                },
                paging: false,
                searching: true,
                info: true,
                lengthChange: false,
            });
        });
    </script>

</body>

</html>
