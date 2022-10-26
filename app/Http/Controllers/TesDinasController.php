<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\TesDinas;
use App\Mapel;
use App\User;
use App\Penilaian;
use App\PaketDinas;
use App\RekapDinas;
use App\RekapTniPolri;


class TesDinasController extends Controller
{
    public function tambahTes(Request $request, $id){
        $mapel = $request->mapel_id;
        $sudah_mapel = TesDinas::where('mapel_id', $mapel)->where('dn_paket_id', $id)->first();
        if(isset($sudah_mapel)){
            Alert::toast('Mata Pelajaran Sudah Ada','error');
            return redirect()->back();
        }
        else {
            TesDinas::create([
                'dn_paket_id' => $id,
                'mapel_id' => $request->mapel_id,
                'nilai_pokok' => $request->nilai_pokok,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'pengajar_id' => $request->pengajar_id,
            ]);

            Alert::toast('Tambah Tes Berhasil','success');
            return redirect()->route('admin.dinas.lihatpaket', $id);
        }

    }

    public function hapusTes($id){
        $tes = TesDinas::find($id);
        $tes->delete();

        Alert::toast('Hapus Tes Berhasil','success');
        return redirect()->back();

    }

    public function editTes($id){
        $user = Auth::user()->nama;
        $tes = TesDinas::find($id);
        $mapel = Mapel::all();
        $pengajar = User::where('role_id', 3)->get();

        return view('admin.dinas.tes.edittes', compact('user','tes','mapel','pengajar'));
    }

    public function updateTes(Request $request, $id){
        $tes = TesDinas::find($id);
        $tes->update($request->all());

        Alert::toast('Update Tes Berhasil','success');
        return redirect()->route('admin.dinas.lihatpaket', $tes->dn_paket_id);
    }

    public function monitor(){
        $user = Auth::user()->nama;
        $paket_aktif = PaketDinas::select('dn_tes.token','mapels.mapel','dn_tes.nilai_pokok','dn_tes.mulai','dn_tes.selesai','dn_tes.id','dn_pakets.nama_paket')
                                    ->join('dn_tes','dn_tes.dn_paket_id','=','dn_pakets.id')
                                    ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                                    ->whereNotNull('dn_tes.token')
                                    ->where('status', 1)
                                    ->get();
        // return $paket_aktif;
        return view('admin.dinas.tes.monitor', compact('paket_aktif','user'));
    }

    public function monitorTes($id){
        $user = Auth::user()->nama;
        // $tes = TesDinas::find($id);
        $pengguna = User::where('kelas_id', 1)->get();

        return view('admin.dinas.tes.monitor_lihat', compact('pengguna','user','id'));
    }

    public function diskualifikasi($id, Request $request){

        $nilai = Penilaian::create([
            'dn_tes_id' => $request->dn_tes_id,
            'pelajar_id' => $request->pelajar_id,
            'nilai' => 0,
            'akumulasi' => 0,
        ]);

       $kategori = TesDinas::join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                                ->select('dn_pakets.id','dn_pakets.kategori','dn_tes.mapel_id')
                                ->where('dn_tes.id', $id)
                                ->first();

        if($kategori->kategori == "Kedinasan"){
            $sudah_ada = RekapDinas::where('dn_paket_id', $kategori->id)->where('pelajar_id', $request->pelajar_id)->first();
            if(isset($sudah_ada)){
                $mapel = $kategori->mapel_id;
                switch ($mapel) {
                case 7 :
                    $sudah_ada->update([
                        'twk' => 0,
                    ]);
                    break;
                case 8 :
                    $sudah_ada->update([
                        'tiu' => 0,
                    ]);
                    break;
                case 9 :
                    $sudah_ada->update([
                        'tkp' => 0,
                    ]);
                    break;
                }
            }
        }
        elseif($kategori->kategori == "TNI/Polri"){
            $sudah_ada = RekapTniPolri::where('dn_paket_id', $kategori->id)->where('pelajar_id', $request->pelajar_id)->first();
            if(isset($sudah_ada)){
                $mapel = $kategori->mapel_id;
                switch ($mapel) {
                    case 1:
                        if($sudah_ada->mtk == null){
                            $sudah_ada->update([
                                'mtk' => 0,
                            ]);
                        }
                        break;
                    case 2:
                        if($sudah_ada->ipu_wk == null){
                            $sudah_ada->update([
                                'ipu_wk' => 0,
                            ]);
                        }
                        break;
                    case 3:
                        if($sudah_ada->bing == null){
                            $sudah_ada->update([
                                'bing' => 0,
                            ]);
                        }
                        break;
                    case 4:
                        if($sudah_ada->bin == null){
                            $sudah_ada->update([
                                'bin' => 0,
                            ]);
                        }
                        break;
                }
            }
        }
        Alert::success('Diskualifikasi Berhasil','Peserta Didik Tidak Bisa Akses');
        return redirect()->back();

    }

    //Pendidik
    public function pendidikTes($id){
        $user = Auth::user()->nama;
        $pengajar = Auth::user()->id;
        $tes = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                    ->select('mapels.mapel','dn_tes.nilai_pokok','dn_tes.mulai','dn_tes.selesai','dn_tes.id')
                    ->where('dn_tes.dn_paket_id', $id)
                    ->where('dn_tes.pengajar_id', $pengajar)
                    ->get();

        return view('pendidik.dinas.tes.tes', compact('user','tes'));
    }

    //Pelajar
    public function pelajarTes($id){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $tes = TesDinas::join('mapels','mapels.id','=','dn_tes.mapel_id')
                        ->select('mapels.mapel','dn_tes.nilai_pokok','dn_tes.mulai','dn_tes.selesai','dn_tes.id')
                        ->where('dn_tes.dn_paket_id', $id)
                        ->get();

        return view('pelajar.dinas.tes.tes', compact('user','tes'));
    }

    public function masukToken(){
        $user = Auth::user()->nama;
        return view('pelajar.dinas.tes.masuk_token',compact('user'));
    }

    public function submitToken(Request $request){
        $tes = TesDinas::whereRaw("BINARY `token`= ?", [$request->token])->first();
        if($tes){
            return redirect()->route('pelajar.dinas.persiapan', $tes->id);
        }else{
            Alert::error('Token Tidak Ditemukan', 'Gagal Akses Soal');
            return redirect()->back();
        }
    }
}
