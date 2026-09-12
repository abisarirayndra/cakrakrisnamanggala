<div class="ck-card ck-login-card p-4 p-md-5 mx-auto">
    <div class="text-center mb-4">
        <img src="{{ asset('img/krisna.png') }}" width="72" height="72" alt="Cakra Krisna Manggala">
        <p class="ck-hint mt-3 mb-1">Sistem E-Learning Terpadu</p>
        <h1 class="h4 mb-0">Cakra Krisna Manggala</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-ck mb-4" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form wire:submit.prevent="masuk" class="row g-3">
        <div class="col-12">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" autocomplete="username" placeholder="nama@email.com">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12" x-data="{ show: false }">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
                <input id="password" :type="show ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" wire:model="password" autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" @click="show = !show" aria-label="Tampilkan password">
                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>
            @error('password') <div class="text-danger ck-error small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input id="remember" class="form-check-input" type="checkbox" wire:model="remember">
                <label class="form-check-label ck-hint" for="remember">Ingat saya</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-ck w-100" wire:loading.attr="disabled">Masuk</button>
        </div>
    </form>

    <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
        <a class="ck-hint text-decoration-none" href="{{ route('reset') }}">Lupa password?</a>
        <a class="ck-hint text-decoration-none" href="{{ route('petunjuk') }}">Daftar peserta didik</a>
    </div>
</div>
