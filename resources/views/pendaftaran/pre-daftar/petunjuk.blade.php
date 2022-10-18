@extends('master.admin1')

@section('title')
    <title>Petunjuk Pendaftaran - Cakra Krisna Manggala</title>
@endsection

@section('content')
 <body class="bg-gradient-warning">
    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-8 col-lg-8 col-md-7">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="text-right pt-3 pr-3">
                            <a href="" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <div class="text-center mt-4">
                            <img src="{{asset('assets/img/favicon.ico')}}" width="50" alt="">

                        </div>
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Petunjuk Pendaftaran</h4>
                        </div>
                        <ul>
                            <li>Registrasi dengan mengisi kebutuhan data</li>
                            <li>Cetak formulir yang sudah diisi dengan mendownload file formulir pada sistem</li>
                            <li>Setalah download file, print file dengan kertas A4 sebagai bukti pendaftaran</li>
                            <li>Datang ke markas Cakra Krisna Manggala yang dituju dengan membawa bukti pendaftaran</li>
                            <li>Melakukan tahap administrasi selanjutnya di markas Cakra Krisna Manggala</li>
                        </ul>
                        <div class="text-center mb-4 mt-4">
                            <a href="{{route('register-email')}}" class="btn btn-warning">Registrasi Email</a>
                            {{-- <hr> --}}
                            {{-- <h6>Sudah Pernah Mendaftar ?</h6>
                            <a href="{{ route('login') }}" class="btn btn-success">Ya, Login</a> --}}
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    @endsection

