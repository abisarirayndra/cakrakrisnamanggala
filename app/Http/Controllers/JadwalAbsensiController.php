<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Mapel;
use App\Pendidik;
use App\Kelas;
use App\User;
use App\Jadwal;
use Alert;

class JadwalAbsensiController extends Controller
{
    public function index(Request $request){
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $admin)->firstOrFail();
        $mapel = Mapel::all();
        $pendidik = User::select('users.id','users.nama')->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')->where('users.role_id', 3)->where('adm_pendidik.markas_id', $markas->markas_id)->get();
        $kelas = Kelas::where('markas_id', $markas->markas_id)->orderBy('nama','asc')->get();
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                            ->join('users','users.id','=','adm_jadwal.pendidik_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_jadwal.staf_id', $admin)
                            ->where('adm_jadwal.kelas_id', $request->kelas)
                            ->whereMonth('adm_jadwal.mulai', $request->bulan)
                            ->whereYear('adm_jadwal.mulai', $request->tahun)
                            ->orderBy('adm_jadwal.mulai', 'asc')
                            ->get();
        $kelas_id = $request->kelas;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        return view('staf-admin.jadwal.index', compact('user','mapel','pendidik','kelas','admin','jadwal','kelas_id','bulan','tahun'));
    }

    public function tambahJadwal(Request $request){
        Jadwal::create($request->all());
        Alert::toast('Tambah Jadwal Berhasil', 'success');
        return redirect()->back();
    }

    public function hapusJadwal($id){
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        Alert::toast('Hapus Jadwal Berhasil','success');
        return redirect()->back();
    }

    public function editJadwal($id){
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $admin)->firstOrFail();
        $mapel = Mapel::all();
        $pendidik = User::select('users.id','users.nama')->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')->where('users.role_id', 3)->where('adm_pendidik.markas_id', $markas->markas_id)->get();
        $kelas = Kelas::where('markas_id', $markas->markas_id)->orderBy('nama','asc')->get();
        $jadwal = Jadwal::findOrFail($id);
        // return $jadwal;
        return view('staf-admin.jadwal.edit-jadwal', compact('user','jadwal','admin','markas','mapel','pendidik','kelas'));
    }

}
