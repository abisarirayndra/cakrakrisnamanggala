<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Tes</p>
            <h1 class="h3 mb-1">Jadwal CAT</h1>
            <p class="ck-hint mb-0">Satu paket bisa memakai beberapa bank soal. Waktu dan token tes sama untuk semua bank di paket itu.</p>
        </div>
        @if (! $formTerbuka)
            <button type="button" class="btn btn-ck" wire:click="bukaForm">Tambah Jadwal</button>
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
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Contoh: UTS Matematika">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="pendidik_id" class="form-label">Filter pendidik</label>
                    <div class="ck-select2" wire:ignore wire:key="pendidik-select-{{ $editId ?? 'baru' }}">
                        <select
                            id="pendidik_id"
                            class="form-select js-select2-pendidik @error('pendidik_id') is-invalid @enderror"
                            data-placeholder="Cari pendidik"
                        >
                            <option value="">Pilih pendidik</option>
                            @foreach ($pendidikList as $guru)
                                <option value="{{ $guru->id }}" @selected((string) $guru->id === $pendidik_id)>{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('pendidik_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="bank_paket_id" class="form-label">Bank soal</label>
                    <div class="d-flex flex-wrap gap-2">
                        <select id="bank_paket_id" class="form-select @error('bank_paket_id') is-invalid @enderror" wire:model="bank_paket_id" @disabled($pendidik_id === '')>
                            <option value="">{{ $pendidik_id === '' ? 'Pilih pendidik dulu' : 'Pilih bank soal' }}</option>
                            @foreach ($bankList as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->nama }}
                                    · {{ \App\Support\BankSoalTipe::label($bank->tipe) }}
                                    · {{ $bank->soal_count }} soal
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-ck-ghost" wire:click="tambahBank" @disabled($pendidik_id === '')>
                            Tambah bank
                        </button>
                    </div>
                    @error('bank_paket_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('bankTerpilih') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                @if (count($bankTerpilih) > 0)
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach ($bankTerpilih as $index => $bank)
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 ck-card p-3">
                                <div>
                                    <p class="fw-semibold mb-0">{{ $bank['nama'] }}</p>
                                    <p class="ck-hint mb-0">
                                        {{ $bank['pendidik'] }}
                                        · {{ $bank['tipe'] }}
                                        · {{ $bank['soal_count'] }} soal
                                    </p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="hapusBank({{ $index }})">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="mulai" class="form-label">Mulai</label>
                        <input id="mulai" type="datetime-local" class="form-control @error('mulai') is-invalid @enderror" wire:model="mulai">
                        @error('mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="selesai" class="form-label">Selesai</label>
                        <input id="selesai" type="datetime-local" class="form-control @error('selesai') is-invalid @enderror" wire:model="selesai">
                        @error('selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-ck">Simpan</button>
            </form>
        </section>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse ($daftar as $item)
            <section class="ck-card p-4" wire:key="cat-jadwal-{{ $item->id }}">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                    <div>
                        <p class="fw-semibold mb-1">{{ $item->nama }}</p>
                        <p class="ck-hint mb-2">
                            {{ $item->banks->pluck('nama')->filter()->join(' · ') ?: 'Belum ada bank soal' }}
                            @if ($item->mulai && $item->selesai)
                                · {{ $item->mulai->format('d M H:i') }}–{{ $item->selesai->format('H:i') }}
                            @endif
                        </p>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="ck-hint">Token tes</span>
                            <code class="fs-5 fw-semibold mb-0">{{ $item->token }}</code>
                            <button
                                type="button"
                                class="btn btn-sm btn-ck-ghost"
                                wire:click="perbaruiToken({{ $item->id }})"
                                wire:confirm="Perbarui token tes ini? Token lama tidak bisa dipakai lagi."
                            >
                                Perbarui
                            </button>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.cat.jadwal.skor', $item) }}" class="btn btn-sm btn-ck">Live skor</a>
                        <a href="{{ route('admin.cat.jadwal.report', $item) }}" class="btn btn-sm btn-ck-ghost">Report</a>
                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihatChat({{ $item->id }})">
                            Teks chat
                        </button>
                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubah({{ $item->id }})">
                            Ubah
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill"
                            wire:click="hapus({{ $item->id }})"
                            wire:confirm="Hapus jadwal CAT ini?"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </section>
        @empty
            <section class="ck-card p-4">
                <p class="ck-hint mb-0">Belum ada jadwal CAT.</p>
            </section>
        @endforelse
    </div>

    @if ($chatId)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="teksChatTitle">
            <section class="ck-card p-4" style="max-width: 520px; width: 100%;" x-data="{ copied: false }">
                <h2 class="h5 mb-2" id="teksChatTitle">Teks chat pelajar</h2>
                <p class="ck-hint mb-3">Salin lalu kirim ke pelajar.</p>
                <textarea id="teks-chat-pelajar" class="form-control font-monospace mb-3" rows="6" readonly x-ref="teks">{{ $teksChat }}</textarea>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-ck-ghost" wire:click="tutupChat">Tutup</button>
                    <button
                        type="button"
                        class="btn btn-ck"
                        @click="navigator.clipboard.writeText($refs.teks.value); copied = true; setTimeout(() => copied = false, 1500)"
                    >
                        <span x-show="!copied">Salin</span>
                        <span x-show="copied" x-cloak>Tersalin</span>
                    </button>
                </div>
            </section>
        </div>
    @endif
</div>

@assets
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endassets
@script
<script>
    const bindPendidikSelect = () => {
        const el = document.querySelector('.js-select2-pendidik');
        if (!el || !window.jQuery?.fn?.select2) {
            return;
        }

        const $el = window.jQuery(el);
        if ($el.data('select2')) {
            return;
        }

        $el.select2({
            width: '100%',
            placeholder: el.getAttribute('data-placeholder') || 'Cari pendidik',
            allowClear: true,
            dropdownParent: window.jQuery(document.body),
            language: {
                noResults: () => 'Tidak ada pendidik',
                searching: () => 'Mencari…',
            },
        });
        $el.on('change', () => {
            $wire.set('pendidik_id', $el.val() || '');
        });
    };

    const loadSelect2 = () => {
        if (window.jQuery?.fn?.select2) {
            bindPendidikSelect();
            return;
        }
        if (window.__ckSelect2Loading) {
            return;
        }
        window.__ckSelect2Loading = true;
        const jquery = document.createElement('script');
        jquery.src = 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js';
        jquery.onload = () => {
            const select2 = document.createElement('script');
            select2.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            select2.onload = () => {
                window.__ckSelect2Loading = false;
                bindPendidikSelect();
            };
            document.head.appendChild(select2);
        };
        document.head.appendChild(jquery);
    };

    loadSelect2();
    Livewire.hook('morph.updated', () => bindPendidikSelect());
</script>
@endscript
