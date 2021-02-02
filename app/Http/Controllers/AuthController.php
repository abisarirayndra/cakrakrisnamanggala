<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function tampilRegister(){
        return view('auth.register');
    }

    public function register(Request $request){
        $request->validate([
            'email' => 'unique:users,email',
            'password' => 'min:8',
        ]);
        
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'whatsapp' => $request->whatsapp,
        ]);

        return redirect()->route('landing');
    }

    public function tampilLogin(){
        return view('auth.login');
    }

    public function login(Request $request){
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if(Auth::attempt($credentials)){
            
            if(auth()->user()->role_id == 1){
                // Alert::success('Selamat datang Administrator');
                return redirect()->route('super.index');
            }
            elseif (auth()->user()->role_id == 3) {
                // Alert::success('Selamat datang Penanggung Jawab');
                return redirect()->route('pengajar.cat.index');
            }
            // elseif (auth()->user()->role_id == 3) {
            //     // Alert::success('Selamat datang Ketua Kelompok');
            //     return redirect()->route('ketua.index');
            // }
        }
        // Alert::error('Akun tidak ditemukan','Gagal');
        return redirect('login');
    }
    public function logout()
    {
        Auth::logout();
        // Alert::success('Kamu berhasil keluar', 'Selamat tinggal!');
        return redirect()->route('login');
    }
}
