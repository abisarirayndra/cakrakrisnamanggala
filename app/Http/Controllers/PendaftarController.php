<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;
use Validator;
use Auth;
use App\Pelajar;
use Alert;
use Image;
use PDF;

class PendaftarController extends Controller
{
    public function petunjuk(){
        return view('pendaftaran.pre-daftar.petunjuk');
    }

    public function registerEmail(){
        return view('pendaftaran.pre-daftar.register');
    }

    public function uploadRegisterEmail(Request $request){
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        // ,[
        //     'nama.required' => 'Nama harus diisi',
        //     'email.required' => 'Email harus diisi',
        //     'email.email' => 'Format email salah ex. johndoe@gmail.com',
        //     'email.users' => 'Email sudah terdaftar, coba login atau daftar dengan email lain',
        //     'password.required' => 'password harus diisi'
        // ]


            User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 5,
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if(Auth::attempt($credentials)){
                if(auth()->user()->role_id == 5){
                    Alert::toast('Selamat Datang Pendaftar','success');
                    return redirect()->route('pendaftar.profil');
                }
            }
            Alert::error('Akses tidak diizinkan','Gagal');
            return redirect()->route('login');
    }

    public function profil(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $ada = Pelajar::where('pelajar_id', $id)->first();


        return view('pendaftaran.profil', compact('user','ada'));
    }

    public function upFormulirPendaftar(Request $request){
        $request->validate([
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'sekolah' => 'required',
            'wa' => 'required',
            'wali' => 'required',
            'wa_wali' => 'required',
            'foto' => 'required|mimes:jpg,jpeg,png|max:500',
            'markas' => 'required',
            'nik' => 'required|max:20',
            'nisn' => 'required',
            'ibu' =>  'required',
        ]);
        $user = Auth::user()->id;
        $image_name = 'Pelajar'.$user.'.'.$request->file('foto')->extension();
        Pelajar::create([
            'pelajar_id' => $user,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'sekolah' => $request->sekolah,
            'wa' => $request->wa,
            'wali' => $request->wali,
            'wa_wali' => $request->wa_wali,
            'foto' => $image_name,
            'markas' => $request->markas,
            'nik' => $request->nik,
            'nisn' => $request->nisn,
            'ibu' =>  $request->ibu,
        ]);

        $image = $request->file('foto');
        $path = public_path('img/pelajar/');
        $image->move($path, $image_name);

        $id = Pelajar::where('pelajar_id', $user)->first();
        $user_id = $id->id;

        Alert::toast('Data Pendaftar Disimpan','success');
        return redirect()->route('pendaftar.cetak-formulir', $user_id);
    }

    public function cetak($id){
        $user = Auth::user()->nama;
        $data = Pelajar::find($id);

        return view('pendaftaran.cetak', compact('data','user'));
    }

    public function cetak_pdf($id)
    {
        $user = Auth::user()->nama;
        $pendaftar = Pelajar::find($id);
        $en_foto = (string) Image::make(public_path('img/pelajar/'. $pendaftar->foto))->encode('data-url');
        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendaftaran.review', ['data' => $pendaftar, 'user' => $user,'foto' => $en_foto, 'logo' => $en_logo])->setPaper('a4');
        return $pdf->stream();
    }

    public function editPendaftar($id){
        $user = Auth::user()->nama;
        $data = Pelajar::find($id);
        return view('pendaftaran.edit', compact('user','data'));

    }

    public function updatePendaftar($id, Request $request){
        $user = Auth::user()->id;
        $data = Pelajar::find($id);

        if($request->file('foto')){
            $request->validate([
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'alamat' => 'required',
                'sekolah' => 'required',
                'wa' => 'required',
                'wali' => 'required',
                'wa_wali' => 'required',
                'foto' => 'required|mimes:jpg,jpeg,png|max:500',
                'markas' => 'required',
                'nik' => 'required|max:20',
                'nisn' => 'required',
                'ibu' =>  'required',
            ]);

            $new_photo = $request->file('foto');
            if($data->foto && file_exists(public_path('img/pelajar/'. $data->foto))){
                File::delete(public_path('img/pelajar/'. $data->foto));
            }
            $images = 'Pelajarbaru'.$id.'.'.$request->file('foto')->extension();
            Image::make($new_photo)->save(public_path('img/pelajar/' . $images));

            $data->update([
                'pelajar_id' => $user,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'sekolah' => $request->sekolah,
                'wa' => $request->wa,
                'wali' => $request->wali,
                'wa_wali' => $request->wa_wali,
                'foto' => $images,
                'markas' => $request->markas,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'ibu' =>  $request->ibu,
            ]);

        }

        $request->validate([
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'sekolah' => 'required',
            'wa' => 'required',
            'wali' => 'required',
            'wa_wali' => 'required',
            'markas' => 'required',
            'nik' => 'required|max:20',
            'nisn' => 'required',
            'ibu' =>  'required',
        ]);

        $data->update([
                'pelajar_id' => $user,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'sekolah' => $request->sekolah,
                'wa' => $request->wa,
                'wali' => $request->wali,
                'wa_wali' => $request->wa_wali,
                'markas' => $request->markas,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'ibu' =>  $request->ibu,
        ]);

        return redirect()->route('pendaftar.cetak-formulir', $id);
    }
}
