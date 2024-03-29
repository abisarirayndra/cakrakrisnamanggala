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
use App\ArsipPaket;
use Carbon\Carbon;
use Alert;
use App\RekapDinas;
use App\RekapTniPolri;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\JawabanGandaDinas;
use App\JawabanGandaPoinDinas;
use App\PaketDinas;
use App\RekapPsikotes;

class HasilDinasController extends Controller
{
    public function hasilPendidik($id, Request $request){
        $user = Auth::user()->nama;
        $uniqode = Str::random(6);
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

        return view('pendidik.dinas.hasil.hasil',  compact('user','nilai','kelas','id','selected','uniqode'));

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



    public function hasilKedinasanAdmin($id){
        $user = Auth::user()->nama;
        $hasil = RekapDinas::join('users','users.id','=','dn_rekapdinas.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekapdinas.twk','dn_rekapdinas.tiu','dn_rekapdinas.tkp','dn_rekapdinas.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekapdinas.kode_arsip', null)
                            ->orderBy('dn_rekapdinas.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.hasildinas', compact('hasil','user','id'));
    }

    public function cetakKedinasanAdmin($id){
        $hasil = RekapDinas::join('users','users.id','=','dn_rekapdinas.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekapdinas.twk','dn_rekapdinas.tiu','dn_rekapdinas.tkp','dn_rekapdinas.total_nilai')
                            ->orderBy('dn_rekapdinas.total_nilai','desc')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekapdinas.kode_arsip', null)
                            ->get();
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
                            ->where('dn_rekap_tnipolri.kode_arsip', null)
                            ->orderBy('dn_rekap_tnipolri.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.hasiltnipolri', compact('hasil','user','id'));
    }

    public function cetakTniPolriAdmin($id){
        $hasil = RekapTniPolri::join('users','users.id','=','dn_rekap_tnipolri.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_tnipolri.ipu_wk','dn_rekap_tnipolri.mtk','dn_rekap_tnipolri.bin','dn_rekap_tnipolri.bing','dn_rekap_tnipolri.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekap_tnipolri.kode_arsip', null)
                            ->orderBy('dn_rekap_tnipolri.total_nilai','desc')
                            ->get();

        $paket = PaketDinas::select('nama_paket')->find($id);

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('admin.dinas.paket.cetaktnipolri', ['logo'=>$en_logo,'hasil'=>$hasil,'paket'=>$paket])->setPaper('a4','landscape');
        return $pdf->stream();
    }

    public function hasilPsikotesAdmin($id){
        $user = Auth::user()->nama;
        $hasil = RekapPsikotes::join('users','users.id','=','dn_rekap_psikotes.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_psikotes.verbal','dn_rekap_psikotes.numerik','dn_rekap_psikotes.figural','dn_rekap_psikotes.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekap_psikotes.kode_arsip', null)
                            ->orderBy('dn_rekap_psikotes.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.hasilpsikotes', compact('hasil','user','id'));
    }

    public function cetakPsikotesAdmin($id){
        $hasil = RekapPsikotes::join('users','users.id','=','dn_rekap_psikotes.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_psikotes.verbal','dn_rekap_psikotes.numerik','dn_rekap_psikotes.figural','dn_rekap_psikotes.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekap_psikotes.kode_arsip', null)
                            ->orderBy('dn_rekap_psikotes.total_nilai','desc')
                            ->get();

        $paket = PaketDinas::select('nama_paket')->find($id);

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('admin.dinas.paket.cetaktnipolri', ['logo'=>$en_logo,'hasil'=>$hasil,'paket'=>$paket])->setPaper('a4','landscape');
        return $pdf->stream();
    }


    public function liveSkorTniPolri($id){
        $user = Auth::user()->nama;
        $hasil = RekapTniPolri::join('users','users.id','=','dn_rekap_tnipolri.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_tnipolri.nilai_ipu','dn_rekap_tnipolri.ipu_wk','dn_rekap_tnipolri.nilai_mtk','dn_rekap_tnipolri.mtk',
                            'dn_rekap_tnipolri.nilai_bin','dn_rekap_tnipolri.bin','dn_rekap_tnipolri.nilai_bing','dn_rekap_tnipolri.bing','dn_rekap_tnipolri.total_nilai')
                            ->where('dn_rekap_tnipolri.dn_paket_id', $id)
                            ->where('dn_rekap_tnipolri.kode_arsip', null)
                            ->orderBy('dn_rekap_tnipolri.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.live_tnipolri', compact('hasil','user'));
    }

    public function liveSkorKedinasan($id){
        $user = Auth::user()->nama;
        $hasil = RekapDinas::join('users','users.id','=','dn_rekapdinas.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekapdinas.nilai_twk','dn_rekapdinas.nilai_tiu','dn_rekapdinas.nilai_tkp','dn_rekapdinas.total_nilai')
                            ->orderBy('dn_rekapdinas.total_nilai','desc')
                            ->where('dn_rekapdinas.kode_arsip', null)
                            ->where('dn_paket_id', $id)
                            ->get();

        return view('admin.dinas.paket.live_kedinasan', compact('hasil','user'));
    }

    public function liveSkorPsikotes($id){
        $user = Auth::user()->nama;
        $hasil = RekapPsikotes::join('users','users.id','=','dn_rekap_psikotes.pelajar_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->select('users.nama','kelas.nama as kelas','dn_rekap_psikotes.verbal','dn_rekap_psikotes.numerik','dn_rekap_psikotes.figural','dn_rekap_psikotes.total_nilai')
                            ->where('dn_paket_id', $id)
                            ->where('dn_rekap_psikotes.kode_arsip', null)
                            ->orderBy('dn_rekap_psikotes.total_nilai','desc')
                            ->get();

        return view('admin.dinas.paket.live_psikotes', compact('hasil','user'));
    }


    public function arsipkanPaket($id){
        // $pendidik = Auth::user()->id;
        $tanggal = Carbon::now();
        $kode = ArsipPaket::create([
            'kode' => Str::random(6),
            'dn_paket_id' => $id,
            'tanggal' => $tanggal,
            // 'pendidik_id' => $pendidik,
        ]);
        $nilai = Penilaian::join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                    ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                    ->where('dn_tes.dn_paket_id', $id)
                    ->where('dn_penilaians.status', null)
                    // ->select('dn_penilaians.status')
                    ->update(['dn_penilaians.status' => $kode->kode]);

        $ganda = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                            ->join('dn_tes','dn_tes.id','=','dn_soalganda.dn_tes_id')
                            ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                            ->where('dn_tes.dn_paket_id', $id)
                            ->where('dn_jawabanganda.status', null)
                            // ->get();
                            ->update(['dn_jawabanganda.status' => $kode->kode]);
        $poin = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                    ->join('dn_tes','dn_tes.id','=','dn_soalgandapoin.dn_tes_id')
                                    ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                    ->where('dn_tes.dn_paket_id', $id)
                                    ->where('dn_jawabangandapoin.status', null)
                                    // ->get();
                                    ->update(['dn_jawabangandapoin.status' => $kode->kode]);

        $paket = PaketDinas::find($id);
        if($paket->kategori == "TNI/Polri"){
            RekapTniPolri::where('dn_paket_id', $id)->where('kode_arsip', null)->update(['kode_arsip' => $kode->kode]);
        }elseif($paket->kategori == "Kedinasan"){
            RekapDinas::where('dn_paket_id', $id)->where('kode_arsip', null)->update(['kode_arsip' => $kode->kode]);
        }elseif($paket->kategori == "Psikotes"){
            RekapPsikotes::where('dn_paket_id', $id)->where('kode_arsip', null)->update(['kode_arsip' => $kode->kode]);
        }

        Alert::toast('Data Berhasil Diarsipkan','success');
        return redirect()->back();

    }

    public function capaian(){
        $id = Auth::user()->id;
        $user = Auth::user()->nama;
        $skd = RekapDinas::where('pelajar_id', $id)->max('total_nilai');
        $akademik = RekapTniPolri::where('pelajar_id', $id)->max('total_nilai');
        $psikotes = RekapPsikotes::where('pelajar_id', $id)->max('total_nilai');
        $capaian_skd = RekapDinas::where('pelajar_id', $id)->get();

        foreach($capaian_skd as $item){
            $skd_categories[] = Carbon::parse($item->created_at)->isoFormat('LL');
            $skd_data[] = $item->total_nilai;
        }

        $capaian_akademik = RekapTniPolri::where('pelajar_id', $id)->get();
        foreach($capaian_akademik as $item){
            $akademik_categories[] = Carbon::parse($item->created_at)->isoFormat('LL');
            $akademik_data[] = $item->total_nilai;
        }

        // $capaian_psikotes = RekapPsikotes::where('pelajar_id', $id)->get();
        // foreach($capaian_psikotes as $item){
        //     $psikotes_categories[] = Carbon::parse($item->created_at)->isoFormat('LL');
        //     $psikotes_data[] = $item->total_nilai;
        // }
        return view('pelajar.dinas.capaian', compact('user','skd','akademik','psikotes',
                                                'skd_categories','skd_data','akademik_categories','akademik_data',
                                                // 'psikotes_categories','psikotes_data'
                                            ));
    }

}
