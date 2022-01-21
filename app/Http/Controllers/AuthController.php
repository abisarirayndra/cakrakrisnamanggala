<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use App\Kelas;
use Cookie;


class AuthController extends Controller
{
    public function tampilLogin(){
        return view('auth.login');
    }

    public function login(Request $request){
        $logintype = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'nomor_registrasi';

        $credentials = [
            $logintype => $request->email,
            'password' => $request->password,
        ];

        if(Auth::attempt($credentials)){
            if(auth()->user()->role_id == 1){
                Alert::success('Selamat datang Super Admin');
                return redirect()->route('super.beranda');
            }
            elseif (auth()->user()->role_id == 2) {
                Alert::success('Selamat datang','Admin');
                return redirect()->route('admin.dinas.paket');
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
                Alert::toast('Selamat Datang Pendaftar','success');
                return redirect()->route('pendaftar.profil');
            }

        }else {
            return redirect()->route('login')->withErrors(['msg' => 'Akun tidak ditemukan']);
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

    public function upReset(Request $request){
        $email = $request->email;

        $user = User::where('email', $email)->first();

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
