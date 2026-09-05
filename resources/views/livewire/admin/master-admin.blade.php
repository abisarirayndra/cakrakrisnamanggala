<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master pengguna</p>
            <h1 class="h3 mb-1">Admin</h1>
            <p class="ck-hint mb-0">Kelola akun admin dan akses markas.</p>
        </div>
    </div>

    @if ($adminDilihat)
        <section class="ck-card p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Detail admin</p>
                    <h2 class="h4 mb-0">{{ $adminDilihat->nama }}</h2>
                </div>
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
            </div>

            <dl class="row ck-meta mb-0">
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $adminDilihat->email }}</dd>

                <dt class="col-sm-3">Flag</dt>
                <dd class="col-sm-9">{{ $adminDilihat->isSuperAdmin() ? 'Superadmin' : 'Admin markas' }}</dd>

                <dt class="col-sm-3">Markas</dt>
                <dd class="col-sm-9">
                    {{ $adminDilihat->markas->pluck('markas')->join(', ') ?: 'Semua markas' }}
                </dd>
            </dl>
        </section>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <section class="ck-card p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <h2 class="h5 mb-0">Daftar admin</h2>
                        <input
                            type="search"
                            class="form-control"
                            style="max-width: 320px;"
                            placeholder="Cari nama atau email"
                            aria-label="Cari admin"
                            wire:model.live.debounce.400ms="cari"
                        >
                    </div>

                    @error('hapus')
                        <div class="alert alert-ck" role="alert">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Flag</th>
                                    <th>Markas</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admins as $admin)
                                    <tr wire:key="admin-{{ $admin->id }}">
                                        <td class="fw-semibold">{{ $admin->nama }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->isSuperAdmin() ? 'Superadmin' : 'Admin' }}</td>
                                        <td>{{ $admin->markas->pluck('markas')->join(', ') ?: '—' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="lihat({{ $admin->id }})">
                                                    Lihat
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-pill"
                                                    wire:click="hapus({{ $admin->id }})"
                                                    wire:confirm="Hapus akun ini?"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center ck-hint py-4">Tidak ada admin ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $admins->links() }}
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="ck-card p-4">
                    <h2 class="h5 mb-4">Tambah admin</h2>

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

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input id="is_super_admin" type="checkbox" class="form-check-input" wire:model.live="is_super_admin">
                            <label for="is_super_admin" class="form-check-label">Superadmin</label>
                        </div>

                        <div class="mb-4">
                            <label for="markas_id" class="form-label">Markas</label>
                            <select
                                id="markas_id"
                                class="form-select @error('markas_id') is-invalid @enderror"
                                wire:model="markas_id"
                                @disabled($is_super_admin)
                            >
                                <option value="">Pilih markas</option>
                                @foreach ($markasList as $markas)
                                    <option value="{{ $markas->id }}">{{ $markas->markas }}</option>
                                @endforeach
                            </select>
                            @error('markas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">
                            Tambah admin
                        </button>
                    </form>
                </section>
            </div>
        </div>
    @endif
</div>
