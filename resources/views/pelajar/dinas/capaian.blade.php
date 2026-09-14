@extends('layouts.panel-pelajar')

@section('title', 'Capaian Tes - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
            <h1 class="h3 mb-1">Capaian Tes</h1>
            <p class="ck-hint mb-0">Nilai tertinggi dan grafik perkembangan Anda.</p>
        </div>
        <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">SKD tertinggi</p>
                <p class="h3 mb-0">{{ $skd ?: '—' }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Tes Akademik Tertinggi</p>
                <p class="h3 mb-0">{{ $akademik ?: '—' }}</p>
            </section>
        </div>
        <div class="col-md-4">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Psikotes Tertinggi</p>
                <p class="h3 mb-0">{{ $psikotes ?: '—' }}</p>
            </section>
        </div>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-4">Grafik Capaian Tes</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div id="skd"></div>
            </div>
            <div class="col-lg-6">
                <div id="akademik"></div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    Highcharts.setOptions({
        colors: ['#B8954A']
    });
    Highcharts.chart('skd', {
        chart: { type: 'area', backgroundColor: 'transparent' },
        title: { text: 'Grafik Pencapaian SKD' },
        xAxis: {
            categories: {!! json_encode($skd_categories) !!},
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: { text: 'Nilai SKD' }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        series: [{
            name: 'Nilai ',
            data: {!! json_encode($skd_data) !!},
        }]
    });
    Highcharts.chart('akademik', {
        chart: { type: 'area', backgroundColor: 'transparent' },
        title: { text: 'Grafik Pencapaian Akademik' },
        xAxis: {
            categories: {!! json_encode($akademik_categories) !!},
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: { text: 'Nilai Akademik' }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        series: [{
            name: 'Nilai ',
            data: {!! json_encode($akademik_data) !!},
        }]
    });
</script>
@endpush