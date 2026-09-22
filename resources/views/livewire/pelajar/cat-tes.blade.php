<div>
    @if ($bankId === null)
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
                <h1 class="h4 mb-1">{{ $jadwal->nama }}</h1>
                <p class="ck-hint mb-0">Pilih bank soal yang ingin dikerjakan.</p>
            </div>
            @include('livewire.pelajar.partials.cat-sisa-waktu')
        </div>
        <div class="d-flex flex-column gap-3">
            @foreach ($ringkasan['banks'] as $bank)
                <section class="ck-card p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-semibold mb-1">{{ $bank['nama'] }}</p>
                        <p class="ck-hint mb-0">{{ $bank['jumlah'] }} soal · {{ $bank['status'] }}</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-ck" wire:click="pilihBank({{ $bank['id'] }})">
                        {{ $bank['status'] === 'Selesai' ? 'Lihat hasil' : ($bank['status'] === 'Berjalan' ? 'Lanjutkan' : 'Kerjakan') }}
                    </button>
                </section>
            @endforeach
        </div>
        @if (collect($ringkasan['banks'])->contains(fn ($bank) => $bank['status'] === 'Selesai'))
            <div class="mt-4">
                <a href="{{ route('pelajar.cat.tes.pdf', $jadwal) }}" class="btn btn-ck-ghost">Unduh PDF</a>
            </div>
        @endif
    @elseif ($sesi?->sudahSelesai())
        <section class="ck-card p-4 p-md-5 mx-auto" style="max-width: 640px;">
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
            <h1 class="h4 mb-2">Tes selesai</h1>
            <p class="ck-hint mb-3">{{ $jadwal->nama }}@if ($bankAktif) · {{ $bankAktif->nama }}@endif</p>
            @if ($laporan)
                <div class="ck-hasil-tes mb-4">
                    <div class="ck-hasil-item ck-hasil-benar">
                        <p class="ck-hint mb-1">Benar</p>
                        <p class="ck-hasil-angka mb-0">{{ $laporan['benar'] }}</p>
                    </div>
                    <div class="ck-hasil-item ck-hasil-salah">
                        <p class="ck-hint mb-1">Salah</p>
                        <p class="ck-hasil-angka mb-0">{{ $laporan['salah'] }}</p>
                    </div>
                    <div class="ck-hasil-item ck-hasil-skor">
                        <p class="ck-hint mb-1">Total skor</p>
                        <p class="ck-hasil-angka mb-0">{{ $ringkasan['total'] }}</p>
                    </div>
                </div>
            @endif
            <div class="table-responsive mb-4">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bank</th>
                            <th>Status</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ringkasan['banks'] as $bank)
                            <tr>
                                <td>{{ $bank['nama'] }}</td>
                                <td>{{ $bank['status'] }}</td>
                                <td class="fw-semibold">{{ $bank['skor'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="fw-semibold">Total</td>
                            <td class="fw-semibold">{{ $ringkasan['total'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($daftarBank->count() > 1)
                    <button type="button" class="btn btn-ck-ghost" wire:click="kePilihan">Pilih bank lain</button>
                @endif
                <a href="{{ route('pelajar.cat.tes.pdf', $jadwal) }}" class="btn btn-ck-ghost">Unduh PDF</a>
                <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-ck">Kembali ke beranda</a>
            </div>
        </section>
    @else
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
                <h1 class="h4 mb-1">{{ $jadwal->nama }}</h1>
                <p class="ck-hint mb-0">
                    @if ($bankAktif){{ $bankAktif->nama }} · @endif
                    Soal {{ $daftarSoal->count() === 0 ? 0 : $nomor + 1 }} dari {{ $daftarSoal->count() }}
                </p>
            </div>
            @include('livewire.pelajar.partials.cat-sisa-waktu')
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($daftarSoal as $index => $item)
                    <button
                        type="button"
                        class="btn btn-sm ck-nav-nomor {{ filled($jawaban[$item->id] ?? null) ? 'ck-nav-nomor-isi' : 'ck-nav-nomor-kosong' }} {{ $index === $nomor ? 'ck-nav-nomor-aktif' : '' }}"
                        wire:click="keSoal({{ $index }})"
                    >
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
            <button
                type="button"
                class="btn btn-sm btn-ck"
                wire:click="kumpulkan"
                wire:confirm="Kumpulkan jawaban sekarang?"
            >
                Kumpulkan
            </button>
        </div>

        @if ($soalAktif)
            <section class="ck-card p-4 mb-4" wire:key="soal-{{ $soalAktif->id }}">
                <div class="{{ $matematis ? 'js-katex ' : '' }}mb-3">{!! nl2br(e($soalAktif->soal)) !!}</div>
                @if ($soalAktif->gambar)
                    <img src="{{ asset('storage/'.$soalAktif->gambar) }}" alt="Gambar soal" class="img-fluid rounded-3 mb-3" style="max-height: 240px;">
                @endif
                <div class="d-flex flex-column gap-2">
                    @foreach ($soalAktif->opsi as $opsi)
                        <button
                            type="button"
                            class="btn text-start {{ ($jawaban[$soalAktif->id] ?? '') === $opsi->kode ? 'btn-ck' : 'btn-ck-ghost' }}"
                            wire:click="pilihJawaban('{{ $opsi->kode }}')"
                        >
                            <span class="fw-semibold me-2">{{ $opsi->kode }}.</span>
                            <span class="{{ $matematis ? 'js-katex' : '' }}">{!! nl2br(e($opsi->teks)) !!}</span>
                            @if ($opsi->gambar)
                                <img src="{{ asset('storage/'.$opsi->gambar) }}" alt="Gambar opsi {{ $opsi->kode }}" class="img-fluid rounded-3 mt-2 d-block" style="max-height: 140px;">
                            @endif
                        </button>
                    @endforeach
                </div>
            </section>
        @else
            <section class="ck-card p-4 mb-4">
                <p class="ck-hint mb-0">Soal kosong.</p>
            </section>
        @endif

        <div class="d-flex flex-wrap justify-content-between gap-2">
            <button
                type="button"
                class="btn btn-ck-ghost"
                wire:click="keSoal({{ max(0, $nomor - 1) }})"
                @disabled($nomor === 0)
            >
                Sebelumnya
            </button>
            @if ($nomor + 1 < $daftarSoal->count())
                <button type="button" class="btn btn-ck-ghost" wire:click="keSoal({{ $nomor + 1 }})">Berikutnya</button>
            @endif
        </div>
    @endif
</div>

@if ($matematis)
    @assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
    @endassets
    @script
    <script>
        const renderKatex = () => {
            if (typeof renderMathInElement !== 'function') {
                return;
            }
            document.querySelectorAll('.js-katex').forEach((el) => {
                renderMathInElement(el, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true },
                        { left: '$', right: '$', display: false },
                    ],
                    throwOnError: false,
                });
            });
        };
        const loadKatex = () => {
            if (typeof renderMathInElement === 'function') {
                renderKatex();
                return;
            }
            if (window.__ckKatexLoading) {
                return;
            }
            window.__ckKatexLoading = true;
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js';
            script.onload = () => {
                const auto = document.createElement('script');
                auto.src = 'https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js';
                auto.onload = () => {
                    window.__ckKatexLoading = false;
                    renderKatex();
                };
                document.head.appendChild(auto);
            };
            document.head.appendChild(script);
        };
        loadKatex();
        Livewire.hook('morph.updated', () => renderKatex());
    </script>
    @endscript
@endif
