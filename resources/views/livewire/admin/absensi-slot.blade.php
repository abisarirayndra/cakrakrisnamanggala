<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Operasional</p>
            <h1 class="h3 mb-1">Absensi</h1>
            <p class="ck-hint mb-0">Pilih kelas dan slot hari ini untuk mencatat kehadiran.</p>
        </div>
        @if ($slot)
            <a href="{{ route('admin.jadwal.histori.pdf', $slot) }}" class="btn btn-ck">
                <i class="bi bi-download me-1"></i>Unduh report
            </a>
        @endif
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

    @include('livewire.partials.loading-toast')

    <div class="row g-4 align-items-start">
        <div class="col-lg-8" wire:loading.class="ck-loading-dim" wire:target="kelas_id,jadwal_id">
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
                    <h2 class="h5 mb-4">Datang ({{ count($datangPendidik) + count($datangPelajar) }})</h2>
                    <div class="d-flex flex-column gap-4">
                        <section>
                            <h3 class="h6 mb-3">Pendidik ({{ count($datangPendidik) }})</h3>
                            @include('livewire.admin.partials.absensi-tabel-datang', ['rows' => $datangPendidik, 'tipe' => 'pendidik'])
                        </section>
                        <section>
                            <h3 class="h6 mb-3">Pelajar ({{ count($datangPelajar) }})</h3>
                            @include('livewire.admin.partials.absensi-tabel-datang', ['rows' => $datangPelajar, 'tipe' => 'pelajar'])
                        </section>
                    </div>
                </section>
                <section id="laporan-izin" class="ck-card p-4">
                    <h2 class="h5 mb-4">Izin / Sakit / Alpa ({{ count($izinPendidik) + count($izinPelajar) }})</h2>
                    <div class="d-flex flex-column gap-4">
                        <section>
                            <h3 class="h6 mb-3">Pendidik ({{ count($izinPendidik) }})</h3>
                            @include('livewire.admin.partials.absensi-tabel-izin', ['rows' => $izinPendidik, 'tipe' => 'pendidik'])
                        </section>
                        <section>
                            <h3 class="h6 mb-3">Pelajar ({{ count($izinPelajar) }})</h3>
                            @include('livewire.admin.partials.absensi-tabel-izin', ['rows' => $izinPelajar, 'tipe' => 'pelajar'])
                        </section>
                    </div>
                </section>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="ck-sticky-card ck-sticky-stack">
            <section class="ck-card p-4 mb-4">
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
                            <span wire:loading wire:target="scan" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                            Simpan
                        </button>
                    </form>
                    @if ($pesan !== '')
                        <div class="alert alert-ck mt-3 mb-0" role="status">{{ $pesan }}</div>
                    @endif
                    <p class="ck-hint mb-0 mt-3">{{ $slot ? 'Siap mencatat absensi.' : 'Pilih slot' }}</p>
                </fieldset>
            </section>
            <section class="ck-card p-4 mb-4" id="input-manual">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="h5 mb-1">Input manual</h2>
                        <p class="ck-hint small mb-0">Dipakai jika scanner bermasalah.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="toggleManual" aria-expanded="{{ $showManual ? 'true' : 'false' }}" aria-controls="form-manual">
                        {{ $showManual ? 'Sembunyikan' : 'Tampilkan' }}
                    </button>
                </div>
                @if ($showManual)
                    <fieldset id="form-manual" class="mt-3" @disabled($slot === null)>
                        <form wire:submit="simpanManual" novalidate>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button
                                    type="button"
                                    class="btn btn-sm {{ $manual_mode === 'datang' ? 'btn-ck' : 'btn-ck-ghost' }}"
                                    wire:click="$set('manual_mode', 'datang')"
                                >
                                    Datang
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm {{ $manual_mode === 'pulang' ? 'btn-ck' : 'btn-ck-ghost' }}"
                                    wire:click="$set('manual_mode', 'pulang')"
                                >
                                    Pulang
                                </button>
                            </div>
                            <div class="mb-3">
                                <label for="manual_user_id" class="form-label">Nama</label>
                                <div class="ck-select2" wire:ignore wire:key="manual-select-{{ $kelas_id }}-{{ $jadwal_id }}">
                                    <select
                                        id="manual_user_id"
                                        class="form-select"
                                        x-data
                                        x-init="ckSelect2Livewire($el, $wire, 'manual_user_id')"
                                        data-placeholder="Cari nama"
                                        data-empty="Tidak ada nama"
                                    >
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
                                </div>
                                @error('manual_user_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <p class="ck-hint small mb-0 mt-1">Jam {{ $manual_mode }} dicatat saat tombol simpan diklik.</p>
                            </div>
                            <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">
                                <span wire:loading wire:target="simpanManual" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                                Simpan {{ $manual_mode }}
                            </button>
                        </form>
                    </fieldset>
                @endif
            </section>
            <section class="ck-card p-4 mb-4" id="input-izin">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <h2 class="h5 mb-0">Izin / Sakit / Alpa</h2>
                    <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="toggleIzin" aria-expanded="{{ $showIzin ? 'true' : 'false' }}" aria-controls="form-izin">
                        {{ $showIzin ? 'Sembunyikan' : 'Tampilkan' }}
                    </button>
                </div>
                @if ($showIzin)
                <fieldset id="form-izin" class="mt-3" @disabled($slot === null)>
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
                            <span wire:loading wire:target="simpanIzin" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                            Simpan
                        </button>
                    </form>
                </fieldset>
                @endif
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
                        <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">
                            <span wire:loading wire:target="simpanJurnalPulang" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    @if ($showEditModal)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="editAbsensiTitle">
            <section class="ck-card p-4" style="max-width: 480px; width: 100%;">
                <h2 class="h5 mb-1" id="editAbsensiTitle">Ubah absensi</h2>
                <p class="ck-hint mb-3">{{ $edit_nama }}</p>
                <form wire:submit="simpanEditAbsensi" novalidate>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select id="edit_status" class="form-select @error('edit_status') is-invalid @enderror" wire:model.live="edit_status">
                            <option value="{{ \App\Support\AbsensiStatus::HADIR }}">Hadir</option>
                            <option value="{{ \App\Support\AbsensiStatus::IZIN }}">Izin</option>
                            <option value="{{ \App\Support\AbsensiStatus::SAKIT }}">Sakit</option>
                            <option value="{{ \App\Support\AbsensiStatus::ALPA }}">Alpa</option>
                        </select>
                        @error('edit_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ((int) $edit_status === \App\Support\AbsensiStatus::HADIR)
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="edit_datang" class="form-label">Jam datang</label>
                                <input type="time" id="edit_datang" class="form-control @error('edit_datang') is-invalid @enderror" wire:model="edit_datang">
                                @error('edit_datang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="edit_pulang" class="form-label">Jam pulang</label>
                                <input type="time" id="edit_pulang" class="form-control @error('edit_pulang') is-invalid @enderror" wire:model="edit_pulang">
                                @error('edit_pulang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <p class="ck-hint small mb-3">Telat atau ontime dihitung dari jam datang.</p>
                    @endif
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <textarea id="edit_keterangan" class="form-control @error('edit_keterangan') is-invalid @enderror" wire:model="edit_keterangan" rows="2"></textarea>
                        @error('edit_keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ($edit_tipe === 'pendidik')
                        <div class="mb-3">
                            <label for="edit_jurnal" class="form-label">Jurnal</label>
                            <textarea id="edit_jurnal" class="form-control @error('edit_jurnal') is-invalid @enderror" wire:model="edit_jurnal" rows="3"></textarea>
                            @error('edit_jurnal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-ck-ghost" wire:click="tutupEdit">Batal</button>
                        <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">
                            <span wire:loading wire:target="simpanEditAbsensi" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>

@assets
@include('livewire.partials.select2-livewire')
@endassets

@script
<script>
    $wire.on('fokus-token', () => {
        requestAnimationFrame(() => document.getElementById('token')?.focus())
    })
</script>
@endscript
