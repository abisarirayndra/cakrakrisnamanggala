<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\TesDinas;
use App\Mapel;
use App\User;

class TesDinasController extends Controller
{
    public function tambahTes(Request $request, $id){
        $mapel = $request->mapel_id;
        $sudah_mapel = TesDinas::where('mapel_id', $mapel)->first();
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
}
