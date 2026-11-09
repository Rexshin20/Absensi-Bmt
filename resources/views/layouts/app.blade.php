<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <meta name="description" content="Sistem informasi absensi karyawan BMT Cinde Barokah.">
    <meta name="keywords" content="BMT Cinde Barokah, absensi, karyawan">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function () {
            try {
                if (localStorage.getItem('materio-theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (error) {}
        })();
    </script>
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/bmt.png') }}?v={{ filemtime(public_path('images/logos/bmt.png')) }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logos/bmt.png') }}?v={{ filemtime(public_path('images/logos/bmt.png')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/mdi/materialdesignicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/materio.css') }}?v={{ filemtime(public_path('assets/css/materio.css')) }}">
    @stack('styles')
</head>
<body>
<div class="layout-wrapper">
    @include('partials.navigation')

    <div class="layout-content-wrapper">
        @include('partials.navbar')

        <main class="layout-page-content content-boxed">
            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>

<div class="scroll-top">
    <button type="button" class="fab" aria-label="scroll back to top">
        <x-icon name="ArrowUp" />
    </button>
</div>

@include('partials.scripts')
</body>
</html>
