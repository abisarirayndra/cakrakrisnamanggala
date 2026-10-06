<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Operasional</p>
            <h1 class="h3 mb-1">{{ $slot ? 'Detail Jadwal' : 'Histori Jadwal' }}</h1>
            <p class="ck-hint mb-0">
                @if ($slot)
                    Absensi dan jurnal slot ini untuk laporan ke orang tua.
                @else
                    Pilih kelas dan bulan untuk melihat slot yang sudah berjalan.
                @endif
            </p>
        </div>
        @if ($slot)
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
                <a href="{{ route('admin.jadwal.histori.pdf', $slot) }}" class="btn btn-ck">Unduh PDF</a>
            </div>
        @endif
    </div>

    @if (! $slot)
        <section class="ck-card p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="kelas_id" class="form-label">Kelas</label>
                    <select id="kelas_id" class="form-select" wire:model.live="kelas_id">
                        <option value="">Semua kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">
                                {{ $kelas->nama }}{{ $kelas->markas ? ' — '.$kelas->markas->markas : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select id="bulan" class="form-select" wire:model.live="bulan">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}">
                                {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select id="tahun" class="form-select" wire:model.live="tahun">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </section>

        @include('livewire.partials.loading-toast')

        <section class="ck-card p-4" wire:loading.class="ck-loading-dim" wire:target="kelas_id,bulan,tahun">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 3rem;">No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Mapel</th>
                            <th>Kelas</th>
                            <th>Pendidik</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($slots as $item)
                            <tr wire:key="histori-slot-{{ $item->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-nowrap">{{ $item->mulai->format('d M Y') }}</td>
                                <td class="text-nowrap">{{ $item->mulai->format('H:i') }}–{{ $item->selesai->format('H:i') }}</td>
                                <td class="fw-semibold">{{ $item->mapel?->mapel }}</td>
                                <td>{{ $item->kelas?->nama }}</td>
                                <td>{{ $item->pendidik?->nama }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="bukaDetail({{ $item->id }})" wire:loading.attr="disabled">
                                            Detail
                                        </button>
                                        <a href="{{ route('admin.jadwal.histori.pdf', $item) }}" class="btn btn-sm btn-ck">
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="ck-hint">Tidak ada jadwal pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <section class="ck-card p-4 mb-4">
            <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                {{ $slot->mulai->translatedFormat('l, d F Y') }}
            </p>
            <p class="mb-1">Mapel <b>{{ $slot->mapel?->mapel }}</b></p>
            <p class="mb-1">Kelas <b>{{ $slot->kelas?->nama }}</b></p>
            <p class="mb-1">Pendidik <b>{{ $slot->pendidik?->nama }}</b></p>
            <p class="ck-hint mb-0">{{ $slot->mulai->format('H:i') }}–{{ $slot->selesai->format('H:i') }}</p>
        </section>

        <section class="ck-card p-4 mb-4">
            <h2 class="h5 mb-3">Jurnal</h2>
            @if (filled($jurnal))
                <p class="mb-0">{{ $jurnal }}</p>
            @else
                <p class="ck-hint mb-0">Belum ada jurnal.</p>
            @endif
        </section>

        <section class="ck-card p-4 mb-4">
            <h2 class="h5 mb-4">Datang ({{ count($datangPendidik) + count($datangPelajar) }})</h2>
            <div class="d-flex flex-column gap-4">
                <section>
                    <h3 class="h6 mb-3">Pendidik ({{ count($datangPendidik) }})</h3>
                    @include('livewire.admin.partials.absensi-tabel-datang', ['rows' => $datangPendidik, 'tipe' => 'pendidik', 'aksi' => false])
                </section>
                <section>
                    <h3 class="h6 mb-3">Pelajar ({{ count($datangPelajar) }})</h3>
                    @include('livewire.admin.partials.absensi-tabel-datang', ['rows' => $datangPelajar, 'tipe' => 'pelajar', 'aksi' => false])
                </section>
            </div>
        </section>

        <section class="ck-card p-4">
            <h2 class="h5 mb-4">Izin / Sakit / Alpa ({{ count($izinPendidik) + count($izinPelajar) }})</h2>
            <div class="d-flex flex-column gap-4">
                <section>
                    <h3 class="h6 mb-3">Pendidik ({{ count($izinPendidik) }})</h3>
                    @include('livewire.admin.partials.absensi-tabel-izin', ['rows' => $izinPendidik, 'tipe' => 'pendidik', 'aksi' => false])
                </section>
                <section>
                    <h3 class="h6 mb-3">Pelajar ({{ count($izinPelajar) }})</h3>
                    @include('livewire.admin.partials.absensi-tabel-izin', ['rows' => $izinPelajar, 'tipe' => 'pelajar', 'aksi' => false])
                </section>
            </div>
        </section>
    @endif
</div>
