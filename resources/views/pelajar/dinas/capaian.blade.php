@extends('layouts.panel-pelajar')

@section('title', 'Capaian Tes - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
            <h1 class="h3 mb-1">Capaian Tes</h1>
            <p class="ck-hint mb-0">Histori tes yang sudah dikumpulkan beserta skor dan grafik perkembangan.</p>
        </div>
        <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Jumlah tes</p>
                <p class="h3 mb-0">{{ $jumlahTes }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Skor tertinggi</p>
                <p class="h3 mb-0">{{ $skorTertinggi ?? '—' }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Skor terakhir</p>
                <p class="h3 mb-0">{{ $skorTerakhir ?? '—' }}</p>
            </section>
        </div>
    </div>

    <section class="ck-card p-4 p-md-5 mb-4">
        <h2 class="h5 mb-4">Grafik Capaian Tes</h2>
        @if ($histori->isEmpty())
            <p class="ck-hint mb-0">Grafik muncul setelah ada tes yang dikumpulkan.</p>
        @else
            <div id="grafik-capaian" style="min-height: 320px;"></div>
        @endif
    </section>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Histori Tes</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Paket</th>
                        <th>Tanggal</th>
                        <th>Skor</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histori as $item)
                        <tr>
                            <td>
                                <p class="fw-semibold mb-0">{{ $item['nama'] }}</p>
                                <p class="ck-hint mb-0">{{ $item['waktu'] }}</p>
                            </td>
                            <td>{{ $item['label_tanggal'] }}</td>
                            <td class="fw-semibold">{{ $item['total'] }}</td>
                            <td>
                                <a href="{{ route('pelajar.cat.tes.pdf', $item['id']) }}" class="btn btn-sm btn-ck-ghost">Unduh PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="ck-hint">Belum ada tes yang dikumpulkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@if ($histori->isNotEmpty())
@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    Highcharts.setOptions({
        colors: ['#B8954A'],
        chart: { style: { fontFamily: 'Plus Jakarta Sans, system-ui, sans-serif' } }
    });
    Highcharts.chart('grafik-capaian', {
        chart: { type: 'area', backgroundColor: 'transparent' },
        title: { text: null },
        credits: { enabled: false },
        legend: { enabled: false },
        xAxis: {
            categories: {!! json_encode($grafikKategori) !!},
            crosshair: true,
            labels: { style: { color: '#5E6B7A' } }
        },
        yAxis: {
            min: 0,
            title: { text: 'Skor', style: { color: '#5E6B7A' } },
            labels: { style: { color: '#5E6B7A' } },
            gridLineColor: '#E4DDD2'
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><br>',
            pointFormat: 'Skor: <b>{point.y}</b>'
        },
        plotOptions: {
            area: {
                fillOpacity: 0.18,
                marker: { radius: 4 },
                lineWidth: 2
            }
        },
        series: [{
            name: 'Skor',
            data: {!! json_encode($grafikData) !!}
        }]
    });
</script>
@endpush
@endif
