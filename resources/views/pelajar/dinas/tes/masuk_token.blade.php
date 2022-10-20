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
                <div class="card-body p-4">
                    <div class="text-center mt-4">
                        <div class="text-right mr-3">
                            <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </div>
                        <img src="{{asset('assets/img/favicon.ico')}}" width="50" alt="">
                    </div>
                        <div class="text-center mt-4 mb-4">
                            <h4 class="text-dark"><i class="fas fa-hashtag text-warning"></i> Masukkan Token</h4>
                        </div>
                            <form action="{{ route('pelajar.submit_token') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Masukkan Token" name="token">
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning"> Submit</button>
                                </div>
                            </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
