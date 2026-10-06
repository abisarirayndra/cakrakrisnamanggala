@extends('layouts.guest-cakra')

@section('title', 'Lupa Password')

@section('content')
<div class="ck-card ck-login-card p-4 p-md-5 mx-auto">
    <div class="text-center mb-4">
        <img src="{{ asset('img/krisna.png') }}" width="72" height="72" alt="Cakra Krisna Manggala">
        <p class="ck-hint mt-3 mb-1">Sistem E-Learning Terpadu</p>
        <h1 class="h4 mb-2">Lupa password</h1>
        <p class="ck-hint small mb-0">Masukkan email dan nomor registrasi yang tertera pada ID card.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-ck mb-4" role="alert">{{ session('error') }}</div>
    @endif

    <form action="{{ route('submit_email') }}" method="post" class="row g-3">
        @csrf
        <div class="col-12">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@email.com" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="token">Nomor registrasi</label>
            <input id="token" type="text" class="form-control" name="token" placeholder="Token / No. ID" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-ck w-100">Lanjutkan</button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a class="ck-hint text-decoration-none" href="{{ route('login') }}">Kembali ke halaman masuk</a>
    </div>
</div>
@endsection
