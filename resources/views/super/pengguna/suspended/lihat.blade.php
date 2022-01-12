@extends('master.super')

@section('title')
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Detail Pelajar</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center">
                        <img src="{{asset('/img/pendaftar/'. $pelajar->foto)}}" width="150" alt="">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                        <div class="row">
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$pelajar->nama}}</td>
                                </tr>
                                <tr>
                                    <td><b>Email</b></td>
                                    <td class="pl-4">{{$pelajar->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    <td class="pl-4">{{$pelajar->nik}}</td>
                                </tr>
                                <tr>
                                    <td><b>NISN</b></td>
                                    <td class="pl-4">{{$pelajar->nisn}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$pelajar->tempat_lahir}}, {{\Carbon\Carbon::parse($pelajar->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$pelajar->alamat}}</td>
                                </tr>
                                <tr>
                                    <td><b>Asal Sekolah</b></td>
                                    <td class="pl-4">{{$pelajar->sekolah}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$pelajar->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu</b></td>
                                    <td class="pl-4">{{$pelajar->ibu}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Wali</b></td>
                                    <td class="pl-4">{{$pelajar->wali}}</td>
                                </tr>
                                <tr>
                                    <td><b>Markas Yang Dipilih</b></td>
                                    <td class="pl-4">{{$pelajar->markas}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Daftar</b></td>
                                    <td class="pl-4">{{\Carbon\Carbon::parse($pelajar->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                </tr>
                            </table>


                        </div>
                        <div class="row">
                            <a href="{{ route('super.penggunasuspend.cabutsuspendpelajar', [$pelajar->id]) }}" class="btn btn-success mt-4" onclick="return confirm('Anda yakin ingin mencabut suspend akun ini ?')"><i class="fas fa-pen"></i> Cabut Suspend</a>
                            <a href="{{ route('super.penggunapelajar.hapus', [$pelajar->id]) }}" class="btn btn-danger mt-4 ml-3" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i> Hapus Data</a>
                        </div>
                    </div>
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
    <script src="{{asset('js/demo/datatables-demo.js')}}"></script>
@endsection
