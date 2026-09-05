<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master pengguna</p>
            <h1 class="h3 mb-1">Pendidik</h1>
            <p class="ck-hint mb-0">Kelola akun pendidik sesuai akses markas.</p>
        </div>
    </div>

    @if ($pendidikDilihat)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Detail pendidik</p>
                    <h2 class="h4 mb-1">{{ $pendidikDilihat->nama }}</h2>
                    <p class="ck-hint mb-0">{{ $pendidikDilihat->email }}</p>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <dl class="row ck-meta mb-4">
                <dt class="col-sm-3">Markas</dt>
                <dd class="col-sm-9">{{ $pendidikDilihat->pendidik->markas?->markas ?: 'Belum ditentukan' }}</dd>
            </dl>

            <button
                type="button"
                class="btn btn-outline-danger rounded-pill"
                wire:click="hapus({{ $pendidikDilihat->id }})"
                wire:confirm="Hapus akun ini?"
            >
                Hapus
            </button>
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
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendidiks as $pendidik)
                                    <tr wire:key="pendidik-{{ $pendidik->id }}">
                                        <td class="fw-semibold">{{ $pendidik->nama }}</td>
                                        <td>{{ $pendidik->email }}</td>
                                        <td>{{ $pendidik->pendidik->markas?->markas ?: 'Belum ditentukan' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihat({{ $pendidik->id }})">
                                                    Lihat
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
                                        <td colspan="4" class="text-center ck-hint py-4">Tidak ada pendidik ditemukan.</td>
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
                            <label for="nama" class="form-label">Nama</label>
                            <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="markas_id" class="form-label">Markas</label>
                            <select id="markas_id" class="form-select @error('markas_id') is-invalid @enderror" wire:model="markas_id">
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
