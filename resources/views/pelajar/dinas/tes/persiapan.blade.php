@extends('master.pelajar')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-6 col-lg-6 col-md-4">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="text-center mt-4">
                        <div class="text-right mr-3">
                            <a href="{{ route('pelajar.dinas.tes',[$paket->dn_paket_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <img src="{{asset('assets/img/favicon.ico')}}" width="50" alt="">
                    </div>
                    @if($sudah)
                    <div class="text-center mt-4 mb-4">
                        <h4 class="text-dark">Soal Sudah Dikerjakan</h4>
                    </div>
                    @elseif (isset($essay))
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Petunjuk</h4>
                        </div>
                            <ul>
                                <li>Kerjakan dengan teliti</li>
                                <li>Dilarang browsing, membuka catatan selama tes berlangsung</li>
                                <li>Tes akan berakhir pada waktu yang sudah tertera</li>
                                <li>Soal berbentuk <b>Essay</b> </li>
                            </ul>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('pendidik.dinas.soalessay',[$id])}}" class="btn btn-warning btn-sm">Mulai</a>
                        </div>
                    @elseif (isset($ganda))
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Petunjuk</h4>
                        </div>
                        <ul>
                            <li>Kerjakan dengan teliti</li>
                            <li>Dilarang browsing, membuka catatan selama tes berlangsung</li>
                            <li>Tes akan berakhir pada waktu yang sudah tertera</li>
                            <li>Soal berbentuk <b>Pilihan Ganda</b> </li>
                        </ul>
                        <div class="text-center mb-4 mt-4">
                            <form action="{{route('pelajar.dinas.soalganda',[$id])}}" method="get">
                                <input type="text" name="q" value="{{ $ganda->id }}" hidden>
                                <button type="submit" class="btn btn-warning btn-sm">Mulai</button>
                            </form>
                        </div>
                    @elseif (isset($poin))
                            <div class="text-center mt-4 mb-4">
                                <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Petunjuk</h4>
                            </div>
                            <ul>
                                <li>Kerjakan dengan teliti</li>
                                <li>Dilarang browsing, membuka catatan selama tes berlangsung</li>
                                <li>Tes akan berakhir pada waktu yang sudah tertera</li>
                                <li>Soal berbentuk <b>Pilihan Ganda Berpoin</b> </li>
                            </ul>
                        <div class="text-center mb-4 mt-4">
                            <form action="{{route('pelajar.dinas.soalgandapoin',[$id])}}" method="get">
                                <input type="text" name="q" value="{{ $poin->id }}" hidden>
                                <button type="submit" class="btn btn-warning btn-sm">Mulai</button>
                            </form>
                        </div>
                    @else
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark">Soal Kosong</h4>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
