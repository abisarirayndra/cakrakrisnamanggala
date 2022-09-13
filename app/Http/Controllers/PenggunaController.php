<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Pendaftar;
use App\Pelajar;
use Auth;
use Alert;
use App\Kelas;
use App\Role;
use File;
use Hash;
use App\Pendidik;
use Str;
use App\Markas;
use Image;
use App\Exports\SiswaExport;
use PDF;

class PenggunaController extends Controller
{
// Pendaftar
    public function penggunaPendaftar(){
        $user = Auth::user()->nama;
        $pendaftar = User::where('role_id', 5)
                    ->orderBy('users.id', 'desc')
                    ->get();

        return view('super.pengguna.pendaftar.penggunapendaftar', compact('user','pendaftar'));
    }

    public function lihatPendaftar($id){
        $user = Auth::user()->nama;
        $pendaftar = Pelajar::join('users','users.id','=','adm_pelajars.pelajar_id')
                                ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
                                ->select('users.nama','users.email','adm_pelajars.pelajar_id','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat','adm_pelajars.sekolah','adm_pelajars.wa',
                                            'adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.foto','adm_markas.markas','adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.ibu')
                                ->where('adm_pelajars.pelajar_id', $id)
                                ->firstOrFail();
        $kelas = Kelas::all();

        return view('super.pengguna.pendaftar.lihat', compact('pendaftar','user','kelas'));
    }

    public function migrasiPendaftar($id, Request $request){
        $akun = User::find($id);
        $akun->update([
            'kelas_id' => $request->kelas_id,
            'nomor_registrasi' => Str::random(6),
            'role_id' => 4,
        ]);

        Alert::toast('Migrasi Pendaftar ke Pelajar Berhasil');
        return redirect()->route('super.penggunapelajar');

    }

    public function hapusPendaftar($id){
        $akun = User::find($id);
        $pendaftar = Pelajar::where('pelajar_id', $id)->first();
        if($pendaftar){
            if($pendaftar->foto && file_exists(public_path('img/pelajar/'. $pendaftar->foto))){
                File::delete(public_path('img/pelajar/'. $pendaftar->foto));
            }
        }
        $akun->delete();
        Alert::toast('Hapus Pendaftar Berhasil');
        return redirect()->route('super.penggunapendaftar');
    }
// Pelajar
    public function penggunaPelajar(){
        $user = Auth::user()->nama;
        $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                    ->select('users.id','users.nama','kelas.nama as kelas','users.nomor_registrasi','users.email','users.updated_at')
                    ->where('role_id', 4)
                    ->orderBy('users.updated_at', 'desc')
                    ->get();
        // return $pelajar;

        return view('super.pengguna.pelajar.penggunapelajar', compact('user','pelajar'));
    }

    public function cetakPenggunaPelajar(){
        return (new SiswaExport)->download('peserta-didik.xlsx');
    }

    public function lihatPelajar($id){
        $user = Auth::user()->nama;
        $pelajar = Pelajar::join('users','users.id','=','adm_pelajars.pelajar_id')
                            // ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
                            ->select('users.id','users.nama','users.email','adm_pelajars.pelajar_id','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat','adm_pelajars.sekolah','adm_pelajars.wa',
                                        'adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.foto','adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.ibu')
                            ->where('adm_pelajars.pelajar_id', $id)
                            ->firstOrFail();
        // return $pelajar;

        return view('super.pengguna.pelajar.lihat', compact('pelajar','user'));
    }

    public function cetakPdfPelajar($id){

        $pendaftar = Pelajar::join('users','users.id','=','adm_pelajars.pelajar_id')
        // ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
                ->select('users.id','users.nama','users.email','adm_pelajars.pelajar_id','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat','adm_pelajars.sekolah','adm_pelajars.wa',
                    'adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.foto','adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.ibu')
                ->where('adm_pelajars.pelajar_id', $id)
                ->firstOrFail();
        $user = $pendaftar->nama;
        $en_foto = (string) Image::make(public_path('img/pelajar/'. $pendaftar->foto))->encode('data-url');
        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('pendaftaran.review', ['data' => $pendaftar, 'user' => $user,'foto' => $en_foto, 'logo' => $en_logo])->setPaper('a4');
        return $pdf->stream();
    }

