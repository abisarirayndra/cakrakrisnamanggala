@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Absensi Kehadiran</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-s mb-0">Mapel <b>{{ $jadwal->mapel }}</b></p>
                                <p class="text-s mb-0">Kelas <b>{{ $jadwal->kelas }}</b></p>
                                <p class="text-s mb-0">{{ $jadwal->nama }}</p>
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }}</b></p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('staf-admin.absensi.upload-absensi') }}" method="POST">
                        @csrf
                        <div class="form-group ml-7">
                            <label for="token">Scan Token</label>
                            <input type="text" class="form-control" name="token" autofocus placeholder="Masukkan Token" autocomplete="off"/>
                            <input type="text" name="jadwal_id" value="{{ $jadwal->id }}" readonly hidden>
                            <input type="text" name="datang" value="{{ \Carbon\Carbon::now() }}" readonly hidden>
                            @php
                                $now = \Carbon\Carbon::now();
                            @endphp
                            @if ($now < $jadwal->mulai)
                                <input type="text" name="status" value="1" readonly hidden>
                            @elseif($now > $jadwal->mulai)
                                <input type="text" name="status" value="0" readonly hidden>
                            @endif
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Absensi</button>
                        </div>
                    </form>
                </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Pendidik</h5>
                <div class="row">
                    @foreach ($pendidik as $item)
                    <div class="col-xl-2">
                        <div class="card mb-4 py-1 border-bottom-warning">
                            <div class="card-body">
                                <div class="text-center">
                                    @if ($item->status == 0)
                                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                    @elseif ($item->status == 1)
                                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                    @endif
                                    @if ($item->pulang == null)
                                        <p><b>Datang </b>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</p>
                                    @else
                                        <p><b>Pulang </b>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</p>
                                    @endif
                                    <img src="{{ asset('/pendidik/img/'. $item->foto) }}" width="100" alt="Belum ada foto">
                                </div>
                                <div class="text-center mt-2">
                                    <p><b>{{Str::limit($item->nama, 10)}}</b></p>
                                </div>
                                <div class="text-center">

                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Pelajar</h5>
                <div class="row">
                    @foreach ($pelajar as $item)
                    <div class="col-xl-2">
                        <div class="card mb-4 py-1 border-bottom-warning">
                            <div class="card-body">
                                <div class="text-center">
                                    @if ($item->status == 0)
                                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                    @elseif ($item->status == 1)
                                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                    @endif
                                    @if ($item->pulang == null)
                                        <p><b>Datang </b>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</p>
                                    @else
                                        <p><b>Pulang </b>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</p>
                                    @endif
                                    <img src="{{ asset('/img/pelajar/'. $item->foto) }}" width="100" alt="Belum ada foto">
                                </div>
                                <div class="text-center mt-2">
                                    <p><b>{{Str::limit($item->nama, 10)}}</b></p>
                                </div>
                                <div class="text-center">

                                </div>
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

@endsection
