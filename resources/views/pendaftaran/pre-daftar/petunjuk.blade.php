@extends('layouts.guest-cakra')

@section('title', 'Petunjuk Pendaftaran')

@section('content')
    <div class="ck-card p-4 p-md-5">
        <p class="ck-hint mb-2">Lembaga Kursus dan Pelatihan</p>
        <h1 class="h3 mb-3">Petunjuk pendaftaran</h1>
        <p class="ck-hint mb-4">Isi data sesuai identitas, cetak bukti, lalu datang ke markas yang dipilih.</p>
        <ol class="ck-hint ps-3 mb-4">
            <li class="mb-2">Registrasi akun dan lengkapi data pelajar.</li>
            <li class="mb-2">Unduh atau simpan bukti pendaftaran (kartu + QR).</li>
            <li class="mb-2">Cetak bukti pada kertas A4.</li>
            <li class="mb-2">Datang ke markas Cakra Krisna Manggala yang dituju.</li>
            <li>Lanjutkan administrasi di markas.</li>
        </ol>
        <a href="{{ route('register-email') }}" class="btn btn-ck">Mulai registrasi</a>
    </div>
@endsection
