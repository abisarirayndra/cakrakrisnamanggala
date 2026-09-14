@extends('layouts.panel-pendidik')

@section('title', 'Analisis Nilai - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
        <h1 class="h3 mb-1">Analisis Nilai</h1>
        <p class="ck-hint mb-0">Analisis sesuai hasil yang diperoleh pelajar dan sudah diarsipkan.</p>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Daftar Arsip Nilai</h2>
        <div class="row g-3">
            @forelse ($arsip as $item)
                <div class="col-md-6 col-xl-4">
                    <section class="ck-card p-4 h-100">
                        <p class="fw-semibold mb-1">Paket {{ $item->nama_paket }}</p>
                        <p class="fw-semibold mb-1">Arsip {{ $item->mapel }}</p>
                        <p class="ck-hint mb-2">Kode arsip : {{ $item->kode }}</p>
                        <p class="mb-3" style="color: var(--ck-success);">
                            {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y HH:mm') }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <form action="{{ route('pendidik.dinas.hasil') }}" method="GET">
                                <input name="token" value="{{ $item->kode }}" hidden>
                                <input type="hidden" name="tes_id" value="{{ $item->tes_id }}">
                                <button class="btn btn-sm btn-ck" type="submit">Hasil</button>
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-ck-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Analisis
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="{{ route('pendidik.dinas.analisispelajar') }}">
                                            <input type="hidden" name="token" value="{{ $item->kode }}">
                                            <input type="hidden" name="tes" value="{{ $item->tes_id }}">
                                            <button type="submit" class="dropdown-item">Pelajar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>
                </div>
            @empty
                <div class="col-12">
                    <p class="ck-hint mb-0">Belum ada arsip nilai.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $arsip->links() }}</div>
    </section>
@endsection
