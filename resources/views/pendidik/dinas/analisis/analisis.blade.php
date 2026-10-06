@extends('layouts.panel-pendidik')

@section('title', 'Analisis Nilai - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
        <h1 class="h3 mb-1">Analisis Nilai</h1>
        <p class="ck-hint mb-0">Paket CAT yang memakai bank soal Anda.</p>
    </div>

    <section class="ck-card p-4">
        <h2 class="h5 mb-4">Paket CAT</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 3rem;">No</th>
                        <th>Nama</th>
                        <th>Bank soal</th>
                        <th>Waktu</th>
                        <th>Peserta</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paketCat as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item['nama'] }}</td>
                            <td>{{ $item['banks']->join(' · ') ?: 'Bank soal Anda' }}</td>
                            <td>{{ $item['waktu'] }}</td>
                            <td>{{ $item['peserta'] }}</td>
                            <td class="text-end">
                                <a href="{{ route('pendidik.cat.analisis', $item['id']) }}" class="btn btn-sm btn-ck">Lihat analisis</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="ck-hint">Belum ada paket CAT yang memakai bank soal Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
