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
    @livewireStyles
    @stack('styles')
</head>
<body class="ck-body ck-app">
    <aside class="ck-sidebar d-none d-lg-flex flex-column">
        <a href="{{ route('pendidik.dinas.beranda') }}" class="ck-sidebar-brand d-flex align-items-center gap-2">
            <img src="{{ asset('assets/img/favicon.ico') }}" width="36" height="36" alt="Cakra Krisna Manggala">
            <span>Cakra Krisna Manggala</span>
        </a>
        <nav class="ck-sidebar-nav flex-grow-1">
            @include('layouts.partials.pendidik-sidebar-nav')
        </nav>
    </aside>

    <div class="offcanvas offcanvas-start ck-offcanvas d-lg-none" tabindex="-1" id="ckSidebar" aria-labelledby="ckSidebarLabel">
        <div class="offcanvas-header">
            <a href="{{ route('pendidik.dinas.beranda') }}" id="ckSidebarLabel" class="ck-sidebar-brand d-flex align-items-center gap-2">
                <img src="{{ asset('assets/img/favicon.ico') }}" width="32" height="32" alt="Cakra Krisna Manggala">
                <span>Cakra Krisna Manggala</span>
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
        </div>
        <div class="offcanvas-body pt-0">
            <nav class="ck-sidebar-nav">
                @include('layouts.partials.pendidik-sidebar-nav')
            </nav>
        </div>
    </div>

    <div class="ck-main">
        <header class="ck-topbar ck-main-topbar">
            <button class="ck-menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#ckSidebar" aria-controls="ckSidebar" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
            <span class="small ms-auto">{{ auth()->user()->nama }}</span>
            <a href="{{ route('logout') }}" class="small fw-semibold">Keluar</a>
        </header>

        <main class="ck-main-content">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    @livewireScripts
    @include('sweetalert::alert')
    @stack('scripts')
</body>
</html>
