@extends('layouts.panel-pendidik')

@section('title', 'Hasil Penilaian - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Analisis</p>
            <h1 class="h3 mb-1">Hasil Penilaian</h1>
        </div>
        <a href="{{ route('pendidik.dinas.analisis') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 p-md-5">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <form action="{{ route('pendidik.dinas.cetakhasil') }}" method="GET">
                <input name="kelas" value="{{ $selected }}" hidden>
                <input name="token" value="{{ $arsip }}" hidden>
                <input name="tes_id" value="{{ $tes_id }}" hidden>
                <button type="submit" class="btn btn-ck">
                    <i class="bi bi-download"></i> Unduh PDF
                </button>
            </form>
        </div>
        <form action="{{ route('pendidik.dinas.hasil') }}" method="GET" class="row g-3 align-items-end mb-4">
            <input type="hidden" name="token" value="{{ $arsip }}">
            <div class="col-md-6">
                <label for="kelas" class="form-label">Filter kelas</label>
                <select name="kelas" id="kelas" class="form-select">
                    <option value="" @selected($selected == '')>Semua kelas</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}" @selected($item->id == $selected)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-ck-ghost" type="submit">Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama pelajar</th>
                        <th>Kelas</th>
                        <th>Nilai</th>
                        <th>Akumulasi bobot</th>
                        <th>Waktu pengumpulan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nilai as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kelas }}</td>
                            <td>{{ $item->nilai }}</td>
                            <td>{{ $item->akumulasi }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('dddd, D MMMM Y HH:mm') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="ck-hint">Belum ada hasil penilaian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
