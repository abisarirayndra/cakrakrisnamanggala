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
            <h5><i class="fas fa-hashtag text-warning"></i> Tahapan Pendaftaran</h5>
            <ul>
                <li>Mengisi data dibawah ini dengan benar</li>
                <li>Data NIP/NIPPPK silakan dikosongi apabila tidak punya</li>
            </ul>
            <div class="p-3 mt-3">
                {{-- @if (isset($ada))
                    <div class="text-center mt-4">
                        <h4>Biodata Sudah Ada, Silakan Mencetak</h4>
                        <a href="{{route('pendaftar.cetak-formulir', [$ada->id])}}" class="btn btn-success mt-4">Cetak</a>
                    </div>
                @else --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                <form class="user" class="" action="{{ route('register-pendidik.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control form-control-user"
                                placeholder="Nama Lengkap" name="nama" value="">
                        </div>
                        <div class="col-sm-4">
                            <label for="tempat">Tempat Lahir</label>
                            <input type="text" class="form-control form-control-user" name="tempat_lahir" placeholder="Tempat Lahir" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-user" name="tanggal_lahir" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="email">Email</label>
                            <input type="email" class="form-control form-control-user"
                                placeholder="Email" name="email" value="email">
                        </div>
                        <div class="col-sm-4">
                            <label for="password">Password</label>
                            <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="password" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="ulangi_password">Ulangi Password</label>
                            <input type="password" class="form-control form-control-user" id="confirm_password" required placeholder="Ulangi Password">
                            <small id='message'></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nik">Nomor Induk Kependudukan</label>
                            <input type="text" class="form-control form-control-user"
                                placeholder="NIK/No. KTP" name="nik" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="nisn">Nomor Induk Pegawai (NIP/NIPPPK)</label>
                            <input type="text" autofocus class="form-control form-control-user" name="nip" placeholder="NISN">
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Nama Ibu Kandung</label>
                            <input type="text" class="form-control form-control-user" name="ibu" placeholder="Nama Ibu Kandung" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="alamat">Alamat</label>
                                <input type="text" class="form-control form-control-user" id="alamat"
                                    placeholder="Alamat" name="alamat" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="wa">No. Telepon/WhatsApp</label>
                            <input type="number" class="form-control form-control-user" name="wa" placeholder="Nomor Telepon/Whatsapp" required>
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="foto">Foto Diri (3x4) <div class="text-danger">Maksimal 500 Kb</div> </label>
                                <input type="file" id="foto"
                                name="foto" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="markas">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control">
                                @foreach ($mapel as $item)
                                    <option value="{{ $item->id }}">{{ $item->mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="markas">Markas</label>
                            <select name="markas_id" class="form-control">
                                @foreach ($markas as $item)
                                    <option value="{{ $item->id }}">{{ $item->markas }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="text-center mt-4">
                        <button id="button" class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
                {{-- @endif --}}

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



