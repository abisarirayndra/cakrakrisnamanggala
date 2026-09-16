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
            @if ($kelas_id === '')
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Pilih kelas</p>
                </section>
            @elseif ($slots->isEmpty())
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Tidak ada mapel hari ini</p>
                </section>
            @else
                <section class="ck-card p-4 mb-4">
                    <h2 class="h5 mb-4">Datang</h2>
                    <div class="d-flex flex-column gap-4">
                        <section>
                            <h3 class="h6 mb-3">Pendidik</h3>
                            @forelse ($datangPendidik as $row)
                                <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom" wire:key="absensi-pendidik-{{ $row->pendidik_id }}">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $row->pendidik?->nama }}</p>
                                        @if ($slot && (int) $slot->pendidik_id === (int) $row->pendidik_id)
                                            <p class="ck-hint small mb-0">Guru utama</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $statusTampil = \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot?->mulai);
                                            $warnaTampil = \App\Support\AbsensiStatus::warnaTampil((int) $row->status, $row->datang, $slot?->mulai);
                                        @endphp
                                        <p class="mb-0" @if ($warnaTampil) style="color: {{ $warnaTampil }};" @endif>
                                            {{ $statusTampil }}
                                        </p>
                                        <p class="ck-hint small mb-0">
                                            {{ $row->datang?->format('H:i') ?: '—' }}
                                            @if ($row->pulang)
                                                · {{ $row->pulang->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="ck-hint mb-0">Belum ada yang datang</p>
                            @endforelse
                        </section>
                        <section>
                            <h3 class="h6 mb-3">Pelajar</h3>
                            @forelse ($datangPelajar as $row)
                                <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom" wire:key="absensi-pelajar-datang-{{ $row->pelajar_id }}">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $row->pelajar?->nama }}</p>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $statusTampil = \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot?->mulai);
                                            $warnaTampil = \App\Support\AbsensiStatus::warnaTampil((int) $row->status, $row->datang, $slot?->mulai);
                                        @endphp
                                        <p class="mb-0" @if ($warnaTampil) style="color: {{ $warnaTampil }};" @endif>
                                            {{ $statusTampil }}
                                        </p>
                                        <p class="ck-hint small mb-0">
                                            {{ $row->datang?->format('H:i') ?: '—' }}
                                            @if ($row->pulang)
                                                · {{ $row->pulang->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="ck-hint mb-0">Belum ada yang datang</p>
                            @endforelse
                        </section>
                    </div>
                </section>
                <section id="laporan-izin" class="ck-card p-4">
                    <h2 class="h5 mb-4">Izin / Sakit / Alpa</h2>
                    <div class="d-flex flex-column gap-4">
                        <section>
                            <h3 class="h6 mb-3">Pendidik</h3>
                            @forelse ($izinPendidik as $row)
                                <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom" wire:key="absensi-pendidik-izin-{{ $row->pendidik_id }}">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $row->pendidik?->nama }}</p>
                                        @if ($row->keterangan)
                                            <p class="ck-hint small mb-0">{{ $row->keterangan }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0">{{ \App\Support\AbsensiStatus::tampilkan((int) $row->status) }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="ck-hint mb-0">Belum ada izin, sakit, atau alpa</p>
                            @endforelse
                        </section>
                        <section>
                            <h3 class="h6 mb-3">Pelajar</h3>
                            @forelse ($izinPelajar as $row)
                                <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom" wire:key="absensi-pelajar-{{ $row->pelajar_id }}">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $row->pelajar?->nama }}</p>
                                        @if ($row->keterangan)
                                            <p class="ck-hint small mb-0">{{ $row->keterangan }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0">{{ \App\Support\AbsensiStatus::tampilkan((int) $row->status) }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="ck-hint mb-0">Belum ada izin, sakit, atau alpa</p>
                            @endforelse
                        </section>
                    </div>
                </section>
            @endif
        </div>
        <div class="col-lg-4">
            <section class="ck-card ck-sticky-card p-4 mb-4">
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
                            x-ref="token"
                            class="form-control @error('token') is-invalid @enderror"
                            wire:model="token"
                            autofocus
                            autocomplete="off"
                        >
                        @error('token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-ck w-100 mt-3" wire:loading.attr="disabled">
                            Simpan
                        </button>
                    </form>
                    @if ($pesan !== '')
                        <div class="alert alert-ck mt-3 mb-0" role="status">{{ $pesan }}</div>
                    @endif
                    <p class="ck-hint mb-0 mt-3">{{ $slot ? 'Siap mencatat absensi.' : 'Pilih slot' }}</p>
                </fieldset>
            </section>
            <section class="ck-card p-4 mb-4">
                <h2 class="h5 mb-3">Izin / Sakit / Alpa</h2>
                <fieldset @disabled($slot === null)>
                    <form wire:submit="simpanIzin" novalidate>
                        <div class="mb-3">
                            <label for="izin_user_id" class="form-label">Nama</label>
                            <select id="izin_user_id" class="form-select @error('izin_user_id') is-invalid @enderror" wire:model="izin_user_id">
                                <option value="">Pilih nama</option>
                                @if ($pelajarList->isNotEmpty())
                                    <optgroup label="Pelajar">
                                        @foreach ($pelajarList as $orang)
                                            <option value="{{ $orang->id }}">{{ $orang->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if ($pendidikList->isNotEmpty())
                                    <optgroup label="Pendidik">
                                        @foreach ($pendidikList as $orang)
                                            <option value="{{ $orang->id }}">{{ $orang->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
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
                </fieldset>
            </section>
            @if ($slot && $adaSisaAlpa && ! $lewatiAlpa)
                <section class="ck-card p-4">
                    <p class="mb-3">Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa?</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-ck" wire:click="tandaiSisaAlpa" wire:loading.attr="disabled">Ya</button>
                        <button type="button" class="btn btn-ck-ghost" wire:click="lewatiSisaAlpa" wire:loading.attr="disabled">Tidak</button>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @if ($showJurnalModal)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="jurnalModalTitle">
            <section class="ck-card p-4" style="max-width: 480px; width: 100%;">
                <h2 class="h5 mb-1" id="jurnalModalTitle">Jurnal pulang</h2>
                <p class="ck-hint mb-3">{{ $jurnalNama }} — jurnal wajib diisi sebelum pulang tercatat.</p>
                <form wire:submit="simpanJurnalPulang" novalidate>
                    <label for="jurnal" class="form-label">Jurnal</label>
                    <textarea
                        id="jurnal"
                        class="form-control @error('jurnal') is-invalid @enderror"
                        wire:model="jurnal"
                        rows="4"
                        autofocus
                    ></textarea>
                    @error('jurnal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-ck-ghost" wire:click="tutupJurnal">Batal</button>
                        <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('fokus-token', () => {
        requestAnimationFrame(() => document.getElementById('token')?.focus())
    })
</script>
@endscript
