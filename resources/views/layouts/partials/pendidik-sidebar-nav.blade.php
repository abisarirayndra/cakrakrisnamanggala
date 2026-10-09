@php
    $active = fn (string ...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
    $absensiMenuOpen = request()->routeIs(
        'pendidik.absensi',
        'pendidik.absensi.histori-mengajar',
        'pendidik.absensi.jurnal',
        'pendidik.absensi.siswa',
        'pendidik.absensi.histori-siswa'
    );
    $catMenuOpen = request()->routeIs(
        'pendidik.cat.bank-soal',
        'pendidik.cat.bank-soal.paket',
        'pendidik.cat.jadwal'
    );
@endphp
<p class="ck-nav-section">Utama</p>
<a href="{{ route('pendidik.dinas.beranda') }}" data-nav="beranda" class="ck-nav-link {{ $active('pendidik.dinas.beranda', 'pendidik.dinas.edit') }}">
    <i class="bi bi-house"></i>
    <span>Beranda</span>
</a>
<div class="ck-nav-fold">
    <div class="ck-nav-fold-head">
        <a href="{{ route('pendidik.cat.bank-soal') }}" data-nav="cat" class="ck-nav-link {{ $active('pendidik.cat.bank-soal', 'pendidik.cat.bank-soal.paket', 'pendidik.cat.jadwal') }}">
            <i class="bi bi-journal-text"></i>
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
        <a href="{{ route('pendidik.cat.bank-soal') }}" data-nav="bank-soal" class="ck-nav-link ck-nav-child {{ $active('pendidik.cat.bank-soal', 'pendidik.cat.bank-soal.paket') }}">
            <i class="bi bi-collection"></i>
            <span>Bank Soal</span>
        </a>
        <a href="{{ route('pendidik.cat.jadwal') }}" data-nav="cat-jadwal" class="ck-nav-link ck-nav-child {{ $active('pendidik.cat.jadwal') }}">
            <i class="bi bi-calendar-event"></i>
            <span>Jadwal CAT</span>
        </a>
    </div>
</div>
<a href="{{ route('pendidik.dinas.analisis') }}" data-nav="analisis" class="ck-nav-link {{ $active('pendidik.dinas.analisis', 'pendidik.dinas.hasil', 'pendidik.dinas.analisispelajar', 'pendidik.dinas.analisissoal', 'pendidik.dinas.jawabanpelajar', 'pendidik.cat.analisis', 'pendidik.cat.analisis.pdf', 'pendidik.cat.analisis.soal.pdf') }}">
    <i class="bi bi-graph-up"></i>
    <span>Analisis</span>
</a>
<div class="ck-nav-fold">
    <div class="ck-nav-fold-head">
        <a href="{{ route('pendidik.absensi') }}" data-nav="absensi" class="ck-nav-link {{ $active('pendidik.absensi', 'pendidik.absensi.histori-mengajar', 'pendidik.absensi.jurnal') }}">
            <i class="bi bi-check2-square"></i>
            <span>Absensi</span>
        </a>
        <button
            type="button"
            class="ck-nav-chevron {{ $absensiMenuOpen ? '' : 'collapsed' }}"
            data-bs-toggle="collapse"
            data-bs-target=".js-nav-absensi"
            aria-expanded="{{ $absensiMenuOpen ? 'true' : 'false' }}"
            aria-label="Tampilkan submenu absensi"
        >
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse js-nav-absensi {{ $absensiMenuOpen ? 'show' : '' }}">
        <a href="{{ route('pendidik.absensi.siswa') }}" data-nav="absensi-siswa" class="ck-nav-link ck-nav-child {{ $active('pendidik.absensi.siswa') }}">
            <i class="bi bi-list-check"></i>
            <span>Absensi Siswa</span>
        </a>
        <a href="{{ route('pendidik.absensi.histori-siswa') }}" data-nav="histori-absensi" class="ck-nav-link ck-nav-child {{ $active('pendidik.absensi.histori-siswa') }}">
            <i class="bi bi-clock-history"></i>
            <span>Histori Absensi</span>
        </a>
    </div>
</div>
