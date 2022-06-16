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
                    <form action="{{ route('staf-admin.absensi.upload-absensi.staf') }}" method="POST">
                        @csrf
                        <div class="form-group ml-7">
                            <label for="token">Scan Token</label>
                            <input type="text" class="form-control" name="token" autofocus placeholder="Masukkan Token" autocomplete="off"/>
                            <input type="text" name="datang" value="{{ \Carbon\Carbon::now() }}" readonly hidden>
                            @php
                                $now = \Carbon\Carbon::now();
                                $masuk = \Carbon\Carbon::today()->addHours(13);
                            @endphp
                            @if ($now < $masuk)
                                <input type="text" name="status" value="1" readonly hidden>
                            @elseif($now > $masuk)
                                <input type="text" name="status" value="0" readonly hidden>
                            @endif
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Absensi</button>
                        </div>
                    </form>
                </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Staf</h5>
                <div class="row">
                    @foreach ($staf as $item)
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
                                    <p><b>{{$item->role}}</b></p>
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
