@extends('layouts.panel-pendidik')

@section('title', 'Paket Soal - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
        <h1 class="h3 mb-1">Paket Soal</h1>
        <p class="ck-hint mb-0">Paket-paket yang disiapkan untuk persiapan Computer Assisted Test.</p>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Daftar Paket Soal</h2>
        <div class="row g-3">
            @forelse ($paket as $item)
                <div class="col-md-6 col-xl-4">
                    <section class="ck-card p-4 h-100">
                        <p class="text-uppercase small fw-semibold mb-3" style="color: var(--ck-gold);">
                            {{ $item->nama_paket }}
                        </p>
                        <a href="{{ route('pendidik.dinas.tes', [$item->id]) }}" class="btn btn-sm btn-ck">Lihat</a>
                    </section>
                </div>
            @empty
                <div class="col-12">
                    <p class="ck-hint mb-0">Belum ada paket soal.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
