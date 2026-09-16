<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Absensi</p>
            <h1 class="h3 mb-1">Histori Absensi</h1>
            <p class="ck-hint mb-0">Daftar kehadiran siswa yang dicentang pada jadwal mapel.</p>
        </div>
        <a href="{{ route('pendidik.absensi.siswa') }}" class="btn btn-ck-ghost">Absensi Siswa</a>
    </div>

    <section class="ck-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select id="bulan" class="form-select" wire:model.live="bulan">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}">
                            {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select id="tahun" class="form-select" wire:model.live="tahun">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-sm-4 col-md-6">
                <label for="cari" class="form-label">Cari nama</label>
                <input
                    id="cari"
                    type="search"
                    class="form-control"
                    placeholder="Cari nama siswa"
                    wire:model.live.debounce.400ms="cari"
                >
            </div>
        </div>
    </section>

    <div class="row g-3">
        @forelse ($slots as $item)
            <div class="col-md-6" wire:key="histori-slot-{{ $item->id }}">
                <section class="ck-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                        {{ $item->mulai->isoFormat('dddd, D MMMM Y') }}
                    </p>
                    <p class="mb-1">Kelas <b>{{ $item->kelas?->nama }}</b></p>
                    <p class="mb-1">Mapel <b>{{ $item->mapel?->mapel }}</b></p>
                    <p class="mb-3 ck-hint">
                        {{ $item->mulai->format('H:i') }} – {{ $item->selesai->format('H:i') }}
                    </p>
                    @if ($item->absensiPendidik->first()?->jurnal)
                        <p class="mb-3">Jurnal<br>"{{ $item->absensiPendidik->first()->jurnal }}"</p>
                    @endif
                    @php $hadirSlot = $hadir->get($item->id, collect()); @endphp
                    @if ($hadirSlot->isEmpty())
                        <p class="ck-hint mb-0">Belum ada siswa dicentang.</p>
                    @else
                        <ul class="mb-0 ps-3">
                            @foreach ($hadirSlot as $row)
                                <li>
                                    {{ $row->pelajar?->nama }}
                                    @if (in_array((int) $row->status, [\App\Support\AbsensiStatus::IZIN, \App\Support\AbsensiStatus::SAKIT, \App\Support\AbsensiStatus::ALPA], true))
                                        — {{ \App\Support\AbsensiStatus::label((int) $row->status) }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        @empty
            <div class="col-12">
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Tidak ada jadwal pada filter ini.</p>
                </section>
            </div>
        @endforelse
    </div>
</div>
