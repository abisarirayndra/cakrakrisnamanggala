@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Jadwal Pembelajaran</h5>
            <div class="p-3 mt-3">
                    <form action="{{ route('staf-admin.absensi.rekap-pembelajaran') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select name="kelas" id="" class="form-control">
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $kelas_id) {{'selected="selected"'}}  @endif>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
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

                <div class="row mt-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mapel</th>
                            <th>Pendidik</th>
                            <th>Mulai - Selesai</th>
                            <th>Aksi</th>
                        </tr>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($jadwal as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y')}}</td>
                            <td>{{ $item->mapel }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}</td>
                            <td>
                                <a href="{{ route('staf-admin.absensi.rekap-pembelajaran.lihat', [$item->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Lihat</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
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
