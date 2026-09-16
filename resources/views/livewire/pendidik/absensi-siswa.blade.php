<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Absensi</p>
            <h1 class="h3 mb-1">Absensi Siswa</h1>
            <p class="ck-hint mb-0">Klik nama siswa untuk mencatat hadir. Jam datang dan pulang mengikuti jadwal.</p>
        </div>
    </div>

    <section class="ck-card p-4 mb-4">
        <label for="jadwal_id" class="form-label">Jadwal hari ini</label>
        <select id="jadwal_id" class="form-select" wire:model.live="jadwal_id">
            @if ($slots->isEmpty())
                <option value="">Tidak ada mapel hari ini</option>
            @else
                @foreach ($slots as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->kelas?->nama }} · {{ $item->mapel?->mapel }} · {{ $item->mulai->format('H:i') }}–{{ $item->selesai->format('H:i') }}
                    </option>
                @endforeach
            @endif
        </select>
    </section>

    @if ($slot)
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <section class="ck-card p-4 h-100">
                    <form wire:submit="simpanJurnal">
                        <label for="jurnal" class="form-label">Jurnal</label>
                        <textarea
                            id="jurnal"
                            class="form-control @error('jurnal') is-invalid @enderror"
                            rows="3"
                            wire:model="jurnal"
                            placeholder="Catatan pembelajaran"
                        ></textarea>
                        @error('jurnal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan jurnal</button>
                        </div>
                    </form>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="ck-card p-4 h-100">
                    <h2 class="h5 mb-3">Izin / Sakit</h2>
                    <form wire:submit="simpanIzin" novalidate>
                        <div class="mb-3">
                            <label for="izin_user_id" class="form-label">Nama</label>
                            <select id="izin_user_id" class="form-select @error('izin_user_id') is-invalid @enderror" wire:model="izin_user_id">
                                <option value="">Pilih nama</option>
                                @foreach ($pelajarKelas as $orang)
                                    <option value="{{ $orang->id }}">{{ $orang->nama }}</option>
                                @endforeach
                            </select>
                            @error('izin_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="izin_status" class="form-label">Status</label>
                            <select id="izin_status" class="form-select @error('izin_status') is-invalid @enderror" wire:model="izin_status">
                                <option value="{{ \App\Support\AbsensiStatus::IZIN }}">Izin</option>
                                <option value="{{ \App\Support\AbsensiStatus::SAKIT }}">Sakit</option>
                                <option value="{{ \App\Support\AbsensiStatus::ALPA }}">Alpa</option>
                            </select>
                            @error('izin_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="izin_keterangan" class="form-label">Keterangan</label>
                            <textarea
                                id="izin_keterangan"
                                class="form-control @error('izin_keterangan') is-invalid @enderror"
                                wire:model="izin_keterangan"
                                rows="3"
                            ></textarea>
                            @error('izin_keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">
                            Simpan
                        </button>
                    </form>
                </section>
            </div>
        </div>
    @endif

    <section class="ck-card p-4">
        @if (! $slot)
            <p class="ck-hint mb-0">Pilih jadwal mengajar hari ini.</p>
        @else
            <input
                type="search"
                class="form-control mb-3"
                placeholder="Cari nama"
                aria-label="Cari nama"
                wire:model.live.debounce.400ms="cari"
            >
            @if ($pelajarList->isEmpty())
                <p class="ck-hint mb-0">{{ $cari !== '' ? 'Tidak ada nama yang cocok.' : 'Belum ada pelajar di kelas ini.' }}</p>
            @else
                <div class="d-flex flex-column">
                    @foreach ($pelajarList as $siswa)
                        @php
                            $absensi = $absensiMap->get((int) $siswa->id);
                            $hadir = $absensi?->datang !== null;
                        @endphp
                        <div
                            role="checkbox"
                            aria-checked="{{ $hadir ? 'true' : 'false' }}"
                            tabindex="0"
                            class="d-flex align-items-center gap-3 py-2 border-bottom"
                            style="cursor: pointer"
                            wire:key="absensi-siswa-{{ $siswa->id }}-{{ $hadir ? 'hadir' : 'kosong' }}"
                            wire:click="toggle({{ $siswa->id }})"
                            wire:keydown.enter="toggle({{ $siswa->id }})"
                            wire:keydown.space.prevent="toggle({{ $siswa->id }})"
                        >
                            <input
                                type="checkbox"
                                class="form-check-input m-0 pe-none"
                                tabindex="-1"
                                @checked($hadir)
                            >
                            <span class="fw-semibold">{{ $siswa->nama }}</span>
                            @if ($absensi && ! $hadir)
                                <span class="ck-hint small ms-auto">{{ \App\Support\AbsensiStatus::label((int) $absensi->status) }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </section>

    @if ($slot && $adaSisaAlpa && ! $lewatiAlpa)
        <section class="ck-card p-4 mt-4">
            <p class="mb-3">Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa?</p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-ck" wire:click="tandaiSisaAlpa" wire:loading.attr="disabled">Ya</button>
                <button type="button" class="btn btn-ck-ghost" wire:click="lewatiSisaAlpa" wire:loading.attr="disabled">Tidak</button>
            </div>
        </section>
    @endif
</div>
