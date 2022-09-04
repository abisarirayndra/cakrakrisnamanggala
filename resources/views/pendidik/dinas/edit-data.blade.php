@extends('master.master')

@section('title')
<title>Profil Pendidik</title>

@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Formulir Profil Pendidik</h1>
    <p class="mb-4">Diisi dengan data yang benar-benar sesuai dengan identitas/KTP.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Administrasi Pendidik</h5>
            <ul>
                <li>Mengisi data dibawah ini dengan benar</li>
                <li>NIP/NIPPPK apabila tidak ada maka bisa dikosongi</li>
            </ul>
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
                <form class="user" action="{{ route('pendidik.dinas.updateprofil') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nama" value="{{$user}}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label for="tempat">Tempat Lahir</label>
                            <input type="text" autofocus class="form-control form-control-user" name="tempat_lahir" placeholder="Tempat Lahir" >
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-user" name="tanggal_lahir" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nik">Nomor Induk Kependudukan</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nik" >
                        </div>
                        <div class="col-sm-4">
                            <label for="nisn">Nomor Induk Pegawai (NIP/NIPPPK) *</label>
                            <input type="text" autofocus class="form-control form-control-user" name="nip" placeholder="NIP/NIPPPK">
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Nama Ibu Kandung</label>
                            <input type="text" class="form-control form-control-user" name="ibu" placeholder="Nama Ibu Kandung" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="alamat">Alamat</label>
                                <input type="text" class="form-control form-control-user" id="alamat"
                                    placeholder="Alamat" name="alamat" >
                        </div>
                        <div class="col-sm-4">
                            <label for="mapel">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control form-control-user" >
                                @foreach ($mapel as $item)
                                    <option value="{{ $item->id }}">{{ $item->mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="wa">No. Telepon/WhatsApp</label>
                            <input type="number" class="form-control form-control-user" name="wa" placeholder="Nomor Telepon/Whatsapp" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="markas">Markas Yang Dituju</label>
                            <select name="markas" class="form-control form-control-user" >
                                <option value="Genteng">Genteng</option>
                                <option value="Banyuwangi">Banyuwangi</option>
                                <option value="Jember">Jember</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="foto">Foto Diri (3x4) <div class="text-danger">Maksimal 500 Kb</div> </label>
                                <input type="file" id="foto"
                                name="foto" >
                        </div>
                        {{-- <div class="col-sm-4">
                            <label for="cv"><i>Curriculum Vitae </i><div class="text-danger">Maksimal 1 Mb</div> </label>
                            <input type="file" id="cv"
                            name="cv" >
                        </div> --}}
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


