<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use App\Kelas;

class AuthController extends Controller
{
    public function tampilRegister(){
        $kelas = Kelas::all();
        return view('auth.register', compact('kelas'));
    }

    public function register(Request $request){
       
        $email = $request->email;
        $regis = $request->nomor_registrasi;
        $sudah_email = User::where('email', $email)->first();
        $sudah_regis = User::where('nomor_registrasi', $regis)->first();
        $sudah_semua = User::where('email', $email)->where('nomor_registrasi', $regis)->first();

        if(isset($sudah_email)){
            Alert::error('Email Sudah Terdaftar','Register Gagal');
            return redirect()->route('auth.register');
        }
        elseif(isset($sudah_regis)){
            Alert::error('Nomor Registrasi Sudah Terdaftar','Register Gagal');
            return redirect()->route('auth.register');
        }
        elseif(isset($sudah_semua)){
            Alert::error('Email dan Nomor Registrasi Sudah Terdaftar','Register Gagal');
            return redirect()->route('auth.register');
        }
        else{
            User::create([
                'nama' => $request->nama,
                'nomor_registrasi' => $request->nomor_registrasi,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'whatsapp' => $request->whatsapp,
                'kelas_id' => $request->kelas_id,
            ]);
    
            Alert::success('Registrasi Berhasil');
    
            return redirect()->route('login');
        }
        
    }

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
                Alert::success('Selamat datang Admin');
                return redirect()->route('super.index');
            }
            elseif (auth()->user()->role_id == 2) {
                Alert::success('Selamat datang','Admin');
                return redirect()->route('admin.cat.paket');
            }
            elseif (auth()->user()->role_id == 3) {
                Alert::success('Selamat datang','Pendidik Cakra');
                return redirect()->route('pengajar.cat.paket');
            }
            elseif (auth()->user()->role_id == 4) {
                Alert::success('Selamat datang','Peserta Didik Cakra');
                return redirect()->route('pelajar.cat.paket');
            }
        }
        Alert::error('Akun tidak ditemukan','Gagal');
        return redirect('login');
    }
    public function logout()
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
