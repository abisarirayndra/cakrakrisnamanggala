<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Master referensi</p>
            <h1 class="h3 mb-1">Mapel</h1>
            <p class="ck-hint mb-0">Kelola nama mata pelajaran. Hapus hanya jika belum dipakai.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="ck-card p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <h2 class="h5 mb-0">Daftar mapel</h2>
                    <input
                        type="search"
                        class="form-control"
                        style="max-width: 320px;"
                        placeholder="Cari nama mapel"
                        aria-label="Cari mapel"
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
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mapelList as $mapel)
                                <tr wire:key="mapel-{{ $mapel->id }}">
                                    <td class="fw-semibold">{{ $mapel->mapel }}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubah({{ $mapel->id }})">
                                                Ubah
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                wire:click="hapus({{ $mapel->id }})"
                                                wire:confirm="Hapus mapel ini?"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center ck-hint py-4">Tidak ada mapel ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $mapelList->links() }}
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="ck-card p-4">
                <h2 class="h5 mb-4">{{ $editId ? 'Ubah mapel' : 'Tambah mapel' }}</h2>

                <form wire:submit="simpan" novalidate>
                    <div class="mb-4">
                        <label for="nama" class="form-label">Nama</label>
                        <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama">
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">
                            {{ $editId ? 'Simpan perubahan' : 'Tambah mapel' }}
                        </button>
                        @if ($editId)
                            <button type="button" class="btn btn-ck-ghost w-100" wire:click="batal">Batal</button>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
