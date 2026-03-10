<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Doctor Portal - University System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/university-system.css') }}" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    @include('partials.doctor-sidebar')

    <div class="app-main">
        @include('partials.doctor-navbar')

        <main class="app-content container-fluid py-4 px-lg-4 px-3">
            @include('partials.alerts')
            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
