<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\JawabanGandaDinas;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\JawabanGandaPoinDinas;
use DB;
use App\TesDinas;
use App\Penilaian;
use App\RekapDinas;
use App\RekapTniPolri;
use App\Kelas;
use App\Mapel;
use App\TotalAkumulasiTniPolri;

class JawabanDinasController extends Controller
{
    public function upJawabanGanda($id, Request $request){
        if($request->jawaban == null){
            return redirect()->back()->withError('Jawaban Kosong Tidak Bisa Disimpan');
        }
        $pelajar = Auth::user()->id;
        $soal = SoalDinasGanda::find($id);
        $kunci = $soal->kunci;
        if($request->jawaban == $kunci){
            $nilai = 1;
        }
        else{
            $nilai = 0;
        }

        $ada_jawaban = JawabanGandaDinas::where('pelajar_id', $pelajar)
                                            ->where('dn_soalganda_id', $id)
                                            ->where('status', null)
                                            ->first();
        $jumlah_soal = SoalDinasGanda::where('dn_tes_id',$soal->dn_tes_id)->count();
        // $total = $jumlah_soal + 2;
        // return $jumlah_soal;
        if(isset($ada_jawaban)){
            $ada_jawaban->update([
                'jawaban' => $request->jawaban,
                'nilai' => $nilai,
            ]);

            $kategori = SoalDinasGanda::join('dn_tes','dn_tes.id','=','dn_soalganda.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id','dn_tes.nilai_pokok','dn_tes.id as tes_id')
                                ->where('dn_soalganda.id', $id)
                                ->first();

            $nilai_pokok = $kategori->nilai_pokok;

            $penilaian = JawabanGandaDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                            ->where('dn_soalganda.dn_tes_id',$kategori->tes_id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();
            $total_nilai = [];
            foreach ($penilaian as $item) {
                $total_nilai = (int)$item->total_nilai;
            }

            $nilai = ($total_nilai/$jumlah_soal)*100;
            $akumulasi = ($nilai_pokok/100)*$nilai;


            if($kategori->kategori == "Kedinasan"){
                $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                    case 7 :
                        // if($sudah_ada->twk == null){
                            // $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_twk' => $nilai,
                                'twk' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 8 :
                        // if($sudah_ada->tiu == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tiu' => $nilai,
                                'tiu' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 9 :
                        // if($sudah_ada->tkp == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tkp' => $nilai,
                                'tkp' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    }
                }
            }
            elseif($kategori->kategori == "TNI/Polri"){
                $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();

                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                        case 1:
                            // if($sudah_ada->mtk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_mtk' => $nilai,
                                    'mtk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 2:
                            // if($sudah_ada->ipu_wk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_ipu' => $nilai,
                                    'ipu_wk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 3:
                            // if($sudah_ada->bing == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bing' => $total_nilai,
                                    'bing' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 4:
                            // if($sudah_ada->bin == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bin' => $total_nilai,
                                    'bin' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                    }
                }
            }

            Alert::success('Berhasil Menjawab', "Jawabanmu $request->jawaban");
            return redirect()->back();
        }
        else{
            JawabanGandaDinas::create([
                'pelajar_id' => $pelajar,
                'dn_soalganda_id' => $id,
                'jawaban' => $request->jawaban,
                'nilai' => $nilai,
            ]);

            $kategori = SoalDinasGanda::join('dn_tes','dn_tes.id','=','dn_soalganda.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id','dn_tes.nilai_pokok','dn_tes.id as tes_id')
                                ->where('dn_soalganda.id', $id)
                                ->first();

            $nilai_pokok = $kategori->nilai_pokok;

            $penilaian = JawabanGandaDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                            ->where('dn_soalganda.dn_tes_id',$kategori->tes_id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();
            $total_nilai = [];
            foreach ($penilaian as $item) {
                $total_nilai = (int)$item->total_nilai;
            }

            $nilai = ($total_nilai/$jumlah_soal)*100;
            $akumulasi = ($nilai_pokok/100)*$nilai;


            if($kategori->kategori == "Kedinasan"){
                $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                    case 7 :
                        // if($sudah_ada->twk == null){
                            // $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_twk' => $nilai,
                                'twk' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 8 :
                        // if($sudah_ada->tiu == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tiu' => $nilai,
                                'tiu' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 9 :
                        // if($sudah_ada->tkp == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tkp' => $nilai,
                                'tkp' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    }
                }
            }
            elseif($kategori->kategori == "TNI/Polri"){
                $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();

                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                        case 1:
                            // if($sudah_ada->mtk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_mtk' => $nilai,
                                    'mtk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 2:
                            // if($sudah_ada->ipu_wk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_ipu' => $nilai,
                                    'ipu_wk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 3:
                            // if($sudah_ada->bing == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bing' => $total_nilai,
                                    'bing' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 4:
                            // if($sudah_ada->bin == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bin' => $total_nilai,
                                    'bin' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                    }
                }
            }

            Alert::success('Berhasil Menjawab', "Jawabanmu $request->jawaban");
            return redirect()->back();
        }
    }

    public function review($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $review = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                    ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                    ->where('dn_soalganda.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        $soal = SoalDinasGanda::select('id')->where('dn_tes_id', $id)->count();
        $soal_terjawab = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $id)
                                            ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                            ->whereNotNull('dn_jawabanganda.jawaban')
                                            ->where('status', null)
                                            ->count();
        $penilaian = JawabanGandaDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_jawabanganda.pelajar_id', $pelajar)
                                            ->where('dn_soalganda.dn_tes_id',$id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();

        $total_nilai = [];
        foreach ($penilaian as $item) {
           $total_nilai = (int)$item->total_nilai;
        }

        $nilai_pokok = TesDinas::find($id);

        // $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
        //                         ->select('dn_pakets.id','dn_pakets.kategori')
        //                         ->where('dn_tes.id', $id)
        //                         ->first();

        // if($kategori->kategori == "Kedinasan"){
        //     $nilai_akm = RekapDinas::select('total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
        //     $total_akumulasi = (float)$nilai_akm->total_nilai;
        // }
        // elseif($kategori->kategori == "TNI/Polri"){
        //     $nilai_akm = RekapTniPolri::select('total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
        //     $total_akumulasi = (float)$nilai_akm->total_nilai;
        // }

        return view('pelajar.dinas.soal.reviewganda', compact('review','soal','soal_terjawab','user','total_nilai','nilai_pokok','id','total_akumulasi'));
    }

    public function kumpulkan($id){
        $pelajar = Auth::user()->id;
        // $benar = $request->n;
        // $pokok = $request->p;
        // $jumlah_soal = $request->s;
        // $akm = $request->akm;

        $sudah_kerja = Penilaian::where('pelajar_id', $pelajar)->where('dn_tes_id', $id)->where('status', null)->first();

        if($sudah_kerja){
            Alert::error('Anda Sudah Mengerjakan');
            return redirect()->back();
        }

        // $nilai_pokok = TesDinas::find($id);

        $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                        ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id')
                        ->where('dn_tes.id', $id)
                        ->first();

        if($kategori->kategori == "Kedinasan"){
            if($kategori->mapel_id == 7){
                $nilai = RekapDinas::select('nilai_twk','total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_twk,
                    'akumulasi' => $nilai->total_nilai,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);

            }elseif($kategori->mapel_id == 8){
                $nilai = RekapDinas::select('nilai_tiu','total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_tiu,
                    'akumulasi' => $nilai->total_nilai,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }elseif($kategori->mapel_id == 9){
                $nilai = RekapDinas::select('nilai_tkp','total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_tkp,
                    'akumulasi' => $nilai->total_nilai,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }
        }
        elseif($kategori->kategori == "TNI/Polri"){
            if($kategori->mapel_id == 1){
                $nilai = RekapTniPolri::select('nilai_mtk','mtk')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_mtk,
                    'akumulasi' => $nilai->mtk,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }elseif($kategori->mapel_id == 2){
                $nilai = RekapTniPolri::select('nilai_ipu','ipu_wk')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_ipu,
                    'akumulasi' => $nilai->ipu_wk,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }elseif($kategori->mapel_id == 3){
                $nilai = RekapTniPolri::select('nilai_bing','bing')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_bing,
                    'akumulasi' => $nilai->bing,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }elseif($kategori->mapel_id == 4){
                $nilai = RekapTniPolri::select('nilai_bin','bin')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                Penilaian::create([
                    'dn_tes_id' => $id,
                    'pelajar_id' => $pelajar,
                    'nilai' => $nilai->nilai_bin,
                    'akumulasi' => $nilai->bin,
                ]);
                Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
                return redirect()->route('pelajar.dinas.nilai', $id);
            }
        }
    }

    public function nilai($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user();
        $kelas = Kelas::find($pelajar->kelas_id);
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->first();
        if($ganda){
            $jawab_benar = JawabanGandaDinas::join('dn_soalganda','dn_soalganda.id','=','dn_jawabanganda.dn_soalganda_id')
                                            ->where('dn_soalganda.dn_tes_id', $id)
                                            ->where('dn_jawabanganda.pelajar_id', $pelajar->id)
                                            ->where('dn_jawabanganda.nilai', 1)
                                            ->where('status', null)
                                            ->count();
            $soal = SoalDinasGanda::where('dn_tes_id', $id)->count();
            $jenis = 1;
        }elseif($poin){
            $jawab_benar = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $id)
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar->id)
                                            ->where('dn_jawabangandapoin.nilai', 1)
                                            ->where('status', null)
                                            ->count();
            $soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->count();
            $jenis = 2;
        }

        $nilai = Penilaian::where('pelajar_id', $pelajar->id)->where('dn_tes_id', $id)->where('status', null)->first();
        $prosentase = TesDinas::find($id);
        $mapel = Mapel::find($prosentase->mapel_id);
        $tes_selanjutnya = " ";

        $kategori = TesDinas::select('dn_pakets.kategori','dn_pakets.id')
                            ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                            ->where('dn_tes.id', $id)
                            ->first();
        if($mapel->id == 7){
            $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                        ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                        ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                        ->where('dn_tes.dn_paket_id', $kategori->id)
                                        ->where('mapel_id', 8)
                                        ->first();
        }elseif($mapel->id == 8){
            $tes_selanjutnya = TesDinas::select('dn_tes.id','mapels.mapel')
                                        ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                        ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                        ->where('dn_tes.dn_paket_id', $kategori->id)
                                        ->where('mapel_id', 9)
                                        ->first();
        }elseif($mapel->id == 9){
            $tes_selanjutnya = "Selesai";
        }


        return view('pelajar.dinas.soal.nilai', compact('user','pelajar','kelas','jawab_benar','soal',
                                                    'nilai','prosentase','jenis','mapel','kategori','tes_selanjutnya'));
    }

    public function upJawabanGandaPoin($id, Request $request){
        if($request->jawaban == null){
            return redirect()->back()->withError('Jawaban Kosong Tidak Bisa Disimpan');
        }
        $pelajar = Auth::user()->id;
        $soal = SoalDinasGandaPoin::find($id);

        $jawaban = $request->jawaban;
        switch($jawaban){
            case "A":
                $nilai = $soal->poin_a;
                break;
            case "B":
                $nilai = $soal->poin_b;
                break;
            case "C":
                $nilai = $soal->poin_c;
                break;
            case "D":
                $nilai = $soal->poin_d;
                break;
            case "E":
                $nilai = $soal->poin_e;
                break;
        }

        $ada_jawaban = JawabanGandaPoinDinas::where('pelajar_id', $pelajar)
                                                ->where('dn_soalgandapoin_id', $id)
                                                ->where('status', null)
                                                ->first();
        if(isset($ada_jawaban)){
            $ada_jawaban->update([
                'jawaban' => $request->jawaban,
                'nilai' => $nilai,
            ]);

            $kategori = SoalDinasGandaPoin::join('dn_tes','dn_tes.id','=','dn_soalgandapoin.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id','dn_tes.nilai_pokok','dn_tes.id as tes_id')
                                ->where('dn_soalgandapoin.id', $id)
                                ->first();

            $nilai_pokok = $kategori->nilai_pokok;

            $penilaian = JawabanGandaPoinDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                            ->where('dn_soalgandapoin.dn_tes_id',$kategori->tes_id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();
            $total_nilai = [];
            foreach ($penilaian as $item) {
                $total_nilai = (int)$item->total_nilai;
            }

            $nilai = $total_nilai;
            $akumulasi = ($nilai_pokok/100)*$nilai;


            if($kategori->kategori == "Kedinasan"){
                $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                    case 7 :
                        // if($sudah_ada->twk == null){
                            // $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_twk' => $nilai,
                                'twk' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 8 :
                        // if($sudah_ada->tiu == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tiu' => $nilai,
                                'tiu' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 9 :
                        // if($sudah_ada->tkp == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tkp' => $nilai,
                                'tkp' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    }
                }
            }
            elseif($kategori->kategori == "TNI/Polri"){
                $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();

                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                        case 1:
                            // if($sudah_ada->mtk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_mtk' => $nilai,
                                    'mtk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 2:
                            // if($sudah_ada->ipu_wk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_ipu' => $nilai,
                                    'ipu_wk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 3:
                            // if($sudah_ada->bing == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bing' => $total_nilai,
                                    'bing' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 4:
                            // if($sudah_ada->bin == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bin' => $total_nilai,
                                    'bin' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                    }
                }
            }

            Alert::success('Berhasil Menjawab', "Jawabanmu $request->jawaban");
            return redirect()->back();
        }
        else{
            JawabanGandaPoinDinas::create([
                'pelajar_id' => $pelajar,
                'dn_soalgandapoin_id' => $id,
                'jawaban' => $request->jawaban,
                'nilai' => $nilai,
            ]);

            $kategori = SoalDinasGandaPoin::join('dn_tes','dn_tes.id','=','dn_soalgandapoin.dn_tes_id')
                                ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id','dn_tes.nilai_pokok','dn_tes.id as tes_id')
                                ->where('dn_soalgandapoin.id', $id)
                                ->first();

            $nilai_pokok = $kategori->nilai_pokok;

            $penilaian = JawabanGandaPoinDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                            ->where('dn_soalgandapoin.dn_tes_id',$kategori->tes_id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();
            $total_nilai = [];
            foreach ($penilaian as $item) {
                $total_nilai = (int)$item->total_nilai;
            }

            $nilai = $total_nilai;
            $akumulasi = ($nilai_pokok/100)*$nilai;


            if($kategori->kategori == "Kedinasan"){
                $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                    case 7 :
                        // if($sudah_ada->twk == null){
                            // $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_twk' => $nilai,
                                'twk' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 8 :
                        // if($sudah_ada->tiu == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tiu' => $nilai,
                                'tiu' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    case 9 :
                        // if($sudah_ada->tkp == null){
                        //     $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'nilai_tkp' => $nilai,
                                'tkp' => $akumulasi,
                            ]);
                            $twk = $sudah_ada->twk;
                            $tiu = $sudah_ada->tiu;
                            $tkp = $sudah_ada->tkp;
                            $total_akumulasi = $twk + $tiu + $tkp;
                            $sudah_ada->update([
                                'total_nilai' => $total_akumulasi,
                            ]);
                        // }
                        break;
                    }
                }
            }
            elseif($kategori->kategori == "TNI/Polri"){
                $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();

                if(isset($sudah_ada)){
                    $mapel = $kategori->mapel_id;
                    switch ($mapel) {
                        case 1:
                            // if($sudah_ada->mtk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_mtk' => $nilai,
                                    'mtk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 2:
                            // if($sudah_ada->ipu_wk == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_ipu' => $nilai,
                                    'ipu_wk' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 3:
                            // if($sudah_ada->bing == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bing' => $total_nilai,
                                    'bing' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                        case 4:
                            // if($sudah_ada->bin == null){
                                // $total_akumulasi = $akm + $akumulasi;
                                $sudah_ada->update([
                                    'nilai_bin' => $total_nilai,
                                    'bin' => $akumulasi,
                                ]);
                                $mtk = $sudah_ada->mtk;
                                $bin = $sudah_ada->bin;
                                $bing = $sudah_ada->bing;
                                $ipu_wk = $sudah_ada->ipu_wk;
                                $total_akumulasi = $mtk + $bin + $bing + $ipu_wk;
                                $sudah_ada->update([
                                    'total_nilai' => $total_akumulasi,
                                ]);
                            // }
                            break;
                    }
                }
            }

            Alert::success('Berhasil Menjawab', "Jawabanmu $request->jawaban");
            return redirect()->back();
        }
    }

    public function reviewGandaPoin($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $review = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                    ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                    ->where('dn_soalgandapoin.dn_tes_id', $id)
                                    ->where('status', null)
                                    ->orderBy('nomor_soal', 'asc')
                                    ->get();

        $soal = SoalDinasGandaPoin::select('id')->where('dn_tes_id', $id)->count();
        $soal_terjawab = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $id)
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                            ->whereNotNull('dn_jawabangandapoin.jawaban')
                                            ->where('status', null)
                                            ->count();
        $penilaian = JawabanGandaPoinDinas::select(DB::raw('SUM(nilai) as total_nilai'))
                                            ->join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                            ->where('dn_soalgandapoin.dn_tes_id',$id)
                                            ->where('status', null)
                                            ->groupBy('pelajar_id')
                                            ->get();

        $total_nilai = [];
        foreach ($penilaian as $item) {
           $total_nilai = (int)$item->total_nilai;
        }

        $nilai_pokok = TesDinas::find($id);

        $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori')
                                ->where('dn_tes.id', $id)
                                ->first();

        if($kategori->kategori == "Kedinasan"){
            $nilai_akm = RekapDinas::select('total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            $total_akumulasi = (float)$nilai_akm->total_nilai;
        }
        elseif($kategori->kategori == "TNI/Polri"){
            $nilai_akm = RekapTniPolri::select('total_nilai')->where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            $total_akumulasi = (float)$nilai_akm->total_nilai;
        }


        return view('pelajar.dinas.soal.reviewgandapoin', compact('review','soal','soal_terjawab','user','total_nilai','nilai_pokok','id','total_akumulasi'));
    }

    public function kumpulkanGandaPoin(Request $request, $id){
        $pelajar = Auth::user()->id;
        $nilai = $request->n;
        $pokok = $request->p;
        $jumlah_soal = $request->s;
        $akm = $request->akm;

        $sudah_kerja = Penilaian::where('pelajar_id', $pelajar)->where('dn_tes_id', $id)->where('status', null)->first();

        if($sudah_kerja){
            Alert::error('Anda Sudah Mengerjakan');
            return redirect()->back();
        }
        $akumulasi = ($pokok/100)*$nilai;

        Penilaian::create([
            'dn_tes_id' => $id,
            'pelajar_id' => $pelajar,
            'nilai' => $nilai,
            'akumulasi' => $akumulasi,
        ]);

        $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id')
                                ->where('dn_tes.id', $id)
                                ->first();

        if($kategori->kategori == "Kedinasan"){
            $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){
                $mapel = $kategori->mapel_id;
                switch ($mapel) {
                case 7 :
                    $total_akumulasi = $akm + $akumulasi;
                    $sudah_ada->update([
                        'twk' => $akumulasi,
                        'total_nilai' => $total_akumulasi,
                    ]);
                    break;
                case 8 :
                    $total_akumulasi = $akm + $akumulasi;
                    $sudah_ada->update([
                        'tiu' => $akumulasi,
                        'total_nilai' => $total_akumulasi,
                    ]);
                    break;
                case 9 :
                    $total_akumulasi = $akm + $akumulasi;
                    $sudah_ada->update([
                        'tkp' => $akumulasi,
                        'total_nilai' => $total_akumulasi,
                    ]);
                    break;
                }
            }
        }
        elseif($kategori->kategori == "TNI/Polri"){
            $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $pelajar)->first();
            if(isset($sudah_ada)){
                $mapel = $kategori->mapel_id;
                switch ($mapel) {
                    case 1:
                        if($sudah_ada->mtk == null){
                            $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'mtk' => $akumulasi,
                                'total_nilai' => $total_akumulasi,
                            ]);
                        }
                        break;
                    case 2:
                        if($sudah_ada->ipu_wk == null){
                            $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'ipu_wk' => $akumulasi,
                                'total_nilai' => $total_akumulasi,
                            ]);
                        }
                        break;
                    case 3:
                        if($sudah_ada->bing == null){
                            $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'bing' => $akumulasi,
                                'total_nilai' => $total_akumulasi,
                            ]);
                        }
                        break;
                    case 4:
                        if($sudah_ada->bin == null){
                            $total_akumulasi = $akm + $akumulasi;
                            $sudah_ada->update([
                                'bin' => $akumulasi,
                                'total_nilai' => $total_akumulasi,
                            ]);
                        }
                        break;
                }
            }
        }


        Alert::success('Jawaban Sudah Dikumpulkan','Terima Kasih Sudah Mengerjakan');
        return redirect()->route('pelajar.dinas.nilai', $id);
    }

    public function nilaiGandaPoin($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $jawab_benar = JawabanGandaPoinDinas::join('dn_soalgandapoin','dn_soalgandapoin.id','=','dn_jawabangandapoin.dn_soalgandapoin_id')
                                            ->where('dn_soalgandapoin.dn_tes_id', $id)
                                            ->where('dn_jawabangandapoin.pelajar_id', $pelajar)
                                            ->where('dn_jawabangandapoin.nilai', 1)
                                            ->count();

        $soal = SoalDinasGandaPoin::where('dn_tes_id', $id)->count();
        $nilai = Penilaian::where('pelajar_id', $pelajar)->where('dn_tes_id', $id)->where('status', null)->first();
        $prosentase = TesDinas::find($id);

        return view('pelajar.dinas.soal.nilai', compact('user','jawab_benar','soal','nilai','prosentase'));
    }

    // TOEFL


}
