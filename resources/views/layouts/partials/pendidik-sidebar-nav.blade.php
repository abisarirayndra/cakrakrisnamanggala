@php
    $active = fn (string ...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
    $pendidikMapel = auth()->user()?->pendidik?->mapel?->mapel;
@endphp

<p class="ck-nav-section">Utama</p>
<a href="{{ route('pendidik.dinas.beranda') }}" data-nav="beranda" class="ck-nav-link {{ $active('pendidik.dinas.beranda', 'pendidik.dinas.edit') }}">
    <i class="bi bi-house"></i>
    <span>Beranda</span>
</a>
<a href="{{ route('pendidik.dinas.paket') }}" data-nav="paket" class="ck-nav-link {{ $active('pendidik.dinas.paket', 'pendidik.dinas.tes', 'pendidik.dinas.penilaian') }}">
    <i class="bi bi-journal-text"></i>
    <span>Paket Soal</span>
</a>
<a href="{{ route('pendidik.dinas.analisis') }}" data-nav="analisis" class="ck-nav-link {{ $active('pendidik.dinas.analisis', 'pendidik.dinas.hasil', 'pendidik.dinas.analisispelajar', 'pendidik.dinas.analisissoal', 'pendidik.dinas.jawabanpelajar') }}">
    <i class="bi bi-graph-up"></i>
    <span>Analisis</span>
</a>
<a href="{{ route('pendidik.absensi') }}" data-nav="absensi" class="ck-nav-link {{ $active('pendidik.absensi', 'pendidik.absensi.histori-mengajar', 'pendidik.absensi.jurnal') }}">
    <i class="bi bi-check2-square"></i>
    <span>Absensi</span>
</a>
@if ($pendidikMapel === 'Jasmani')
    <a href="{{ route('pendidik.absensi.jadwal_jasmani') }}" data-nav="jasmani" class="ck-nav-link {{ $active('pendidik.absensi.jadwal_jasmani', 'pendidik.absensi.jadwal_jasmani.absensi') }}">
        <i class="bi bi-geo-alt"></i>
        <span>Absen Lapangan</span>
    </a>
@endif
