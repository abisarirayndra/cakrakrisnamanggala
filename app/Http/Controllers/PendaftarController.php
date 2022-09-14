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
use App\Markas;
use App\Mapel;
use App\Pendidik;
use File;
use Str;

class PendaftarController extends Controller
{
    public function petunjuk(){
        return view('pendaftaran.pre-daftar.petunjuk');
    }

    public function registerEmail(){
        return view('pendaftaran.pre-daftar.register');
    }

    public function uploadRegisterEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ],[
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email salah ex. johndoe@gmail.com',
            'email.users' => 'Email sudah terdaftar, coba login atau daftar dengan email lain',
            'password.required' => 'password harus diisi'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $userMake = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 5,
            ]);

            Pelajar::create([
                'pelajar_id' => $userMake->id,
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
    }

    public function profil(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $ada = Pelajar::where('pelajar_id', $id)->whereNotNull('tempat_lahir')->first();
        $markas = Markas::all();


        return view('pendaftaran.profil', compact('user','ada','markas'));
    }

    public function upFormulirPendaftar(Request $request){
        $validator = Validator::make($request->all(), [
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'nik' => 'required',
            'nisn' => 'required',
            'wa' => 'required',
            'ibu' => 'required',
            'wali' => 'required',
            'wa_wali' => 'required',
            'foto' => 'required|mimes:jpg,jpeg,png|max:512',
            'markas_id' => 'required',
            'sekolah' => 'required',
            'status_sekolah' => 'required'
        ],[
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.date' => 'Format harus berupa tanggal',
            'alamat.required' => 'Alamat harus diisi',
            'nik.required' => 'NIK harus diisi',
            'nisn.required' => 'NISN harus diisi',
            'wa.required' => 'WA harus diisi',
            'wali.required' => 'Nama wali harus diisi',
            'wa_wali.required' => 'Nomor WA Wali harus disertakan',
            'ibu.required' => 'Nama Ibu harus diisi',
            'foto.required' => 'Foto harus diisi',
            'foto.mimes' => 'Format foto hanya jpg, jpeg, png !',
            'foto.max' => 'Ukuran file terlalu besar, max 500 Kb',
            'markas_id.required' => 'Markas belum dipilih',
            'sekolah.required' => 'Asal sekolah harus diisi',
            'status_sekolah.required' => 'Status sekolah harus dipilih',
        ]);
        $user = Auth::user()->id;
        $image_name = 'Pelajar'.$user.'.'.$request->file('foto')->extension();
        $update_nama = User::find($user);
        $update_nama->update([
            'nama' => $request->nama,
        ]);
        $ada = Pelajar::where('pelajar_id',$user)->first();
            $ada->update([
                'pelajar_id' => $user,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'sekolah' => $request->sekolah,
                'wa' => $request->wa,
                'wali' => $request->wali,
                'wa_wali' => $request->wa_wali,
                'foto' => $image_name,
                'markas_id' => $request->markas_id,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'ibu' =>  $request->ibu,
                'status_sekolah' => $request->status_sekolah,
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
        $data = Pelajar::select('adm_markas.markas','adm_pelajars.id','adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat',
                                'adm_pelajars.sekolah','adm_pelajars.status_sekolah','adm_pelajars.wa','adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.ibu',
                                'adm_pelajars.created_at','adm_pelajars.foto')
                        ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
                        ->where('adm_pelajars.id',$id)->first();

        return view('pendaftaran.cetak', compact('data','user'));
    }

    public function cetak_pdf($id)
    {
        $user = Auth::user()->nama;
        $pendaftar = Pelajar::select('adm_markas.markas','adm_pelajars.id','adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat',
        'adm_pelajars.sekolah','adm_pelajars.status_sekolah','adm_pelajars.wa','adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.ibu',
        'adm_pelajars.created_at','adm_pelajars.foto')
->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
->where('adm_pelajars.id',$id)->first();
        $en_foto = (string) Image::make(public_path('img/pelajar/'. $pendaftar->foto))->encode('data-url');
        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendaftaran.review', ['data' => $pendaftar, 'user' => $user,'foto' => $en_foto, 'logo' => $en_logo])->setPaper('a4');
        return $pdf->stream();
    }

    public function editPendaftar($id){
        $user = Auth::user()->nama;
        $data = Pelajar::find($id);
        $markas = Markas::all();

        return view('pendaftaran.edit', compact('user','data','markas'));

    }

    public function updatePendaftar($id, Request $request){
        $user = Auth::user()->id;
        $data = Pelajar::find($id);

        if($request->file('foto')){
            $validator = Validator::make($request->all(), [
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required',
                'nik' => 'required',
                'nisn' => 'required',
                'wa' => 'required',
                'ibu' => 'required',
                'wali' => 'required',
                'wa_wali' => 'required',
                'foto' => 'required|mimes:jpg,jpeg,png|max:512',
                'markas_id' => 'required',
                'sekolah' => 'required'
            ],[
                'tempat_lahir.required' => 'Tempat lahir harus diisi',
                'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
                'tanggal_lahir.date' => 'Format harus berupa tanggal',
                'alamat.required' => 'Alamat harus diisi',
                'nik.required' => 'NIK harus diisi',
                'nisn.required' => 'NISN harus diisi',
                'wa.required' => 'WA harus diisi',
                'wali.required' => 'Nama wali harus diisi',
                'wa_wali.required' => 'Nomor WA Wali harus disertakan',
                'ibu.required' => 'Nama Ibu harus diisi',
                'foto.required' => 'Foto harus diisi',
                'foto.mimes' => 'Format foto hanya jpg, jpeg, png !',
                'foto.max' => 'Ukuran file terlalu besar, max 500 Kb',
                'markas_id.required' => 'Markas belum dipilih',
                'sekolah.required' => 'Asal sekolah harus diisi',
                'status_sekolah.required' => 'Status sekolah harus dipilih',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{

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
                    'markas_id' => $request->markas_id,
                    'nik' => $request->nik,
                    'nisn' => $request->nisn,
                    'ibu' =>  $request->ibu,
                    'status_sekolah' => $request->status_sekolah,
                ]);
            }
        }else{
            $validator = Validator::make($request->all(), [
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required',
                'nik' => 'required',
                'nisn' => 'required',
                'wa' => 'required',
                'ibu' => 'required',
                'wali' => 'required',
                'wa_wali' => 'required',
                'markas_id' => 'required',
                'sekolah' => 'required'
            ],[
                'tempat_lahir.required' => 'Tempat lahir harus diisi',
                'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
                'tanggal_lahir.date' => 'Format harus berupa tanggal',
                'alamat.required' => 'Alamat harus diisi',
                'nik.required' => 'NIK harus diisi',
                'nisn.required' => 'NISN harus diisi',
                'wa.required' => 'WA harus diisi',
                'wali.required' => 'Nama wali harus diisi',
                'wa_wali.required' => 'Nomor WA Wali harus disertakan',
                'ibu.required' => 'Nama Ibu harus diisi',
                'markas_id.required' => 'Markas belum dipilih',
                'sekolah.required' => 'Asal sekolah harus diisi',
                'status_sekolah.required' => 'Status sekolah harus dipilih',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $data->update([
                        'pelajar_id' => $user,
                        'tempat_lahir' => $request->tempat_lahir,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'alamat' => $request->alamat,
                        'sekolah' => $request->sekolah,
                        'wa' => $request->wa,
                        'wali' => $request->wali,
                        'wa_wali' => $request->wa_wali,
                        'markas_id' => $request->markas_id,
                        'nik' => $request->nik,
                        'nisn' => $request->nisn,
                        'ibu' =>  $request->ibu,
                        'status_sekolah' => $request->status_sekolah,
                ]);
            }
        }

        return redirect()->route('pendaftar.cetak-formulir', $id);
    }

    public function registerPendidik(Request $request){
        $markas = Markas::all();
        $mapel = Mapel::all();
        return view('pendaftaran.pre-daftar.registrasi-pendidik', compact('markas','mapel'));
    }

    public function upRegisterPendidik(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'nik' => 'required',
            'wa' => 'required',
            'ibu' => 'required',
            'mapel_id' => 'required',
            'markas_id' => 'required',
            'foto' => 'mimes:jpeg,jpg,png'
        ],[
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email salah ex. johndoe@gmail.com',
            'email.users' => 'Email sudah terdaftar, coba login atau daftar dengan email lain',
            'password.required' => 'password harus diisi',
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.date' => 'Format harus berupa tanggal',
            'alamat.required' => 'Alamat harus diisi',
            'nik.required' => 'NIK harus diisi',
            'wa.required' => 'WA harus diisi',
            'wali.required' => 'Nama wali harus diisi',
            'ibu.required' => 'Nama Ibu harus diisi',
            'markas_id.required' => 'Markas belum dipilih',
            'mapel_id.required' => 'Mapel belum dipilih',
            'foto.mimes' => 'Format foto harus .jpg, .png, .jpeg'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
           $userMake = User::create([
                'nama' => $request->nama,
                'nomor_registrasi' => Str::random(6),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' =>  3,
            ]);

            $image_name = 'Pendidik-'.$userMake->id.'.'.$request->file('foto')->extension();
            Pendidik::create([
                'pendidik_id' => $userMake->id,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'wa' => $request->wa,
                'foto' => $image_name,
                'markas_id' => $request->markas_id,
                'mapel_id' => $request->mapel_id,
                'nik' => $request->nik,
                'nip' => $request->nip,
                'ibu' =>  $request->ibu,
            ]);

        $image = $request->file('foto');
        $path = public_path('pendidik/img/');
        $image->move($path, $image_name);
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
            if(Auth::attempt($credentials)){
                if (auth()->user()->role_id == 3) {
                    Alert::success('Selamat datang','Pendidik Cakra');
                    return redirect()->route('pendidik.dinas.beranda');
                }
            }
        }
    }
}
