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
    public function tampilLogin(){
        return view('auth.login');
    }

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

            if(Auth::attempt($credentials)){
                if(auth()->user()->role_id == 1){
                    return abort(403);
                }
                elseif (auth()->user()->role_id == 2) {
                    Alert::success('Selamat datang','Admin');
                    return redirect()->route('admin.beranda');
                }
                elseif (auth()->user()->role_id == 3) {
                    Alert::success('Selamat datang','Pendidik Cakra');
                    return redirect()->route('pendidik.dinas.beranda');
                }
                elseif (auth()->user()->role_id == 4) {
                    Alert::success('Selamat datang','Peserta Didik Cakra');
                    return redirect()->route('pelajar.dinas.beranda');
                }
                elseif(auth()->user()->role_id == 5){
                    return redirect()->route('pendaftar.profil');
                }
                elseif(auth()->user()->role_id == 7 ){
                    Alert::toast('Selamat Datang Staf Admin','success');
                    return redirect()->route('staf-admin.beranda');
                }
            }

        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        Alert::success('Kamu berhasil keluar', 'Selamat tinggal!');
        return redirect()->route('login');
    }

    public function reset(){
        return view('auth.reset');
    }

    public function kirimEmail(Request $request){
        $cek = User::where('email', $request->email)->first();
        if(!$cek){
            return redirect()->back()->with('error','Email Tidak Terdaftar');
        }elseif($cek->nomor_registrasi != $request->token){
            return redirect()->back()->with('error','Token salah, Lihat pada ID Card/Konfirmasi admin markas');
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
        return view('auth.reset.form_reset');
    }

    public function upReset(Request $request){
        $token = $request->token;

        $user = User::where('token_reset', $token)->first();

        if(isset($user)){
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            Alert::success('Reset Berhasil');
            return redirect()->route('login');
        }
        else {
            Alert::error('Email Tidak Ditemukan', 'Reset Gagal');
        }
    }


}
