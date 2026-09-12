<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Operasional</p>
            <h1 class="h3 mb-1">Jadwal</h1>
            <p class="ck-hint mb-0">Pilih kelas dan minggu untuk melihat slot mengajar.</p>
        </div>
    </div>

    <section class="ck-card p-4">
        <div class="row g-3 align-items-end mb-4">
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
                <label for="senin" class="form-label">Senin</label>
                <input id="senin" type="date" class="form-control" wire:model.live="senin">
                <p class="ck-hint mb-0 mt-2">Minggu Senin–Minggu</p>
            </div>
        </div>

        @if ($kelas_id === '')
            <p class="ck-hint mb-0">Pilih kelas</p>
        @else
            <div class="d-flex flex-column gap-3">
                @for ($offset = 0; $offset < 7; $offset++)
                    @php
                        $tanggal = $seninCarbon->copy()->addDays($offset);
                        $hariSlots = $slotsByDay->get($tanggal->toDateString(), collect());
                    @endphp
                    <section class="border rounded-3 p-3" wire:key="hari-{{ $offset }}">
                        <h2 class="h6 mb-3">{{ $hariList[$offset] }}, {{ $tanggal->format('d M Y') }}</h2>
                        @forelse ($hariSlots as $slot)
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2" wire:key="slot-{{ $slot->id }}">
                                <div>
                                    <p class="fw-semibold mb-0">{{ $slot->mapel?->mapel }}</p>
                                    <p class="ck-hint mb-0">{{ $slot->pendidik?->nama }}</p>
                                </div>
                                <p class="mb-0">{{ $slot->mulai->format('H:i') }}–{{ $slot->selesai->format('H:i') }}</p>
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
