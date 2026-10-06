<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use App\Kelas;
use Str;


class AuthController extends Controller
{
    public function login(Request $request){

        $cek = User::where('email', $request->email)->first();

        if(!$cek){
            return redirect()->route('login')->withErrors(['msg' => 'Login gagal, email tidak terdaftar']);
        }elseif(!Hash::check($request->password, $cek->password)){
            return redirect()->route('login')->withErrors(['msg' => 'Login gagal, password salah']);
        }else{
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if(Auth::attempt($credentials, $request->boolean('remember'))){
                $request->session()->regenerate();
                $user = auth()->user();

                if($user->role_id == 1){
                    return abort(403);
                }

                $route = $user->dashboardRouteName();

                if ($route === null) {
                    return;
                }

                if ($user->role_id == 2) {
                    Alert::success('Selamat datang', $user->isSuperAdmin() ? 'Admin' : 'Admin');
                }
                elseif ($user->role_id == 3) {
                    Alert::success('Selamat datang','Pendidik Cakra');
                }
                elseif ($user->role_id == 4) {
                    Alert::success('Selamat datang','Peserta Didik Cakra');
                }

                return redirect()->route($route);
            }

        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Alert::success('Kamu berhasil keluar', 'Selamat tinggal!');
        return redirect()->route('login');
    }

    public function reset(){
        return view('auth.reset');
    }

    public function kirimEmail(Request $request){
        $cek = User::where('email', $request->email)->first();
        if(!$cek){
            return redirect()->back()->withInput($request->only('email'))->with('error','Email Tidak Terdaftar');
        }elseif($cek->nomor_registrasi != $request->token){
            return redirect()->back()->withInput($request->only('email'))->with('error','Token salah, Lihat pada ID Card/Konfirmasi admin markas');
        }else{
            $cek->update([
                'token_reset' => Str::random(40),
            ]);

            return redirect()->route('form_reset')->with([
                'token' => $cek->token_reset,
                'success' => "Akun ditemukan, silakan memperbarui password anda!",
            ]);
        }

    }

    public function formReset(){
        $token = session('token') ?? old('token');

        if (! $token) {
            return redirect()->route('reset');
        }

        return view('auth.reset.form_reset', ['token' => $token]);
    }

    public function upReset(Request $request){
        $user = User::where('token_reset', $request->token)->whereNotNull('token_reset')->first();

        if (! $user) {
            return redirect()->route('reset')->with('error', 'Link reset tidak valid atau sudah dipakai, silakan ulangi.');
        }

        $request->validate([
            'password' => ['required', 'confirmed'],
        ], [
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Password tidak cocok',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'token_reset' => null,
        ]);

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui, silakan masuk.');
    }


}
