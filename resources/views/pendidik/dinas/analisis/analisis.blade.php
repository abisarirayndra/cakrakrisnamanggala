@extends('layouts.panel-pendidik')

@section('title', 'Analisis Nilai - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
        <h1 class="h3 mb-1">Analisis Nilai</h1>
        <p class="ck-hint mb-0">Paket CAT yang memakai bank soal Anda.</p>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Paket CAT</h2>
        <div class="row g-3">
            @forelse ($paketCat as $item)
                <div class="col-md-6 col-xl-4">
                    <section class="ck-card p-4 h-100">
                        <p class="fw-semibold mb-1">{{ $item['nama'] }}</p>
                        <p class="ck-hint mb-2">{{ $item['banks']->join(' · ') ?: 'Bank soal Anda' }}</p>
                        <p class="ck-hint mb-3">{{ $item['waktu'] }} · {{ $item['peserta'] }} peserta</p>
                        <a href="{{ route('pendidik.cat.analisis', $item['id']) }}" class="btn btn-sm btn-ck">Lihat analisis</a>
                    </section>
                </div>
            @empty
                <div class="col-12">
                    <p class="ck-hint mb-0">Belum ada paket CAT yang memakai bank soal Anda.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
