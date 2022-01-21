@extends('master.pendaftar')

@section('title')
<title>Profil Pendaftar</title>

@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Formulir Pendaftaran</h1>
    <p class="mb-4">Diisi dengan data yang benar-benar sesuai dengan identitas/KTP.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Tahapan Pendaftaran</h5>
            <ul>
                <li>Mencetak formulir dibawah sebagai bukti pendaftaran</li>
                <li>Datang ke lokasi markas yang dipilih dengan membawa bukti pendaftaran yang sudah dicetak</li>
                <li>Melakukan proses administrasi selanjutnya di markas</li>
            </ul>
            <div class="p-3 mt-3">
                <h5 class="mb-4">Data Pendaftar Cakra Krisna Manggala</h5>
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center">
                        <img src="{{asset('/img/pelajar/'. $data->foto)}}" width="150" alt="">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                        <div class="row">
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
                                <a href="{{route('pendaftar.cetak-formulir-pdf', [$data->id])}}" target="_blank" class="btn btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh PDF</a>
                                <a href="{{route('pendaftar.edit-pendaftar', [$data->id])}}" class="btn btn-warning mt-4 ml-3"><i class="fas fa-edit"></i> Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script type="text/javascript" src="{{asset('back/vendor/ckeditor/ckeditor.js')}}"></script>


@endsection



