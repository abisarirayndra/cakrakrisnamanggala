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
use Carbon\Carbon;
use App\AbsensiPendidik;
use App\AbsensiPelajar;
use App\AbsensiStaf;
use Image;
use PDF;

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
        return view('staf-admin.jadwal.edit-jadwal', compact('user','jadwal','admin','markas','mapel','pendidik','kelas'));
    }

    public function updateJadwal($id, Request $request){
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->all());
        Alert::toast('Edit Jadwal Berhasil','success');
        return redirect()->back();
    }

    // Absensi
    public function berandaAbsensi(){
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $sekarang = Carbon::now()->format('Y-m-d');
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                            ->join('users','users.id','=','adm_jadwal.pendidik_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_jadwal.staf_id', $admin)
                            ->whereDate('adm_jadwal.mulai', $sekarang)
                            ->orderBy('adm_jadwal.mulai', 'asc')
                            ->get();

        return view('staf-admin.absensi.index', compact('user','jadwal'));
    }

    public function absensi($id){
        $user = Auth::user()->nama;
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                            ->join('users','users.id','=','adm_jadwal.pendidik_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_jadwal.id', $id)
                            ->firstOrFail();
        $pendidik = AbsensiPendidik::select('users.nama','adm_pendidik.foto','adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.status')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->get();
        $pelajar = AbsensiPelajar::select('users.nama','adm_pelajars.foto','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('adm_pelajars','adm_pelajars.pelajar_id','=','users.id')
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->get();

        return view('staf-admin.absensi.absen', compact('user','jadwal','pendidik','pelajar'));
    }

    public function absenStaf(){
        $user = Auth::user()->nama;
        $id_staf = Auth::user()->id;
        $now = Carbon::today()->format('Y-m-d');
        // return $now;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $id_staf)->first();
        $staf = AbsensiStaf::select('roles.role','users.nama','adm_pendidik.foto','adm_absensi_staf.datang','adm_absensi_staf.pulang','adm_absensi_staf.status')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereDate('datang', $now)
                            ->get();
        return view('staf-admin.absensi.absensi-staf', compact('user','staf'));
    }

    public function uploadAbsensi(Request $request){
        $pengguna = User::where('nomor_registrasi', $request->token)->firstOrFail();
            if($pengguna){
                if($pengguna->role_id == 3){
                        $pendidik = AbsensiPendidik::where('pendidik_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                        if(isset($pendidik)){
                            Alert::toast('Sudah Melakukan Absen','error');
                            return redirect()->back();
                        }else{
                            AbsensiPendidik::create([
                                'jadwal_id' => $request->jadwal_id,
                                'pendidik_id' => $pengguna->id,
                                'datang' => $request->datang,
                                'status' => $request->status,
                            ]);
                            Alert::toast('Absensi Berhasil','success');
                            return redirect()->back();
                        }
                }
                if($pengguna->role_id == 4){
                    $pelajar = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                    if(isset($pelajar)){
                        Alert::toast('Sudah Melakukan Absen','error');
                        return redirect()->back();
                    }else{
                        AbsensiPelajar::create([
                            'jadwal_id' => $request->jadwal_id,
                            'pelajar_id' => $pengguna->id,
                            'datang' => $request->datang,
                            'status' => $request->status,
                        ]);
                        Alert::toast('Absensi Berhasil','success');
                        return redirect()->back();
                    }
                }
                elseif($pengguna->role_id == 7 || $pengguna->role_id == 2 || $pengguna->role_id == 1){
                    Alert::error('Tidak Bisa Akses');
                    return redirect()->back();
                }
            }else{
                Alert::toast('Pengguna Tidak Ditemukan','error');
                return redirect()->back();
            }
    }
    public function uploadAbsensiStaf(Request $request){
        $pengguna = User::where('nomor_registrasi', $request->token)->firstOrFail();
            if($pengguna){
                if($pengguna->role_id == 7 || $pengguna->role_id == 2 || $pengguna->role_id == 1){
                    $now = Carbon::now()->format('Y-m-d');
                    $staf = AbsensiStaf::where('staf_id', $pengguna->id)->whereDate('datang', $now)->first();
                    if(isset($staf)){
                        Alert::toast('Sudah Melakukan Absen','error');
                        return redirect()->back();
                    }else{
                        AbsensiStaf::create([
                            'staf_id' => $pengguna->id,
                            'datang' => $request->datang,
                            'status' => $request->status,
                        ]);
                        Alert::toast('Absensi Berhasil','success');
                        return redirect()->back();
                    }
                }
                elseif($pengguna->role_id == 3 || $pengguna->role_id == 4){
                    Alert::error('Tidak Bisa Akses');
                    return redirect()->back();
                }
            }else{
                Alert::toast('Pengguna Tidak Ditemukan','error');
                return redirect()->back();
            }
    }

    // Pendidik
    public function scanAbsensiPendidik(){
        $user = Auth::user()->nama;
        $token = Auth::user()->nomor_registrasi;
        $data = Pendidik::where('pendidik_id', Auth::user()->id)->firstOrFail();
        $jadwal = AbsensiPendidik::select('adm_absensi_pendidik.id','mapels.mapel','kelas.nama as kelas','adm_absensi_pendidik.jurnal','adm_jadwal.mulai','adm_jadwal.selesai','adm_absensi_pendidik.status')
                                    ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pendidik.jadwal_id')
                                    ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                                    ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                                    ->where('adm_absensi_pendidik.pendidik_id', Auth::user()->id)
                                    ->where('adm_absensi_pendidik.pulang', null)
                                    ->orderBy('adm_absensi_pendidik.id', 'desc')
                                    ->get();


        return view('pendidik.absensi.index', compact('user','token','jadwal','data'));
    }
    public function jurnalPendidik($id){
        $user = Auth::user()->nama;
        $jadwal = AbsensiPendidik::select('adm_absensi_pendidik.id','mapels.mapel','kelas.nama as kelas','adm_absensi_pendidik.jurnal','adm_jadwal.mulai','adm_jadwal.selesai','adm_absensi_pendidik.status')
                                    ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pendidik.jadwal_id')
                                    ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                                    ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                                    ->where('adm_absensi_pendidik.id', $id)
                                    ->firstOrFail();
        return view('pendidik.absensi.isi-jurnal', compact('user','jadwal'));
    }
    public function upJurnalPendidik($id, Request $request){
        $absensi = AbsensiPendidik::findOrFail($id);
        if($request->jurnal == null){
            return redirect()->back()->withErrors(['msg' => 'Jurnal tidak boleh kosong']);
        }
        $absensi->update([
            'jurnal' => $request->jurnal,
        ]);
        Alert::toast('Jurnal Berhasil Diupload','success');
        return redirect()->route('pendidik.absensi');
    }
    public function selesaiPendidik($id, Request $request){
        $absensi = AbsensiPendidik::findOrFail($id);
        $absensi->update([
            'pulang' => $request->pulang,
        ]);
        Alert::toast('Pembelajaran Selesai', 'success');
        return redirect()->route('pendidik.absensi');
    }
    public function historiMengajar(Request $request){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $id)->firstOrFail();
        $mapel = Mapel::all();
        $kelas = Kelas::where('markas_id', $markas->markas_id)->orderBy('nama','asc')->get();
        $jadwal = AbsensiPendidik::select('mapels.mapel','kelas.nama as kelas','adm_jadwal.mulai','adm_jadwal.selesai',
                                'adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.jurnal','adm_absensi_pendidik.status')
                            ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                            ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pendidik.jadwal_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_absensi_pendidik.pendidik_id', $id)
                            ->where('adm_jadwal.kelas_id', $request->kelas)
                            ->whereMonth('adm_jadwal.mulai', $request->bulan)
                            ->whereYear('adm_jadwal.mulai', $request->tahun)
                            ->whereNotNull('adm_absensi_pendidik.jurnal')
                            ->orderBy('adm_jadwal.mulai', 'desc')
                            ->get();
        $kelas_id = $request->kelas;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return view('pendidik.absensi.histori',compact('mapel','kelas','jadwal','kelas_id','bulan','tahun','user'));
    }

    // Pelajar
    public function scanAbsensiPelajar(){
        $user = Auth::user()->nama;
        $token = Auth::user()->nomor_registrasi;
        $jadwal = AbsensiPelajar::select('adm_absensi_pelajar.id','mapels.mapel','kelas.nama as kelas','adm_jadwal.mulai',
                                'adm_jadwal.selesai','adm_absensi_pelajar.status','adm_absensi_pelajar.datang')
                                    ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pelajar.jadwal_id')
                                    ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                                    ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                                    ->where('adm_absensi_pelajar.pelajar_id', Auth::user()->id)
                                    ->where('adm_absensi_pelajar.pulang', null)
                                    ->orderBy('adm_absensi_pelajar.id', 'desc')
                                    ->get();

        return view('pelajar.absensi.index', compact('user','token','jadwal'));
    }

    public function selesaiPelajar($id, Request $request){
        $absensi = AbsensiPelajar::findOrFail($id);
        $absensi->update([
            'pulang' => $request->pulang,
        ]);
        Alert::toast('Pembelajaran Selesai', 'success');
        return redirect()->route('pelajar.absensi');
    }
    public function historiPelajar(Request $request){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $jadwal = AbsensiPelajar::select('mapels.mapel','kelas.nama as kelas','adm_jadwal.mulai','adm_jadwal.selesai',
                                'adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                            ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                            ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pelajar.pelajar_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_absensi_pelajar.pelajar_id', $id)
                            ->whereMonth('adm_jadwal.mulai', $request->bulan)
                            ->whereYear('adm_jadwal.mulai', $request->tahun)
                            ->whereNotNull('adm_absensi_pelajar.pulang')
                            ->orderBy('adm_jadwal.mulai', 'desc')
                            ->get();

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return view('pelajar.absensi.histori', compact('user','jadwal','bulan','tahun'));
    }
    public function rekapAbsensiPembelajaran(Request $request){
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $admin)->first();
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
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kelas_id = $request->kelas;

        return view('staf-admin.absensi.rekap', compact('user','kelas','jadwal','bulan','tahun','kelas_id'));
    }
    public function lihatRekapAbsensiPembelajaran($id){
        $user = Auth::user()->nama;
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                        ->join('users','users.id','=','adm_jadwal.pendidik_id')
                        ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                        ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                        ->where('adm_jadwal.id', $id)
                        ->firstOrFail();
        $pendidik = AbsensiPendidik::select('users.nama','adm_absensi_pendidik.jurnal','adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.status')
                        ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                        ->where('adm_absensi_pendidik.jadwal_id', $id)
                        ->orderBy('adm_absensi_pendidik.datang', 'asc')
                        ->get();
        $pelajar = AbsensiPelajar::select('users.nama','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                        ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                        ->where('adm_absensi_pelajar.jadwal_id', $id)
                        ->orderBy('adm_absensi_pelajar.datang', 'asc')
                        ->get();

        return view('staf-admin.absensi.lihat-rekap', compact('user','pendidik','pelajar','jadwal'));
    }
    public function cetakJurnalHarian($id){
        $user = Auth::user()->nama;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', Auth::user()->id)->first();
        $jadwal = Jadwal::select('mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                        ->join('users','users.id','=','adm_jadwal.pendidik_id')
                        ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                        ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                        ->where('adm_jadwal.id', $id)
                        ->firstOrFail();
        $pendidik = AbsensiPendidik::select('users.nama','adm_absensi_pendidik.jurnal','adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.status')
                        ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                        ->where('adm_absensi_pendidik.jadwal_id', $id)
                        ->orderBy('adm_absensi_pendidik.datang', 'asc')
                        ->get();
        $pelajar = AbsensiPelajar::select('users.nama','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                        ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                        ->where('adm_absensi_pelajar.jadwal_id', $id)
                        ->orderBy('adm_absensi_pelajar.datang', 'asc')
                        ->get();

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('staf-admin.absensi.cetak-jurnal', ['logo'=>$en_logo,
                                'jadwal'=>$jadwal,
                                'pelajar'=>$pelajar,
                                'pendidik'=>$pendidik,
                                'user' => $user,
                                'markas' => $markas])->setPaper('a4','landscape');
        return $pdf->stream();
    }
    public function rekapAbsensiStaf(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $admin)->first();
        $staf = AbsensiStaf::select('roles.role','users.nama','adm_absensi_staf.datang','adm_absensi_staf.pulang','adm_absensi_staf.status','adm_absensi_staf.jurnal')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereMonth('adm_absensi_staf.datang', $bulan)
                            ->whereYear('adm_absensi_staf.datang', $tahun)
                            ->orderBy('adm_absensi_staf.id', 'desc')
                            ->get();
        return view('staf-admin.absensi.rekap-staf', compact('bulan','tahun','user','staf'));
    }
    public function cetakJurnalStaf(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user = Auth::user()->nama;
        $admin = Auth::user()->id;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $admin)->first();
        $staf = AbsensiStaf::select('roles.role','users.nama','adm_absensi_staf.datang','adm_absensi_staf.pulang','adm_absensi_staf.status','adm_absensi_staf.jurnal')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereMonth('adm_absensi_staf.datang', $bulan)
                            ->whereYear('adm_absensi_staf.datang', $tahun)
                            ->orderBy('adm_absensi_staf.id', 'desc')
                            ->get();
        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('staf-admin.absensi.cetak-jurnal-staf', ['logo'=>$en_logo,
                                'staf'=> $staf,
                                'bulan'=>$bulan,
                                'tahun'=>$tahun,
                                'user' => $user,
                                'markas' => $markas])->setPaper('a4','landscape');
        return $pdf->stream();
    }

}
