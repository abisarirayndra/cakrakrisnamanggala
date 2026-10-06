<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Tes</p>
            <h1 class="h3 mb-1">Jadwal CAT</h1>
            <p class="ck-hint mb-0">Jadwal mandiri memakai bank soal milik Anda. Waktu dan token tes sama untuk semua bank di paket itu.</p>
        </div>
        @if (! $formTerbuka)
            <button type="button" class="btn btn-ck" wire:click="bukaForm">Tambah Jadwal</button>
        @endif
    </div>

    @if ($formTerbuka)
        <section class="ck-card p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h5 mb-0">{{ $editId ? 'Ubah jadwal' : 'Jadwal baru' }}</h2>
                <button type="button" class="btn btn-ck-ghost" wire:click="batal">Batal</button>
            </div>
            <form wire:submit="simpan">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama paket</label>
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Contoh: Latihan Matematika">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="bank_paket_id" class="form-label">Bank soal</label>
                    <div class="d-flex flex-wrap gap-2">
                        <select id="bank_paket_id" class="form-select @error('bank_paket_id') is-invalid @enderror" wire:model="bank_paket_id">
                            <option value="">Pilih bank soal</option>
                            @foreach ($bankList as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->nama }}
                                    · {{ \App\Support\BankSoalTipe::label($bank->tipe) }}
                                    · {{ $bank->soal_count }} soal
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-ck-ghost" wire:click="tambahBank">
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
                                        {{ $bank['mapel'] ?: 'Tanpa mapel' }}
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

    <section class="ck-card p-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 3rem;">No</th>
                        <th>Nama</th>
                        <th>Bank soal</th>
                        <th>Waktu</th>
                        <th>Token tes</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar as $item)
                        <tr wire:key="cat-jadwal-{{ $item->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td>{{ $item->banks->pluck('nama')->filter()->join(' · ') ?: 'Belum ada bank soal' }}</td>
                            <td class="text-nowrap">
                                @if ($item->mulai && $item->selesai)
                                    {{ $item->mulai->format('d M H:i') }}–{{ $item->selesai->format('H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <code class="fw-semibold">{{ $item->token }}</code>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-ck-ghost"
                                        wire:click="perbaruiToken({{ $item->id }})"
                                        wire:confirm="Perbarui token tes ini? Token lama tidak bisa dipakai lagi."
                                    >
                                        Perbarui
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    <a href="{{ route('pendidik.cat.analisis', $item) }}" class="btn btn-sm btn-ck">Analisis</a>
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="ck-hint">Belum ada jadwal CAT mandiri.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

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
