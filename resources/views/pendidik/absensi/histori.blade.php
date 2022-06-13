@extends('master.master')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Histori Mengajar</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <form action="" method="GET">
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
                            <div class="form-group ml-2 pt-4">
                                <button class="btn btn-sm btn-warning">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row mt-4">
                    @foreach ($jadwal as $item)
                    <div class="col-sm-3">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                            </div>
                            <div class="card-body">
                                @if ($item->status == 0)
                                    <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                @elseif ($item->status == 1)
                                    <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                @endif
                                <p class="text-s mb-0">Kelas <b>{{ $item->kelas }}</b></p>
                                <p class="text-s mb-0">Mapel <b>{{ $item->mapel }}</b></p>
                                <p class="text-s mb-0">Jadwal <b>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}</b></p>
                                <p class="text-s mb-0">Datang <b>{{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}}</b></p>
                                <p class="text-s mb-0">Pulang <b>{{\Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm')}}</b></p>
                                <p class="text-s mb-0">Jurnal <b> <br>"{{ $item->jurnal }}"</b></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
