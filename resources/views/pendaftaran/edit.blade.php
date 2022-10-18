@extends('master.pendaftar-pendidik')

@section('title')
<title>Profil Pendaftar</title>

@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Formulir Pendaftaran</h1>
    <p class="mb-4">Diisi dengan data yang benar-benar sesuai dengan identitas/KTP.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="text-right pt-3 pr-3">
                <a href="{{ route('register-email') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
            </div>
            <h5><i class="fas fa-hashtag text-warning"></i> Edit Data</h5>
            <div class="p-3 mt-3">
                @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <form class="user" class="" action="{{route('pendaftar.update-pendaftar', [$data->id])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nama" value="{{ $user->nama }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="tempat">Tempat Lahir</label>
                            <input type="text" autofocus class="form-control form-control-user" name="tempat_lahir" value="{{$data->tempat_lahir}}" placeholder="Tempat Lahir" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-user" name="tanggal_lahir" value="{{$data->tanggal_lahir}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nik">Nomor Induk Kependudukan</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nik" value="{{$data->nik}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="nisn">Nomor Induk Siswa Nasional (NISN)</label>
                            <input type="text" autofocus class="form-control form-control-user" name="nisn" placeholder="NISN" value="{{$data->nisn}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Nama Ibu Kandung</label>
                            <input type="text" class="form-control form-control-user" name="ibu" placeholder="Nama Ibu Kandung" value="{{$data->ibu}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="alamat">Alamat</label>
                                <input type="text" class="form-control form-control-user" id="alamat"
                                    placeholder="Alamat" name="alamat" value="{{$data->alamat}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="sekolah">Asal Sekolah</label>
                            <input type="text" class="form-control form-control-user" id="sekolah"
                                placeholder="Asal Sekolah" name="sekolah" value="{{$data->sekolah}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="wa">No. Telepon/WhatsApp</label>
                            <input type="number" class="form-control form-control-user" name="wa" value="{{$data->wa}}" placeholder="Nomor Telepon/Whatsapp" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="wali">Nama Wali</label>
                            <input type="text" class="form-control form-control-user" name="wali" value="{{$data->wali}}" placeholder="Nama Wali" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="wa-wali">No. Telepon/Whatsapp Wali</label>
                            <input type="number" class="form-control form-control-user" name="wa_wali" value="{{$data->wa_wali}}" placeholder="Nomor Telepon/Whatsapp Wali" required>
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <img src="{{asset('img/pelajar/'. $data->foto)}}" width="50" alt="" class="m-3">
                            <label for="foto">Foto Diri (3x4) <div class="text-danger">Maksimal 500 Kb</div> </label>
                                <input type="file" id="foto"
                                name="foto">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="status">Status Sekolah</label>
                            <select name="status_sekolah" class="form-control">
                                <option value="0" @if($data->status_sekolah == 0) {{'selected="selected"'}} @endif>Belum Lulus</option>
                                <option value="1" @if($data->status_sekolah == 1) {{'selected="selected"'}} @endif>Lulus</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="markas">Markas Yang Dituju</label>
                            <select name="markas_id" class="form-control">
                                @foreach ($markas as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $data->markas_id) {{'selected="selected"'}} @endif>{{ $item->markas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button id="button" class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

</div>

@endsection

@section('js')
<script type="text/javascript" src="{{asset('back/vendor/ckeditor/ckeditor.js')}}"></script>

<script type="text/javascript">
    $('#password, #confirm_password').on('keyup', function () {
          if ($('#password').val() == $('#confirm_password').val()) {
            $('#message').html('Password Cocok').css('color', 'green');
            $('#button').removeAttr("disabled");
          } else {
            $('#message').html('Password Tidak Cocok').css('color', 'red');
            var element = document.getElementById('button');
            element.setAttribute('disabled','disabled');
          }

        });
  </script>
@endsection



