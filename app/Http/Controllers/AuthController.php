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
        $request->validate([
            'email' => 'unique:users,email',
            'password' => 'min:8',
        ]);
        
        User::create([
            'nama' => $request->nama,
            'nomor_registrasi' => $request->nomor_registrasi,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'whatsapp' => $request->whatsapp,
        ]);

        Alert::success('Registrasi Berhasil');

        return redirect()->route('login');
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
                // Alert::success('Selamat datang Administrator');
                return redirect()->route('super.index');
            }
            elseif (auth()->user()->role_id == 3) {
                Alert::success('Selamat datang Pengajar');
                return redirect()->route('pengajar.cat.tema');
            }
            elseif (auth()->user()->role_id == 4) {
                Alert::success('Selamat datang pelajar cakra');
                return redirect()->route('pelajar.cat.tema');
            }
        }
        // Alert::error('Akun tidak ditemukan','Gagal');
        return redirect('login');
    }
    public function logout()
    {
        Auth::logout();
        Alert::success('Kamu berhasil keluar', 'Selamat tinggal!');
        return redirect()->route('login');
    }
}
