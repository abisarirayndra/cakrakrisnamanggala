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

        <section class="ck-card p-4">
            @forelse ($slots as $item)
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}" wire:key="histori-slot-{{ $item->id }}">
                    <div>
                        <p class="fw-semibold mb-0">{{ $item->mapel?->mapel }}</p>
                        <p class="ck-hint mb-0">
                            {{ $item->kelas?->nama }}
                            · {{ $item->pendidik?->nama }}
                            · {{ $item->mulai->format('d M Y') }}
                            · {{ $item->mulai->format('H:i') }}–{{ $item->selesai->format('H:i') }}
                        </p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="bukaDetail({{ $item->id }})">
                            Detail
                        </button>
                        <a href="{{ route('admin.jadwal.histori.pdf', $item) }}" class="btn btn-sm btn-ck">
                            PDF
                        </a>
                    </div>
                </div>
            @empty
                <p class="ck-hint mb-0">Tidak ada jadwal pada filter ini.</p>
            @endforelse
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

        <div class="row g-4">
            <div class="col-lg-6">
                <section class="ck-card p-4 h-100">
                    <h2 class="h5 mb-4">Datang</h2>
                    <section class="mb-4">
                        <h3 class="h6 mb-3">Pendidik</h3>
                        @forelse ($datangPendidik as $row)
                            <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom">
                                <p class="fw-semibold mb-0">{{ $row->pendidik?->nama }}</p>
                                <div class="text-end">
                                    @php
                                        $statusTampil = \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot->mulai);
                                        $warnaTampil = \App\Support\AbsensiStatus::warnaTampil((int) $row->status, $row->datang, $slot->mulai);
                                    @endphp
                                    <p class="mb-0" @if ($warnaTampil) style="color: {{ $warnaTampil }};" @endif>{{ $statusTampil }}</p>
                                    <p class="ck-hint small mb-0">
                                        {{ $row->datang?->format('H:i') ?: '—' }}
                                        @if ($row->pulang)
                                            · {{ $row->pulang->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="ck-hint mb-0">Belum ada pendidik datang.</p>
                        @endforelse
                    </section>
                    <section>
                        <h3 class="h6 mb-3">Pelajar</h3>
                        @forelse ($datangPelajar as $row)
                            <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom">
                                <p class="fw-semibold mb-0">{{ $row->pelajar?->nama }}</p>
                                <div class="text-end">
                                    @php
                                        $statusTampil = \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot->mulai);
                                        $warnaTampil = \App\Support\AbsensiStatus::warnaTampil((int) $row->status, $row->datang, $slot->mulai);
                                    @endphp
                                    <p class="mb-0" @if ($warnaTampil) style="color: {{ $warnaTampil }};" @endif>{{ $statusTampil }}</p>
                                    <p class="ck-hint small mb-0">
                                        {{ $row->datang?->format('H:i') ?: '—' }}
                                        @if ($row->pulang)
                                            · {{ $row->pulang->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="ck-hint mb-0">Belum ada pelajar datang.</p>
                        @endforelse
                    </section>
                </section>
            </div>
            <div class="col-lg-6">
                <section class="ck-card p-4 h-100">
                    <h2 class="h5 mb-4">Izin / Sakit / Alpa</h2>
                    <section class="mb-4">
                        <h3 class="h6 mb-3">Pendidik</h3>
                        @forelse ($izinPendidik as $row)
                            <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom">
                                <div>
                                    <p class="fw-semibold mb-0">{{ $row->pendidik?->nama }}</p>
                                    @if (filled($row->keterangan))
                                        <p class="ck-hint small mb-0">{{ $row->keterangan }}</p>
                                    @endif
                                </div>
                                <p class="mb-0">{{ \App\Support\AbsensiStatus::label((int) $row->status) }}</p>
                            </div>
                        @empty
                            <p class="ck-hint mb-0">Tidak ada.</p>
                        @endforelse
                    </section>
                    <section>
                        <h3 class="h6 mb-3">Pelajar</h3>
                        @forelse ($izinPelajar as $row)
                            <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom">
                                <div>
                                    <p class="fw-semibold mb-0">{{ $row->pelajar?->nama }}</p>
                                    @if (filled($row->keterangan))
                                        <p class="ck-hint small mb-0">{{ $row->keterangan }}</p>
                                    @endif
                                </div>
                                <p class="mb-0">{{ \App\Support\AbsensiStatus::label((int) $row->status) }}</p>
                            </div>
                        @empty
                            <p class="ck-hint mb-0">Tidak ada.</p>
                        @endforelse
                    </section>
                </section>
            </div>
        </div>
    @endif
</div>
