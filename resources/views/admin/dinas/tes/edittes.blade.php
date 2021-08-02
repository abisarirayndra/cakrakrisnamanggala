@extends('master.admin')

@section('title')
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Paket Soal</h1>
    <p class="mb-4">Paket-paket yang disiapkan oleh admin untuk persiapan <i>Computer Assisted Test</i>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Edit Tes</h5>
            <div class="p-3 mt-3">
                <form action="{{route('admin.dinas.updatetes',[$tes->id])}}" method="POST">
                    @csrf
                        <div class="form-group">
                            <label for="nama">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control">
                                @foreach ($mapel as $val)
                                    <option value="{{$val->id}}" @if($val->id == $tes->mapel_id) {{'selected="selected"'}} @endif>{{$val->mapel}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nilai Pokok/Bobot</label>
                            <input type="number" class="form-control" name="nilai_pokok" value="{{$tes->nilai_pokok}}">
                        </div>
                        <div class="form-group">
                            <label for="nama">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" name="mulai" value="{{$tes->mulai}}" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" name="selesai" value="{{$tes->selesai}}" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="nama">Durasi (Dalam Menit)</label>
                            <input type="number" class="form-control" name="durasi" value="{{ $tes->durasi }}">
                        </div> --}}
                        <div class="form-group">
                            <label for="nama">Pengajar</label>
                            <select name="pengajar_id" class="form-control">
                                @foreach ($pengajar as $item)
                                    <option value="{{$item->id}}" @if($item->id == $tes->pengajar_id) {{'selected="selected"'}} @endif>{{$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{route('admin.dinas.lihatpaket',[$tes->dn_paket_id])}}" class="btn btn-danger">Batal</a>
                        <button type="submit" class="btn btn-warning">Simpan</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')

@endsection
