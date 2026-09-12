<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master pengguna</p>
            <h1 class="h3 mb-1">Pendaftar</h1>
            <p class="ck-hint mb-0">Tinjau pendaftaran dan terima ke kelas sesuai akses markas.</p>
        </div>
    </div>

    @if ($halaman === 'lihat' && $pendaftarAktif)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Detail pendaftar</p>
                    <h2 class="h4 mb-1">{{ $pendaftarAktif->user->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pendaftarAktif->user->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <div class="row g-4">
                <div class="col-md-3 text-center">
                    @if ($pendaftarAktif->foto)
                        <img src="{{ asset('img/pelajar/'.$pendaftarAktif->foto) }}" alt="Foto pendaftar" class="rounded" width="140">
                    @else
                        <div class="ck-hint">Belum ada foto</div>
                    @endif
                </div>
                <div class="col-md-9">
                    <dl class="row ck-meta mb-4">
                        <dt class="col-sm-4">NIK / NISN</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->nik ?: '—' }} / {{ $pendaftarAktif->nisn ?: '—' }}</dd>
                        <dt class="col-sm-4">Tempat, tanggal lahir</dt>
                        <dd class="col-sm-8">
                            {{ $pendaftarAktif->tempat_lahir ?: '—' }},
                            {{ $pendaftarAktif->tanggal_lahir?->format('d-m-Y') ?: '—' }}
                        </dd>
                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->alamat ?: '—' }}</dd>
                        <dt class="col-sm-4">Sekolah</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->sekolah ?: '—' }}</dd>
                        <dt class="col-sm-4">WhatsApp</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->wa ?: '—' }}</dd>
                        <dt class="col-sm-4">Ibu / wali</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->ibu ?: '—' }} / {{ $pendaftarAktif->wali ?: '—' }}</dd>
                        <dt class="col-sm-4">WhatsApp wali</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->wa_wali ?: '—' }}</dd>
                        <dt class="col-sm-4">Markas</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->markas?->markas ?: 'Belum ditentukan' }}</dd>
                        <dt class="col-sm-4">Tanggal daftar</dt>
                        <dd class="col-sm-8">{{ $pendaftarAktif->user->created_at?->isoFormat('dddd, D MMMM Y HH:mm') ?: '—' }}</dd>
                    </dl>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-ck" wire:click="bukaMigrasi">Terima</button>
                        <button type="button" class="btn btn-outline-danger rounded-pill" wire:click="hapus({{ $pendaftarAktif->pelajar_id }})" wire:confirm="Hapus pendaftar ini?">Hapus</button>
                    </div>
                </div>
            </div>
        </section>

        @if ($showMigrasi)
            <div class="ck-modal-backdrop">
                <section class="ck-card p-4" style="max-width: 420px; width: 100%;">
                    <h2 class="h5 mb-3">Migrasi ke pelajar</h2>
                    <form wire:submit="migrasi" novalidate>
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Masukkan ke kelas</label>
                            <select id="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" wire:model="kelas_id">
                                <option value="">Pilih kelas</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-ck-ghost" wire:click="tutupMigrasi">Batal</button>
                            <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan</button>
                        </div>
                    </form>
                </section>
            </div>
        @endif
    @else
        <section class="ck-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <h2 class="h5 mb-0">Daftar pendaftar</h2>
                <input
                    type="search"
                    class="form-control"
                    style="max-width: 360px;"
                    placeholder="Cari nama atau email"
                    aria-label="Cari pendaftar"
                    wire:model.live.debounce.400ms="cari"
                >
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Markas</th>
                            <th>Tanggal daftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendaftars as $pendaftar)
                            <tr wire:key="pendaftar-{{ $pendaftar->id }}">
                                <td class="fw-semibold">{{ $pendaftar->nama }}</td>
                                <td>{{ $pendaftar->email }}</td>
                                <td>{{ $pendaftar->pelajar->markas?->markas ?: 'Belum ditentukan' }}</td>
                                <td>{{ $pendaftar->created_at?->isoFormat('D MMM Y HH:mm') ?: '—' }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihat({{ $pendaftar->id }})">Lihat</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="hapus({{ $pendaftar->id }})" wire:confirm="Hapus pendaftar ini?">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center ck-hint py-4">Tidak ada pendaftar ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pendaftars->links() }}
            </div>
        </section>
    @endif
</div>
