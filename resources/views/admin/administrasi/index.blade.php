@extends('master.admin')

@section('title')
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <style>
        td{
            font-size: 80%
        }
        .highcharts-figure,
        .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
        }

        #container {
        height: 400px;
        }

        .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
        }

        .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
        }

        .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
        padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
        background: #f1f7ff;
        }

            </style>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Diagram Penyebaran Pengguna</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-6 text-center">
                        <figure class="highcharts-figure">
                            <div id="pelajar"></div>
                        </figure>
                    </div>
                    <div class="col-xl-6 text-center">
                        <figure class="highcharts-figure">
                            <div id="pendidik"></div>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Pilih Opsi Pengguna</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.penggunapendaftar') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Pendaftar</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.penggunapelajar') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Pelajar</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.penggunapendidik') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Pendidik</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.penggunastafadmin') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Staf Admin</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.penggunasuspend') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Suspended</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    Highcharts.chart('pelajar', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Penyebaran Pelajar'
  },
  subtitle: {
    text: 'Jumlah Pelajar Tiap Markas'
  },
  xAxis: {
    categories: {!!json_encode($categories)!!},
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Jumlah'
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
    name: 'Jumlah',
    data: {!! json_encode($jumlah_pelajar) !!}

  }]
});
</script>
<script>
    Highcharts.chart('pendidik', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Penyebaran Pendidik'
  },
  subtitle: {
    text: 'Jumlah Pendidik Tiap Markas'
  },
  xAxis: {
    categories: {!!json_encode($categories)!!},
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Jumlah'
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
    name: 'Jumlah',
    data: {!! json_encode($jumlah_pendidik) !!}

  }]
});
</script>
@endsection
