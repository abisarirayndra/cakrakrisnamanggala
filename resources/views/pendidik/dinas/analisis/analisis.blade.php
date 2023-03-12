@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Analisis Nilai</h1>
    <p class="mb-4">Analisis sesuai hasil yang diperoleh pelajar dan sudah diarsipkan.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Daftar Arsip Nilai</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    @foreach ($arsip as $item)
                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                            Paket {{ $item->nama_paket }}</div>
                                        <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                            Arsip {{ $item->mapel }}</div>
                                            <div class=" font-weight-bold text-dark mb-1">
                                                Kode Arsip : {{ $item->kode }}</div>
                                            <div class="font-weight-bold mb-1" style="font-size: 10pt;">
                                                <div class="text-success">{{\Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y HH:mm')}}</div>
                                                <div class="row ml-1 mt-2">
                                                        <form action="{{ route('pendidik.dinas.hasil') }}" method="GET" >
                                                            <input name="token" value="{{ $item->kode }}" hidden>
                                                            <input type="text" name="tes_id" value="{{ $item->tes_id }}" hidden>
                                                            <button class="btn btn-sm btn-warning mt-3" type="submit"><i class="fas fa-list-alt"></i> Hasil</button>
                                                        </form>
                                                        <div class="dropdown no-arrow">
                                                            <a class="dropdown-toggle btn btn-sm btn-warning mt-3 ml-3" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fas fa-list-alt"></i> Analisis
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                                                                <div class="dropdown-header">Analisis Berdasarkan</div>
                                                                {{-- <form action="{{ route('pendidik.dinas.analisissoal') }}">
                                                                    <input type="text" name="token" hidden value="{{ $item->kode }}">
                                                                    <input type="text" name="tes" hidden value="{{ $item->id }}">
                                                                    <button type="submit" class="dropdown-item">Soal</button>
                                                                </form> --}}
                                                                <form action="{{ route('pendidik.dinas.analisispelajar') }}">
                                                                    <input type="text" name="token" hidden value="{{ $item->kode }}">
                                                                    <input type="text" name="tes" hidden value="{{ $item->tes_id }}">
                                                                    <button type="submit" class="dropdown-item">Pelajar</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{ $arsip->links() }}
                <a href="{{ route('pendidik.dinas.beranda') }}" class="btn btn-sm btn-danger">Keluar</a>
            </div>
        </div>
    </div>

</div>

@endsection


