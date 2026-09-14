<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Operasional</p>
            <h1 class="h3 mb-1">Jadwal</h1>
            <p class="ck-hint mb-0">Pilih kelas dan minggu untuk melihat slot mengajar.</p>
        </div>
    </div>

    <section class="ck-card p-4 mb-4">
        <p class="ck-hint mb-3">Minggu Senin–Minggu</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select id="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" wire:model.live="kelas_id">
                    <option value="">Pilih kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">
                            {{ $kelas->nama }}{{ $kelas->markas ? ' — '.$kelas->markas->markas : '' }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label for="senin" class="form-label">Senin</label>
                <input id="senin" type="date" class="form-control" wire:model.live="senin">
            </div>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="ck-card p-4">
                @error('jadwal')
                    <div class="alert alert-ck" role="alert">{{ $message }}</div>
                @enderror
                @if ($kelas_id === '')
                    <p class="ck-hint mb-0">Pilih kelas</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @for ($offset = 0; $offset < 7; $offset++)
                            @php
                                $tanggal = $seninCarbon->copy()->addDays($offset);
                                $hariSlots = $slotsByDay->get($tanggal->toDateString(), collect());
                            @endphp
                            <section class="ck-day-block border rounded-3 p-3" wire:key="hari-{{ $offset }}">
                                <h2 class="h6 mb-3">{{ $hariList[$offset] }}, {{ $tanggal->format('d M Y') }}</h2>
                                @forelse ($hariSlots as $slot)
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2" wire:key="slot-{{ $slot->id }}">
                                        <div>
                                            <p class="fw-semibold mb-0">{{ $slot->mapel?->mapel }}</p>
                                            <p class="ck-hint mb-0">{{ $slot->pendidik?->nama }}</p>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <p class="mb-0">{{ $slot->mulai->format('H:i') }}–{{ $slot->selesai->format('H:i') }}</p>
                                            <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubah({{ $slot->id }})">
                                                Ubah
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                wire:click="hapus({{ $slot->id }})"
                                                wire:confirm="Hapus slot ini?"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="ck-hint mb-0">Tidak ada slot</p>
                                @endforelse
                            </section>
                        @endfor
                    </div>
                @endif
            </section>
        </div>
        <div class="col-lg-4">
            <section class="ck-card ck-sticky-card p-4">
                <h2 class="h5 mb-4">{{ $editId ? 'Ubah slot' : 'Tambah slot' }}</h2>
                <form wire:submit="simpan" novalidate>
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari</label>
                        <select id="hari" class="form-select @error('hari') is-invalid @enderror" wire:model="hari" @disabled($kelas_id === '')>
                            @foreach ($hariList as $offset => $namaHari)
                                <option value="{{ $offset }}">{{ $namaHari }}</option>
                            @endforeach
                        </select>
                        @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="mapel_id" class="form-label">Mapel</label>
                        <select id="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror" wire:model="mapel_id" @disabled($kelas_id === '')>
                            <option value="">Pilih mapel</option>
                            @foreach ($mapelList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->mapel }}</option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pendidik_id" class="form-label">Pendidik</label>
                        <select id="pendidik_id" class="form-select @error('pendidik_id') is-invalid @enderror" wire:model="pendidik_id" @disabled($kelas_id === '')>
                            <option value="">Pilih pendidik</option>
                            @foreach ($pendidikList as $pendidik)
                                <option value="{{ $pendidik->id }}">{{ $pendidik->nama }}</option>
                            @endforeach
                        </select>
                        @error('pendidik_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label for="jam_mulai" class="form-label">Jam mulai</label>
                            <input id="jam_mulai" type="time" class="form-control @error('jam_mulai') is-invalid @enderror" wire:model="jam_mulai" @disabled($kelas_id === '')>
                            @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label for="jam_selesai" class="form-label">Jam selesai</label>
                            <input id="jam_selesai" type="time" class="form-control @error('jam_selesai') is-invalid @enderror" wire:model="jam_selesai" @disabled($kelas_id === '')>
                            @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled" @disabled($kelas_id === '')>
                            {{ $editId ? 'Simpan perubahan' : 'Tambah slot' }}
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
