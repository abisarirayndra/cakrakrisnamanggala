@extends('layouts.panel-cakra')

@section('title', 'Beranda Admin - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">{{ auth()->user()->isSuperAdmin() ? 'Superadmin' : 'Admin' }}</p>
        <h1 class="h3 mb-1">Selamat datang, {{ auth()->user()->nama }}</h1>
        <p class="ck-hint mb-0">Ringkasan pendaftar, pelajar, jadwal, dan CAT hari ini.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Pendaftar</p>
                <p class="h3 mb-0">{{ $jumlahPendaftar }}</p>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Pelajar</p>
                <p class="h3 mb-0">{{ $jumlahPelajar }}</p>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Jadwal hari ini</p>
                <p class="h3 mb-0">{{ $jadwalHariIni->count() }}</p>
            </section>
        </div>
        <div class="col-md-6 col-xl-3">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">CAT hari ini</p>
                <p class="h3 mb-0">{{ $catHariIni->count() }}</p>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <section class="ck-card p-4 h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h5 mb-0">Pendaftar</h2>
                    <a href="{{ route('admin.pengguna.pendaftar') }}" class="btn btn-sm btn-ck-ghost">Lihat semua</a>
                </div>
                @forelse ($pendaftar as $item)
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <p class="fw-semibold mb-0">{{ $item->nama }}</p>
                            <p class="ck-hint mb-0">{{ $item->pelajar?->markas?->markas ?: 'Belum ada markas' }}</p>
                        </div>
                        <p class="ck-hint mb-0">{{ $item->created_at?->isoFormat('D MMM Y') ?: '—' }}</p>
                    </div>
                @empty
                    <p class="ck-hint mb-0">Belum ada pendaftar.</p>
                @endforelse
            </section>
        </div>
        <div class="col-lg-6">
            <section class="ck-card p-4 h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h5 mb-0">Pelajar</h2>
                    <a href="{{ route('admin.pengguna.pelajar') }}" class="btn btn-sm btn-ck-ghost">Lihat semua</a>
                </div>
                @forelse ($kelasPelajar as $kelas)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <p class="mb-0">{{ $kelas['nama'] }}</p>
                        <p class="fw-semibold mb-0">{{ $kelas['jumlah'] }}</p>
                    </div>
                @empty
                    <p class="ck-hint mb-0">Belum ada pelajar.</p>
                @endforelse
            </section>
        </div>
    </div>

    <section class="ck-card p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h5 mb-0">Jadwal hari ini</h2>
            <a href="{{ route('admin.jadwal') }}" class="btn btn-sm btn-ck-ghost">Kelola jadwal</a>
        </div>
        @forelse ($jadwalHariIni as $baris)
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                <div>
                    <p class="fw-semibold mb-0">{{ $baris->mapel?->mapel ?: 'Mapel' }}</p>
                    <p class="ck-hint mb-0">{{ $baris->kelas?->nama ?: 'Kelas' }} · {{ $baris->pendidik?->nama ?: 'Pendidik' }}</p>
                </div>
                <p class="mb-0">{{ $baris->mulai?->format('H:i') }}–{{ $baris->selesai?->format('H:i') }}</p>
            </div>
        @empty
            <p class="ck-hint mb-0">Tidak ada jadwal hari ini.</p>
        @endforelse
    </section>

    <section class="ck-card p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h5 mb-0">CAT hari ini</h2>
            <a href="{{ route('admin.cat.jadwal') }}" class="btn btn-sm btn-ck-ghost">Kelola CAT</a>
        </div>
        @forelse ($catHariIni as $item)
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                <div>
                    <p class="fw-semibold mb-0">{{ $item['nama'] }}</p>
                    <p class="ck-hint mb-0">{{ $item['waktu'] }}</p>
                    <p class="ck-hint mb-0">{{ $item['banks']->join(' · ') ?: 'Bank soal' }} · Token {{ $item['token'] }}</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="ck-hint">{{ $item['status'] }}</span>
                    <a href="{{ route('admin.cat.jadwal.skor', $item['id']) }}" class="btn btn-sm btn-ck">Live skor</a>
                </div>
            </div>
        @empty
            <p class="ck-hint mb-0">Tidak ada CAT hari ini.</p>
        @endforelse
    </section>
@endsection
