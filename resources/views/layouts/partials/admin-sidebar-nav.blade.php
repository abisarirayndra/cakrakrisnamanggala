@php
    $active = fn (string ...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
@endphp

<p class="ck-nav-section">Utama</p>
<a href="{{ route('admin.beranda') }}" data-nav="beranda" class="ck-nav-link {{ $active('admin.beranda') }}">
    <i class="bi bi-house"></i>
    <span>Beranda</span>
</a>

<p class="ck-nav-section">Pengguna</p>
<a href="{{ route('admin.pengguna.pendaftar') }}" data-nav="pendaftar" class="ck-nav-link {{ $active('admin.pengguna.pendaftar') }}">
    <i class="bi bi-person-plus"></i>
    <span>Pendaftar</span>
</a>
<a href="{{ route('admin.pengguna.pelajar') }}" data-nav="pelajar" class="ck-nav-link {{ $active('admin.pengguna.pelajar') }}">
    <i class="bi bi-mortarboard"></i>
    <span>Pelajar</span>
</a>
<a href="{{ route('admin.pengguna.pendidik') }}" data-nav="pendidik" class="ck-nav-link {{ $active('admin.pengguna.pendidik') }}">
    <i class="bi bi-person-badge"></i>
    <span>Pendidik</span>
</a>
@if (auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.pengguna.admin') }}" data-nav="admin" class="ck-nav-link {{ $active('admin.pengguna.admin') }}">
        <i class="bi bi-shield-check"></i>
        <span>Admin</span>
    </a>
@endif

@if (auth()->user()->isSuperAdmin())
    <p class="ck-nav-section">Referensi</p>
    <a href="{{ route('admin.master.markas') }}" data-nav="markas" class="ck-nav-link {{ $active('admin.master.markas') }}">
        <i class="bi bi-geo-alt"></i>
        <span>Markas</span>
    </a>
    <a href="{{ route('admin.master.kelas') }}" data-nav="kelas" class="ck-nav-link {{ $active('admin.master.kelas') }}">
        <i class="bi bi-grid"></i>
        <span>Kelas</span>
    </a>
    <a href="{{ route('admin.master.mapel') }}" data-nav="mapel" class="ck-nav-link {{ $active('admin.master.mapel') }}">
        <i class="bi bi-journal-text"></i>
        <span>Mapel</span>
    </a>
@endif

<p class="ck-nav-section">Operasional</p>
@php
    $jadwalMenuOpen = request()->routeIs('admin.jadwal', 'admin.jadwal.histori', 'admin.jadwal.histori.pdf');
@endphp
<div class="ck-nav-fold">
    <div class="ck-nav-fold-head">
        <a href="{{ route('admin.jadwal') }}" data-nav="jadwal" class="ck-nav-link {{ $active('admin.jadwal') }}">
            <i class="bi bi-calendar3"></i>
            <span>Jadwal</span>
        </a>
        <button
            type="button"
            class="ck-nav-chevron {{ $jadwalMenuOpen ? '' : 'collapsed' }}"
            data-bs-toggle="collapse"
            data-bs-target=".js-nav-jadwal"
            aria-expanded="{{ $jadwalMenuOpen ? 'true' : 'false' }}"
            aria-label="Tampilkan submenu jadwal"
        >
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse js-nav-jadwal {{ $jadwalMenuOpen ? 'show' : '' }}">
        <a href="{{ route('admin.jadwal.histori') }}" data-nav="histori-jadwal" class="ck-nav-link ck-nav-child {{ $active('admin.jadwal.histori', 'admin.jadwal.histori.pdf') }}">
            <i class="bi bi-clock-history"></i>
            <span>Histori Jadwal</span>
        </a>
    </div>
</div>
<a href="{{ route('admin.absensi') }}" data-nav="absensi" class="ck-nav-link {{ $active('admin.absensi') }}">
    <i class="bi bi-check2-square"></i>
    <span>Absensi</span>
</a>

<p class="ck-nav-section">Tes</p>
@php
    $catMenuOpen = request()->routeIs('admin.cat.*');
@endphp
<div class="ck-nav-fold">
    <div class="ck-nav-fold-head">
        <a href="{{ route('admin.cat.jadwal') }}" data-nav="cat" class="ck-nav-link {{ $active('admin.cat.jadwal', 'admin.cat.jadwal.skor', 'admin.cat.jadwal.report', 'admin.cat.jadwal.report.pdf') }}">
            <i class="bi bi-clipboard-data"></i>
            <span>CAT</span>
        </a>
        <button
            type="button"
            class="ck-nav-chevron {{ $catMenuOpen ? '' : 'collapsed' }}"
            data-bs-toggle="collapse"
            data-bs-target=".js-nav-cat"
            aria-expanded="{{ $catMenuOpen ? 'true' : 'false' }}"
            aria-label="Tampilkan submenu CAT"
        >
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse js-nav-cat {{ $catMenuOpen ? 'show' : '' }}">
        <a href="{{ route('admin.cat.jadwal') }}" data-nav="cat-jadwal" class="ck-nav-link ck-nav-child {{ $active('admin.cat.jadwal', 'admin.cat.jadwal.skor', 'admin.cat.jadwal.report', 'admin.cat.jadwal.report.pdf') }}">
            <i class="bi bi-calendar-event"></i>
            <span>Jadwal CAT</span>
        </a>
    </div>
</div>
