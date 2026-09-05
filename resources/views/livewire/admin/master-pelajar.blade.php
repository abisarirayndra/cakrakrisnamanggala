<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master pengguna</p>
            <h1 class="h3 mb-1">Pelajar</h1>
            <p class="ck-hint mb-0">Lihat dan perbarui biodata pelajar sesuai akses markas.</p>
        </div>
    </div>

    @if ($halaman === 'edit' && $pelajarAktif)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Edit biodata</p>
                    <h2 class="h4 mb-1">{{ $pelajarAktif->user->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pelajarAktif->user->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <form wire:submit="simpan" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nik" class="form-label">NIK</label>
                        <input id="nik" type="text" class="form-control @error('nik') is-invalid @enderror" wire:model="nik">
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nisn" class="form-label">NISN</label>
                        <input id="nisn" type="text" class="form-control @error('nisn') is-invalid @enderror" wire:model="nisn">
                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tempat_lahir" class="form-label">Tempat lahir</label>
                        <input id="tempat_lahir" type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" wire:model="tempat_lahir">
                        @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                        <input id="tanggal_lahir" type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" wire:model="tanggal_lahir">
                        @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" wire:model="alamat"></textarea>
                        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="sekolah" class="form-label">Sekolah</label>
                        <input id="sekolah" type="text" class="form-control @error('sekolah') is-invalid @enderror" wire:model="sekolah">
                        @error('sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="wa" class="form-label">WhatsApp</label>
                        <input id="wa" type="text" class="form-control @error('wa') is-invalid @enderror" wire:model="wa">
                        @error('wa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="ibu" class="form-label">Nama ibu</label>
                        <input id="ibu" type="text" class="form-control @error('ibu') is-invalid @enderror" wire:model="ibu">
                        @error('ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="wali" class="form-label">Nama wali</label>
                        <input id="wali" type="text" class="form-control @error('wali') is-invalid @enderror" wire:model="wali">
                        @error('wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="wa_wali" class="form-label">WhatsApp wali</label>
                        <input id="wa_wali" type="text" class="form-control @error('wa_wali') is-invalid @enderror" wire:model="wa_wali">
                        @error('wa_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="markas_id" class="form-label">Markas</label>
                        <select id="markas_id" class="form-select @error('markas_id') is-invalid @enderror" wire:model="markas_id">
                            <option value="">Pilih markas</option>
                            @foreach ($markasList as $markas)
                                <option value="{{ $markas->id }}">{{ $markas->markas }}</option>
                            @endforeach
                        </select>
                        @error('markas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-ck-ghost" wire:click="lihat({{ $pelajarAktif->pelajar_id }})">Batal</button>
                    <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan</button>
                </div>
            </form>
        </section>
    @elseif ($halaman === 'lihat' && $pelajarAktif)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Detail pelajar</p>
                    <h2 class="h4 mb-1">{{ $pelajarAktif->user->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pelajarAktif->user->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <dl class="row ck-meta mb-4">
                <dt class="col-sm-3">Nomor registrasi</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->user->nomor_registrasi ?: '—' }}</dd>
                <dt class="col-sm-3">NIK / NISN</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->nik ?: '—' }} / {{ $pelajarAktif->nisn ?: '—' }}</dd>
                <dt class="col-sm-3">Tempat, tanggal lahir</dt>
                <dd class="col-sm-9">
                    {{ $pelajarAktif->tempat_lahir ?: '—' }},
                    {{ $pelajarAktif->tanggal_lahir?->format('d-m-Y') ?: '—' }}
                </dd>
                <dt class="col-sm-3">Alamat</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->alamat ?: '—' }}</dd>
                <dt class="col-sm-3">Sekolah</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->sekolah ?: '—' }}</dd>
                <dt class="col-sm-3">WhatsApp</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->wa ?: '—' }}</dd>
                <dt class="col-sm-3">Ibu / wali</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->ibu ?: '—' }} / {{ $pelajarAktif->wali ?: '—' }}</dd>
                <dt class="col-sm-3">WhatsApp wali</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->wa_wali ?: '—' }}</dd>
                <dt class="col-sm-3">Markas</dt>
                <dd class="col-sm-9">{{ $pelajarAktif->markas?->markas ?: 'Belum ditentukan' }}</dd>
            </dl>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-ck" wire:click="edit({{ $pelajarAktif->pelajar_id }})">Edit biodata</button>
                @if ($isSuperAdmin)
                    <button type="button" class="btn btn-outline-danger rounded-pill" wire:click="suspend({{ $pelajarAktif->pelajar_id }})" wire:confirm="Suspend akun ini?">Suspend</button>
                    <button type="button" class="btn btn-outline-danger rounded-pill" wire:click="hapus({{ $pelajarAktif->pelajar_id }})" wire:confirm="Hapus akun ini?">Hapus</button>
                @endif
            </div>
        </section>
    @else
        <section class="ck-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <h2 class="h5 mb-0">Daftar pelajar</h2>
                <input
                    type="search"
                    class="form-control"
                    style="max-width: 360px;"
                    placeholder="Cari nama, email, atau nomor registrasi"
                    aria-label="Cari pelajar"
                    wire:model.live.debounce.400ms="cari"
                >
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Nomor registrasi</th>
                            <th>Markas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelajars as $pelajar)
                            <tr wire:key="pelajar-{{ $pelajar->id }}">
                                <td class="fw-semibold">{{ $pelajar->nama }}</td>
                                <td>{{ $pelajar->email }}</td>
                                <td>{{ $pelajar->nomor_registrasi ?: '—' }}</td>
                                <td>{{ $pelajar->pelajar->markas?->markas ?: 'Belum ditentukan' }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihat({{ $pelajar->id }})">Lihat</button>
                                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="edit({{ $pelajar->id }})">Edit</button>
                                        @if ($isSuperAdmin)
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="suspend({{ $pelajar->id }})" wire:confirm="Suspend akun ini?">Suspend</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="hapus({{ $pelajar->id }})" wire:confirm="Hapus akun ini?">Hapus</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center ck-hint py-4">Tidak ada pelajar ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pelajars->links() }}
            </div>
        </section>
    @endif
</div>
