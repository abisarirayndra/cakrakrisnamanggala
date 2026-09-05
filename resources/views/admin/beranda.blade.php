@extends('layouts.panel-cakra')

@section('title', 'Beranda Admin - Cakra Krisna Manggala')

@section('content')
    <section class="ck-card p-4 p-md-5">
        <p class="ck-hint mb-2">{{ auth()->user()->isSuperAdmin() ? 'Superadmin' : 'Admin' }}</p>
        <h1 class="h3 mb-2">Selamat datang, {{ auth()->user()->nama }}</h1>
        <p class="ck-hint mb-4">Pilih data yang ingin Anda kelola.</p>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.pengguna.pelajar') }}" class="btn btn-ck">Pelajar</a>
            <a href="{{ route('admin.pengguna.pendidik') }}" class="btn btn-ck">Pendidik</a>
            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.pengguna.admin') }}" class="btn btn-ck-ghost">Admin</a>
                <a href="{{ route('admin.dinas.paket') }}" class="btn btn-ck-ghost">CAT</a>
            @endif
        </div>
    </section>
@endsection
