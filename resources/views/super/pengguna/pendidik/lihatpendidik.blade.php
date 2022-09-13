@extends('master.super')

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Detail Pendidik</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center">
                        <img src="{{asset('/pendidik/img/'. $pendidik->foto)}}" width="150" alt="">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                        <div class="row">
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$pendidik->nama}}</td>
                                </tr>
                                <tr>
                                    <td><b>Email</b></td>
                                    <td class="pl-4">{{$pendidik->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    <td class="pl-4">{{$pendidik->nik}}</td>
                                </tr>
                                <tr>
                                    <td><b>NISN</b></td>
                                    <td class="pl-4">{{$pendidik->nip}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$pendidik->tempat_lahir}}, {{\Carbon\Carbon::parse($pendidik->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$pendidik->alamat}}</td>
                                </tr>

                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$pendidik->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu</b></td>
                                    <td class="pl-4">{{$pendidik->ibu}}</td>
                                </tr>
                                <tr>
                                    <td><b>Markas</b></td>
                                    <td class="pl-4">{{$pendidik->markas}}</td>
                                </tr>
                            </table>


                        </div>
                        <div class="row">
                            {{-- <a href="{{ route('super.penggunapelajar.editdata', [$pelajar->id]) }}" class="btn btn-success mt-4"><i class="fas fa-pen"></i> Edit Data</a>
                            <a href="{{ route('super.penggunapelajar.cetak-pdf', [$pelajar->id]) }}" class="btn btn-warning mt-4 ml-3"><i class="fas fa-print"></i> Cetak</a>
                            <a href="{{ route('super.penggunapelajar.suspend', [$pelajar->id]) }}" class="btn btn-danger mt-4 ml-3" onclick="return confirm('Anda yakin ingin suspend akun ini ?')"><i class="fas fa-ban"></i> Suspend</a>
                            <a href="{{ route('super.penggunapelajar.hapus', [$pelajar->id]) }}" class="btn btn-danger mt-4 ml-3" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i> Hapus Data</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


