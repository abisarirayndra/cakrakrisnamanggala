<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">CAT</p>
            <h1 class="h3 mb-1">Bank Soal</h1>
            <p class="ck-hint mb-0">Buat paket dulu, pilih tipe soalnya, lalu isi bank di dalam paket itu.</p>
        </div>
        @if (! $formTerbuka)
            <button type="button" class="btn btn-ck" wire:click="bukaForm">Tambah Paket</button>
        @endif
    </div>

    @if ($formTerbuka)
        <section class="ck-card p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h5 mb-0">{{ $editId ? 'Ubah paket' : 'Paket baru' }}</h2>
                <button type="button" class="btn btn-ck-ghost" wire:click="batal">Batal</button>
            </div>
            <form wire:submit="simpan">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama paket</label>
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Contoh: Matematika Dasar">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <p class="form-label">Tipe penilaian</p>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="tipe-tunggal" value="{{ \App\Support\BankSoalTipe::TUNGGAL }}" wire:model="tipe">
                            <label class="form-check-label" for="tipe-tunggal">
                                Jawaban Tunggal — satu kunci, satu poin untuk seluruh soal di paket ini
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="tipe-pembobotan" value="{{ \App\Support\BankSoalTipe::PEMBOBOTAN }}" wire:model="tipe">
                            <label class="form-check-label" for="tipe-pembobotan">
                                Pembobotan — setiap opsi punya skor sendiri
                            </label>
                        </div>
                    </div>
                    @error('tipe') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <p class="form-label">Tipe soal</p>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="bentuk-biasa" value="{{ \App\Support\BankSoalBentuk::BIASA }}" wire:model="bentuk">
                            <label class="form-check-label" for="bentuk-biasa">
                                Biasa — teks biasa, tanpa konversi rumus
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="bentuk-matematis" value="{{ \App\Support\BankSoalBentuk::MATEMATIS }}" wire:model="bentuk">
                            <label class="form-check-label" for="bentuk-matematis">
                                Matematis — ada sisip rumus dan pratinjau LaTeX
                            </label>
                        </div>
                    </div>
                    @error('bentuk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-ck">Simpan</button>
            </form>
        </section>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse ($daftar as $item)
            <section class="ck-card p-4" wire:key="paket-{{ $item->id }}">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <p class="fw-semibold mb-1">{{ $item->nama }}</p>
                        <p class="ck-hint mb-0">
                            {{ \App\Support\BankSoalTipe::label($item->tipe) }}
                            · {{ \App\Support\BankSoalBentuk::label($item->bentuk ?: \App\Support\BankSoalBentuk::MATEMATIS) }}
                            · {{ $item->soal_count }} soal
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('pendidik.cat.bank-soal.paket', $item) }}" class="btn btn-sm btn-ck">Isi Soal</a>
                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubah({{ $item->id }})">Ubah</button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill"
                            wire:click="hapus({{ $item->id }})"
                            wire:confirm="Hapus paket ini beserta soalnya?"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </section>
        @empty
            <section class="ck-card p-4">
                <p class="ck-hint mb-0">Belum ada paket. Buat paket untuk menentukan tipe soal, lalu isi banknya.</p>
            </section>
        @endforelse
    </div>
</div>
