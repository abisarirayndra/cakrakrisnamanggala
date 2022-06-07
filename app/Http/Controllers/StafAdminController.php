<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pendidik;
use Auth;
use Validator;
use Alert;
use Carbon\Carbon;

class StafAdminController extends Controller
{
    public function index(){
        $user = Auth::user()->nama;
        $token = Auth::user()->nomor_registrasi;
        $data = Pendidik::where('pendidik_id', Auth::user()->id)->firstOrFail();
        $sekarang = Carbon::now();
        return view('staf-admin.index', compact('user','data','token','sekarang'));
    }
    public function update($id, Request $request){
        // //validate data
        // $validator = Validator::make($request->all(), [
        //     'tempat_lahir' => 'required',
        //     'tanggal_lahir' => 'required|date',
        //     'alamat' => 'required',
        //     'nik' => 'required',
        //     'mapel_id' => 'required',
        //     'wa' => 'required',
        //     'ibu' => 'required',
        //     'foto' => 'required|mimes:jpg,jpeg,png|size:500',
        //     'cv' => 'required|mimes:pdf|size:1000',
        //     'markas' => 'required'
        // ],[
        //     'tempat_lahir.required' => 'Tempat lahir harus diisi',
        //     'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
        //     'tanggal_lahir.date' => 'Format harus berupa tanggal',
        //     'alamat.required' => 'Alamat harus diisi',
        //     'nik.required' => 'NIK harus diisi',
        //     'mapel_id.required' => 'Mapel harus diisi',
        //     'wa.required' => 'WA harus diisi',
        //     'ibu.required' => 'Nama Ibu harus diisi',
        //     'foto.required' => 'Foto harus diisi',
        //     'cv.required' => 'CV harus disertakan',
        //     'foto.mimes' => 'Format foto hanya jpg, jpeg, png !',
        //     'foto.size' => 'Ukuran file terlalu besar, max 500 Kb',
        //     'cv.mimes' => 'Format cv hanya pdf !',
        //     'cv.size' => 'Ukuran file terlalu besar, max 1 Mb',
        //     'markas.required' => 'Markas belum dipilih'
        // ]);
        //     if ($validator->fails()) {
        //         return redirect()->back()->withErrors($validator)->withInput();
        //     }else{
                $id = Auth::user()->id;
                $data = Pendidik::where('pendidik_id', $id)->firstOrFail();
                if($request->file('cv')){
                    $cv = $request->file('cv');
                    $nama_cv = 'cv-admin'.$id.'.'.$request->file('cv')->extension();
                    $path = public_path('pendidik/cv/');
                    $cv->move($path, $nama_cv);
                }
                if($request->file('foto')){
                    $foto = $request->file('foto');
                    $nama_foto = 'staf-admin'.$id.'.'.$request->file('foto')->extension();
                    $path = public_path('pendidik/img/');
                    $foto->move($path, $nama_foto);
                }
                $data->update([
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'nik' => $request->nik,
                    'nip' => $request->nip,
                    'mapel_id' => $request->mapel_id,
                    'wa' => $request->wa,
                    'ibu' => $request->ibu,
                    'foto' => $nama_foto,
                    'cv' => $nama_cv,
                ]);



                Alert::toast('Pembaruan data diri berhasil','success');
                return redirect()->route('staf-admin.beranda');
            // }

    }
}
