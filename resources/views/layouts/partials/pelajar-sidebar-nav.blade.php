@php
    $active = fn (string ...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
@endphp

<p class="ck-nav-section">Utama</p>
<a href="{{ route('pelajar.dinas.beranda') }}" data-nav="beranda" class="ck-nav-link {{ $active('pelajar.dinas.beranda') }}">
    <i class="bi bi-house"></i>
    <span>Beranda</span>
</a>
<a href="{{ route('pelajar.masukkan_token') }}" data-nav="cat" class="ck-nav-link {{ $active('pelajar.masukkan_token', 'pelajar.dinas.paket', 'pelajar.dinas.tes', 'pelajar.dinas.persiapan', 'pelajar.cat.tes') }}">
    <i class="bi bi-clipboard-data"></i>
    <span>CAT</span>
</a>
<a href="{{ route('pelajar.capaian') }}" data-nav="capaian" class="ck-nav-link {{ $active('pelajar.capaian') }}">
    <i class="bi bi-graph-up"></i>
    <span>Capaian Tes</span>
</a>
<a href="{{ route('pelajar.absensi') }}" data-nav="absensi" class="ck-nav-link {{ $active('pelajar.absensi', 'pelajar.absensi.histori-pembelajaran') }}">
    <i class="bi bi-check2-square"></i>
    <span>Absensi</span>
</a>
