<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\SoalDinasGanda;
use App\SoalDinasGandaPoin;
use App\SoalDinasEssay;
use App\TesDinas;

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
        return view('pendidik.dinas.soal.ganda', compact('user','id','soal'));
    }

    public function pendidikUpSoalGanda($id, Request $request){
        $request->validate([
            'soal' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'kunci' => 'required'
        ]);

        SoalDinasGanda::create([
            'dn_tes_id' => $id,
            'soal' => $request->soal,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci' => $request->kunci,
        ]);

        Alert::toast('Tambah Soal Berhasil','success');

        return redirect()->back();
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
        return view('pendidik.dinas.soal.gandapoin', compact('user','id','soal'));
    }

    public function pendidikUpSoalGandaPoin($id, Request $request){
        $request->validate([
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

        SoalDinasGandaPoin::create([
            'dn_tes_id' => $id,
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

    public function pendidikSoalEssay($id){
        $user = Auth::user()->nama;
        $soal = SoalDinasEssay::where('dn_tes_id', $id)->get();

        return view('pendidik.dinas.soal.essay', compact('user','id','soal'));
    }

    public function pendidikUpSoalEssay($id, Request $request){
        $request->validate([
            'soal' => 'required',
        ]);

        SoalDinasEssay::create([
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
        $essay = SoalDinasEssay::where('dn_tes_id', $id)->first();
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->first();

        return view('pelajar.dinas.tes.persiapan', compact('user','essay','ganda','poin','id'));
    }

    public function pelajarSoalGanda($id, Request $request){
        $user = Auth::user()->nama;
        $nomor = SoalDinasGanda::select('id','dn_tes_id')->where('dn_tes_id', $id)->get();

        if(isset($request->q)){
            $ganda = SoalDinasGanda::where('dn_tes_id', $id)->where('id', $request->q)->get();
        }

        return view('pelajar.dinas.soal.soalganda', compact('nomor','user','ganda'));
    }
}
