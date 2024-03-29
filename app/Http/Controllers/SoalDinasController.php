<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\SoalDinasEssay;
use App\JawabanGandaDinas;
use App\JawabanGandaPoinDinas;
use App\TesDinas;
use App\Penilaian;
use App\RekapDinas;
use App\RekapTniPolri;
use App\RekapPsikotes;
use App\Imports\SoalGandaImport;
use App\Imports\SoalGandaPoinImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Image;
use PDF;
use DB;

class SoalDinasController extends Controller
{
    // Pendidik

    public function pendidikPilihTipe($id){
        $user = Auth::user()->nama;
        $paket = TesDinas::find($id);
        $essay = SoalDinasEssay::where('dn_tes_id', $id)->first();
        $jumlah_essay = SoalDinasEssay::where('dn_tes_id', $id)->count();
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->first();
        $jumlah_ganda = SoalDinasGanda::where('dn_tes_id', $id)->count();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->first();
        $jumlah_poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->count();

        return view('pendidik.dinas.soal.tipe', compact('user','id','essay','ganda','poin','jumlah_ganda','jumlah_poin','jumlah_essay','paket'));
    }

    public function pendidikHapusEssay($id){
        $essay = SoalDinasEssay::where('dn_tes_id', $id)->delete();
        Alert::toast('Soal Berhasil Dihapus');
        return redirect()->back();
    }

    public function pendidikHapusGanda($id){
        $essay = SoalDinasGanda::where('dn_tes_id', $id)->delete();
        Alert::toast('Soal Berhasil Dihapus');
        return redirect()->back();
    }

    public function pendidikHapusGandaPoin($id){
        $essay = SoalDinasGandaPoin::where('dn_tes_id', $id)->delete();
        Alert::toast('Soal Berhasil Dihapus');
        return redirect()->back();
    }

    public function pendidikSoalGanda($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasGanda::where('dn_tes_id', $id)->get();
        $ada_soal = SoalDinasGanda::where('dn_tes_id', $id)->orderBy('id','desc')->first();
        return view('pendidik.dinas.soal.ganda', compact('user','id','soal','ada_soal'));
    }

    public function pendidikCetakSoalGanda($id){
        $soal = SoalDinasGanda::where('dn_tes_id', $id)->get();
        $data = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                            ->join('users','users.id','=','dn_tes.pengajar_id')
                            ->select('users.nama','mapels.mapel')
                            ->where('dn_tes.id', $id)
                            ->first();

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendidik.dinas.soal.cetakganda', ['soal'=>$soal,'logo'=>$en_logo,'data'=>$data])->setPaper('a4');
        return $pdf->stream();
    }

    public function pendidikUpSoalGanda($id, Request $request){
        $request->validate([
            'nomor_soal' => 'required',
            'soal' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'kunci' => 'required'
        ]);

        if(SoalDinasGanda::where('dn_tes_id', $id)->where('nomor_soal', $request->nomor_soal)->first()){
            Alert::toast('Nomor Sudah Digunakan', 'error');
            return redirect()->back();
        }

        SoalDinasGanda::create([
            'dn_tes_id' => $id,
            'nomor_soal' => $request->nomor_soal,
            'soal' => $request->soal,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci' => $request->kunci,
        ]);


        Alert::toast('Tambah Soal Berhasil','success');

        return redirect()->back()
        // ->with(['nomor_lanjut => $nomor_lanjut'])
        ;
    }

