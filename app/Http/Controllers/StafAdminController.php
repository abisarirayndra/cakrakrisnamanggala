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
        // $staf = AbsensiStaf::select('roles.role','users.nama','adm_pendidik.foto','adm_absensi_staf.datang','adm_absensi_staf.pulang','adm_absensi_staf.status','adm_absensi_staf.jurnal')
        //                     ->join('users','users.id','=','adm_absensi_staf.staf_id')
        //                     ->join('roles','roles.id','=','users.role_id')
        //                     ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
        //                     ->whereDate('datang', $sekarang)
        //                     ->where('staf_id', Auth::user()->id)
        //                     ->first();
        return view('staf-admin.index', compact('user','data','token','sekarang'));
    }
    public function update($id, Request $request){
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
