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

class JawabanDinasController extends Controller
{
    public function upJawabanGanda($id, Request $request){
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
        if(isset($ada_jawaban)){
            $ada_jawaban->update([
                'jawaban' => $request->jawaban,
                'nilai' => $nilai,
            ]);
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

        return view('pelajar.dinas.soal.reviewganda', compact('review','soal','soal_terjawab','user','total_nilai','nilai_pokok','id','total_akumulasi'));
    }

    public function kumpulkan(Request $request, $id){
        $pelajar = Auth::user()->id;
        $benar = $request->n;
        $pokok = $request->p;
        $jumlah_soal = $request->s;
        $akm = $request->akm;

        $sudah_kerja = Penilaian::where('pelajar_id', $pelajar)->where('dn_tes_id', $id)->where('status', null)->first();

        if($sudah_kerja){
            Alert::error('Anda Sudah Mengerjakan');
            return redirect()->back();
        }

        $nilai = ($benar/$jumlah_soal)*100;
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
                    if($sudah_ada->twk == null){
                        $total_akumulasi = $akm + $akumulasi;
                        $sudah_ada->update([
                            'twk' => $akumulasi,
                            'total_nilai' => $total_akumulasi,
                        ]);
                    }
                    break;
                case 8 :
                    if($sudah_ada->tiu == null){
                        $total_akumulasi = $akm + $akumulasi;
                        $sudah_ada->update([
                            'tiu' => $akumulasi,
                            'total_nilai' => $total_akumulasi,
                        ]);
                    }
                    break;
                case 9 :
                    if($sudah_ada->tkp == null){
                        $total_akumulasi = $akm + $akumulasi;
                        $sudah_ada->update([
                            'tkp' => $akumulasi,
                            'total_nilai' => $total_akumulasi,
                        ]);
                    }
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

        return view('pelajar.dinas.soal.nilai', compact('user','pelajar','kelas','jawab_benar','soal','nilai','prosentase','jenis','mapel'));
    }

    public function upJawabanGandaPoin($id, Request $request){
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
