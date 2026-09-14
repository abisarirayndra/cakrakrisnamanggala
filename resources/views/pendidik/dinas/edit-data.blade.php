@extends('layouts.panel-pendidik')

@section('title', 'Profil Pendidik - Cakra Krisna Manggala')

@section('content')
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Pendidik</p>
        <h1 class="h3 mb-1">Formulir Profil Pendidik</h1>
        <p class="ck-hint mb-0">Diisi dengan data yang benar-benar sesuai dengan identitas/KTP.</p>
    </div>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-2">Administrasi Pendidik</h2>
        <ul class="ck-hint mb-4">
            <li>Mengisi data di bawah ini dengan benar</li>
            <li>NIP/NIPPPK apabila tidak ada maka bisa dikosongi</li>
        </ul>
        @if ($errors->any())
            <div class="alert alert-ck mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('pendidik.dinas.updateprofil') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $user }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="tempat_lahir" class="form-label">Tempat lahir</label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Tempat lahir" autofocus>
                </div>
                <div class="col-md-4">
                    <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                </div>
                <div class="col-md-4">
                    <label for="nik" class="form-label">Nomor Induk Kependudukan</label>
                    <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK">
                </div>
                <div class="col-md-4">
                    <label for="nip" class="form-label">Nomor Induk Pegawai (NIP/NIPPPK)</label>
                    <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP/NIPPPK">
                </div>
                <div class="col-md-4">
                    <label for="ibu" class="form-label">Nama ibu kandung</label>
                    <input type="text" class="form-control" id="ibu" name="ibu" placeholder="Nama ibu kandung">
                </div>
                <div class="col-md-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat">
                </div>
                <div class="col-md-4">
                    <label for="mapel_id" class="form-label">Mata pelajaran</label>
                    <select name="mapel_id" id="mapel_id" class="form-select">
                        @foreach ($mapel as $item)
                            <option value="{{ $item->id }}">{{ $item->mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="wa" class="form-label">No. telepon/WhatsApp</label>
                    <input type="number" class="form-control" id="wa" name="wa" placeholder="Nomor telepon/WhatsApp">
                </div>
                <div class="col-md-4">
                    <label for="markas" class="form-label">Markas yang dituju</label>
                    <select name="markas" id="markas" class="form-select">
                        <option value="Genteng">Genteng</option>
                        <option value="Banyuwangi">Banyuwangi</option>
                        <option value="Jember">Jember</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="foto" class="form-label">Foto diri (3x4)</label>
                    <input type="file" class="form-control" id="foto" name="foto">
                    <p class="ck-hint small mt-1 mb-0">Maksimal 500 Kb</p>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-ck" type="submit">Simpan</button>
            </div>
        </form>
    </section>
@endsection
