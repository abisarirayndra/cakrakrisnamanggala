@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
                <div class="float-right">
                    <form action="{{ route('staf-admin.jadwal') }}" method="GET">
                        <input type="text" value="{{ $jadwal->kelas_id }}" hidden name="kelas">
                        <input type="text" value="{{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('MM') }}" hidden name="bulan">
                        <input type="text" value="{{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('Y') }}" hidden name="tahun">
                        <button type="submit" class="btn btn-sm btn-danger"><b>X</b></button>
                    </form>
                </div>
            <h5><i class="fas fa-hashtag text-warning"></i> Revisi Jadwal</h5>
            <div class="p-3 mt-3">
                <form action="{{ route('staf-admin.jadwal.update', [$jadwal->id]) }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <input type="text" value="{{ $admin }}" name="staf_id" hidden>
                        <select name="mapel_id" id="" class="form-control">
                            @foreach ($mapel as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $jadwal->mapel_id) {{'selected="selected"'}}  @endif>{{ $item->mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" id="" class="form-control">
                            @foreach ($kelas as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $jadwal->kelas_id) {{'selected="selected"'}}  @endif>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pendidik (Penanggung Jawab)</label>
                        <select name="pendidik_id" id="" class="form-control">
                            @foreach ($pendidik as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $jadwal->pendidik_id) {{'selected="selected"'}}  @endif>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="datetime-local" class="form-control" name="mulai" value="{{ $jadwal->mulai }}" required>
                    </div>
                    <div class="form-group">
                        <label>Selesai</label>
                        <input type="datetime-local" class="form-control" name="selesai" value="{{ $jadwal->selesai }}" required>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
