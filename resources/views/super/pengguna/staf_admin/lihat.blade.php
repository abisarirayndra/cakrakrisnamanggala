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
            <h5><i class="fas fa-hashtag text-warning"></i> Detail Admin</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center">
                        {{-- <img src="https://cakrakrisnamanggala.com/img/pendaftar/{{$admin->foto}}" width="120" alt=""> --}}
                    </div>
                    <div class="col-xl-8 col-sm-8">
                        <div class="row">
                            @if($admin == !null)
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$admin->nama}}</td>
                                </tr>
                                <tr>
                                    <td><b>Email</b></td>
                                    <td class="pl-4">{{$admin->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    <td class="pl-4">{{$admin->nik}}</td>
                                </tr>
                                <tr>
                                    <td><b>NISN</b></td>
                                    <td class="pl-4">{{$admin->nip}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$admin->tempat_lahir}}, {{\Carbon\Carbon::parse($admin->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$admin->alamat}}</td>
                                </tr>
                                <tr>
                                    <td><b>Asal Sekolah</b></td>
                                    <td class="pl-4">{{$admin->mapel}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$admin->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu</b></td>
                                    <td class="pl-4">{{$admin->ibu}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Wali</b></td>
                                    <td class="pl-4">{{$admin->cv}}</td>
                                </tr>
                                <tr>
                                    <td><b>Markas Yang Dipilih</b></td>
                                    <td class="pl-4">{{$admin->status_dapodik}}</td>
                                </tr>
                            </table>
                                {{-- <a href="" class="btn btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh PDF</a>
                                <a href="" class="btn btn-danger mt-4 ml-3"><i class="fas fa-trash"></i> Hapus Data</a> --}}
                            @else
                            <h4>Data Kosong</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
