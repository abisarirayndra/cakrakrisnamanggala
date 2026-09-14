@extends('layouts.panel-pendidik')

@section('title', 'Absen Lapangan - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Kehadiran</p>
        <h1 class="h3 mb-1">Jadwal Hari Ini</h1>
        <p class="ck-hint mb-0">Absensi lapangan untuk sesi jasmani hari ini.</p>
    </div>

    <div class="row g-3">
        @forelse ($jadwal as $item)
            <div class="col-md-6 col-xl-3">
                <section class="ck-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                        {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y') }}
                    </p>
                    <p class="mb-1">Mapel <b>{{ $item->mapel }}</b></p>
                    <p class="mb-1">Kelas <b>{{ $item->kelas }}</b></p>
                    <p class="mb-1">{{ $item->nama }}</p>
                    <p class="mb-3 ck-hint">
                        {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm') }} –
                        {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('pendidik.absensi.jadwal_jasmani.absensi', [$item->id]) }}" class="btn btn-sm btn-ck">Datang</a>
                        <a href="{{ route('staf-admin.absen-pulang', [$item->id]) }}" class="btn btn-sm btn-ck-ghost">Pulang</a>
                    </div>
                </section>
            </div>
        @empty
            <div class="col-12">
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Tidak ada jadwal hari ini.</p>
                </section>
            </div>
        @endforelse
    </div>
@endsection
