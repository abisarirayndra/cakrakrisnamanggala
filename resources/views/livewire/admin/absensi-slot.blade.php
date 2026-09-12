<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Operasional</p>
            <h1 class="h3 mb-1">Absensi</h1>
            <p class="ck-hint mb-0">Pilih kelas dan slot hari ini untuk mencatat kehadiran.</p>
        </div>
    </div>

    <section class="ck-card p-4 mb-4">
        <p class="ck-hint mb-3">Hari ini</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select id="kelas_id" class="form-select" wire:model.live="kelas_id">
                    <option value="">Pilih kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">
                            {{ $kelas->nama }}{{ $kelas->markas ? ' — '.$kelas->markas->markas : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="jadwal_id" class="form-label">Slot</label>
                <select id="jadwal_id" class="form-select" wire:model.live="jadwal_id" @disabled($kelas_id === '')>
                    @if ($kelas_id === '')
                        <option value="">Pilih kelas</option>
                    @elseif ($slots->isEmpty())
                        <option value="">Tidak ada mapel hari ini</option>
                    @else
                        <option value="">Pilih slot</option>
                        @foreach ($slots as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->mapel?->mapel }} · {{ $item->mulai->format('H:i') }}–{{ $item->selesai->format('H:i') }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="ck-card p-4">
                @if ($kelas_id === '')
                    <p class="ck-hint mb-0">Pilih kelas</p>
                @elseif ($slots->isEmpty())
                    <p class="ck-hint mb-0">Tidak ada mapel hari ini</p>
                @else
                    <div class="d-flex flex-column gap-4">
                        <section>
                            <h2 class="h6 mb-3">Pendidik</h2>
                            <p class="ck-hint mb-0">Belum ada absensi</p>
                        </section>
                        <section>
                            <h2 class="h6 mb-3">Pelajar</h2>
                            <p class="ck-hint mb-0">Belum ada absensi</p>
                        </section>
                    </div>
                @endif
            </section>
        </div>
        <div class="col-lg-4">
            <section class="ck-card ck-sticky-card p-4">
                <h2 class="h5 mb-4">Scan</h2>
                <fieldset @disabled($slot === null)>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button
                            type="button"
                            class="btn btn-sm {{ $mode === 'datang' ? 'btn-ck' : 'btn-ck-ghost' }}"
                            wire:click="$set('mode', 'datang')"
                        >
                            Datang
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm {{ $mode === 'pulang' ? 'btn-ck' : 'btn-ck-ghost' }}"
                            wire:click="$set('mode', 'pulang')"
                        >
                            Pulang
                        </button>
                    </div>
                    <form wire:submit="scan">
                        <label for="token" class="form-label">Nomor registrasi</label>
                        <input
                            id="token"
                            class="form-control @error('token') is-invalid @enderror"
                            wire:model="token"
                            autofocus
                            autocomplete="off"
                        >
                        @error('token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($mode === 'pulang')
                            <label for="jurnal" class="form-label mt-3">Jurnal</label>
                            <textarea
                                id="jurnal"
                                class="form-control @error('jurnal') is-invalid @enderror"
                                wire:model="jurnal"
                                rows="3"
                            ></textarea>
                            @error('jurnal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="ck-hint mb-0 mt-2">Hanya dipakai saat scan guru utama</p>
                        @endif
                    </form>
                    @if ($pesan !== '')
                        <p class="ck-hint mt-3 mb-0">{{ $pesan }}</p>
                    @endif
                    <p class="ck-hint mb-0 mt-3">{{ $slot ? 'Siap mencatat absensi.' : 'Pilih slot' }}</p>
                </fieldset>
            </section>
        </div>
    </div>
</div>
