@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Jadwal Hari Ini</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    @foreach ($jadwal as $item)
                    <div class="col-sm-3">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-s mb-0">Mapel <b>{{ $item->mapel }}</b></p>
                                <p class="text-s mb-0">Kelas <b>{{ $item->kelas }}</b></p>
                                <p class="text-s mb-0">{{ $item->nama }}</p>
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}</b></p>
                                <a href="{{ route('staf-admin.absen', [$item->id]) }}" class="btn btn-sm btn-success mt-2">Datang</a>
                                <a href="{{ route('staf-admin.absen-pulang', [$item->id]) }}" class="btn btn-sm btn-danger mt-2">Pulang</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
