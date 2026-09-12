<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Cakra Krisna Manggala')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/cakra-admin.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="ck-body">
    <nav class="ck-topbar py-3">
        <div class="container ck-shell d-flex align-items-center justify-content-between">
            <a href="{{ route('petunjuk') }}" class="ck-brand d-flex align-items-center gap-2">
                <img src="{{ asset('assets/img/favicon.ico') }}" width="36" height="36" alt="Cakra Krisna Manggala">
                <span>Cakra Krisna Manggala</span>
            </a>
            @if (request()->routeIs('login', 'landing'))
                <a href="{{ route('petunjuk') }}" class="small fw-semibold">Daftar</a>
            @else
                <a href="{{ route('login') }}" class="small fw-semibold">Masuk</a>
            @endif
        </div>
    </nav>

    <main class="container ck-shell py-4 py-md-5">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
