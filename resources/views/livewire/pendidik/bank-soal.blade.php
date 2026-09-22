<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">CAT</p>
            <h1 class="h3 mb-1">{{ $paket->nama }}</h1>
            <p class="ck-hint mb-0">
                {{ \App\Support\BankSoalTipe::label($paket->tipe) }}
                · {{ \App\Support\BankSoalBentuk::label($paket->bentuk ?: \App\Support\BankSoalBentuk::MATEMATIS) }}.
                @if ($paket->isMatematis())
                    Rumus dan gambar bisa dipakai di soal maupun opsi.
                @else
                    Isi soal dan opsi dengan teks biasa. Gambar tetap bisa dilampirkan.
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pendidik.cat.bank-soal') }}" class="btn btn-ck-ghost">Kembali</a>
            <button type="button" class="btn btn-ck-ghost" wire:click="bukaImpor">Impor Excel</button>
            @if (! $formTerbuka)
                <button type="button" class="btn btn-ck" wire:click="bukaForm">Tambah Soal</button>
            @endif
        </div>
    </div>

    @if ($formTerbuka)
        <section class="ck-card p-4 mb-4" wire:key="form-bank-soal-{{ $editId ?? 'baru' }}" x-data="ckBankGambarCropper">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h5 mb-0">{{ $editId ? 'Ubah soal' : 'Soal baru' }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    @if ($paket->isMatematis())
                        <button type="button" class="btn btn-ck-ghost" wire:click="bukaPanduanRumus">Cara sisipkan rumus</button>
                    @endif
                    <button type="button" class="btn btn-ck-ghost" wire:click="batal">Batal</button>
                </div>
            </div>

            <form wire:submit="simpan">
                @if ($paket->tipe === \App\Support\BankSoalTipe::TUNGGAL)
                    <div class="mb-3">
                        <label for="poin" class="form-label">Poin jika benar</label>
                        <input id="poin" type="number" min="0" class="form-control @error('poin') is-invalid @enderror" wire:model="poin">
                        @error('poin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                        <label for="soal" class="form-label mb-0">Soal</label>
                        <div class="d-flex flex-wrap gap-2">
                            @if ($paket->isMatematis())
                                <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="bukaRumusWidget">Sisip rumus</button>
                            @endif
                            <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="toggleGambarSoal">{{ $showGambarSoal ? 'Tutup gambar' : 'Sisip gambar' }}</button>
                        </div>
                    </div>
                    @if ($paket->isMatematis())
                        <textarea id="soal" rows="4" class="form-control @error('soal') is-invalid @enderror" wire:model.live.debounce.400ms="soal" placeholder="Contoh: Nilai dari $\frac{1}{2} + \frac{1}{3}$ adalah ..."></textarea>
                    @else
                        <textarea id="soal" rows="4" class="form-control @error('soal') is-invalid @enderror" wire:model="soal" placeholder="Tulis soal"></textarea>
                    @endif
                    @error('soal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if ($paket->isMatematis() && filled($soal))
                    <div class="ck-soal-preview mb-3 p-3 border rounded-3">
                        <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">Pratinjau</p>
                        <div class="js-katex">{!! nl2br(e($soal)) !!}</div>
                    </div>
                @endif

                @if ($showGambarSoal)
                    <div class="mb-4">
                        <label for="gambar" class="form-label">Gambar soal</label>
                        <p class="ck-hint mb-2">Pilih gambar, potong area yang dipakai, lalu pilih ukuran.</p>
                        <input id="gambar" type="file" accept="image/*" class="form-control @error('gambar') is-invalid @enderror" @change="open($event, 'soal')">
                        @error('gambar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="gambar" class="ck-hint small mt-1">Mengunggah…</div>
                        @if ($gambar)
                            <img src="{{ $gambar->temporaryUrl() }}" alt="Pratinjau gambar" class="img-fluid rounded-3 mt-2" style="max-height: 180px;">
                        @elseif ($gambarLama)
                            <div class="d-flex flex-wrap align-items-start gap-2 mt-2">
                                <img src="{{ asset('storage/'.$gambarLama) }}" alt="Gambar soal" class="img-fluid rounded-3" style="max-height: 180px;">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="hapusGambar">Hapus gambar</button>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach (['A', 'B', 'C', 'D', 'E'] as $kode)
                        <div class="border rounded-3 p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <label for="opsi-{{ $kode }}" class="form-label mb-0">Opsi {{ $kode }}{{ $kode === 'E' ? ' (opsional)' : '' }}</label>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    @if ($paket->isMatematis())
                                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="bukaRumusWidget('{{ $kode }}')">Sisip rumus</button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="toggleGambarOpsi('{{ $kode }}')">{{ ($showGambarOpsi[$kode] ?? false) ? 'Tutup gambar' : 'Sisip gambar' }}</button>
                                    @if ($paket->tipe === \App\Support\BankSoalTipe::TUNGGAL)
                                        <div class="form-check mb-0">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="kunci"
                                                id="kunci-{{ $kode }}"
                                                value="{{ $kode }}"
                                                wire:model="kunci"
                                            >
                                            <label class="form-check-label" for="kunci-{{ $kode }}">Kunci</label>
                                        </div>
                                    @else
                                        <div style="max-width: 8rem;">
                                            <label for="poin-{{ $kode }}" class="form-label small mb-1">Skor</label>
                                            <input id="poin-{{ $kode }}" type="number" class="form-control form-control-sm @error('poinOpsi.'.$kode) is-invalid @enderror" wire:model="poinOpsi.{{ $kode }}">
                                            @error('poinOpsi.'.$kode) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($paket->isMatematis())
                                <textarea id="opsi-{{ $kode }}" rows="2" class="form-control @error('opsi.'.$kode) is-invalid @enderror" wire:model.live.debounce.400ms="opsi.{{ $kode }}"></textarea>
                            @else
                                <textarea id="opsi-{{ $kode }}" rows="2" class="form-control @error('opsi.'.$kode) is-invalid @enderror" wire:model="opsi.{{ $kode }}"></textarea>
                            @endif
                            @error('opsi.'.$kode) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($paket->isMatematis() && filled($opsi[$kode] ?? ''))
                                <div class="ck-soal-preview mt-2 p-2 border rounded-3">
                                    <div class="js-katex">{!! nl2br(e($opsi[$kode])) !!}</div>
                                </div>
                            @endif
                            @if ($showGambarOpsi[$kode] ?? false)
                                <div class="mt-2">
                                    <label for="gambar-opsi-{{ $kode }}" class="form-label small">Gambar opsi</label>
                                    <input id="gambar-opsi-{{ $kode }}" type="file" accept="image/*" class="form-control form-control-sm @error('gambarOpsi.'.$kode) is-invalid @enderror" @change="open($event, '{{ $kode }}')">
                                    @error('gambarOpsi.'.$kode) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div wire:loading wire:target="gambarOpsi.{{ $kode }}" class="ck-hint small mt-1">Mengunggah…</div>
                                    @if ($gambarOpsi[$kode] ?? null)
                                        <img src="{{ $gambarOpsi[$kode]->temporaryUrl() }}" alt="Pratinjau opsi {{ $kode }}" class="img-fluid rounded-3 mt-2" style="max-height: 120px;">
                                    @elseif ($gambarOpsiLama[$kode] ?? null)
                                        <div class="d-flex flex-wrap align-items-start gap-2 mt-2">
                                            <img src="{{ asset('storage/'.$gambarOpsiLama[$kode]) }}" alt="Gambar opsi {{ $kode }}" class="img-fluid rounded-3" style="max-height: 120px;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="hapusGambarOpsi('{{ $kode }}')">Hapus gambar</button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @error('kunci') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-ck">Simpan</button>
            </form>

            <div class="modal fade" tabindex="-1" wire:ignore x-ref="cropModal" aria-labelledby="cropGambarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h2 class="modal-title h5" id="cropGambarLabel">Potong gambar</h2>
                            <button type="button" class="btn-close" @click="cancel()" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body pt-0">
                            <p class="ck-hint mb-3">Geser dan perbesar bingkai, lalu pilih ukuran simpan.</p>
                            <div class="ck-cropper-wrap mb-3">
                                <img x-ref="cropImage" alt="Gambar untuk dipotong">
                            </div>
                            <label class="form-label" for="ck-crop-size">Ukuran</label>
                            <select id="ck-crop-size" class="form-select" x-model="maxSize">
                                <option value="640">Kecil (640 px)</option>
                                <option value="960">Sedang (960 px)</option>
                                <option value="1200">Besar (1200 px)</option>
                            </select>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-ck-ghost" @click="cancel()">Batal</button>
                            <button type="button" class="btn btn-ck" @click="apply()" :disabled="uploading">
                                <span x-show="!uploading">Gunakan gambar</span>
                                <span x-cloak x-show="uploading">Menyimpan…</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        @if ($pesanImpor)
            <p class="mb-3" style="color: var(--ck-success);">{{ $pesanImpor }}</p>
        @endif
        <section class="ck-card p-4 mb-4">
            <label for="cari" class="form-label">Cari soal</label>
            <input id="cari" type="search" class="form-control" placeholder="Cari teks soal" wire:model.live.debounce.400ms="cari">
        </section>

        <div class="d-flex flex-column gap-3">
            @forelse ($daftar as $item)
                <section class="ck-card p-4" wire:key="bank-{{ $item->id }}">
                    <div class="d-flex flex-wrap align-items-start justify-content-end gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubah({{ $item->id }})">Ubah</button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill"
                            wire:click="hapus({{ $item->id }})"
                            wire:confirm="Hapus soal ini dari paket?"
                        >
                            Hapus
                        </button>
                    </div>
                    <div class="{{ $paket->isMatematis() ? 'js-katex ' : '' }}mb-3">{!! nl2br(e($item->soal)) !!}</div>
                    @if ($item->gambar)
                        <img src="{{ asset('storage/'.$item->gambar) }}" alt="Gambar soal" class="img-fluid rounded-3 mb-3" style="max-height: 200px;">
                    @endif
                    <ol type="A" class="mb-2 ps-3">
                        @foreach ($item->opsi as $baris)
                            <li class="mb-2">
                                <span class="{{ $paket->isMatematis() ? 'js-katex' : '' }}">{!! nl2br(e($baris->teks)) !!}</span>
                                @if ($paket->tipe === \App\Support\BankSoalTipe::PEMBOBOTAN)
                                    <span class="ck-hint"> · skor {{ $baris->poin }}</span>
                                @elseif ($item->kunci === $baris->kode)
                                    <span class="fw-semibold" style="color: var(--ck-success);"> · kunci</span>
                                @endif
                                @if ($baris->gambar)
                                    <div class="mt-1">
                                        <img src="{{ asset('storage/'.$baris->gambar) }}" alt="Gambar opsi {{ $baris->kode }}" class="img-fluid rounded-3" style="max-height: 120px;">
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                    @if ($paket->tipe === \App\Support\BankSoalTipe::TUNGGAL)
                        <p class="ck-hint mb-0">Poin {{ $item->poin }}</p>
                    @endif
                </section>
            @empty
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Belum ada soal di paket ini.</p>
                </section>
            @endforelse
        </div>
    @endif

    @if ($showImpor)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="imporExcelTitle">
            <section class="ck-card p-4" style="max-width: 520px; width: 100%;">
                <h2 class="h5 mb-1" id="imporExcelTitle">Impor Excel</h2>
                <p class="ck-hint mb-3">Unduh template, isi soal, lalu unggah file yang sama.</p>
                <a href="{{ route('pendidik.cat.bank-soal.template', $paket) }}" class="btn btn-ck-ghost mb-3">Unduh template</a>
                <label for="fileImpor" class="form-label">File Excel</label>
                <input id="fileImpor" type="file" accept=".xlsx,.xls" class="form-control @error('fileImpor') is-invalid @enderror" wire:model="fileImpor">
                @foreach ($errors->get('fileImpor') as $pesan)
                    <div class="text-danger small mt-1">{{ $pesan }}</div>
                @endforeach
                <div wire:loading wire:target="fileImpor" class="ck-hint small mt-1">Mengunggah…</div>
                <p class="ck-hint small mt-2 mb-0">Opsi E boleh kosong. Gambar tidak ikut diimpor.</p>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-ck-ghost" wire:click="tutupImpor">Batal</button>
                    <button type="button" class="btn btn-ck" wire:click="impor" wire:loading.attr="disabled" wire:target="impor,fileImpor">Impor</button>
                </div>
            </section>
        </div>
    @endif

    @if ($paket->isMatematis() && $showRumusWidget)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="rumusWidgetTitle">
            <section class="ck-card p-4" style="max-width: 640px; width: 100%;">
                <h2 class="h5 mb-1" id="rumusWidgetTitle">Editor rumus</h2>
                <p class="ck-hint mb-3">
                    Susun rumus di kotak bawah, lalu masukkan ke
                    <b>{{ $rumusTarget ? 'opsi '.$rumusTarget : 'soal' }}</b>.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="\frac{\placeholder{}}{\placeholder{}}">Pecahan</button>
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="\sqrt{\placeholder{}}">Akar</button>
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="^{\placeholder{}}">Pangkat</button>
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="\times">Kali</button>
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="\div">Bagi</button>
                    <button type="button" class="btn btn-sm btn-ck-ghost js-math-chip" data-latex="\pi">Pi</button>
                </div>
                <div wire:ignore>
                    <math-field
                        id="ck-math-widget"
                        class="ck-math-widget"
                        virtual-keyboard-mode="onfocus"
                        fonts-directory="https://cdn.jsdelivr.net/npm/mathlive@0.105.3/fonts/"
                        sounds-directory="https://cdn.jsdelivr.net/npm/mathlive@0.105.3/sounds/"
                    ></math-field>
                </div>
                @error('rumus')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                <p class="ck-hint small mt-2 mb-0">Papan ketik rumus muncul saat kotak diklik. Bisa juga ketik langsung, misalnya 1/2 lalu spasi.</p>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-ck-ghost" wire:click="tutupRumusWidget">Batal</button>
                    <button type="button" class="btn btn-ck" id="ck-math-insert">Masukkan</button>
                </div>
            </section>
        </div>
    @endif

    @if ($paket->isMatematis() && $showPanduanRumus)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="panduanRumusTitle">
            <section class="ck-card p-4" style="max-width: 560px; width: 100%;">
                <h2 class="h5 mb-3" id="panduanRumusTitle">Cara sisipkan rumus</h2>
                <ol class="ps-3 mb-4">
                    <li class="mb-2">Klik <b>Sisip rumus</b> pada soal atau opsi. Editor rumus akan terbuka.</li>
                    <li class="mb-2">Susun rumus di widget, atau pilih pintasan Pecahan, Akar, dan Pangkat.</li>
                    <li class="mb-2">Klik kotak rumus untuk membuka papan ketik matematis.</li>
                    <li class="mb-2">Tekan <b>Masukkan</b>. Rumus tampil di pratinjau, contoh <span class="js-katex">$\frac{1}{2}$</span>.</li>
                    <li class="mb-0">Kalau perlu, rumus masih bisa diketik manual dengan <code>$...$</code>.</li>
                </ol>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-ck" wire:click="tutupPanduanRumus">Mengerti</button>
                </div>
            </section>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('ckBankGambarCropper', () => ({
        cropper: null,
        uploading: false,
        target: 'soal',
        maxSize: '960',
        propertyName() {
            return this.target === 'soal' ? 'gambar' : `gambarOpsi.${this.target}`;
        },
        open(event, target) {
            const file = event.target.files?.[0];
            event.target.value = '';
            if (!file || !file.type.startsWith('image/')) {
                return;
            }
            this.target = target;
            if (typeof Cropper === 'undefined') {
                this.uploadFile(file);
                return;
            }

            const image = this.$refs.cropImage;
            const objectUrl = URL.createObjectURL(file);
            const modalEl = this.$refs.cropModal;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            const startCropper = () => {
                this.cropper?.destroy();
                this.cropper = new Cropper(image, {
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    dragMode: 'move',
                    responsive: true,
                });
            };

            modalEl.addEventListener('shown.bs.modal', startCropper, { once: true });
            modalEl.addEventListener('hidden.bs.modal', () => {
                this.cropper?.destroy();
                this.cropper = null;
                if (image.src.startsWith('blob:')) {
                    URL.revokeObjectURL(image.src);
                }
            }, { once: true });

            image.onload = () => modal.show();
            image.src = objectUrl;
        },
        cancel() {
            this.cropper?.destroy();
            this.cropper = null;
            bootstrap.Modal.getOrCreateInstance(this.$refs.cropModal).hide();
        },
        uploadFile(file) {
            this.$wire.upload(this.propertyName(), file, () => {
                this.uploading = false;
                this.cancel();
            }, () => {
                this.uploading = false;
            });
        },
        apply() {
            if (!this.cropper || this.uploading) {
                return;
            }
            this.uploading = true;
            const max = Number(this.maxSize) || 960;
            this.cropper.getCroppedCanvas({
                maxWidth: max,
                maxHeight: max,
                imageSmoothingQuality: 'high',
            }).toBlob((blob) => {
                if (!blob) {
                    this.uploading = false;
                    return;
                }
                const cropped = new File([blob], 'gambar-soal.jpg', { type: 'image/jpeg' });
                this.uploadFile(cropped);
            }, 'image/jpeg', 0.88);
        },
    }));
</script>
@endscript

@if ($paket->isMatematis())
@assets
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mathlive@0.105.3/mathlive-static.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mathlive@0.105.3/mathlive-fonts.css">
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
        if (window.__ckKatexLoading) {
            return;
        }
        if (typeof renderMathInElement === 'function') {
            renderKatex();
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

    const loadMathlive = () => {
        if (window.customElements?.get('math-field')) {
            return;
        }
        if (window.__ckMathliveLoading) {
            return;
        }
        window.__ckMathliveLoading = true;
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/mathlive@0.105.3/mathlive.min.js';
        script.onload = () => { window.__ckMathliveLoading = false; };
        document.head.appendChild(script);
    };

    const mathField = () => document.getElementById('ck-math-widget');

    if (!window.__ckMathWidgetBound) {
        window.__ckMathWidgetBound = true;
        document.addEventListener('click', (event) => {
            const insert = event.target.closest('#ck-math-insert');
            if (insert) {
                event.preventDefault();
                const latex = String(mathField()?.value ?? '').trim();
                $wire.sisipLatex(latex);
                return;
            }
            const chip = event.target.closest('.js-math-chip');
            if (! chip) {
                return;
            }
            event.preventDefault();
            const mf = mathField();
            const snippet = chip.getAttribute('data-latex') || '';
            if (mf && snippet) {
                if (typeof mf.executeCommand === 'function') {
                    mf.executeCommand(['insert', snippet]);
                } else if (typeof mf.insert === 'function') {
                    mf.insert(snippet);
                } else {
                    mf.value = String(mf.value || '') + snippet;
                }
                mf.focus?.();
            }
        });
    }

    loadKatex();
    loadMathlive();
    Livewire.hook('morph.updated', () => {
        renderKatex();
        loadMathlive();
    });
</script>
@endscript
@endif
