@extends('master.super')

@section('title')
    <title>Administrasi - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Edit Data Pelajar</h5>
            <div class="p-3 mt-3">
                @if ($role == 2)
                <form class="user" action="{{ route('super.penggunapelajar.updatedata', [$pelajar->id]) }}" method="POST">
                    @csrf
                    <input type="text" hidden name="auth" value="2">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" readonly value="{{$data->nama}}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" readonly value="{{$data->email}}">
                    </div>
                    <div class="form-group">
                        <label>Nomor Registrasi</label>
                        <input type="text" class="form-control" readonly value="{{$data->nomor_registrasi}}">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" value="{{$pelajar->nik}}" placeholder="--Masukkan NIK--">
                    </div>
                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" class="form-control" name="nisn" value="{{$pelajar->nisn}}" placeholder="--Masukkan NISN--">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" value="{{$pelajar->tempat_lahir}}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" value="{{$pelajar->tanggal_lahir}}">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="{{$pelajar->alamat}}">
                    </div>
                    <div class="form-group">
                        <label>Sekolah</label>
                        <input type="text" class="form-control" name="sekolah" value="{{$pelajar->sekolah}}">
                    </div>
                    <div class="form-group">
                        <label>WA</label>
                        <input type="number" class="form-control" name="wa" value="{{$pelajar->wa}}">
                    </div>
                    <div class="form-group">
                        <label>Nama Ibu Kandung</label>
                        <input type="text" class="form-control" name="ibu" value="{{$pelajar->ibu}}" placeholder="--Masukkan Nama Ibu Kandung--">
                    </div>
                    <div class="form-group">
                        <label>Nama Wali</label>
                        <input type="text" class="form-control" name="wali" value="{{$pelajar->wali}}">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA Wali</label>
                        <input type="number" class="form-control" name="wa_wali" value="{{$pelajar->wa_wali}}" placeholder="--Masukkan Nomor WA Wali--">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA Wali</label>
                        <select name="markas" class="form-control">
                            <option value="Genteng" @if($pelajar->markas == "Genteng") {{'selected="selected"'}} @endif>Genteng</option>
                            <option value="Banyuwangi" @if($pelajar->markas == "Banyuwangi") {{'selected="selected"'}} @endif>Banyuwangi</option>
                            <option value="Jember" @if($pelajar->markas == "Jember") {{'selected="selected"'}} @endif>Jember</option>
                        </select>
                    </div>
                    <img src="{{ asset('img/pendaftar/'. $pelajar->foto) }}" width="100" alt="">
                    <div class="form-group mt-3">
                        <label>Foto (Maksimal 500kb)</label>
                        <input type="file" class="form-control" name="foto">
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('super.penggunapelajar.lihat', [$pelajar->pelajar_id]) }}" class="btn btn-danger">Batal</a>
                        <button class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
                @elseif ($role == 7)
                <form class="user" action="{{ route('staf-admin.penggunapelajar.updatedata', [$pelajar->id]) }}" method="POST">
                    @csrf
                    <input type="text" hidden name="auth" value="7">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" readonly value="{{$data->nama}}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" readonly value="{{$data->email}}">
                    </div>
                    <div class="form-group">
                        <label>Nomor Registrasi</label>
                        <input type="text" class="form-control" readonly value="{{$data->nomor_registrasi}}">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" value="{{$pelajar->nik}}" placeholder="--Masukkan NIK--">
                    </div>
                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" class="form-control" name="nisn" value="{{$pelajar->nisn}}" placeholder="--Masukkan NISN--">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" value="{{$pelajar->tempat_lahir}}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" value="{{$pelajar->tanggal_lahir}}">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="{{$pelajar->alamat}}">
                    </div>
                    <div class="form-group">
                        <label>Sekolah</label>
                        <input type="text" class="form-control" name="sekolah" value="{{$pelajar->sekolah}}">
                    </div>
                    <div class="form-group">
                        <label>WA</label>
                        <input type="number" class="form-control" name="wa" value="{{$pelajar->wa}}">
                    </div>
                    <div class="form-group">
                        <label>Nama Ibu Kandung</label>
                        <input type="text" class="form-control" name="ibu" value="{{$pelajar->ibu}}" placeholder="--Masukkan Nama Ibu Kandung--">
                    </div>
                    <div class="form-group">
                        <label>Nama Wali</label>
                        <input type="text" class="form-control" name="wali" value="{{$pelajar->wali}}">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA Wali</label>
                        <input type="number" class="form-control" name="wa_wali" value="{{$pelajar->wa_wali}}" placeholder="--Masukkan Nomor WA Wali--">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA Wali</label>
                        <select name="markas" class="form-control">
                            <option value="Genteng" @if($pelajar->markas == "Genteng") {{'selected="selected"'}} @endif>Genteng</option>
                            <option value="Banyuwangi" @if($pelajar->markas == "Banyuwangi") {{'selected="selected"'}} @endif>Banyuwangi</option>
                            <option value="Jember" @if($pelajar->markas == "Jember") {{'selected="selected"'}} @endif>Jember</option>
                        </select>
                    </div>
                    <img src="{{ asset('img/pendaftar/'. $pelajar->foto) }}" width="100" alt="">
                    <div class="form-group mt-3">
                        <label>Foto (Maksimal 500kb)</label>
                        <input type="file" class="form-control" name="foto">
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('staf-admin.penggunapelajar.lihat', [$pelajar->pelajar_id]) }}" class="btn btn-danger">Batal</a>
                        <button class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
                @endif

            </div>
        </div>
    </div>

</div>
@endsection

@section('js')

@endsection
