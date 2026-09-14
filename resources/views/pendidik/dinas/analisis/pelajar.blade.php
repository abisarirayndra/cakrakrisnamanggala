@extends('layouts.panel-pendidik')

@section('title', 'Analisis Pelajar - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Analisis</p>
            <h1 class="h3 mb-1">Daftar pelajar yang mengikuti tes</h1>
        </div>
        <a href="{{ route('pendidik.dinas.analisis') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 p-md-5">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelajar as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kelas }}</td>
                            <td>
                                <form action="{{ route('pendidik.dinas.jawabanpelajar') }}">
                                    <input type="hidden" name="token" value="{{ $item->status }}">
                                    <input type="hidden" name="tes" value="{{ $item->dn_tes_id }}">
                                    <input type="hidden" name="auth" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-sm btn-ck">Jawaban pelajar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="ck-hint">Belum ada pelajar pada arsip ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
