@extends('master.pelajar')

@section('title')
    <title>Capaian Tes - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                SKD tertinggi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $skd }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Annual) Card Example -->
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Tes Akademik Tertinggi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $akademik }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Card Example -->
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Psikotes Tertinggi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $psikotes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="float-right">
                <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
            </div>
            <h5><i class="fas fa-hashtag text-warning"></i> Grafik Capaian Tes</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-6 col-md-6 text-center">
                        <div id="skd"></div>
                    </div>
                    <div class="col-xl-6 col-md-6 text-center">
                        <div id="akademik"></div>

                    </div>
                    <div class="col-xl-6 col-md-3 text-center">
                        <div id="psikotes"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
  Highcharts.setOptions({
                    colors: ['#fb6340']
                });
  Highcharts.chart('skd', {
    chart: {
        type: 'area'
    },
    title: {
        text: 'Grafik Pencapaian SKD'
    },
    xAxis: {
        categories: {!!json_encode($skd_categories)!!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Nilai SKD'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Nilai ',
        data: {!!json_encode($skd_data)!!},
        // data: [1,2],
    }]
});
</script>
<script>
    Highcharts.setOptions({
                      colors: ['#fb6340']
                  });
    Highcharts.chart('akademik', {
      chart: {
          type: 'area'
      },
      title: {
          text: 'Grafik Pencapaian Akademik'
      },
      xAxis: {
          categories: {!!json_encode($akademik_categories)!!},
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Nilai Akademik'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0
          }
      },
      series: [{
          name: 'Nilai ',
          data: {!!json_encode($akademik_data)!!},
          // data: [1,2],
      }]
  });
</script>
{{-- <script>
    Highcharts.setOptions({
                      colors: ['#fb6340']
                  });
    Highcharts.chart('psikotes', {
      chart: {
          type: 'area'
      },
      title: {
          text: 'Grafik Pencapaian Psikotes'
      },
      xAxis: {
          categories: {!!json_encode($psikotes_categories)!!},
          crosshair: true
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Nilai Psikotes'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0
          }
      },
      series: [{
          name: 'Nilai ',
          data: {!!json_encode($psikotes_data)!!},
          // data: [1,2],
      }]
  });
</script> --}}
@endsection
