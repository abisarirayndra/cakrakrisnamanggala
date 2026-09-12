@extends('layouts.guest-cakra')

@section('content')
    <div class="ck-card overflow-hidden">
        <div class="ck-receipt-head">
            <p class="ck-serif fs-4 mb-1">Bukti Pendaftaran</p>
            <p class="small mb-0 opacity-75">Data pendaftar Cakra Krisna Manggala</p>
        </div>
        <div class="p-4 p-md-5">
            @include('pendaftaran.partials.kartu-bukti', ['data' => $data])
            <p class="ck-hint mt-4 mb-0">Datang ke markas yang dipilih dengan membawa bukti pendaftaran yang sudah dicetak, lalu lanjutkan administrasi di lokasi.</p>
        </div>
    </div>
@endsection
