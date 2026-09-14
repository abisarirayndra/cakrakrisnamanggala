@extends('layouts.panel-pendidik')

@section('title', 'Isi Jurnal - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Kehadiran</p>
            <h1 class="h3 mb-1">Isi Jurnal</h1>
            <p class="ck-hint mb-0">Catat materi yang diajarkan pada sesi ini.</p>
        </div>
        <a href="{{ route('pendidik.absensi') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                    {{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="mb-1">Mapel <b>{{ $jadwal->mapel }}</b></p>
                <p class="mb-1">Kelas <b>{{ $jadwal->kelas }}</b></p>
                <p class="mb-0 ck-hint">
                    {{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm') }} –
                    {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }}
                </p>
            </section>
        </div>
        <div class="col-md-8">
            <section class="ck-card p-4 p-md-5">
                @if ($errors->any())
                    <div class="alert alert-ck mb-3">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('pendidik.absensi.up-jurnal', [$jadwal->id]) }}" method="POST">
                    @csrf
                    <label for="jurnal" class="form-label fw-semibold">Jurnal</label>
                    <textarea name="jurnal" id="jurnal" rows="6" class="form-control mb-3">{{ $jadwal->jurnal }}</textarea>
                    <button class="btn btn-ck" type="submit">Simpan</button>
                </form>
            </section>
        </div>
    </div>
@endsection
