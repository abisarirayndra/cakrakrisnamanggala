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
use DB;
use App\Imports\SoalGandaImport;
use App\Imports\SoalGandaPoinImport;
use Maatwebsite\Excel\Facades\Excel;

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

        return redirect()->back();
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
        return view('pendidik.dinas.soal.gandapoin', compact('user','id','soal'));
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

        if(SoalDinasGanda::where('dn_tes_id', $id)->where('nomor_soal', $request->nomor_soal)->first()){
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
        $paket = TesDinas::find($id);
        $essay = SoalDinasEssay::where('dn_tes_id', $id)->orderBy('id','asc')->first();
        $ganda = SoalDinasGanda::where('dn_tes_id', $id)->orderBy('id','asc')->first();
        $poin = SoalDinasGandaPoin::where('dn_tes_id', $id)->orderBy('id','asc')->first();

        $sudah = Penilaian::where('dn_tes_id', $id)->where('pelajar_id', $pelajar)->where('status', null)->first();

        return view('pelajar.dinas.tes.persiapan', compact('user','essay','ganda','poin','id','paket','sudah'));
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
                    'total_nilai' => 0,
                ]);
            }
        }

        return view('pelajar.dinas.soal.soalganda', compact('nomor','user','ganda','sudah_jawab','selesai','id'));
    }

    public function pelajarSoalGandaPoin($id, Request $request){
        $user = Auth::user()->nama;
        $pelajar = Auth::user()->id;
        $selesai = TesDinas::find($id);
        $nomor = SoalDinasGandaPoin::select('id','dn_tes_id')->where('dn_tes_id', $id)->get();
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
                    'total_nilai' => 0,
                ]);
            }
        }
        return view('pelajar.dinas.soal.soalgandapoin', compact('nomor','user','ganda','sudah_jawab','selesai','id'));
    }
}