    public function pendidikImportSoalGanda(Request $request){
        // $this->validate($request, [
        //     'file' => 'mimes:csv'
        // ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file'); //GET FILE
            Excel::import(new SoalGandaImport, $file); //IMPORT FILE
            Alert::toast('Import Berhasil', 'success');
            return redirect()->back();
        }
    }

    public function pendidikEditSoalGanda($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasGanda::find($id);


        return view('pendidik.dinas.soal.editganda', compact('soal','id','user'));
    }

    public function pendidikUpdateSoalGanda($id, Request $request){
        $soal = SoalDinasGanda::find($id);
        $soal->update($request->all());
        Alert::toast('Update Soal Berhasil','success');

        return redirect()->route('pendidik.dinas.soalganda', $soal->dn_tes_id);

    }

    public function pendidikSoalGandaPoin($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->get();
        $ada_soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->orderBy('id','desc')->first();
        return view('pendidik.dinas.soal.gandapoin', compact('user','id','soal','ada_soal'));
    }

    public function pendidikCetakSoalGandaPoin($id){
        $soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->get();
        $data = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                            ->join('users','users.id','=','dn_tes.pengajar_id')
                            ->select('users.nama','mapels.mapel')
                            ->where('dn_tes.id', $id)
                            ->first();

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendidik.dinas.soal.cetakgandapoin', ['soal'=>$soal,'logo'=>$en_logo,'data'=>$data])->setPaper('a4');
        return $pdf->stream();
    }

    public function pendidikUpSoalGandaPoin($id, Request $request){
        $request->validate([
            'nomor_soal' => 'required',
            'soal' => 'required',
            'opsi_a' => 'required',
            'poin_a' => 'required',
            'opsi_b' => 'required',
            'poin_b' => 'required',
            'opsi_c' => 'required',
            'poin_c' => 'required',
            'opsi_d' => 'required',
            'poin_d' => 'required',

        ]);

        if(SoalDinasGandaPoin::where('dn_tes_id', $id)->where('nomor_soal', $request->nomor_soal)->first()){
            Alert::toast('Nomor Sudah Digunakan', 'error');
            return redirect()->back();
        }

        SoalDinasGandaPoin::create([
            'dn_tes_id' => $id,
            'nomor_soal' => $request->nomor_soal,
            'soal' => $request->soal,
            'opsi_a' => $request->opsi_a,
            'poin_a' => $request->poin_a,
            'opsi_b' => $request->opsi_b,
            'poin_b' => $request->poin_b,
            'opsi_c' => $request->opsi_c,
            'poin_c' => $request->poin_c,
            'opsi_d' => $request->opsi_d,
            'poin_d' => $request->poin_d,
            'opsi_e' => $request->opsi_e,
            'poin_e' => $request->poin_e,
        ]);

        Alert::toast('Tambah Soal Berhasil','success');

        return redirect()->back();
    }

    public function pendidikImportSoalGandaPoin(Request $request){
        // $this->validate($request, [
        //     'file' => 'mimes:csv'
        // ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file'); //GET FILE
            Excel::import(new SoalGandaPoinImport, $file); //IMPORT FILE
            Alert::toast('Import Berhasil', 'success');
            return redirect()->back();
        }
    }

    public function pendidikEditSoalGandaPoin($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasGandaPoin::find($id);


        return view('pendidik.dinas.soal.editgandapoin', compact('soal','id','user'));
    }

    public function pendidikUpdateSoalGandaPoin($id, Request $request){
        $soal = SoalDinasGandaPoin::find($id);
        $soal->update($request->all());
        Alert::toast('Update Soal Berhasil','success');

        return redirect()->route('pendidik.dinas.soalgandapoin', $soal->dn_tes_id);
    }

    public function adminCetakSoalGandaPoin($id){
        $soal_poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->first();
        $soal_ganda = SoalDinasGanda::where('dn_tes_id', $id)->first();

        if(isset($soal_poin)){
            $soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->get();
            $data = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                            ->join('users','users.id','=','dn_tes.pengajar_id')
                            ->select('users.nama','mapels.mapel')
                            ->where('dn_tes.id', $id)
                            ->first();

                $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
                $pdf = PDF::loadview('pendidik.dinas.soal.cetakgandapoin', ['soal'=>$soal,'logo'=>$en_logo,'data'=>$data])->setPaper('a4');
                return $pdf->stream();
        }elseif(isset($soal_ganda)){
            $soal = SoalDinasGanda::where('dn_tes_id', $id)->get();
            $data = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                                ->join('users','users.id','=','dn_tes.pengajar_id')
                                ->select('users.nama','mapels.mapel')
                                ->where('dn_tes.id', $id)
                                ->first();

            $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
            $pdf = PDF::loadview('pendidik.dinas.soal.cetakganda', ['soal'=>$soal,'logo'=>$en_logo,'data'=>$data])->setPaper('a4');
            return $pdf->stream();
        }else{
            return abort(404);
        }

    }

    public function pendidikSoalEssay($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasEssay::where('dn_tes_id', $id)->get();

        return view('pendidik.dinas.soal.essay', compact('user','id','soal'));
    }

    public function pendidikUpSoalEssay($id, Request $request){
        $request->validate([
            'nomor_soal' => 'required',
            'soal' => 'required',
        ]);

        if(SoalDinasGanda::where('dn_tes_id', $id)->where('nomor_soal', $request->nomor_soal)->first()){
            Alert::toast('Nomor Sudah Digunakan', 'error');
            return redirect()->back();
        }

        SoalDinasEssay::create([
            'nomor_soal' => $request->nomor_soal,
            'dn_tes_id' => $id,
            'soal' => $request->soal
        ]);

        Alert::toast('Tambah Soal Berhasil','success');

        return redirect()->back();
    }

    public function pendidikEditSoalEssay($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasEssay::find($id);


        return view('pendidik.dinas.soal.editessay', compact('soal','id','user'));
    }

    public function pendidikUpdateSoalEssay($id, Request $request){
        $soal = SoalDinasEssay::find($id);
        $soal->update($request->all());
        Alert::toast('Update Soal Berhasil','success');

        return redirect()->route('pendidik.dinas.soalessay', $soal->dn_tes_id);

    }

    public function pendidikHapusSoalGanda($id){
        $soal = SoalDinasGanda::find($id);
        $soal->delete();
        Alert::toast('Hapus Soal Berhasil', 'success');
        return redirect()->back();
    }

    public function pendidikHapusSoalGandaPoin($id){
        $soal = SoalDinasGandaPoin::find($id);
        $soal->delete();
        Alert::toast('Hapus Soal Berhasil', 'success');
        return redirect()->back();
    }

    public function pendidikHapusSoalEssay($id){
        $soal = SoalDinasEssay::find($id);
        $soal->delete();
        Alert::toast('Hapus Soal Berhasil', 'success');
        return redirect()->back();
    }

    //Pelajar
    public function pelajarPersiapan($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $paket = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')->where('dn_tes.id', $id)->first();
        // return $paket;
        $essay = SoalDinasEssay::where('dn_tes_id', $id)->orderBy('id','asc')->first();
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->orderBy('id','asc')->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->orderBy('id','asc')->first();

        $sudah = Penilaian::select('dn_tes.dn_paket_id','dn_pakets.kategori')
                            ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                            ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                            ->where('dn_penilaians.dn_tes_id', $id)
                            ->where('dn_penilaians.pelajar_id', $pelajar)
                            ->where('dn_penilaians.status', null)
                            ->first();
        if($sudah){
            $cek_paket = Penilaian::select('dn_pakets.kategori')
                                ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->where('dn_tes.dn_paket_id', $sudah->dn_paket_id)
                                ->where('dn_penilaians.pelajar_id', $pelajar)
                                ->where('dn_penilaians.status', null)
                                ->orderBy('dn_penilaians.id','desc')
                                ->first();
            if($cek_paket->kategori == "Psikotes"){
                $cek_belum = Penilaian::select('dn_tes.dn_paket_id','mapels.id as mapel')
                                ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                ->where('dn_tes.dn_paket_id', $sudah->dn_paket_id)
                                ->where('dn_penilaians.pelajar_id', $pelajar)
                                ->where('dn_penilaians.status', null)
                                ->orderBy('dn_penilaians.id','desc')
                                ->first();

                if($cek_belum->mapel == 11){
                    $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                                ->where('dn_tes.dn_paket_id', $cek_belum->dn_paket_id)
                                                ->where('mapel_id', 12)
                                                ->first();
                }
                elseif($cek_belum->mapel == 12){
                    $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                                ->where('dn_paket_id', $cek_belum->dn_paket_id)
                                                ->where('mapel_id', 13)
                                                ->first();
                }
                elseif($cek_belum->mapel == 13){
                    $tes_selanjutnya = "Selesai";
                }
            }elseif($cek_paket->kategori == "Kedinasan"){
                $cek_belum = Penilaian::select('dn_tes.dn_paket_id','mapels.id as mapel')
                                ->join('dn_tes','dn_tes.id','=','dn_penilaians.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                ->where('dn_tes.dn_paket_id', $sudah->dn_paket_id)
                                ->where('dn_penilaians.pelajar_id', $pelajar)
                                ->where('dn_penilaians.status', null)
                                ->orderBy('dn_penilaians.id','desc')
                                ->first();
                // return $cek_belum;
                if($cek_belum->mapel == 7){
                    $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                                ->where('dn_paket_id', $cek_belum->dn_paket_id)
                                                ->where('mapel_id', 8)
                                                ->first();
                }
                elseif($cek_belum->mapel == 8){
                    $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                                ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                                ->where('dn_paket_id', $cek_belum->dn_paket_id)
                                                ->where('mapel_id', 9)
                                                ->first();
                }
                elseif($cek_belum->mapel == 9){
                    $tes_selanjutnya = "Selesai";
                }
            }else{
                $tes_selanjutnya = " ";
            }
        }
        else{
             $tes_selanjutnya = " ";
        }

        // return $tes_selanjutnya;


        return view('pelajar.dinas.tes.persiapan', compact('user','essay','ganda','poin','id','paket','sudah','tes_selanjutnya'));
    }

    public function pelajarSoalGanda($id, Request $request){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $selesai = TesDinas::find($id);
        $nomor = SoalDinasGanda::select('id','dn_tes_id')->where('dn_tes_id', $id)->get();
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->where('id', $request->q)->get();
        $sudah = JawabanGandaDinas::where('dn_soalganda_id', $request->q)
                                            ->where('pelajar_id', $pelajar)
                                            ->where('status', null)
                                            ->first();

        if($sudah){}
        else{
            JawabanGandaDinas::create([
                'pelajar_id' => $pelajar,
                'dn_soalganda_id' => $request->q,
                'nilai' => 0,
            ]);
        }

        $sudah_jawab = JawabanGandaDinas::where('dn_soalganda_id', $request->q)
                                            ->where('pelajar_id', $pelajar)
                                            ->where('status', null)
                                            ->first();

        $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori')
                                ->where('dn_tes.id', $id)
                                ->first();

        if($kategori->kategori == "Kedinasan"){
            $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapDinas::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'nilai_twk' => 0,
                    'nilai_tiu' => 0,
                    'nilai_tkp' => 0,
                    'twk' => 0,
                    'tiu' => 0,
                    'tkp' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }
        elseif($kategori->kategori == "TNI/Polri"){
            $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapTniPolri::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'bin' => 0,
                    'bing' => 0,
                    'mtk' => 0,
                    'ipu_wk' => 0,
                    'nilai_bin' => 0,
                    'nilai_bing' => 0,
                    'nilai_mtk' => 0,
                    'nilai_ipu' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }elseif($kategori->kategori == "Psikotes"){
            $sudah_ada = RekapPsikotes::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapPsikotes::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'verbal' => 0,
                    'numerik' => 0,
                    'figural' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }

        $review_belum = JawabanGandaDinas::select('dn_soalganda.nomor_soal')
                                    ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                    ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                    ->where('dn_soalganda.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->where('jawaban', null)
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        $review_sudah = JawabanGandaDinas::select('dn_soalganda.nomor_soal')
                                    ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                    ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                    ->where('dn_soalganda.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->whereNotNull('jawaban')
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        return view('pelajar.dinas.soal.soalganda', compact('nomor','user','ganda','sudah_jawab','selesai','id','review_sudah','review_belum'));
    }

    public function pelajarSoalGandaPoin($id, Request $request){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $selesai = TesDinas::find($id);
        $nomor = SoalDinasGandaPoin::select('id','dn_tes_id','nomor_soal')->where('dn_tes_id', $id)->get();
        $ganda = SoalDinasGandaPoin::where('dn_tes_id', $id)->where('id', $request->q)->get();
        $sudah = JawabanGandaPoinDinas::where('dn_soalgandapoin_id', $request->q)
                ->where('pelajar_id', $pelajar)
                ->where('status', null)
                ->first();

        if($sudah){}
        else{
            JawabanGandaPoinDinas::create([
                'pelajar_id' => $pelajar,
                'dn_soalgandapoin_id' => $request->q,
                'nilai' => 0,
            ]);
        }

        $sudah_jawab = JawabanGandaPoinDinas::where('dn_soalgandapoin_id', $request->q)
                                            ->where('pelajar_id', $pelajar)
                                            ->where('status', null)
                                            ->first();

        $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori')
                                ->where('dn_tes.id', $id)
                                ->first();

        if($kategori->kategori == "Kedinasan"){
            $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapDinas::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'nilai_twk' => 0,
                    'nilai_tiu' => 0,
                    'nilai_tkp' => 0,
                    'twk' => 0,
                    'tiu' => 0,
                    'tkp' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }
        elseif($kategori->kategori == "TNI/Polri"){
            $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapTniPolri::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'bin' => 0,
                    'bing' => 0,
                    'mtk' => 0,
                    'ipu_wk' => 0,
                    'nilai_bin' => 0,
                    'nilai_bing' => 0,
                    'nilai_mtk' => 0,
                    'nilai_ipu' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }elseif($kategori->kategori == "Psikotes"){
            $sudah_ada = RekapPsikotes::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){}
            else{
                RekapPsikotes::create([
                    'pelajar_id' => $pelajar,
                    'dn_paket_id' => $kategori->id,
                    'verbal' => 0,
                    'numerik' => 0,
                    'figural' => 0,
                    'total_nilai' => 0,
                ]);
            }
        }

        $review_belum = JawabanGandaPoinDinas::select('dn_soalgandapoin.nomor_soal')
                                    ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                    ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                    ->where('dn_soalgandapoin.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->where('jawaban', null)
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        $review_sudah = JawabanGandaPoinDinas::select('dn_soalgandapoin.nomor_soal')
                                    ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                    ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                    ->where('dn_soalgandapoin.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->whereNotNull('jawaban')
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        // $belum = JawabanGandaPoinDinas::
        //                             // select('dn_soalgandapoin.nomor_soal')
        //                             leftJoin('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
        //                             ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
        //                             ->where('dn_soalgandapoin.dn_tes_id', $id)
        //                             ->where('status', null)
        //                             ->whereNull('jawaban')
        //                             ->orderBy('nomor_soal', 'asc')
        //                             ->get();

        // $belum = SoalDinasGandaPoin::leftJoin('dn_jawabangandapoin', function($join)
        //                             {
        //                                 $join->on('dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id');

        //                             })
        //                             ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
        //                             ->where('dn_soalgandapoin.dn_tes_id', $id)
        //                             ->where('dn_jawabangandapoin.status', null)
        //                             ->whereNull('dn_jawabangandapoin.jawaban')
        //                             ->orderBy('dn_soalgandapoin.nomor_soal', 'asc')
        //                             ->get();
        // return $belum;

        // $soal = SoalDinasGandaPoin::select('nomor_soal')->where('dn_tes_id', $id)->get();
        // $jumlah_soal = SoalDinasGandaPoin::select('nomor_soal')->where('dn_tes_id', $id)->count();

        // $slice = $jumlah_soal - $jumlah_sudah;
        // $soal_belum = array_slice($soal, $slice - 1);

        // for($a = 0; $a < $ulangi; $a++){
        //     $soal_belum = $soal[$a];
        // }

        // return $soal_belum;

        // $data = collect();
        // $data->push($review_sudah, $soal);
        // $review = $data->collapse()->values()->all();

        // return $belum;


        return view('pelajar.dinas.soal.soalgandapoin', compact('nomor','user','ganda','sudah_jawab','selesai','id','review_belum',
        'review_sudah'));
    }
}