    public function editPelajar($id){
        $user = Auth::user()->nama;
        $pelajar = User::find($id);
        $kelas = Kelas::all();
        $roles = Role::all();
        return view('super.pengguna.pelajar.edit', compact('pelajar','kelas','user','roles'));
    }

    public function updatePelajar($id, Request $request){
        $pelajar = User::find($id);
        if($request->password){
            $pelajar->update([
                'nama' => $request->nama,
                'nomor_registrasi' => $request->nomor_registrasi,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kelas_id' => $request->kelas_id,
                'role_id' => $request->role_id,
            ]);
            Alert::toast('Update Pelajar Berhasil','success');
            return redirect()->route('super.penggunapelajar');
        }else{
            $pelajar->update([
                'nama' => $request->nama,
                'nomor_registrasi' => $request->nomor_registrasi,
                'email' => $request->email,
                'kelas_id' => $request->kelas_id,
                'role_id' => $request->role_id,
            ]);
            Alert::toast('Update Pelajar Berhasil','success');
            return redirect()->route('super.penggunapelajar');
        }
    }

    public function editDataPelajar($id){
        $user = Auth::user()->nama;
        $data = User::select('nama', 'email', 'nomor_registrasi')->where('id', $id)->first();
        $pelajar = Pelajar::where('pelajar_id', $id)->first();
        return view('super.pengguna.pelajar.editdata', compact('user','pelajar','data'));
    }

    public function updateDataPelajar($id, Request $request){
        $pelajar = Pelajar::find($id);
        if($request->foto){
            $request->validate([
                'foto' => 'mimes:jpg,jpeg,png|dimensions:ratio=3/2|size:500',
            ]);
            $pelajar->update($request->all());
            Alert::toast('Update Data Berhasil','success');
            return redirect()->route('super.penggunapelajar.lihat',$id);
        }else{
            $pelajar->update([
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'sekolah' => $request->sekolah,
                'wa' => $request->wa,
                'ibu' => $request->ibu,
                'wali' => $request->wali,
                'wa_wali' => $request->wa_wali,
                'markas' => $request->markas,
            ]);
            Alert::toast('Update Data Berhasil','success');
            return redirect()->route('super.penggunapelajar.lihat',$pelajar->pelajar_id);
        }
    }

    public function suspendPelajar($id){
        $pelajar = User::find($id);
        $pelajar->update([
            'role_id' => 6,
        ]);
        Alert::toast('Akun Pelajar Disuspend','success');
        return redirect()->route('super.penggunapelajar');
    }

    public function destroyPelajar($id){
        $akun = User::find($id);
        $pendaftar = Pelajar::where('pelajar_id', $id)->first();
        if($pendaftar){
            if($pendaftar->foto && file_exists(public_path('img/pelajar/'. $pendaftar->foto))){
                File::delete(public_path('img/pelajar/'. $pendaftar->foto));
            }
        }
        $akun->delete();
        Alert::toast('Hapus Pendaftar Berhasil', 'success');
        return redirect()->route('super.penggunapelajar');
    }

// Pendidik
    public function penggunaPendidik(){
        $user = Auth::user()->nama;
        $pendidik = User::where('role_id', 3)
                    ->orderBy('users.id', 'desc')
                    ->get();
        $markas = Markas::all();

        return view('super.pengguna.pendidik.penggunapendidik', compact('user','pendidik','markas'));
    }

    public function tambahPendidik(Request $request){
        $buat = User::create([
            'nama' => $request->nama,
            'nomor_registrasi' => Str::random(6),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3,
        ]);

        Pendidik::create([
            'pendidik_id' => $buat->id,
            'mapel_id' => 10,
            'markas_id' => $request->markas_id,
        ]);

        Alert::toast('Tambah Pendidik Berhasil','success');
        return redirect()->back();
    }

