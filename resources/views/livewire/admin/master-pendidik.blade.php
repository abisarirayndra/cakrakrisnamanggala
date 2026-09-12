<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master pengguna</p>
            <h1 class="h3 mb-1">Pendidik</h1>
            <p class="ck-hint mb-0">Lihat dan perbarui biodata pendidik sesuai akses markas.</p>
        </div>
    </div>

    @if ($halaman === 'edit' && $pendidikAktif)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Edit biodata</p>
                    <h2 class="h4 mb-1">{{ $pendidikAktif->user->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pendidikAktif->user->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <form wire:submit="simpan" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama</label>
                        <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama">
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nik" class="form-label">NIK</label>
                        <input id="nik" type="text" class="form-control @error('nik') is-invalid @enderror" wire:model="nik">
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nip" class="form-label">NIP</label>
                        <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" wire:model="nip">
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <label for="wa" class="form-label">WhatsApp</label>
                        <input id="wa" type="text" class="form-control @error('wa') is-invalid @enderror" wire:model="wa">
                        @error('wa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="ibu" class="form-label">Nama ibu</label>
                        <input id="ibu" type="text" class="form-control @error('ibu') is-invalid @enderror" wire:model="ibu">
                        @error('ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <div class="col-md-6">
                        <label for="mapel_id" class="form-label">Mapel</label>
                        <select id="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror" wire:model="mapel_id">
                            <option value="">Pilih mapel</option>
                            @foreach ($mapelList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->mapel }}</option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-ck-ghost" wire:click="lihat({{ $pendidikAktif->pendidik_id }})">Batal</button>
                    <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan</button>
                </div>
            </form>
        </section>
    @elseif ($halaman === 'lihat' && $pendidikAktif)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Detail pendidik</p>
                    <h2 class="h4 mb-1">{{ $pendidikAktif->user->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pendidikAktif->user->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <div class="row g-4">
                <div class="col-md-3 text-center">
                    @if ($pendidikAktif->foto)
                        <img src="{{ asset('pendidik/img/'.$pendidikAktif->foto) }}" alt="Foto pendidik" class="rounded" width="140">
                    @else
                        <div class="ck-hint">Belum ada foto</div>
                    @endif
                </div>
                <div class="col-md-9">
                    <dl class="row ck-meta mb-4">
                        <dt class="col-sm-4">NIK</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->nik ?: '—' }}</dd>
                        <dt class="col-sm-4">NIP</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->nip ?: '—' }}</dd>
                        <dt class="col-sm-4">Tempat, tanggal lahir</dt>
                        <dd class="col-sm-8">
                            {{ $pendidikAktif->tempat_lahir ?: '—' }},
                            {{ $pendidikAktif->tanggal_lahir?->format('d-m-Y') ?: '—' }}
                        </dd>
                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->alamat ?: '—' }}</dd>
                        <dt class="col-sm-4">WhatsApp</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->wa ?: '—' }}</dd>
                        <dt class="col-sm-4">Nama ibu</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->ibu ?: '—' }}</dd>
                        <dt class="col-sm-4">Markas</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->namaMarkas() ?: 'Belum ditentukan' }}</dd>
                        <dt class="col-sm-4">Mapel</dt>
                        <dd class="col-sm-8">{{ $pendidikAktif->mapel?->mapel ?: '—' }}</dd>
                    </dl>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-ck" wire:click="edit({{ $pendidikAktif->pendidik_id }})">Edit biodata</button>
                        <button
                            type="button"
                            class="btn btn-outline-danger rounded-pill"
                            wire:click="hapus({{ $pendidikAktif->pendidik_id }})"
                            wire:confirm="Hapus akun ini?"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <section class="ck-card p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <h2 class="h5 mb-0">Daftar pendidik</h2>
                        <input
                            type="search"
                            class="form-control"
                            style="max-width: 320px;"
                            placeholder="Cari nama atau email"
                            aria-label="Cari pendidik"
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
                                    <th>Mapel</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendidiks as $pendidik)
                                    <tr wire:key="pendidik-{{ $pendidik->id }}">
                                        <td class="fw-semibold">{{ $pendidik->nama }}</td>
                                        <td>{{ $pendidik->email }}</td>
                                        <td>{{ $pendidik->pendidik?->namaMarkas() ?: 'Belum ditentukan' }}</td>
                                        <td>{{ $pendidik->pendidik?->mapel?->mapel ?: '—' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihat({{ $pendidik->id }})">
                                                    Lihat
                                                </button>
                                                <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="edit({{ $pendidik->id }})">
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-pill"
                                                    wire:click="hapus({{ $pendidik->id }})"
                                                    wire:confirm="Hapus akun ini?"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center ck-hint py-4">Tidak ada pendidik ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pendidiks->links() }}
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="ck-card p-4">
                    <h2 class="h5 mb-2">Tambah pendidik</h2>
                    <p class="ck-hint small mb-4">Password awal: {{ \App\Pendidik::DEFAULT_PASSWORD }}</p>

                    <form wire:submit="tambah" novalidate>
                        <div class="mb-3">
                            <label for="nama_baru" class="form-label">Nama</label>
                            <input id="nama_baru" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email_baru" class="form-label">Email</label>
                            <input id="email_baru" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="markas_baru" class="form-label">Markas</label>
                            <select id="markas_baru" class="form-select @error('markas_id') is-invalid @enderror" wire:model="markas_id">
                                <option value="">Pilih markas</option>
                                @foreach ($markasList as $markas)
                                    <option value="{{ $markas->id }}">{{ $markas->markas }}</option>
                                @endforeach
                            </select>
                            @error('markas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">
                            Tambah pendidik
                        </button>
                    </form>
                </section>
            </div>
        </div>
    @endif
</div>
