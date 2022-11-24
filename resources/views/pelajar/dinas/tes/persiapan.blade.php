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
                        {{-- <div class="text-right mr-3">
                            <a href="{{ route('pelajar.dinas.tes',[$paket->dn_paket_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div> --}}
                        <div class="text-right mr-3">
                            <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <img src="{{asset('assets/img/favicon.ico')}}" width="100" alt="">
                    </div>
                    @if($sudah)
                    <div class="text-center mt-4 mb-4">
                        <h4 class="text-dark">Soal Sudah Dikerjakan</h4>
                    </div>
                    @if ($sudah->kategori == "Kedinasan")
                        <div class="p-3 mt-2">
                            @if ($tes_selanjutnya == "Selesai")
                                <div class="text-center">
                                    {{-- <h3>Tes Kompetensi Dasar Selesai</h3> --}}
                                </div>
                            @else
                            <h5><i class="fas fa-hashtag text-warning"></i> Tes Selanjutnya</h5>
                            <div class="mt-3">
                                <div class="col-xl-12 col-md-12 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                                        {{ $tes_selanjutnya->mapel }}</div>
                                                        <div class="font-weight-bold mb-1" style="font-size: 10pt;">
                                                            <a href="{{ route('pelajar.dinas.persiapan', [$tes_selanjutnya->id]) }}" class="btn btn-sm btn-warning mt-3"><i class="fas fa-folder-open"></i> Buka</a>
                                                        </div>
                                                    </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @elseif ($sudah->kategori == "Psikotes")
                        <div class="p-3 mt-2">
                            @if ($tes_selanjutnya == "Selesai")
                                <div class="text-center">
                                    {{-- <h3>Psikotes Selesai</h3> --}}
                                </div>
                            @else
                            <h5><i class="fas fa-hashtag text-warning"></i> Tes Selanjutnya</h5>
                            <div class="mt-3">
                                <div class="col-xl-12 col-md-12 mb-12">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                                        {{ $tes_selanjutnya->mapel }}</div>
                                                        <div class="font-weight-bold mb-1" style="font-size: 10pt;">
                                                            <a href="{{ route('pelajar.dinas.persiapan', [$tes_selanjutnya->id]) }}" class="btn btn-sm btn-warning mt-3"><i class="fas fa-folder-open"></i> Buka</a>
                                                        </div>
                                                    </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
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
                            <h4 class="text-dark"> {{ $paket->mapel }}</h4>
                            Jam :  <b><h7 id="jam"></h7></b>
                        </div>
                        <ul>
                            {{-- <li><b>Mata Pelajaran : {{ $paket->mapel }}</b></li> --}}
                            <li>Kerjakan dengan teliti</li>
                            <li>Dilarang browsing, membuka catatan selama tes berlangsung</li>
                            <li>Tes akan berakhir pada waktu yang sudah tertera</li>
                            <li>Soal berbentuk <b>Pilihan Ganda</b> </li>
                        </ul>
                        <div class="text-center mb-4 mt-4">
                            <form action="{{route('pelajar.dinas.soalganda',[$id])}}" method="get">
                                <input type="text" name="q" value="{{ $ganda->id }}" hidden>
                                @php
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                @if ($paket->mulai > $now)
                                    <button type="submit" class="btn btn-danger btn-sm" disabled>Tidak Bisa Dibuka</button>
                                @else
                                    <button type="submit" class="btn btn-warning btn-sm">Mulai</button>
                                @endif
                            </form>
                        </div>
                    @elseif (isset($poin))
                            <div class="text-center mt-4 mb-4">
                                <h4 class="text-dark"> {{ $paket->mapel }}</h4>
                                Jam :  <b><h7 id="jam"></h7></b>
                            </div>
                            <ul>
                                {{-- <li><b>Mata Pelajaran : {{ $paket->mapel }}</b></li> --}}
                                <li>Kerjakan dengan teliti</li>
                                <li>Dilarang browsing, membuka catatan selama tes berlangsung</li>
                                <li>Tes akan berakhir pada waktu yang sudah tertera</li>
                                <li>Soal berbentuk <b>Pilihan Ganda Berpoin</b> </li>
                            </ul>
                        <div class="text-center mb-4 mt-4">
                            <form action="{{route('pelajar.dinas.soalgandapoin',[$id])}}" method="get">
                                <input type="text" name="q" value="{{ $poin->id }}" hidden>
                                @php
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                @if ($paket->mulai > $now)
                                    <button type="submit" class="btn btn-danger btn-sm" disabled>Tidak Bisa Dibuka</button>
                                @else
                                    <button type="submit" class="btn btn-warning btn-sm">Mulai</button>
                                @endif
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

@section('js')
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

<!-- Page level custom scripts -->
<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
    $('.dataTable').DataTable();
    });
</script>
<script type="text/javascript">
    window.onload = function() { jam(); }

    function jam() {
     var e = document.getElementById("jam"),
     d = new Date(), h, m, s;
     h = d.getHours();
     m = set(d.getMinutes());
     s = set(d.getSeconds());

     e.innerHTML = h +':'+ m +':'+ s;

     setTimeout('jam()', 1000);


    }

    function set(e) {
     e = e < 10 ? '0'+ e : e;
     return e;
    }
   </script>

@endsection
