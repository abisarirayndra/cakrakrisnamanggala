<?php

namespace App\Livewire\Auth;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest-cakra')]
#[Title('Masuk')]
class FormLogin extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function masuk()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email salah ex. johndoe@gmail.com',
            'password.required' => 'password harus diisi',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Login gagal, email tidak terdaftar');

            return;
        }

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Login gagal, password salah');

            return;
        }

        Auth::login($user, $this->remember);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        if ((int) $user->role_id === 1) {
            abort(403);
        }

        $route = $user->dashboardRouteName();

        if ($route === null) {
            return;
        }

        return $this->redirect(route($route), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.form-login');
    }
}
