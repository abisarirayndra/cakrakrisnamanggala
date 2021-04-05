<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Kelas;
use App\PaketSoal;
use App\Tema;
use App\Soal;
use App\Imports\ImportSoal;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use App\LembarJawaban;
use DB;
use App\RekapNilai;
use App\Exports\HasilExport;
use Image;
use Illuminate\Support\Facades\Storage;
use App\Mapel;
use App\User;
use App\RekapAkademik;
use Carbon\Carbon;



class CatController extends Controller
{
    // Admin
    public function paket(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $paket = PaketSoal::where('user_id', $id)->orderBy('id','desc')->get();
        $kelas = Kelas::all();

        return view('admin.cat.paket', compact('user','paket','id','kelas'));
    }

    public function buatPaket(Request $request){
        PaketSoal::create($request->all());
        Alert::toast('Paket Soal Berhail Dibuat','success');

        return redirect()->back();
    }

    public function editPaket($id){
        $paket = PaketSoal::find($id);
        $selected_kelas = PaketSoal::select('kelas_id')->where('id',$id)->first();
        $kelas = Kelas::all();

        return view('admin.cat.editpaket', compact('paket','selected_kelas','kelas'));
    }

    public function updatePaket($id, Request $request){
        $paket = PaketSoal::find($id);
        $paket->update($request->all());
        Alert::toast('Paket Berhasil Diperbarui','success');

        return redirect()->route('admin.cat.paket');
    }

    public function destroyPaket($id){
        $paket = PaketSoal::find($id);
        $paket->delete();
        Alert::toast('Paket Berhasil Dihapus');

        return redirect()->route('admin.cat.paket');
    }

    public function daftarTema($id){
        $user = Auth::user()->nama;
        $paket = PaketSoal::find($id);
        $pengajar_id = User::where('role_id', 3)->get();
        $tema = Tema::where('paket_id', $id)->orderBy('id','desc')->get();
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        return view('admin.cat.tema', compact('user','tema','kelas','pengajar_id','mapel','id','paket'));
    }

    public function buatTema(Request $request){
        Tema::create($request->all());
        Alert::toast('Tema Berhasil Dibuat');

        return redirect()->back();
    }

    public function editTemaAdmin($id){
        $tema = Tema::find($id);
        $pengajar_id = User::where('role_id', 3)->get();
        $selected_mapel = Tema::select('mapel_id')->where('id', $id)->first();
        $selected_pengajar = Tema::select('pengajar_id')->where('id', $id)->first();
        $mapel = Mapel::all();


        return view('admin.cat.edittema', compact('tema','pengajar_id','mapel','selected_mapel','selected_pengajar'));
        
    }

    public function updateTemaAdmin($id, Request $request){
        $tema = Tema::find($id);
        $paket = $tema->paket_id;
        $tema->update($request->all());
        Alert::toast('Tes Berhasil Diupdate','success');

        return redirect()->route('admin.cat.tema', $paket);
    }

    public function destroyTemaAdmin($id){
        $tema = Tema::find($id);
        $tema->delete();
        Alert::toast('Tes Berhasil Dihapus','success');

        return redirect()->back();
    }

    public function hasilAdmin($id){
        $user = Auth::user()->nama;
        $paket = PaketSoal::find($id);
        $hasil = RekapAkademik::where('paket_id', $id)->orderBy('nilai_akademik', 'desc')->get();
        
        return view('admin.cat.hasil', compact('user','hasil','paket'));
    }
    
    // Pendidik
    public function paketPendidik(){
        $user = Auth::user()->nama;
        $pengajar_id = Auth::user()->id;
        $job = PaketSoal::select('paket_soals.id','paket_soals.nama_paket','paket_soals.kelas_id')
                            ->join('temas','temas.paket_id','=','paket_soals.id')
                            ->where('temas.pengajar_id', $pengajar_id)
                            ->where('paket_soals.status', 1)
                            ->orderBy('id','desc')
                            ->distinct()
                            ->get();

        return view('pengajar.cat.paket', compact('job','user'));
    }
    public function tema($id){
        $user = Auth::user()->nama;
        $user_id = Auth::user()->id;
        $kelas = Kelas::all();
        $tema = Tema::where('pengajar_id', $user_id)->where('paket_id', $id)->orderBy('id','desc')->get();

        return view('pengajar.cat.tema', compact('user','kelas','tema','id'));

    }

