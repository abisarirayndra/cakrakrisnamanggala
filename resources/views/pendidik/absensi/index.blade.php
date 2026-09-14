@extends('layouts.panel-pendidik')

@section('title', 'Absensi Pendidik - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Kehadiran</p>
            <h1 class="h3 mb-1">Absensi Pendidik</h1>
            <p class="ck-hint mb-0">Tunjukkan kode QR kepada petugas, isi jurnal, lalu selesaikan sesi setelah pembelajaran berakhir.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button id="btn-qrcode" type="button" class="btn btn-ck" data-bs-toggle="modal" data-bs-target="#qrcode-modal">
                <i class="bi bi-qr-code-scan"></i> Kode QR
            </button>
            <form action="{{ route('pendidik.absensi.histori-mengajar') }}" method="GET">
                @if ($data->markas_id == 1)
                    <input type="hidden" name="kelas" value="1">
                @elseif ($data->markas_id == 2)
                    <input type="hidden" name="kelas" value="4">
                @elseif ($data->markas_id == 3)
                    <input type="hidden" name="kelas" value="9">
                @endif
                @php
                    $sekarang = \Carbon\Carbon::now();
                @endphp
                <input type="hidden" name="bulan" value="{{ $sekarang->format('m') }}">
                <input type="hidden" name="tahun" value="{{ $sekarang->format('Y') }}">
                <button class="btn btn-ck-ghost" type="submit">
                    <i class="bi bi-clock-history"></i> Histori Mengajar
                </button>
            </form>
        </div>
    </div>

    <section class="ck-card p-4 mb-4">
        <ol class="ck-hint mb-0 ps-3">
            <li>Klik tombol <b>Kode QR</b> di bawah ini</li>
            <li>Dekatkan layar perangkat anda ke scanner, tambah kecerahan perangkat anda bila tidak bisa terbaca scanner</li>
            <li>Sebelum klik selesai, pastikan isi jurnal pembelajaran</li>
            <li>Klik tombol <b>Selesai</b> jika proses pembelajaran berakhir</li>
        </ol>
        <input id="text" value="{{ $token }}" hidden>
        @if ($errors->any())
            <div class="alert alert-ck mt-3 mb-0">{{ $errors->first() }}</div>
        @endif
    </section>

    <div class="row g-3">
        @forelse ($jadwal as $item)
            <div class="col-md-6 col-xl-3">
                <section class="ck-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                        {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y') }}
                    </p>
                    @if ($item->status == 0)
                        <p class="mb-2" style="color: var(--ck-danger);">Terlambat</p>
                    @elseif ($item->status == 1)
                        <p class="mb-2" style="color: var(--ck-success);">Ontime</p>
                    @endif
                    <p class="mb-1">Mapel <b>{{ $item->mapel }}</b></p>
                    <p class="mb-1">Kelas <b>{{ $item->kelas }}</b></p>
                    <p class="mb-3 ck-hint">
                        {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm') }} –
                        {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}
                    </p>
                    @if (isset($item->jurnal))
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('pendidik.absensi.jurnal', [$item->id]) }}" class="btn btn-sm btn-ck-ghost">Lihat</a>
                            <form action="{{ route('pendidik.absensi.selesai', [$item->id]) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-ck">Pulang</button>
                            </form>
                        </div>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('pendidik.absensi.jurnal', [$item->id]) }}" class="btn btn-sm btn-ck">Isi Jurnal</a>
                            <button class="btn btn-sm btn-ck-ghost" disabled>Selesai</button>
                        </div>
                    @endif
                </section>
            </div>
        @empty
            <div class="col-12">
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Belum ada absensi yang sedang berjalan.</p>
                </section>
            </div>
        @endforelse
    </div>

    <div class="modal fade" id="qrcode-modal" tabindex="-1" aria-labelledby="qrcodeModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ck-card">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5" id="qrcodeModalTitle">QR Code Absensi</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="qrcode" class="ck-qr mx-auto"></div>
                    <p class="mt-3 mb-0">Token : <b>{{ $token }}</b></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let input = document.querySelector('#text');
    let button = document.querySelector('#btn-qrcode');
    let qrcode = new QRCode(document.querySelector('#qrcode'), {
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    button.addEventListener('click', () => {
        qrcode.makeCode(input.value);
    });
</script>
@endpush
