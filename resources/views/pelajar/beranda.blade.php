@extends('master.pelajar')

@section('title')
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <style>
        td{
            font-size: 80%
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Data Diri</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center pb-4">
                        <img src="{{asset('img/pelajar/'. $data->foto)}}}" width="120" alt="">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$user}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    @if ($data->nik == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->nik}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>NISN</b></td>
                                    @if ($data->nisn == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->nisn}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$data->tempat_lahir}}, {{\Carbon\Carbon::parse($data->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$data->alamat}}</td>
                                </tr>
                                <tr>
                                    <td><b>Asal Sekolah</b></td>
                                    <td class="pl-4">{{$data->sekolah}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$data->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Wali</b></td>
                                    <td class="pl-4">{{$data->wali}}</td>
                                </tr>
                                <tr>
                                    <td><b>WA Wali</b></td>
                                    @if ($data->wa_wali == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->wa_wali}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu Kandung</b></td>
                                    @if ($data->ibu == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->ibu}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Tanggal Daftar</b></td>
                                    <td class="pl-4">{{\Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                </tr>
                            </table>
                                {{-- <a href="{{route('pendaftar.cetak_pdf', [$data->id])}}" target="_blank" class="btn btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh PDF</a> --}}
                                {{-- <a href="" class="btn btn-warning mt-4 ml-3"><i class="fas fa-edit"></i> Edit</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Menu</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pelajar.dinas.paket') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Paket Soal</h6>
                        </a>
                    </div>
                    {{-- <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pelajar.dinas.paket') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-qrcode fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Absensi</h6>
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
