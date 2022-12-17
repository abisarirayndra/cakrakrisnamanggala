@extends('master.admin')

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
            <h5><i class="fas fa-hashtag text-warning"></i> Welcome to Mission Control, Sir !</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    {{-- <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('pelajar.dinas.paket') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Paket Soal</h6>
                        </a>
                    </div> --}}
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('super.administrasi') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-user-cog fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Administrasi</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('admin.dinas.paket') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">CAT</h6>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
