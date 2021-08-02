@extends('master.pelajar')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">
    <div class="text-right mr-3">
        <a href="{{ route('pelajar.dinas.paket') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Computer Assisted Test - Cakra Krisna Manggala </h1>
    <p class="mb-4">Perhatikan waktu mulai dan selesai disetiap tes, soal akan dibuka dan ditutup sesuai waktu yang tertera.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Daftar Tes</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    @foreach ($tes as $item)
                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                            {{ $item->mapel }} (Bobot {{ $item->nilai_pokok }}%) </div>
                                            <div class="font-weight-bold mb-1" style="font-size: 10pt;">
                                                <div class="text-success">{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y HH:mm')}}</div>
                                                <div class="text-danger">{{\Carbon\Carbon::parse($item->selesai)->isoFormat('dddd, D MMMM Y HH:mm')}}</div>
                                                @php
                                                $now = \Carbon\Carbon::now();
                                                @endphp
                                                    @if ($now < $item->mulai)
                                                        <span class="badge badge-success">Belum Tersedia</span>
                                                    @elseif ($now > $item->selesai)
                                                        <span class="badge badge-danger">Selesai</span>
                                                    @else
                                                        <a href="{{ route('pelajar.dinas.persiapan', [$item->id]) }}" class="btn btn-sm btn-warning mt-3"><i class="fas fa-folder-open"></i> Buka</a>
                                                    @endif
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
            </div>
        </div>
    </div>

</div>

@endsection

