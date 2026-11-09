<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
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
    <title>@yield('title', 'Page') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/bmt.png') }}?v={{ filemtime(public_path('images/logos/bmt.png')) }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logos/bmt.png') }}?v={{ filemtime(public_path('images/logos/bmt.png')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/mdi/materialdesignicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/materio.css') }}?v={{ filemtime(public_path('assets/css/materio.css')) }}">
    @stack('styles')
</head>
<body>
<div class="blank-wrapper">
    <div class="app-content">
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>
