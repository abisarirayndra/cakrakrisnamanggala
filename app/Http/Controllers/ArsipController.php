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
use App\JawabanGandaPoinDinas;
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
                            ->where('dn_arsipnilai.pendidik_id', Auth::user()->id)
                            ->orderBy('tanggal','desc')
                            ->get();
        // return $arsip;
        return view('pendidik.dinas.analisis.analisis', compact('arsip','user'));
    }

    public function hasil(Request $request){
        $user = Auth::user()->nama;
        $arsip = $request->token;
        $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas','dn_penilaians.created_at')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('status', $request->token)
                        ->orderBy('nilai','desc')
                        ->get();
            $selected = "";
        $kelas = Kelas::all();
        if($request->kelas){
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas','dn_penilaians.created_at')
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

    public function cetakHasil(Request $request){
        if ($request->kelas == ""){
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas', 'dn_penilaians.created_at')
                                ->join('users','users.id','=','dn_penilaians.pelajar_id')
                                ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                                ->join('kelas','kelas.id','=','users.kelas_id')
                                ->where('status', $request->token)
                                ->orderBy('nilai','desc')
                                ->get();
        }
        else{
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas', 'dn_penilaians.created_at')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('status', $request->token)
                        ->where('users.kelas_id', $request->kelas)
                        ->orderBy('nilai','desc')
                        ->get();
        }
        $arsip = ArsipNilai::join('dn_tes','dn_tes.id','=','dn_arsipnilai.dn_tes_id')
                            ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                            ->select('mapels.mapel')
                            ->where('dn_arsipnilai.kode', $request->token)->first();

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendidik.dinas.analisis.cetakhasil', ['nilai'=>$nilai,'logo'=>$en_logo,'arsip'=>$arsip])->setPaper('a4','landscape');
        return $pdf->stream();
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
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $request->tes)->first();
        // $essay = SoalDinasEssay::where('dn_tes_id', $request->tes)->first();
        if($ganda){
            $pelajar = JawabanGandaDinas::select('users.id','users.nama','dn_jawabanganda.status','dn_soalganda.dn_tes_id','kelas.nama as kelas')
                                ->join('users','users.id','=','dn_jawabanganda.pelajar_id')
                                ->join('kelas','kelas.id','=','users.kelas_id')
                                ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                ->where('status', $request->token)
                                ->distinct()
                                ->get();
            $jenis = 1;
            return view('pendidik.dinas.analisis.pelajar', compact('pelajar','user','jenis'));
        }elseif($poin){
            $pelajar = JawabanGandaPoinDinas::select('users.id','users.nama','dn_jawabangandapoin.status','dn_soalgandapoin.dn_tes_id','kelas.nama as kelas')
                                ->join('users','users.id','=','dn_jawabangandapoin.pelajar_id')
                                ->join('kelas','kelas.id','=','users.kelas_id')
                                ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                ->where('status', $request->token)
                                ->distinct()
                                ->get();
            $jenis = 2;
            return view('pendidik.dinas.analisis.pelajar', compact('pelajar','user','jenis'));
        }
    }

    public function jawabanPelajar(Request $request){
        $user = Auth::user()->nama;
        $ganda = SoalDinasGanda::where('dn_tes_id', $request->tes)->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $request->tes)->first();
        // $essay = SoalDinasEssay::where('dn_tes_id', $request->tes)->first();
        $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama as pelajar','kelas.nama as kelas')
                            ->where('users.id',$request->auth)
                            ->first();

        if($ganda){
            $jawaban = SoalDinasGanda::select('dn_soalganda.soal','dn_soalganda.nomor_soal','dn_jawabanganda.nilai','dn_jawabanganda.jawaban',
                                                'dn_soalganda.opsi_a', 'dn_soalganda.opsi_b', 'dn_soalganda.opsi_b', 'dn_soalganda.opsi_c', 'dn_soalganda.opsi_d'
                                                ,'dn_soalganda.opsi_e', 'dn_soalganda.kunci')
                                            ->join('dn_jawabanganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
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
            $jenis = 1;
        }elseif($poin){
            $jawaban = SoalDinasGandaPoin::select('dn_soalgandapoin.soal','dn_soalgandapoin.nomor_soal','dn_jawabangandapoin.nilai','dn_jawabangandapoin.jawaban',
                                                    'dn_soalgandapoin.opsi_a','dn_soalgandapoin.opsi_b','dn_soalgandapoin.opsi_b', 'dn_soalgandapoin.opsi_c', 'dn_soalgandapoin.opsi_d'
                                                    ,'dn_soalgandapoin.opsi_e','dn_soalgandapoin.poin_a','dn_soalgandapoin.poin_b','dn_soalgandapoin.poin_c','dn_soalgandapoin.poin_d','dn_soalgandapoin.poin_e')
                                            ->leftJoin('dn_jawabangandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $request->tes)
                                            ->where('dn_jawabangandapoin.status', $request->token)
                                            ->where('dn_jawabangandapoin.pelajar_id', $request->auth)
                                            ->orderBy('dn_soalgandapoin.nomor_soal', 'asc')
                                            ->get();
            $soal = SoalDinasGandaPoin::select('id')->where('dn_tes_id', $request->tes)->count();
            $soal_terjawab = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $request->tes)
                                            ->where('dn_jawabangandapoin.pelajar_id', $request->auth)
                                            ->where('dn_jawabangandapoin.status', $request->token)
                                            ->whereNotNull('dn_jawabangandapoin.jawaban')
                                            ->count();
            $nilai = Penilaian::where('dn_penilaians.dn_tes_id', $request->tes)
                                ->where('dn_penilaians.pelajar_id', $request->auth)
                                ->where('dn_penilaians.status', $request->token)
                                ->first();
            $jenis = 2;
        }
        return view('pendidik.dinas.analisis.jawabanpelajar', compact('user','pelajar','jawaban','soal','soal_terjawab','nilai','jenis'));


    }
    public function cetakJawaban(Request $request){
            $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama as pelajar','kelas.nama as kelas')
                            ->where('users.id',$request->auth)
                            ->first();

            $nilai = Penilaian::where('dn_penilaians.dn_tes_id', $request->tes)
                                ->where('dn_penilaians.pelajar_id', $request->auth)
                                ->where('dn_penilaians.status', $request->token)
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

            $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
            $pdf = PDF::loadview('pendidik.dinas.analisis.cetakjawaban', ['soal'=>$soal,'logo'=>$en_logo,'pelajar'=>$pelajar,'soal_terjawab'=>$soal_terjawab,'jawaban'=>$jawaban,'nilai'=>$nilai])->setPaper('a4');
            return $pdf->stream();
    }

    public function cetakJawabanPoin(Request $request){
            $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama as pelajar','kelas.nama as kelas')
                            ->where('users.id',$request->auth)
                            ->first();

            $nilai = Penilaian::where('dn_penilaians.dn_tes_id', $request->tes)
                                ->where('dn_penilaians.pelajar_id', $request->auth)
                                ->where('dn_penilaians.status', $request->token)
                                ->first();
            $jawaban = SoalDinasGandaPoin::leftJoin('dn_jawabangandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $request->tes)
                                            ->where('dn_jawabangandapoin.status', $request->token)
                                            ->where('dn_jawabangandapoin.pelajar_id', $request->auth)
                                            ->orderBy('dn_soalgandapoin.nomor_soal', 'asc')
                                            ->get();
            $soal = SoalDinasGandaPoin::select('id')->where('dn_tes_id', $request->tes)->count();
            $soal_terjawab = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                                ->where('dn_soalgandapoin.dn_tes_id', $request->tes)
                                                ->where('dn_jawabangandapoin.pelajar_id', $request->auth)
                                                ->where('dn_jawabangandapoin.status', $request->token)
                                                ->whereNotNull('dn_jawabangandapoin.jawaban')
                                                ->count();
            $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
            $pdf = PDF::loadview('pendidik.dinas.analisis.cetakjawabanpoin', ['soal'=>$soal,'logo'=>$en_logo,'pelajar'=>$pelajar,'soal_terjawab'=>$soal_terjawab,'jawaban'=>$jawaban,'nilai'=>$nilai])->setPaper('a4');
            return $pdf->stream();
    }

}
