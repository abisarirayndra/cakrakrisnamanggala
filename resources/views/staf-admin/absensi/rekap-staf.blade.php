@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Jurnal Staf</h5>
            <div class="p-3 mt-3">
                    <form action="{{ route('staf-admin.absensi.rekap-staf') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group ml-2">
                                <label for="bulan">Bulan</label>
                                <input type="text" class="form-control" id="datepicker-month" name="bulan" value="{{ $bulan }}" placeholder="Masukkan Bulan" autocomplete="off"/>
                            </div>
                            <div class="form-group ml-2">
                                <label for="tahun">Tahun</label>
                                <input type="text" class="form-control" id="datepicker-year" name="tahun" value="{{ $tahun }}" placeholder="Masukkan Tahun" autocomplete="off"/>
                            </div>

                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </form>
                    <form action="{{ route('staf-admin.absensi.rekap-staf.cetak') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group ml-2">
                                <input type="text" class="form-control" hidden name="bulan" value="{{ $bulan }}" placeholder="Masukkan Bulan" autocomplete="off"/>
                            </div>
                            <div class="form-group ml-2">
                                <input type="text" class="form-control" hidden name="tahun" value="{{ $tahun }}" placeholder="Masukkan Tahun" autocomplete="off"/>
                            </div>

                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-print"></i> Cetak Bulan Ini</button>
                        </div>
                    </form>

                <div class="row mt-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Tanggal</th>
                            <th>Datang - Pulang</th>
                            <th>Status</th>
                            <th>Laporan</th>
                        </tr>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($staf as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{$item->nama}}</td>
                            <td>{{ $item->role }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('dddd, D MMMM Y') }}</td>
                            <td>
                                @if ($item->pulang ==  null)
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - ????</td>
                                @else
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                @endif
                            <td>
                                @if ($item->status == 0)
                                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                @elseif ($item->status == 1)
                                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                @endif
                            </td>
                            <td>
                                @if ($item->jurnal ==  null)
                                    <p class="text-danger"><b> Belum Absen Pulang</b></p>
                                @else
                                    {{ $item->jurnal }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <h5><i class="fas fa-hashtag text-warning"></i> Izin Tidak Masuk</h5>
                <div class="card mb-4 border-bottom-warning">
                    <div class="card-body">
                        <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                  <th>No.</th>
                                  <th>Nama</th>
                                  <th>Role</th>
                                  <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($izin_staf as $item)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$item->nama}}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $("#datepicker-month").datepicker( {
    format: "mm",
    startView: "months",
    minViewMode: "months"
});
$("#datepicker-year").datepicker( {
    format: "yyyy",
    startView: "years",
    minViewMode: "years"
});
</script>
@endsection
