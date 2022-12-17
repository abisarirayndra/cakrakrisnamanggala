@extends('master.master')

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
                        <img src="{{ asset('pendidik/img/'. $data->foto) }}" width="120" alt="Belum ada foto">
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
                                    <td><b>NIP</b></td>
                                    @if ($data->nip == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->nip}}</td>
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
                                    <td><b>Mata Pelajaran</b></td>
                                    <td class="pl-4">{{$data->mapel}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$data->wa}}</td>
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
                                    <td><b>Status Dapodik</b></td>
                                    <td class="pl-4">{{$data->status_dapodik}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Daftar</b></td>
                                    <td class="pl-4">{{\Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                </tr>
                            </table>
                                {{-- <a href="" class="btn btn-sm btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh CV</a> --}}
                                <a href="{{ route('pendidik.dinas.edit') }}" class="btn btn-warning btn-sm mt-4 ml-3"><i class="fas fa-edit"></i> Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($data->tempat_lahir == null)
            <h5><i class="fas fa-hashtag text-warning"></i> Menu (Not Available)</h5>
            <ul>
                <li class="text-danger"><b>SILAKAN MELAKUKAN EDIT DATA DIRI DAHULU SEBELUM MENGAKSES MENU</b></li>
            </ul>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-3 col-md-3 text-center">
                        <button style="background-color: transparent; border: 0px" disabled>
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Paket Soal (Not Available)</h6>
                        </button>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <button style="background-color: transparent; border: 0px" disabled>
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-qrcode fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Absensi (Not Available)</h6>
                        </button>
                    </div>
                </div>
            </div>
            @else
            <h5><i class="fas fa-hashtag text-warning"></i> Menu</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pendidik.dinas.paket') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Paket Soal</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pendidik.dinas.analisis') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-chart-bar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Analisis</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pendidik.absensi') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-qrcode fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Absensi</h6>
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