    public function lihatPendidik($id){
        $user = Auth::user()->nama;
        $pendidik = Pendidik::join('users','users.id','=','adm_pendidik.pendidik_id')
                            ->join('adm_markas','adm_markas.id','=','adm_pendidik.markas_id')
                            ->select('users.id','users.nama','users.email','adm_pendidik.pendidik_id','adm_pendidik.tempat_lahir','adm_pendidik.tanggal_lahir','adm_pendidik.nik',
                                        'adm_pendidik.foto','adm_pendidik.nip','adm_pendidik.ibu','adm_markas.markas')
                            ->where('adm_pendidik.pendidik_id', $id)
                            ->first();
        // return $pelajar;

        return view('super.pengguna.pendidik.lihatpendidik', compact('pendidik','user'));
    }

    public function hapusPendidik($id){
        $pendidik = User::find($id);

        $pendidik->delete();
    }

//Staf Admin
    public function penggunaStafAdmin(){
        $user = Auth::user()->nama;
        $admin = User::where('role_id', 7)
                        ->orderBy('users.id','desc')
                        ->get();

        return view('super.pengguna.staf_admin.penggunaadmin', compact('user','admin'));
    }

    public function tambahStafAdmin(Request $request){
        $request->validate([
            'email' => 'required|unique:users',
            'nomor_registrasi' => 'required|unique:users',
        ]);

        $userMake = User::create([
            'nama' => $request->nama,
            'nomor_registrasi' => Str::random(6),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 7,
        ]);

        Pendidik::create([
            'pendidik_id' => $userMake->id,
            'mapel_id' => 10,
        ]);

        Alert::toast('Tambah Staf Admin Berhasil','success');
        return redirect()->back();
    }

    public function lihatStafAdmin($id){
        $user = Auth::user()->nama;
        $admin = Pendidik::select('users.nama','adm_pendidik.tempat_lahir','adm_pendidik.tanggal_lahir',
                        'adm_pendidik.alamat','adm_pendidik.nik','adm_pendidik.nip','mapels.mapel','adm_pendidik.wa',
                        'adm_pendidik.ibu','adm_pendidik.foto','adm_pendidik.cv','adm_pendidik.status_dapodik')
                        ->join('users','users.id','=','adm_pendidik.pendidik_id')
                        ->join('mapels','mapels.id','=','adm_pendidik.mapel_id')
                        ->where('adm_pendidik.pendidik_id', $id)->first();

        return view('super.pengguna.staf_admin.lihat', compact('user','admin'));
    }

    public function destroyStafAdmin($id){
        $staf = User::find($id);
        $staf->delete();
        Alert::toast('Hapus Staf Berhasil','success');
        return redirect()->back();
    }

//Suspended
    public function penggunaPelajarSuspend(Request $request){
        $user = Auth::user()->nama;
        $pelajar = User::join('kelas','kelas.id','=','users.kelas_id')
                    ->select('users.id','users.nama','kelas.nama as kelas','users.nomor_registrasi','users.email','users.updated_at')
                    ->where('role_id', 6)
                    ->orderBy('users.updated_at', 'desc')
                    ->get();
        return view('super.pengguna.suspended.suspended', compact('user','pelajar'));
    }

    public function lihatSuspended($id){
        $user = Auth::user()->nama;
        $pelajar = Pelajar::join('users','users.id','=','adm_pelajars.pelajar_id')
                                ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
                                ->select('users.id','users.nama','users.email', 'adm_pelajars.nik','adm_pelajars.nisn','adm_pelajars.ibu','adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat','adm_pelajars.sekolah','adm_pelajars.wa',
                                            'adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.foto','adm_markas.markas')
                                ->where('adm_pelajars.pelajar_id', $id)
                                ->first();

        return view('super.pengguna.suspended.lihat', compact('pelajar','user'));
    }

    public function cabutSuspendPelajar($id){
        $pengguna = User::find($id);
        $pengguna->update([
            'role_id' => 4,
        ]);
        Alert::toast('Suspend Dicabut','success');
        return redirect()->route('super.penggunapelajar');
    }

    public function hapusSuspendPelajar($id){
        $pelajar = User::find($id);
        $pengguna->delete();
        Alert::toast('Data Berhasil Dihapus','success');
        return redirect()->route('super.penggunasuspend');
    }
}
