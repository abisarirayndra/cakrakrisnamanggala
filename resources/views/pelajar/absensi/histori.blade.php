@extends('layouts.panel-pelajar')

@section('title', 'Histori Pembelajaran - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Kehadiran</p>
            <h1 class="h3 mb-1">Histori Pembelajaran</h1>
            <p class="ck-hint mb-0">Lihat catatan kehadiran yang sudah selesai.</p>
        </div>
        <a href="{{ route('pelajar.absensi') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 mb-4">
        <form action="{{ route('pelajar.absensi.histori-pembelajaran') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select id="bulan" name="bulan" class="form-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" @selected((int) $bulan === $m)>
                            {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select id="tahun" name="tahun" class="form-select">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected((int) $tahun === $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <button class="btn btn-ck" type="submit">Filter</button>
            </div>
        </form>
    </section>

    <div class="row g-3">
        @forelse ($jadwal as $item)
            <div class="col-md-6 col-xl-3">
                <section class="ck-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
                        {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y') }}
                    </p>
                    @if ($item->status == 0)
                        <p class="mb-2" style="color: var(--ck-danger);">Terlambat</p>
                    @elseif ($item->status == 1)
                        <p class="mb-2" style="color: var(--ck-success);">Ontime</p>
                    @endif
                    <p class="mb-1">Kelas <b>{{ $item->kelas }}</b></p>
                    <p class="mb-1">Mapel <b>{{ $item->mapel }}</b></p>
                    <p class="mb-1">
                        Jadwal
                        <b>
                            {{ \Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm') }} –
                            {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}
                        </b>
                    </p>
                    <p class="mb-1">Datang <b>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</b></p>
                    <p class="mb-0">Pulang <b>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</b></p>
                </section>
            </div>
        @empty
            <div class="col-12">
                <section class="ck-card p-4">
                    <p class="ck-hint mb-0">Tidak ada histori pada bulan ini.</p>
                </section>
            </div>
        @endforelse
    </div>
@endsection
