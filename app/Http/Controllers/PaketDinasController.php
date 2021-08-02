<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\PaketDinas;
use App\Kelas;
use App\KelasDinas;
use App\TesDinas;
use App\Mapel;
use App\User;

class PaketDinasController extends Controller
{
    // /Admin

    public function paket(){
        $user = Auth::user()->nama;
        $paket = PaketDinas::orderBy('id','desc')->get();

        return view('admin.dinas.paket.paket', compact('user','paket'));
    }

    public function tambah(){
        $user = Auth::user()->nama;
        return view('admin.dinas.paket.tambah', compact('user'));
    }

    public function up(Request $request){
        PaketDinas::create($request->all());
        Alert::toast('Paket Soal Berhasil Dibuat','success');

        return redirect()->route('admin.dinas.paket');
    }

    public function lihat($id){
        $user = Auth::user()->nama;
        $paket = PaketDinas::find($id);
        $kelas = KelasDinas::select('kelas.nama','dn_kelas.id')
                            ->join('kelas','kelas.id','=','dn_kelas.kelas_id')
                            ->where('dn_kelas.dn_paket_id', $id)->get();
        $list_kelas = Kelas::all();

        // Tes
        $mapel = Mapel::all();
        $pengajar = User::where('role_id', 3)->get();
        $tes = TesDinas::select('mapels.mapel','dn_tes.nilai_pokok','dn_tes.mulai','dn_tes.selesai','dn_tes.id','users.nama','dn_tes.durasi')
                        ->join('mapels','mapels.id','=','dn_tes.mapel_id')
                        ->join('users','users.id','=','dn_tes.pengajar_id')
                        ->where('dn_tes.dn_paket_id', $id)
                        ->get();


        return view('admin.dinas.paket.lihat', compact('paket','user','kelas','list_kelas','tes','mapel','pengajar'));
    }

    public function tambahKelas(Request $request, $id){
            $kelas = $request->kelas_id;
            $kelas_sudah = KelasDinas::where('dn_paket_id', $id)->where('kelas_id',$kelas)->first();

            if(isset($kelas_sudah)){
                Alert::toast('Kelas Sudah Ada','error');
                return redirect()->route('admin.dinas.lihatpaket', $id);
            }
            else {
                KelasDinas::create([
                    'dn_paket_id' => $id,
                    'kelas_id' => $request->kelas_id,
                ]);

                Alert::toast('Tambah Kelas Berhasil','success');
                return redirect()->route('admin.dinas.lihatpaket', $id);
            }
    }

    public function hapusKelas($id){
        $kelas = KelasDinas::find($id);
        $kelas->delete();

        Alert::toast('Hapus Kelas Berhasil','success');
        return redirect()->back();

    }

    public function hapusPaket($id){
        $paket = PaketDinas::find($id);
        $paket->delete();

        Alert::toast('Hapus Paket Berhasil','success');
        return redirect()->back();
    }

    public function editPaket($id){
        $user = Auth::user()->nama;
        $paket = PaketDinas::find($id);

        return view('admin.dinas.paket.edit', compact('paket','user'));
    }

    public function updatePaket($id, Request $request){
        $paket = PaketDinas::find($id);
        $paket->update($request->all());

        Alert::toast('Update Paket Berhasil','success');
        return redirect()->route('admin.dinas.paket');
    }


    // Pendidik
    public function pendidikPaket(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $paket = TesDinas::select('dn_tes.dn_paket_id','dn_pakets.nama_paket','dn_pakets.id')
                            ->join('dn_pakets','dn_pakets.id','=','dn_tes.dn_paket_id')
                            ->where('dn_pakets.status', 1)
                            ->where('dn_tes.pengajar_id', $id)
                            ->orderBy('dn_tes.mulai','asc')
                            ->distinct()
                            ->get();

        // return $paket;
        return view('pendidik.dinas.paket.paket', compact('paket','user'));
    }

    //Pelajar
    public function pelajarPaket(){
        $user = Auth::user()->nama;
        $kelas = Auth::user()->kelas_id;

        $paket = KelasDinas::select('dn_pakets.nama_paket','dn_pakets.id')
                            ->join('dn_pakets','dn_pakets.id','=','dn_kelas.dn_paket_id')
                            ->where('dn_kelas.kelas_id', $kelas)
                            ->where('dn_pakets.status', 1)
                            ->orderBy('dn_pakets.id','desc')
                            ->get();


        return view('pelajar.dinas.paket.paket', compact('user','paket'));

    }


}