    public function editTema($id){
        $tema = Tema::find($id);
        $id_user = Auth::user()->id;
        $kelas = Kelas::all();

        return view('pengajar.cat.edit', compact('tema','id_user','kelas'));
    }

    public function updateTema($id, Request $request){
        $update = Tema::find($id);
        $paket = $update->paket_id;
        $update->update($request->all());
        Alert::toast('Data Berhasil Diupdate','success');

        return redirect()->route('pengajar.cat.tema', $paket);
    }
    
    public function hasilPengajar($id, Request $request){
        $user = Auth::user()->nama;       
        $tema = Tema::where('id', $id)->first();
        $mapel = $tema->mapel_id;
        $kelas = Kelas::all();
        $kelas_id = $request->kelas;

        if(isset($request->kelas)){
            $rekap = RekapNilai::select('users.nama','rekap_nilais.total_nilai','rekap_nilais.created_at','kelas.nama as kelas','users.nomor_registrasi','kelas.id as kelas_id')
                            ->join('users','users.id','=','rekap_nilais.user_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->join('temas','temas.id','=','rekap_nilais.tema_id')
                            ->join('paket_soals','paket_soals.id','=','rekap_nilais.paket_id')
                            ->where('rekap_nilais.tema_id', $id)
                            ->where('paket_soals.kelas_id', $kelas_id)
                            ->orderBy('rekap_nilais.total_nilai', 'desc')
                            ->get();
           
        
        }
        else{
            $rekap = RekapNilai::select('users.nama','rekap_nilais.total_nilai','rekap_nilais.created_at','kelas.nama as kelas','users.nomor_registrasi','kelas.id as kelas_id')
                            ->join('users','users.id','=','rekap_nilais.user_id')
                            ->join('kelas','kelas.id','=','users.kelas_id')
                            ->where('rekap_nilais.tema_id', $id)
                            ->orderBy('rekap_nilais.total_nilai', 'desc')
                            ->get();
            
        }
        
        return view('pengajar.cat.hasil', compact('user','rekap','tema','kelas','kelas_id'));       

    }

    public function editSoal($id){
        $soal = Soal::find($id);

        return view('pengajar.cat.editsoal', compact('soal'));
    }

    public function hapusSoal($id){
        $soal = Soal::find($id);
        $tema_id = $soal->tema_id;
        $soal->delete();
        Alert::toast('Soal Berhasil Dihapus','success');

        return redirect()->route('pengajar.cat.soal', $tema_id);
    }

    public function updateSoal($id, Request $request){
        $soal = Soal::find($id);
        $tema = $soal->tema_id;
        $soal->update($request->all());
        Alert::toast('Update Berhasil','success');

        return redirect()->route('pengajar.cat.soal',$tema);
    }

    public function tambahGambar($id){
        $soal = Soal::find($id);
        return view('pengajar.cat.tambahgambar', compact('soal'));
    }
    
    public function upGambar($id, Request $request){
        $soal = Soal::find($id);
        $tema_id = $soal->tema_id;
        $image = $request->file('foto');
            $images = 'soal'.$id.'.'.$request->file('foto')->extension();
            Image::make($image)->save(storage_path('app/public/soal/' . $images));
            $soal->update([
                'foto' => $images,
            ]);
            
        Alert::toast('Tambah Foto Berhasil', 'success');

        return redirect()->route('pengajar.cat.soal', $tema_id);
    }

    public function editGambar($id){
        $soal = Soal::find($id);

        return view('pengajar.cat.editgambar', compact('soal'));
    }
    
    public function updateGambar($id, Request $request){
        $new_photo = $request->file('foto');
        $soal = Soal::find($id);
        $tema_id = $soal->tema_id;

        if($soal->foto && file_exists(storage_path('app/public/soal/' .$soal->foto))){
            Storage::delete('public/soal/'. $soal->foto);
        }
        $images = 'soalbaru'.$id.'.'.$request->file('foto')->extension();
        Image::make($new_photo)->save(storage_path('app/public/soal/' . $images));
        $soal->update([
            'foto' => $images,
        ]);
        
        Alert::toast('Update Gambar Berhasil', 'success');

        return redirect()->route('pengajar.cat.soal', $tema_id);
    }

    public function hapusGambar($id){
        $soal = Soal::find($id);
        Storage::delete('public/soal/'. $soal->foto);
        $soal->update([
            'foto' => null,
        ]);
        
        Alert::toast('Hapus Gambar Berhasil', 'success');
        return redirect()->back();
    }

    public function destroyTema($id){
        $tema = Tema::find($id);
        $tema->delete();

        Alert::toast('Hapus Berhasil', 'success');
        return redirect()->back();
    }

    public function store(Request $request){
        // $request->validate([
        //     'email' => 'unique:users,email',
        //     'password' => 'min:8',
        // ]);
        $id = Auth::user()->id;

        PaketSoal::create([
            'nama_paket' => $request->nama_paket,
            'user_id' => $id,
            'status' => 0,
            'kelas_id' => $request->kelas_id
        ]);

        return redirect()->route('pengajar.cat.index');
    }

    public function Soal($id){
        $user = Auth::user()->nama;
        $tema_id = $id;
        $tema = Tema::find($id);
        $soal = Soal::where('tema_id', $id)->get();

        // $tenggat = Tema::select('tenggat')->where('id',$id)->first();

        return view('pengajar.cat.soal', compact('user', 'tema_id','soal','tema'));
    }

    public function upJumlahSoal($id, Request $request){
        $jumlah = Tema::find($id);
        $jumlah->update($request->all());
        Alert::toast('Jumlah Soal Berhasil Diupload','success');

        return redirect()->route('pengajar.cat.soal', $id);
    }

    public function importSoal(Request $request){
        // $this->validate($request, [
        //     'file' => 'required|mimes:csv'
        // ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file'); //GET FILE
            Excel::import(new ImportSoal, $file); //IMPORT FILE 
            Alert::toast('Import Berhasil', 'success');
            return redirect()->back();
        } 
        Alert::toast('Pastikan File Sudah Ada', 'warning');
    }

