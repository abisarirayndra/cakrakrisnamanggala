<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\ArsipNilai;
use App\Penilaian;
use App\Kelas;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\SoalDinasEssay;
use App\JawabanGandaDinas;
use DB;
use App\User;
use Image;
use PDF;

class ArsipController extends Controller
{
    public function analisis(){
        $user = Auth::user()->nama;
        $arsip = ArsipNilai::select('dn_tes.id','mapels.mapel','dn_arsipnilai.tanggal','dn_arsipnilai.kode')
                            ->join('dn_tes','dn_tes.id','=','dn_arsipnilai.dn_tes_id')
                            ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                            ->orderBy('tanggal','desc')
                            ->get();
        // return $arsip;
        return view('pendidik.dinas.analisis.analisis', compact('arsip','user'));
    }

    public function hasil(Request $request){
        $user = Auth::user()->nama;
        $arsip = $request->token;
        $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('status', $request->token)
                        ->orderBy('nilai','desc')
                        ->get();
            $selected = "";
        $kelas = Kelas::all();
        if($request->kelas){
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('status', $request->token)
                        ->where('users.kelas_id', $request->kelas)
                        ->orderBy('nilai','desc')
                        ->get();
            $selected = $request->kelas;
        }
        return view('pendidik.dinas.analisis.hasil',  compact('user','nilai','kelas','selected','arsip'));
    }

    public function soal(Request $request){
        $user = Auth::user()->nama;
        $ganda = SoalDinasGanda::where('dn_tes_id', $request->tes)->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $request->tes)->first();
        $essay = SoalDinasEssay::where('dn_tes_id', $request->tes)->first();

        if($ganda){
            // $soal = SoalDinasGanda::leftJoin('dn_jawabanganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
            //                         // ->where('dn_soalganda.dn_tes_id', $request->tes)
            //                         // ->where('dn_jawabanganda.status', $request->token)
            //                         ->orderBy('dn_soalganda.nomor_soal', 'asc')
            //                         ->get();
            $soal = SoalDinasGanda::orderBy('dn_soalganda.nomor_soal', 'asc')
                    ->leftJoin('dn_jawabanganda', function($join){
                    $join->on('dn_jawabanganda.dn_soalganda_id','=','dn_soalganda.id');
            })
            // ->where('dn_soalganda.dn_tes_id', $request->tes)
            // ->where('dn_jawabanganda.status', $request->token)
            ->get();
            return $soal;
        }
    }

    public function pelajar(Request $request){
        $user = Auth::user()->nama;
        $ganda = SoalDinasGanda::where('dn_tes_id', $request->tes)->first();
        // $poin = SoalDinasGandaPoin::where('dn_tes_id', $request->tes)->first();
        // $essay = SoalDinasEssay::where('dn_tes_id', $request->tes)->first();
        if($ganda){
            $pelajar = JawabanGandaDinas::select('users.id','users.nama','dn_jawabanganda.status','dn_soalganda.dn_tes_id')
                                ->join('users','users.id','=','dn_jawabanganda.pelajar_id')
                                ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                ->where('status', $request->token)
                                ->distinct()
                                ->get();
            // return $pelajar;
            return view('pendidik.dinas.analisis.pelajar', compact('pelajar','user'));
        }
    }

    public function jawabanPelajar(Request $request){
        $user = Auth::user()->nama;
        $ganda = SoalDinasGanda::where('dn_tes_id', $request->tes)->first();
        // $poin = SoalDinasGandaPoin::where('dn_tes_id', $request->tes)->first();
        // $essay = SoalDinasEssay::where('dn_tes_id', $request->tes)->first();
        $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama as pelajar','kelas.nama as kelas')
                            ->where('users.id',$request->auth)
                            ->first();

        if($ganda){
            $jawaban = SoalDinasGanda::leftJoin('dn_jawabanganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $request->tes)
                                            ->where('dn_jawabanganda.status', $request->token)
                                            ->where('dn_jawabanganda.pelajar_id', $request->auth)
                                            ->orderBy('dn_soalganda.nomor_soal', 'asc')
                                            ->get();
            $soal = SoalDinasGanda::select('id')->where('dn_tes_id', $request->tes)->count();
            $soal_terjawab = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $request->tes)
                                            ->where('dn_jawabanganda.pelajar_id', $request->auth)
                                            ->where('dn_jawabanganda.status', $request->token)
                                            ->whereNotNull('dn_jawabanganda.jawaban')
                                            ->count();
            $nilai = Penilaian::where('dn_penilaians.dn_tes_id', $request->tes)
                                ->where('dn_penilaians.pelajar_id', $request->auth)
                                ->where('dn_penilaians.status', $request->token)
                                ->first();
            // return $jawaban;
            return view('pendidik.dinas.analisis.jawabanpelajar', compact('user','pelajar','jawaban','soal','soal_terjawab','nilai'));
        }

    }
    public function cetakJawaban(Request $request){
            $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama as pelajar','kelas.nama as kelas')
                            ->where('users.id',$request->auth)
                            ->first();
            $jawaban = SoalDinasGanda::leftJoin('dn_jawabanganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $request->tes)
                                            ->where('dn_jawabanganda.status', $request->token)
                                            ->where('dn_jawabanganda.pelajar_id', $request->auth)
                                            ->orderBy('dn_soalganda.nomor_soal', 'asc')
                                            ->get();
            $soal = SoalDinasGanda::select('id')->where('dn_tes_id', $request->tes)->count();
            $soal_terjawab = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $request->tes)
                                            ->where('dn_jawabanganda.pelajar_id', $request->auth)
                                            ->where('dn_jawabanganda.status', $request->token)
                                            ->whereNotNull('dn_jawabanganda.jawaban')
                                            ->count();
            $nilai = Penilaian::where('dn_penilaians.dn_tes_id', $request->tes)
                                ->where('dn_penilaians.pelajar_id', $request->auth)
                                ->where('dn_penilaians.status', $request->token)
                                ->first();

            $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
            $pdf = PDF::loadview('pendidik.dinas.analisis.cetakjawaban', ['soal'=>$soal,'logo'=>$en_logo,'pelajar'=>$pelajar,'soal_terjawab'=>$soal_terjawab,'jawaban'=>$jawaban,'nilai'=>$nilai])->setPaper('a4');
            return $pdf->stream();
    }

}
