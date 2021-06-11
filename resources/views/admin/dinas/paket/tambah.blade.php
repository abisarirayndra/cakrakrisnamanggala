@extends('master.master')

@section('title')
    <title>CAT - Kedinasan</title>
@endsection

@section('content')
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Paket Soal Kedinasan</h1>
    <p class="mb-4">Paket-paket yang disiapkan oleh admin untuk persiapan <i>Computer Assisted Test</i>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Tambah Paket Soal</h5>
            <div class="p-3 mt-3">
                <form class="user" action="{{route('admin.dinas.uppaket')}}" method="POST">
                    @csrf
                    <div class="form-group">
                            <label for="nama">Nama Paket</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                 name="nama_paket" required>
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