    // Peserta Didik
    public function paketPelajar(){
        $user = Auth::user()->nama;
        $kelas = Auth::user()->kelas_id;
        $paket = PaketSoal::where('kelas_id', $kelas)
                            ->where('status', 1)
                            ->orderBy('id','desc')
                            ->get();

        return view('pelajar.cat.paket',compact('user','paket'));
    }
    public function temaSoal($id){
        $user = Auth::user()->nama;
        $kelas = Auth::user()->kelas_id;
        $tema = Tema::where('paket_id', $id)->where('status', 1)->orderBy('id','desc')->get();

        return view('pelajar.cat.tema', compact('user','tema'));
    }

    public function upJawaban(Request $request){
        LembarJawaban::create([
            'soal_id' => $request->soal_id,
            'user_id' => $request->user_id,
        ]);
            return redirect()->back();
    }

    public function lembarSoal($id, Request $request){
        $user = Auth::user()->nama;
        $user_id = Auth::user()->id;
        $tema_id = $id;
        $soal = Soal::where('tema_id', $id)->get();       

        $nomor = $request->nomor;
        $soal_id = $request->soal;

        $soal_pertama = Soal::where('tema_id', $id)->first();

        $tampil_soal = Soal::where('tema_id', $id)->where('nomor_soal', $nomor)->get();
        // $tampil_soal = Soal::where('tema_id', $id)->paginate(1);
        

        // Lembar Jawaban
        $lembar = LembarJawaban::where('soal_id', $soal_id)->where('user_id', $user_id)->first();

        if(isset($lembar)){}
        else{
            LembarJawaban::create([
                'soal_id' => $soal_id,
                'user_id' => $user_id,
                'skor' => 0,
            ]);
        }

        $sudah_jawab = LembarJawaban::select('lembar_jawabans.jawaban')
        ->join('soals','soals.id','=','lembar_jawabans.soal_id')
        ->where('lembar_jawabans.user_id', $user_id)
        ->where('lembar_jawabans.soal_id', $soal_id)
        ->where('soals.nomor_soal', $nomor)
        ->first();
        
        $tenggat = Tema::select('paket_id','tenggat','jenis')->where('id',$id)->first();

        $paket = $tenggat->paket_id;

        $sudah_nilai = RekapAkademik::where('paket_id', $paket)->where('user_id', $user_id)->first();

        if(isset($sudah_nilai)){
        }
        else {
            RekapAkademik::create([
                'paket_id' => $paket,
                'user_id' => $user_id,
                'nilai_akademik' => 0,
            ]);     
        }
        

        return view('pelajar.cat.soal',compact('user','soal','tampil_soal','sudah_jawab','user_id','nomor_soal','tenggat','tema_id'));
    }

