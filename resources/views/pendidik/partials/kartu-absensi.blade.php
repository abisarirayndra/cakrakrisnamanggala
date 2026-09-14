<div class="modal fade" id="kartu-absensi-modal" tabindex="-1" aria-labelledby="kartuAbsensiTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ck-card">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title h5" id="kartuAbsensiTitle">Kartu Absensi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <article class="ck-id-card mx-auto">
                    <header class="ck-id-card-head">
                        <p class="mb-0 small fw-semibold">Cakra Krisna Manggala</p>
                        <p class="mb-0 small opacity-75">Kartu Absensi Pendidik</p>
                    </header>
                    <div class="ck-id-card-body">
                        <div class="ck-photo-frame ck-id-card-photo">
                            @if ($data->foto)
                                <img src="{{ asset('pendidik/img/'.$data->foto) }}" alt="Foto {{ $user }}">
                            @endif
                        </div>
                        <div class="ck-id-card-meta">
                            <p class="ck-hint mb-1">Nama</p>
                            <p class="fw-semibold mb-3">{{ $user }}</p>
                            <p class="ck-hint mb-1">Mata pelajaran</p>
                            <p class="fw-semibold mb-3">{{ $data->mapel ?: '—' }}</p>
                            <p class="ck-hint mb-1">Nomor registrasi</p>
                            <p class="fw-semibold mb-0">{{ $nomor_registrasi ?: '—' }}</p>
                        </div>
                        <div class="ck-id-card-qr">
                            @if ($nomor_registrasi)
                                <div id="kartu-absensi-qr" class="ck-qr" data-qr-token="{{ $nomor_registrasi }}"></div>
                                <p class="ck-hint small text-center mb-0 mt-2">Tunjukkan ke scanner</p>
                            @else
                                <p class="ck-hint mb-0">Nomor registrasi belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
            <div class="modal-footer border-0 pt-0">
                @if ($nomor_registrasi)
                    <a href="{{ route('pendidik.kartu-absensi') }}" class="btn btn-ck">
                        <i class="bi bi-download"></i> Unduh PDF
                    </a>
                @endif
                <button type="button" class="btn btn-ck-ghost" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
