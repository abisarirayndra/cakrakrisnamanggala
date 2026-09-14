@extends('layouts.panel-pendidik')

@section('title', 'Beranda Pendidik - Cakra Krisna Manggala')

@section('content')
    <section class="ck-card p-4 p-md-5 mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Pendidik</p>
        <h1 class="h3 mb-2">Selamat datang, {{ $user }}</h1>
        <p class="ck-hint mb-4">Ringkasan data diri Anda.</p>

        <div class="row g-4 align-items-start">
            <div class="col-md-3 text-center">
                <div class="ck-photo-frame mx-auto">
                    @if ($data->foto)
                        <img src="{{ asset('pendidik/img/'.$data->foto) }}" alt="Foto {{ $user }}">
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h5 mb-0">Data Diri</h2>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-ck-ghost" data-bs-toggle="modal" data-bs-target="#kartu-absensi-modal">
                            Kartu Absensi
                        </button>
                        <a href="{{ route('pendidik.dinas.edit') }}" class="btn btn-ck-ghost">Edit</a>
                    </div>
                </div>
                <dl class="ck-meta row mb-0">
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $user }}</dd>
                    <dt class="col-sm-4">NIK</dt>
                    <dd class="col-sm-8">{{ $data->nik ?: '—' }}</dd>
                    <dt class="col-sm-4">NIP</dt>
                    <dd class="col-sm-8">{{ $data->nip ?: '—' }}</dd>
                    <dt class="col-sm-4">Tempat, tanggal lahir</dt>
                    <dd class="col-sm-8">
                        {{ $data->tempat_lahir ?: '—' }},
                        {{ $data->tanggal_lahir ? \Carbon\Carbon::parse($data->tanggal_lahir)->isoFormat('D MMMM Y') : '—' }}
                    </dd>
                    <dt class="col-sm-4">Alamat</dt>
                    <dd class="col-sm-8">{{ $data->alamat ?: '—' }}</dd>
                    <dt class="col-sm-4">Mata pelajaran</dt>
                    <dd class="col-sm-8">{{ $data->mapel ?: '—' }}</dd>
                    <dt class="col-sm-4">No. telpon/WhatsApp</dt>
                    <dd class="col-sm-8">{{ $data->wa ?: '—' }}</dd>
                    <dt class="col-sm-4">Nama ibu kandung</dt>
                    <dd class="col-sm-8">{{ $data->ibu ?: '—' }}</dd>
                    <dt class="col-sm-4">Status Dapodik</dt>
                    <dd class="col-sm-8">{{ $data->status_dapodik ?: '—' }}</dd>
                    <dt class="col-sm-4">Tanggal daftar</dt>
                    <dd class="col-sm-8">
                        {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm') : '—' }}
                    </dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="ck-card p-4 p-md-5">
        @if ($data->tempat_lahir == null)
            <h2 class="h5 mb-3">Menu</h2>
            <p class="mb-2" style="color: var(--ck-danger);"><b>SILAKAN MELAKUKAN EDIT DATA DIRI DAHULU SEBELUM MENGAKSES MENU</b></p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-ck-ghost" disabled>Paket Soal (Not Available)</button>
                <button type="button" class="btn btn-ck-ghost" disabled>Absensi (Not Available)</button>
            </div>
        @else
            <h2 class="h5 mb-3">Menu</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('pendidik.dinas.paket') }}" class="btn btn-ck">Paket Soal</a>
                <a href="{{ route('pendidik.dinas.analisis') }}" class="btn btn-ck-ghost">Analisis</a>
                <a href="{{ route('pendidik.absensi') }}" class="btn btn-ck-ghost">Absensi</a>
                @if ($data->mapel == 'Jasmani')
                    <a href="{{ route('pendidik.absensi.jadwal_jasmani') }}" class="btn btn-ck-ghost">Absen Lapangan</a>
                @endif
            </div>
        @endif
    </section>
    @include('pendidik.partials.kartu-absensi')
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
