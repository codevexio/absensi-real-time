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
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex bg-gray-100 ">

        <x-sidebar />

        <div class="min-h-screen flex-auto bg-gray-100">
            <!-- Navbar/Header -->
            <livewire:layout.navigation />
            <!-- Main Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>

        <script>
            $(document).ready(function () {
                new DataTable('#data-table', {
                    language: {
                        emptyTable: "Tidak ada data tersedia pada tabel"
                    },
                    paging: false,
                    searching: true,
                    info: true,
                    lengthChange: false,
                    // dom: 'lfrtip'
                });
            });

        </script>

</body>

</html>