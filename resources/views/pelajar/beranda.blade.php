@extends('layouts.panel-pelajar')

@section('title', 'Beranda Pelajar - Cakra Krisna Manggala')

@section('content')
    <section class="ck-card p-4 p-md-5 mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Pelajar</p>
        <h1 class="h3 mb-2">Selamat datang, {{ $user }}</h1>
        <p class="ck-hint mb-4">Ringkasan data diri dan kehadiran Anda.</p>

        <div class="row g-4 align-items-start">
            <div class="col-md-3 text-center">
                <div class="ck-photo-frame mx-auto">
                    @if ($data?->foto)
                        <img src="{{ asset('img/pelajar/'.$data->foto) }}" alt="Foto {{ $user }}">
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h5 mb-0">Data Diri</h2>
                    <button type="button" class="btn btn-ck-ghost" data-bs-toggle="modal" data-bs-target="#kartu-absensi-modal">
                        Kartu Absensi
                    </button>
                </div>
                <dl class="ck-meta row mb-0">
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $user }}</dd>
                    <dt class="col-sm-4">NIK</dt>
                    <dd class="col-sm-8">{{ $data?->nik ?: '—' }}</dd>
                    <dt class="col-sm-4">NISN</dt>
                    <dd class="col-sm-8">{{ $data?->nisn ?: '—' }}</dd>
                    <dt class="col-sm-4">Tempat, tanggal lahir</dt>
                    <dd class="col-sm-8">
                        {{ $data?->tempat_lahir ?: '—' }},
                        {{ $data?->tanggal_lahir?->isoFormat('D MMMM Y') ?: '—' }}
                    </dd>
                    <dt class="col-sm-4">Alamat</dt>
                    <dd class="col-sm-8">{{ $data?->alamat ?: '—' }}</dd>
                    <dt class="col-sm-4">Asal sekolah</dt>
                    <dd class="col-sm-8">{{ $data?->sekolah ?: '—' }}</dd>
                    <dt class="col-sm-4">Nama wali</dt>
                    <dd class="col-sm-8">{{ $data?->wali ?: '—' }}</dd>
                    <dt class="col-sm-4">Tanggal daftar</dt>
                    <dd class="col-sm-8">{{ $data?->created_at?->isoFormat('dddd, D MMMM Y HH:mm') ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Jumlah Ontime</p>
                <p class="h3 mb-0" style="color: var(--ck-success);">{{ $jumlah_ontime }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Jumlah Terlambat</p>
                <p class="h3 mb-0" style="color: var(--ck-danger);">{{ $jumlah_terlambat }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Jumlah Izin</p>
                <p class="h3 mb-0">{{ $jumlah_izin }}</p>
            </section>
        </div>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-3">Menu</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pelajar.masukkan_token') }}" class="btn btn-ck">CAT</a>
            <a href="{{ route('pelajar.capaian') }}" class="btn btn-ck-ghost">Capaian Tes</a>
            <a href="{{ route('pelajar.absensi') }}" class="btn btn-ck-ghost">Absensi</a>
        </div>
    </section>
    @include('pelajar.partials.kartu-absensi')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('kartu-absensi-modal');
        const el = document.querySelector('#kartu-absensi-qr');
        if (!modal || !el || !el.dataset.qrToken || typeof QRCode === 'undefined') {
            return;
        }

        modal.addEventListener('shown.bs.modal', () => {
            if (el.dataset.ready === '1') {
                return;
            }
            new QRCode(el, {
                text: el.dataset.qrToken,
                width: 148,
                height: 148,
                colorDark: '#1B2430',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
            el.dataset.ready = '1';
        });
    });
</script>
@endpush
