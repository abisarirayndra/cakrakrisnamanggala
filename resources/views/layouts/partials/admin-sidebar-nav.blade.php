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
<a href="{{ route('staf-admin.jadwal') }}" data-nav="jadwal" class="ck-nav-link {{ $active('staf-admin.jadwal*') }}">
    <i class="bi bi-calendar3"></i>
    <span>Jadwal</span>
</a>
<a href="{{ route('staf-admin.absensi.beranda') }}" data-nav="absensi" class="ck-nav-link {{ $active('staf-admin.absensi*', 'staf-admin.absen*') }}">
    <i class="bi bi-check2-square"></i>
    <span>Absensi</span>
</a>

<p class="ck-nav-section">Tes</p>
<a href="{{ route('admin.dinas.paket') }}" data-nav="cat" class="ck-nav-link {{ $active('admin.dinas.*', 'admin.cetak_soal') }}">
    <i class="bi bi-clipboard-data"></i>
    <span>CAT</span>
</a>