    public function jawaban(Request $request){
        
        $nomor_soal = $request->nomorsoal;
        $soal_id = $request->soal;
        $user = Auth::user()->id;
        $jawaban = $request->jawaban;

        $kunci = Soal::select('kunci')->where('id',$soal_id)->first();
        $ada_jawaban = LembarJawaban::where('user_id', $user)->where('soal_id', $soal_id)->first();

        if(isset($ada_jawaban)){
            if($request->jawaban == $kunci->kunci){
                $nilai = 1;
            }
            else{
                $nilai = 0;
            }

            $ada_jawaban->update([
            'jawaban' => $request->jawaban,
            'skor' => $nilai,
            ]);

            return redirect()->back();
        }
  
    }

    public function reviewJawaban($id, Request $request){
        $user = Auth::user()->nama;
        $user_id = Auth::user()->id;
        $tema_id = $id;
       $jawaban = LembarJawaban::select('soals.nomor_soal','lembar_jawabans.jawaban')
                            ->join('soals','soals.id','=','lembar_jawabans.soal_id')
                            ->join('temas','temas.id','=','soals.tema_id')
                            ->where('soals.tema_id', $id)
                            ->where('lembar_jawabans.user_id', $user_id)
                            ->orderBy('soals.nomor_soal', 'asc')
                            ->get();

        $jml_soal = Tema::select('jumlah_soal')->where('id', $id)->first();

        $total = LembarJawaban::select(DB::raw('SUM(lembar_jawabans.skor) as total'))
                    ->join('soals','soals.id','=','lembar_jawabans.soal_id')
                    ->join('temas','temas.id','=','soals.tema_id')
                    ->where('soals.tema_id', $id)
                    ->where('lembar_jawabans.user_id', $user_id)
                    ->get();
        
                    $total_skor = [];
                    foreach($total as $item){
                        $total_skor = (int)$item->total;
                    }
        
        $mapel = Tema::select('mapel_id','paket_id')->where('id', $id)->first();
        $paket = $mapel->paket_id;

        $akademik = RekapAkademik::where('user_id', $user_id)->where('paket_id', $paket)->first();

        
        
        return view('pelajar.cat.kumpulkan', compact('user','jawaban','tema_id','jml_soal','total_skor','mapel','akademik'));
    }

    public function skoring($id, Request $request){
        $user = Auth::user()->nama;
        $id_user = Auth::user()->id;
        $jml_soal = $request->q;
        $skor = $request->n;
        $tema_id = $request->t;
        $mapel = $request->m;
        $paket = $request->p;
        $akademik = $request->akademik;
        
        $nilai = ($skor/$jml_soal) * 100;

        $sudah = RekapNilai::where('user_id',$id_user)->where('tema_id', $id)->first();

        if(isset($sudah)){
            Alert::error('Anda Sudah Mengerjakan');
        }
        else{
            RekapNilai::create([
                'user_id' => $id_user,
                'paket_id' => $paket,
                'tema_id' => $tema_id,
                'total_nilai' => $nilai,
            ]);
            Alert::toast('Selamat, Anda Sudah Menyelesaikan Tes', 'success');
        }

        $rekap_akademik = RekapAkademik::where('user_id', $id_user)->where('paket_id', $paket)->first();
        if($mapel == 1 ){
            $mtk_akademik = (($nilai * 30) / 100) + $akademik;
            $rekap_akademik->update([
                'nilai_mtk' => $nilai,
                'nilai_akademik' => $mtk_akademik,
            ]);
        }
        elseif($mapel == 2 ){
            $ipu_akademik = (($nilai * 25) / 100) + $akademik;
            $rekap_akademik->update([
                'nilai_ipu' => $nilai,
                'nilai_akademik' => $ipu_akademik,
            ]);
        }
        elseif($mapel == 3 ){
            $bing_akademik = (($nilai * 25) / 100) + $akademik;
            $rekap_akademik->update([
                'nilai_bing' => $nilai,
                'nilai_akademik' => $bing_akademik,
            ]);
        }
        elseif($mapel == 4 ){
            $bi_akademik = (($nilai * 20) / 100) + $akademik;
            $rekap_akademik->update([
                'nilai_bi' => $nilai,
                'nilai_akademik' => $bi_akademik,
            ]);
        }
        

        return redirect()->route('pelajar.cat.hasil');
    }

    public function hasilPelajar(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $rekap = RekapNilai::where('user_id', $id)->orderBy('id','desc')->get();

        return view('pelajar.cat.hasil', compact('rekap','user'));
    }

    
}
