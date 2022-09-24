<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Mapel;
use App\Pendidik;
use App\Pelajar;
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
        $markas_admin = Pendidik::select('markas_id')->where('pendidik_id', Auth::user()->id)->first();
        // return $markas_admin;
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                            ->join('users','users.id','=','adm_jadwal.pendidik_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_jadwal.id', $id)
                            ->firstOrFail();
        $nama_pelajar = Pelajar::select('users.id','users.nama')
                                ->join('users','users.id','=','adm_pelajars.pelajar_id')
                                ->where('adm_pelajars.markas_id', $markas_admin->markas_id)
                                ->get();
        $nama_pendidik = Pendidik::select('users.id','users.nama')
                                ->join('users','users.id','=','adm_pendidik.pendidik_id')
                                ->where('adm_pendidik.markas_id', $markas_admin->markas_id)
                                ->get();
        // return $nama_pendidik;
        $pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','adm_pendidik.foto','adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.status')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->where('adm_absensi_pendidik.keterangan',null)
                                    ->orderBy('adm_absensi_pendidik.datang', 'desc')
                                    ->get();
        $pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','adm_pelajars.foto','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('adm_pelajars','adm_pelajars.pelajar_id','=','users.id')
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->where('adm_absensi_pelajar.keterangan', null)
                                    ->orderBy('adm_absensi_pelajar.datang', 'desc')
                                    ->get();
        $izin_pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','roles.role','adm_absensi_pelajar.status','adm_absensi_pelajar.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pelajar.status', 2)
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->get();
        $izin_pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','roles.role','adm_absensi_pendidik.status','adm_absensi_pendidik.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pendidik.status', 2)
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->get();
        // return $izin_pendidik;
        return view('staf-admin.absensi.absen', compact('user','jadwal','pendidik','pelajar','nama_pelajar','nama_pendidik','izin_pendidik','izin_pelajar'));
    }

    public function absensiPulang($id){
        $user = Auth::user()->nama;
        $markas_admin = Pendidik::select('markas_id')->where('pendidik_id', Auth::user()->id)->first();
        // return $markas_admin;
        $jadwal = Jadwal::select('adm_jadwal.id','mapels.mapel','kelas.nama as kelas','users.nama','adm_jadwal.mulai','adm_jadwal.selesai')
                            ->join('users','users.id','=','adm_jadwal.pendidik_id')
                            ->join('mapels','mapels.id','=','adm_jadwal.mapel_id')
                            ->join('kelas','kelas.id','=','adm_jadwal.kelas_id')
                            ->where('adm_jadwal.id', $id)
                            ->firstOrFail();
        $nama_pelajar = Pelajar::select('users.id','users.nama')
                                ->join('users','users.id','=','adm_pelajars.pelajar_id')
                                ->where('adm_pelajars.markas_id', $markas_admin->markas_id)
                                ->get();
        $nama_pendidik = Pendidik::select('users.id','users.nama')
                                ->join('users','users.id','=','adm_pendidik.pendidik_id')
                                ->where('adm_pendidik.markas_id', $markas_admin->markas_id)
                                ->get();
        // return $nama_pendidik;
        $pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','adm_pendidik.foto','adm_absensi_pendidik.datang','adm_absensi_pendidik.pulang','adm_absensi_pendidik.status')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->where('adm_absensi_pendidik.keterangan',null)
                                    ->orderBy('adm_absensi_pendidik.datang', 'desc')
                                    ->get();
        $pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','adm_pelajars.foto','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('adm_pelajars','adm_pelajars.pelajar_id','=','users.id')
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->where('adm_absensi_pelajar.keterangan', null)
                                    ->orderBy('adm_absensi_pelajar.datang', 'desc')
                                    ->get();
        $izin_pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','roles.role','adm_absensi_pelajar.status','adm_absensi_pelajar.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pelajar.status', 2)
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->get();
        $izin_pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','roles.role','adm_absensi_pendidik.status','adm_absensi_pendidik.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pendidik.status', 2)
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->get();
        // return $izin_pendidik;
        return view('staf-admin.absensi.absen-pulang', compact('user','jadwal','pendidik','pelajar','nama_pelajar','nama_pendidik','izin_pendidik','izin_pelajar'));
    }

    public function selesaiPelajar(Request $request){
        $pengguna = User::where('nomor_registrasi', $request->token)->first();
        if($pengguna->role_id == 4){
            $pelajar = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
            if($pelajar){
                $pelajar_belum_pulang = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                if($pelajar_belum_pulang->pulang == null){
                    $pelajar_absen_pulang = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->where('pulang', null)->first();
                    $pelajar_absen_pulang->update([
                        'pulang' => $request->pulang,
                    ]);
                    Alert::success("Hai $pelajar_absen_pulang->nama !",'Selesai mengikuti pembelajaran');
                    return redirect()->back();
                }elseif($pelajar_belum_pulang->pulang != null){
                    $pelajar_sudah_pulang = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                    Alert::error("Hai $pelajar_sudah_pulang->nama !",'Kamu Sudah Absen Pulang');
                    return redirect()->back();
                }
            }else{
                $pelajar = User::where('nomor_registrasi', $request->token)->first();
                Alert::error("$pelajar->nama, Kamu Belum Absen !",'Gagal');
                return redirect()->back();
            }

        }elseif($pengguna->role_id == 3){
            Alert::error('Akses Absensi di Hp Anda','Login dan buka menu absensi, lalu isi jurnal');
            return redirect()->back();
        }
        else{
            Alert::error('Pengguna Tidak Ditemukan','Pengguna Tidak Terdaftar');
            return redirect()->back();
        }

        Alert::toast('Pembelajaran Selesai', 'success');
        return redirect()->route('pelajar.absensi');
    }


    public function absenStaf(){
        $user = Auth::user()->nama;
        $id_staf = Auth::user()->id;
        $now = Carbon::today()->format('Y-m-d');
        // return $now;
        $markas = Pendidik::select('markas_id')->where('pendidik_id', $id_staf)->first();
        $staf = AbsensiStaf::select('adm_absensi_staf.id','roles.role','users.nama','adm_absensi_staf.datang','adm_absensi_staf.pulang','adm_absensi_staf.status')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereDate('datang', $now)
                            ->get();
        $nama_staf = Pendidik::select('users.id','users.nama')
                                ->join('users','users.id','=','adm_pendidik.pendidik_id')
                                ->where('users.role_id', 7)
                                ->orWhere('users.role_id', 2)
                                ->get();
        $izin_staf = AbsensiStaf::select('adm_absensi_staf.id','roles.role','users.nama','adm_absensi_staf.status','adm_absensi_staf.keterangan')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereDate('adm_absensi_staf.created_at', $now)
                            ->where('adm_absensi_staf.status', 2)
                            ->get();
        return view('staf-admin.absensi.absensi-staf', compact('user','staf','nama_staf','izin_staf'));
    }

    public function uploadAbsensi(Request $request){
        $pengguna = User::where('nomor_registrasi', $request->token)->first();
            if($pengguna){
                if($pengguna->role_id == 3){
                        $pendidik = AbsensiPendidik::where('pendidik_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                        if(isset($pendidik)){
                            $pendidik = AbsensiPendidik::join('users','users.id','=','adm_absensi_pendidik.pendidik_id')->where('pendidik_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                            Alert::error("$pendidik->nama, Kamu Sudah Absen !",'Gagal');
                            return redirect()->back();
                        }else{
                            AbsensiPendidik::create([
                                'jadwal_id' => $request->jadwal_id,
                                'pendidik_id' => $pengguna->id,
                                'datang' => $request->datang,
                                'status' => $request->status,
                            ]);
                            $pendidik = AbsensiPendidik::join('users','users.id','=','adm_absensi_pendidik.pendidik_id')->where('pendidik_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                            Alert::success("Hai $pendidik->nama !",'Berhasil Absen');
                            return redirect()->back();
                        }
                }
                if($pengguna->role_id == 4){
                    $pelajar = AbsensiPelajar::where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                    if(isset($pelajar)){
                        $pelajar = AbsensiPelajar::join('users','users.id','=','adm_absensi_pelajar.pelajar_id')->where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                        Alert::error("$pelajar->nama, Kamu Sudah Absen !",'Gagal');
                        return redirect()->back();
                    }else{
                        AbsensiPelajar::create([
                            'jadwal_id' => $request->jadwal_id,
                            'pelajar_id' => $pengguna->id,
                            'datang' => $request->datang,
                            'status' => $request->status,
                        ]);
                        $pelajar = AbsensiPelajar::join('users','users.id','=','adm_absensi_pelajar.pelajar_id')->where('pelajar_id', $pengguna->id)->where('jadwal_id', $request->jadwal_id)->first();
                        Alert::success("Hai $pelajar->nama !",'Berhasil Absen');
                        return redirect()->back();
                    }
                }
                elseif($pengguna->role_id == 7 || $pengguna->role_id == 2 || $pengguna->role_id == 1){
                    Alert::error('Anda Bukan Pelajar/Pendidik','Silakan Ke Menu Absensi staf');
                    return redirect()->back();
                }
            }else{
                Alert::error('Pengguna Tidak Ditemukan','Pengguna Tidak Terdaftar');
                return redirect()->back();
            }
    }

    public function uploadAbsensiIzinPelajar(Request $request){
        // return $request;
        $pelajar = AbsensiPelajar::where('pelajar_id', $request->pelajar_id)->where('jadwal_id', $request->jadwal_id)->first();
        if($pelajar){
            Alert::toast('Sudah Melakukan Absen','error');
            return redirect()->back();
        }else{
            AbsensiPelajar::create([
                'jadwal_id' => $request->jadwal_id,
                'pelajar_id' => $request->pelajar_id,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);
            Alert::toast('Absensi Izin Berhasil','success');
            return redirect()->back();
        }

    }
    public function uploadAbsensiIzinPendidik(Request $request){
        // return $request;
        $pendidik = AbsensiPendidik::where('pendidik_id', $request->pendidik_id)->where('jadwal_id', $request->jadwal_id)->first();
        if($pendidik){
            Alert::toast('Sudah Melakukan Absen','error');
            return redirect()->back();
        }else{
            AbsensiPendidik::create([
                'jadwal_id' => $request->jadwal_id,
                'pendidik_id' => $request->pendidik_id,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);
            Alert::toast('Absensi Izin Berhasil','success');
            return redirect()->back();
        }

    }
    public function uploadAbsensiIzinStaf(Request $request){
        // return $request;
        $now = Carbon::now();
        $staf = AbsensiStaf::where('staf_id', $request->staf_id)->whereDate('created_at', $now)->first();
        if($staf){
            Alert::toast('Sudah Melakukan Absen','error');
            return redirect()->back();
        }else{
            AbsensiStaf::create([
                'staf_id' => $request->staf_id,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);
            Alert::toast('Absensi Izin Berhasil','success');
            return redirect()->back();
        }

    }
    public function hapusIzinPendidik($id){
        $absen = AbsensiPendidik::find($id);
        $absen->delete();
        Alert::toast('Hapus Izin Berhasil', 'success');
        return redirect()->back();
    }
    public function hapusIzinPelajar($id){
        $absen = AbsensiPelajar::find($id);
        $absen->delete();
        Alert::toast('Hapus Izin Berhasil', 'success');
        return redirect()->back();
    }
    public function hapusIzinStaf($id){
        $absen = AbsensiStaf::find($id);
        $absen->delete();
        Alert::toast('Hapus Izin Berhasil', 'success');
        return redirect()->back();
    }
    public function uploadAbsensiStaf(Request $request){
        $pengguna = User::where('nomor_registrasi', $request->token)->first();
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
                    Alert::error('Anda Bukan Staf','Silakan Ke Menu Absensi Pembelajaran');
                    return redirect()->back();
                }
            }else{
                Alert::error('Pengguna Tidak Ditemukan','Pengguna Tidak Terdaftar');
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
        $absensi = AbsensiPendidik::join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pendidik.jadwal_id')
                                    ->where('adm_absensi_pendidik.id', $id)
                                    ->firstOrFail();
        if($absensi->selesai > $request->pulang){
            return redirect()->back()->withErrors(['msg' => 'Pembelajaran Belum Selesai']);
        }
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


    public function historiPelajar(Request $request){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $jadwal = AbsensiPelajar::select('mapels.mapel','kelas.nama as kelas','adm_jadwal.mulai','adm_jadwal.selesai',
                                'adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                            ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                            ->join('adm_jadwal','adm_jadwal.id','=','adm_absensi_pelajar.jadwal_id')
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
                        ->where('adm_absensi_pendidik.keterangan', null)
                        ->orderBy('adm_absensi_pendidik.datang', 'asc')
                        ->get();
        $pelajar = AbsensiPelajar::select('users.nama','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                        ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                        ->where('adm_absensi_pelajar.jadwal_id', $id)
                        ->where('adm_absensi_pelajar.keterangan', null)
                        ->orderBy('adm_absensi_pelajar.datang', 'asc')
                        ->get();
        $izin_pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','roles.role','adm_absensi_pelajar.status','adm_absensi_pelajar.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pelajar.status', 2)
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->get();
        $izin_pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','roles.role','adm_absensi_pendidik.status','adm_absensi_pendidik.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pendidik.status', 2)
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->get();

        return view('staf-admin.absensi.lihat-rekap', compact('user','pendidik','pelajar','jadwal','izin_pelajar','izin_pendidik'));
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
                        ->where('adm_absensi_pendidik.keterangan', null)
                        ->orderBy('adm_absensi_pendidik.datang', 'asc')
                        ->get();
        $pelajar = AbsensiPelajar::select('users.nama','adm_absensi_pelajar.datang','adm_absensi_pelajar.pulang','adm_absensi_pelajar.status')
                        ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                        ->where('adm_absensi_pelajar.jadwal_id', $id)
                        ->where('adm_absensi_pelajar.keterangan', null)
                        ->orderBy('adm_absensi_pelajar.datang', 'asc')
                        ->get();
        $izin_pelajar = AbsensiPelajar::select('adm_absensi_pelajar.id as pelajar_id','users.nama','roles.role','adm_absensi_pelajar.status','adm_absensi_pelajar.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pelajar.pelajar_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pelajar.status', 2)
                                    ->where('adm_absensi_pelajar.jadwal_id', $id)
                                    ->get();
        $izin_pendidik = AbsensiPendidik::select('adm_absensi_pendidik.id as pendidik_id','users.nama','roles.role','adm_absensi_pendidik.status','adm_absensi_pendidik.keterangan')
                                    ->join('users','users.id','=','adm_absensi_pendidik.pendidik_id')
                                    ->join('roles','roles.id','=','users.role_id')
                                    ->where('adm_absensi_pendidik.status', 2)
                                    ->where('adm_absensi_pendidik.jadwal_id', $id)
                                    ->get();

        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('staf-admin.absensi.cetak-jurnal', ['logo'=>$en_logo,
                                'jadwal'=>$jadwal,
                                'pelajar'=>$pelajar,
                                'pendidik'=>$pendidik,
                                'user' => $user,
                                'markas' => $markas,
                                'izin_pendidik' => $izin_pendidik,
                                'izin_pelajar' => $izin_pelajar])->setPaper('a4','landscape');
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
        $izin_staf = AbsensiStaf::select('roles.role','users.nama','adm_absensi_staf.status','adm_absensi_staf.keterangan')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereMonth('adm_absensi_staf.created_at', $bulan)
                            ->whereYear('adm_absensi_staf.created_at', $tahun)
                            ->where('adm_absensi_staf.status', 2)
                            ->orderBy('adm_absensi_staf.id', 'desc')
                            ->get();
        return view('staf-admin.absensi.rekap-staf', compact('bulan','tahun','user','staf','izin_staf'));
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
        $izin_staf = AbsensiStaf::select('roles.role','users.nama','adm_absensi_staf.status','adm_absensi_staf.keterangan')
                            ->join('users','users.id','=','adm_absensi_staf.staf_id')
                            ->join('adm_pendidik','adm_pendidik.pendidik_id','=','users.id')
                            ->join('roles','roles.id','=','users.role_id')
                            ->where('adm_pendidik.markas_id', $markas->markas_id)
                            ->whereMonth('adm_absensi_staf.created_at', $bulan)
                            ->whereYear('adm_absensi_staf.created_at', $tahun)
                            ->where('adm_absensi_staf.status', 2)
                            ->orderBy('adm_absensi_staf.id', 'desc')
                            ->get();
        $en_logo = (string) Image::make(public_path('img/krisna.png'))->encode('data-url');
        $pdf = PDF::loadview('staf-admin.absensi.cetak-jurnal-staf', ['logo'=>$en_logo,
                                'staf'=> $staf,
                                'bulan'=>$bulan,
                                'tahun'=>$tahun,
                                'user' => $user,
                                'markas' => $markas,
                                'izin_staf' => $izin_staf])->setPaper('a4','landscape');
        return $pdf->stream();
    }

}
