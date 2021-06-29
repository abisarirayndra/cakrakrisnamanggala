@extends('master.master')

@section('title')
    <title>CAT - Kedinasan</title>
@endsection

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-8 col-lg-8 col-md-7">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="text-center mt-4">
                        <div class="text-right mr-3">
                            <a href="{{ route('pendidik.dinas.tes', [$paket->dn_paket_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <img src="{{asset('assets/img/favicon.ico')}}" width="50" alt="">
                    </div>
                    @if (isset($essay))
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Soal Sudah Tersedia Dalam Bentuk Essay</h4>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h6 class="text-dark">Jumlah Soal : {{ $jumlah_essay }}</h6>
                        </div>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('pendidik.dinas.soalessay',[$id])}}" class="btn btn-warning">Lihat</a>
                            <a href="{{route('pendidik.dinas.hapusessay',[$id])}}" class="btn btn-danger">Hapus</a>
                        </div>
                    @elseif (isset($ganda))
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Soal Sudah Tersedia Dalam Bentuk Pilihan Ganda</h4>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h6 class="text-dark">Jumlah Soal : {{ $jumlah_ganda }}</h6>
                        </div>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('pendidik.dinas.soalganda',[$id])}}" class="btn btn-warning">Lihat</a>
                            <a href="{{route('pendidik.dinas.hapusganda',[$id])}}" onclick="return confirm('Anda yakin ingin Menghapus ?')" class="btn btn-danger">Hapus</a>
                        </div>
                    @elseif (isset($poin))
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Soal Sudah Tersedia Dalam Bentuk Pilihan Ganda Berpoin</h4>
                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h6 class="text-dark">Jumlah Soal : {{ $jumlah_poin }}</h6>
                        </div>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('pendidik.dinas.soalgandapoin',[$id])}}" class="btn btn-warning">Lihat</a>
                            <a href="{{route('pendidik.dinas.hapusgandapoin',[$id])}}" class="btn btn-danger">Hapus</a>
                        </div>
                    @else
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Tipe Soal</h4>
                        </div>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('pendidik.dinas.soalganda',[$id])}}" class="btn btn-warning">Pilihan Ganda</a>
                            <a href="{{route('pendidik.dinas.soalgandapoin',[$id])}}" class="btn btn-warning">Pilihan Ganda Berpoin</a>
                            <a href="{{route('pendidik.dinas.soalessay',[$id])}}" class="btn btn-warning">Essay</a>

                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
