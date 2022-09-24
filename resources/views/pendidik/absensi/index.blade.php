@extends('master.master')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Absensi Pendidik</h5>
            <ul>
                <li>Klik tombol <b>Kode QR</b> di bawah ini</li>
                <li>Dekatkan layar perangkat anda ke scanner, tambah kecerahan perangkat anda bila tidak bisa terbaca scanner</li>
                <li>Sebelum klik selesai, pastikan isi jurnal pembelajaran</li>
                <li>Klik tombol <b>Selesai</b> jika proses pembelajaran berakhir</li>
            </ul>
            <input id="text" value="{{ $token }}" hidden/>
            <div class="row">
                <button id="btn-qrcode" class="btn btn-success btn-sm mt-4 ml-3" data-toggle="modal" data-target="#qrcode-modal"><i class="fas fa-qrcode"></i> Kode QR</button>
                <form action="{{ route('pendidik.absensi.histori-mengajar') }}" method="GET">
                    @if ($data->markas_id == 1)
                        <input type="text" value="1" name="kelas" hidden>
                    @elseif ($data->markas_id == 2)
                        <input type="text" value="4" name="kelas" hidden>
                    @elseif ($data->markas_id == 3)
                        <input type="text" value="9" name="kelas" hidden>
                    @endif
                    @php
                        $sekarang = \Carbon\Carbon::now();
                    @endphp
                    <input type="text" value="{{ $sekarang->format('m') }}" hidden name="bulan">
                    <input type="text" value="{{ $sekarang->format('Y') }}" hidden name="tahun">
                    <button class="btn btn-sm btn-warning mt-4 ml-2" type="submit"><i class="fas fa-list"></i> Histori Mengajar</a>
                </form>
            </div>

            <div class="p-3 mt-3">
                @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                <li>{{ $errors->first() }}</li>
                                            </ul>
                                        </div>
                                    @endif
                <div class="row">
                    @foreach ($jadwal as $item)
                    <div class="col-sm-3">
                        <div class="card mb-4 border-bottom-warning">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                            </div>
                            <div class="card-body">
                                @if ($item->status == 0)
                                    <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                @elseif ($item->status == 1)
                                    <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                @endif
                                <p class="text-s mb-0">Mapel <b>{{ $item->mapel }}</b></p>
                                <p class="text-s mb-0">Kelas <b>{{ $item->kelas }}</b></p>
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}</b></p>
                                <div class="mt-3">
                                    @if (isset($item->jurnal))
                                    <div class="row">
                                        <a href="{{ route('pendidik.absensi.jurnal', [$item->id]) }}" class="btn btn-sm btn-warning mr-2"><i class="fas fa-eye"></i> Lihat</a>
                                        <form action="{{ route('pendidik.absensi.selesai', [$item->id]) }}" method="post">
                                            @csrf
                                            @php 
                                                $now = \Carbon\Carbon::now();
                                            @endphp
                                            <input type="datetime" value="{{ $now }}" name="pulang" hidden>
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check-circle"></i> Selesai</button>
                                        </form>
                                    </div>

                                    @else
                                    <a href="{{ route('pendidik.absensi.jurnal', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i> Isi</a>
                                    <button class="btn btn-sm btn-success" disabled><i class="fas fa-check-circle"></i> Selesai</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="qrcode-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">QR Code Absensi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div id="qrcode"></div>
                    <div class="text-left mt-4">
                        <p>Token : <b>{{ $token }}</b></p>
                    </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/js/qrcode.min.js') }}"></script>
<script>
    let input = document.querySelector('#text');
    let button = document.querySelector('#btn-qrcode');
    let qrcode = new QRCode(document.querySelector('#qrcode'), {

        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    button.addEventListener('click', () => {
      let inputValue = input.value;
      qrcode.makeCode(inputValue);
    })
</script>
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
@endsection
