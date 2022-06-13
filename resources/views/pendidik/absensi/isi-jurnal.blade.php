@extends('master.master')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Isi Jurnal</h5>
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
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }}</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                <li>{{ $errors->first() }}</li>
                                            </ul>
                                        </div>
                                    @endif
                        <form action="{{ route('pendidik.absensi.up-jurnal',[$jadwal->id]) }}" method="POST">
                            @csrf
                            <div class="form-group ml-7">
                                <label for="token"><b>Jurnal</b> </label>
                                @if ($jadwal->jurnal == null)
                                    <textarea name="jurnal" id="" cols="30" rows="5" class="form-control"></textarea>
                                @else
                                    <textarea name="jurnal" id="" cols="30" rows="5" class="form-control">{{ $jadwal->jurnal }}</textarea>
                                @endif
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Simpan</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
