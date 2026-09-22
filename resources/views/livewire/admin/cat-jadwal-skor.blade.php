<div wire:poll.5s>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Tes</p>
            <h1 class="h3 mb-1">Live skor</h1>
            <p class="ck-hint mb-0">{{ $jadwal->nama }} · {{ $jadwal->banks->pluck('nama')->filter()->join(' · ') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.cat.jadwal.report', $jadwal) }}" class="btn btn-ck-ghost">Report</a>
            <a href="{{ route('admin.cat.jadwal') }}" class="btn btn-ck-ghost">Kembali</a>
        </div>
    </div>

    <section class="ck-card p-4">
        @include('livewire.admin.partials.cat-skor-tabel')
    </section>
</div>
