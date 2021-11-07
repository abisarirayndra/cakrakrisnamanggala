<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penilaian;
use Auth;
use App\Kelas;
use PDF;
use Image;
use App\TesDinas;
use Str;
use App\ArsipNilai;
use Carbon\Carbon;
use Alert;
use App\RekapDinas;
use App\RekapTniPolri;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\JawabanGandaDinas;
use App\JawabanGandaPoinDinas;
use App\PaketDinas;

class HasilDinasController extends Controller
{
    public function hasilPendidik($id, Request $request){
        $user = Auth::user()->nama;
        $uniqode = Str::random(6);
        $tes = TesDinas::select('selesai')->where('id',$id)->first();
        $now = Carbon::now();
        $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas','dn_penilaians.created_at')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('dn_tes_id', $id)
                        ->where('status', null)
                        ->orderBy('nilai','desc')
                        ->get();
                        $selected = "";
        $kelas = Kelas::all();
        if($request->kelas){
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas','dn_penilaians.created_at')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('dn_tes_id', $id)
                        ->where('status', null)
                        ->where('users.kelas_id', $request->kelas)
                        ->orderBy('nilai','desc')
                        ->get();
            $selected = $request->kelas;
        }

        return view('pendidik.dinas.hasil.hasil',  compact('user','nilai','kelas','id','selected','uniqode','tes','now'));

    }

    public function cetakPdfHasil($id, Request $request){
        if ($request->kelas == ""){
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas', 'dn_penilaians.created_at')
                                ->join('users','users.id','=','dn_penilaians.pelajar_id')
                                ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                                ->join('kelas','kelas.id','=','users.kelas_id')
                                ->where('dn_tes_id', $id)
                                ->where('status', null)
                                ->orderBy('nilai','desc')
                                ->get();
            $data = TesDinas::select('users.nama','mapels.mapel')
                                    ->join('users','users.id','=','dn_tes.pengajar_id')
                                    ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                    ->where('dn_tes.id', $id)
                                    ->first();
        }
        else{
            $nilai = Penilaian::select('users.nama','dn_penilaians.nilai','dn_penilaians.akumulasi','kelas.nama as kelas', 'dn_penilaians.created_at')
                        ->join('users','users.id','=','dn_penilaians.pelajar_id')
                        ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                        ->join('kelas','kelas.id','=','users.kelas_id')
                        ->where('dn_tes_id', $id)
                        ->where('status', null)
                        ->where('users.kelas_id', $request->kelas)
                        ->orderBy('nilai','desc')
                        ->get();
                        $data = TesDinas::select('users.nama','mapels.mapel')
                                    ->join('users','users.id','=','dn_tes.pengajar_id')
                                    ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                    ->where('dn_tes.id', $id)
                                    ->first();
        }



        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendidik.dinas.hasil.cetak_hasil', ['nilai'=>$nilai,'logo'=>$en_logo,'data'=>$data])->setPaper('a4','landscape');
        return $pdf->stream();
    }

    public function arsipkan($id, Request $request){
        $pendidik = Auth::user()->id;
        $tanggal = Carbon::now();
        $request->validate([
            'kode' => 'unique:dn_arsipnilai',
        ]);
        ArsipNilai::create([
            'kode' => $request->kode,
            'dn_tes_id' => $id,
            'tanggal' => $tanggal,
            'pendidik_id' => $pendidik,
        ]);
        Penilaian::where('dn_tes_id', $id)->where('status', null)->update(['status' => $request->kode]);
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->first();
        if($ganda){
            JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
            ->where('dn_soalganda.dn_tes_id', $id)->where('status', null)->update(['status' => $request->kode]);
        }elseif($poin){
            JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
            ->where('dn_soalgandapoin.dn_tes_id', $id)->where('status', null)->update(['status' => $request->kode]);
        }

        Alert::toast('Data Berhasil Diarsipkan','success');
        return redirect()->back();

    }

    public function hasilKedinasanAdmin($id){
        $user = Auth::user()->nama;
        $hasil = RekapDinas::join('users','users.id','=','dn_rekapdinas.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekapdinas.twk','dn_rekapdinas.tiu','dn_rekapdinas.tkp','dn_rekapdinas.total_nilai')
                            ->orderBy('dn_rekapdinas.total_nilai','desc')
                            ->where('dn_paket_id', $id)->get();

        return view('admin.dinas.paket.hasildinas', compact('hasil','user','id'));
    }

    public function cetakKedinasanAdmin($id){
        $hasil = RekapDinas::join('users','users.id','=','dn_rekapdinas.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekapdinas.twk','dn_rekapdinas.tiu','dn_rekapdinas.tkp','dn_rekapdinas.total_nilai')
                            ->orderBy('dn_rekapdinas.total_nilai','desc')
                            ->where('dn_paket_id', $id)->get();
        $paket = PaketDinas::select('nama_paket')->find($id);

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('admin.dinas.paket.cetakdinas', ['logo'=>$en_logo,'hasil'=>$hasil,'paket'=>$paket])->setPaper('a4','landscape');
        return $pdf->stream();
    }

    public function hasilTniPolriAdmin($id){
        $user = Auth::user()->nama;
        $hasil = RekapTniPolri::join('users','users.id','=','dn_rekap_tnipolri.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_tnipolri.ipu_wk','dn_rekap_tnipolri.mtk','dn_rekap_tnipolri.bin','dn_rekap_tnipolri.bing','dn_rekap_tnipolri.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->orderBy('dn_rekap_tnipolri.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.hasiltnipolri', compact('hasil','user','id'));
    }

    public function cetakTniPolriAdmin($id){
        $hasil = RekapTniPolri::join('users','users.id','=','dn_rekap_tnipolri.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_tnipolri.ipu_wk','dn_rekap_tnipolri.mtk','dn_rekap_tnipolri.bin','dn_rekap_tnipolri.bing','dn_rekap_tnipolri.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->orderBy('dn_rekap_tnipolri.total_nilai','desc')
                            ->get();

        $paket = PaketDinas::select('nama_paket')->find($id);

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('admin.dinas.paket.cetaktnipolri', ['logo'=>$en_logo,'hasil'=>$hasil,'paket'=>$paket])->setPaper('a4','landscape');
        return $pdf->stream();
    }
}
