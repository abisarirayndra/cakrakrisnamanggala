@extends('layouts.guest-cakra')

@section('title', 'Reset Password')

@section('content')
<div class="ck-card ck-login-card p-4 p-md-5 mx-auto">
    <div class="text-center mb-4">
        <img src="{{ asset('img/krisna.png') }}" width="72" height="72" alt="Cakra Krisna Manggala">
        <p class="ck-hint mt-3 mb-1">Sistem E-Learning Terpadu</p>
        <h1 class="h4 mb-0">Buat password baru</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4" role="alert">{{ session('success') }}</div>
    @endif

    <form
        action="{{ route('upreset') }}"
        method="post"
        class="row g-3"
        x-data="{ show: false, password: '', konfirmasi: '' }"
    >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="col-12">
            <label class="form-label" for="password">Password baru</label>
            <div class="input-group">
                <input id="password" :type="show ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" name="password" x-model="password" autocomplete="new-password" required>
                <button class="btn btn-outline-secondary" type="button" @click="show = !show" aria-label="Tampilkan password">
                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>
            @error('password') <div class="text-danger ck-error small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <label class="form-label" for="password_confirmation">Ulangi password</label>
            <input id="password_confirmation" :type="show ? 'text' : 'password'" class="form-control" name="password_confirmation" x-model="konfirmasi" autocomplete="new-password" required>
            <small
                class="d-block mt-1"
                x-show="konfirmasi.length"
                x-cloak
                :class="password === konfirmasi ? 'text-success' : 'text-danger ck-error'"
                x-text="password === konfirmasi ? 'Password cocok' : 'Password tidak cocok'"
            ></small>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-ck w-100" :disabled="!password || password !== konfirmasi">Simpan password</button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a class="ck-hint text-decoration-none" href="{{ route('login') }}">Kembali ke halaman masuk</a>
    </div>
</div>
@endsection
