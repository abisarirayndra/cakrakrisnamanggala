@extends('layouts.panel-pelajar')

@section('title', 'Daftar Tes - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
            <h1 class="h3 mb-1">Computer Assisted Test - Cakra Krisna Manggala</h1>
            <p class="ck-hint mb-0">Perhatikan waktu mulai dan selesai di setiap tes. Soal akan dibuka dan ditutup sesuai waktu yang tertera.</p>
        </div>
        <a href="{{ route('pelajar.dinas.paket') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Daftar Tes</h2>
        <div class="row g-3">
            @forelse ($tes as $item)
                @php
                    $now = \Carbon\Carbon::now();
                @endphp
                <div class="col-md-6 col-xl-4">
                    <section class="ck-card p-4 h-100">
                        <p class="fw-semibold mb-2">{{ $item->mapel }} (Bobot {{ $item->nilai_pokok }}%)</p>
                        <p class="mb-1" style="color: var(--ck-success);">
                            {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y HH:mm') }}
                        </p>
                        <p class="mb-3" style="color: var(--ck-danger);">
                            {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('dddd, D MMMM Y HH:mm') }}
                        </p>
                        @if ($now < $item->mulai)
                            <span class="badge rounded-pill" style="background: var(--ck-gold-soft); color: var(--ck-navy);">Belum Tersedia</span>
                        @elseif ($now > $item->selesai)
                            <span class="badge rounded-pill" style="background: #f8eceb; color: var(--ck-danger);">Selesai</span>
                        @else
                            <a href="{{ route('pelajar.dinas.persiapan', [$item->id]) }}" class="btn btn-sm btn-ck">Buka</a>
                        @endif
                    </section>
                </div>
            @empty
                <div class="col-12">
                    <p class="ck-hint mb-0">Belum ada tes pada paket ini.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
