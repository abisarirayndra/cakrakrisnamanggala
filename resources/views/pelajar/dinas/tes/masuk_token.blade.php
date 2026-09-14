@extends('layouts.panel-pelajar')

@section('title', 'Masukkan Token - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 p-md-5 mx-auto" style="max-width: 440px;">
        <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Computer Assisted Test</p>
        <h1 class="h4 mb-2">Masukkan Token</h1>
        <p class="ck-hint mb-4">Gunakan token yang diberikan pendidik untuk membuka tes.</p>

        <form action="{{ route('pelajar.submit_token') }}" method="post">
            @csrf
            <label for="token" class="form-label">Token</label>
            <input id="token" type="text" class="form-control mb-3" placeholder="Masukkan Token" name="token" autofocus>
            <button type="submit" class="btn btn-ck w-100">Submit</button>
        </form>
    </section>
@endsection
