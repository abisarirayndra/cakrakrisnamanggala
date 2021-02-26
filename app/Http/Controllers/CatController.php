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



class CatController extends Controller
{
    // public function index(){
    //     $user = Auth::user()->nama;
    //     $id = Auth::user()->id;
    //     $kelas = Kelas::all();
    //     $paket = PaketSoal::select('paket_soals.id','users.nama as pengajar','paket_soals.nama_paket','paket_soals.status','paket_soals.kelas_id','paket_soals.created_at')
    //                 ->join('users','users.id','=','paket_soals.user_id')
    //                 ->where('user_id', $id)
    //                 ->get();

    //     return view('pengajar.cat.index', compact('user','kelas','paket'));

    // }

    public function tema(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $kelas = Kelas::all();
        $tema = Tema::where('pengajar_id', $id)->get();

        return view('pengajar.cat.tema', compact('user','kelas','tema','id'));

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

    public function edit($id){
        $user = Auth::user()->nama;
        $nama = PaketSoal::select('paket_soals.nama_paket')->where('id', $id)->get();
        $tema = Tema::where('paket_id', $id)->get();
        $paket_id = $id;

        return view('pengajar.cat.edit', compact('user','nama','tema','paket_id'));
    }

    public function buatTema(Request $request){
        Tema::create($request->all());
        Alert::toast('Tes Berhasil Dibuat', 'success');

        return back();
    }

    public function Soal($id){
        $user = Auth::user()->nama;
        $tema_id = $id;
        $soal = Soal::where('tema_id', $id)->get();

        $tenggat = Tema::select('tenggat')->where('id',$id)->first();

        return view('pengajar.cat.soal', compact('user', 'tema_id','soal','tenggat'));
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

    public function temaSoal(){
        $user = Auth::user()->nama;
        $kelas = Auth::user()->kelas_id;
        $tema = Tema::where('kelas_id', $kelas)->get();

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
        ->get();
        
        $tenggat = Tema::select('tenggat','jenis')->where('id',$id)->first();
        

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

    public function reviewJawaban($id){
        $user = Auth::user()->nama;
        $tema_id = $id;
       $jawaban = LembarJawaban::select('temas.tema','soals.nomor_soal','lembar_jawabans.jawaban')
                            ->join('soals','soals.id','=','lembar_jawabans.soal_id')
                            ->join('temas','temas.id','=','soals.tema_id')
                            ->where('soals.tema_id', $id)
                            ->orderBy('soals.nomor_soal', 'asc')
                            ->get();
        $jml_soal = Tema::select('jumlah_soal')->where('id', $id)->first();

        $total = LembarJawaban::select(DB::raw('SUM(lembar_jawabans.skor) as total'))
                    ->join('soals','soals.id','=','lembar_jawabans.soal_id')
                    ->join('temas','temas.id','=','soals.tema_id')
                    ->where('soals.tema_id', $id)
                    ->get();
        
                    $total_skor = [];
                    foreach($total as $item){
                        $total_skor = (int)$item->total;
                    }
        
        return view('pelajar.cat.kumpulkan', compact('user','jawaban','tema_id','jml_soal','total_skor'));
    }

    public function skoring($id, Request $request){
        $user = Auth::user()->nama;
        $id_user = Auth::user()->id;
        $jml_soal = $request->q;
        $skor = $request->n;
        $tema_id = $request->t;
    
        $nilai = ($jml_soal/$skor) * 100;

        $sudah = RekapNilai::where('user_id',$id_user)->where('tema_id', $id)->first();

        if(isset($sudah)){
            Alert::error('Anda Sudah Mengerjakan');
        }
        else{
            RekapNilai::create([
                'user_id' => $id_user,
                'tema_id' => $tema_id,
                'total_nilai' => $nilai,
            ]);
            Alert::toast('Selamat, Anda Sudah Menyelesaikan Tes', 'success');
        }
        

        return redirect()->route('pelajar.cat.tema');
    }

    public function hasilPelajar(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $rekap = RekapNilai::where('user_id', $id)->get();

        return view('pelajar.cat.hasil', compact('rekap','user'));
    }

    public function editTema($id){
        $tema = Tema::find($id);
        $id_user = Auth::user()->id;
        $kelas = Kelas::all();

        return view('pengajar.cat.edit', compact('tema','id_user','kelas'));
    }

    public function updateTema($id, Request $request){
        $update = Tema::find($id);
        $update->update($request->all());
        Alert::toast('Data Berhasil Diupdate','success');

        return redirect()->route('pengajar.cat.tema');
    }
    
    public function hasilPengajar($id){
        $user = Auth::user()->nama;
        
        $rekap = RekapNilai::select('users.nama','rekap_nilais.total_nilai')
                            ->join('users','users.id','=','rekap_nilais.user_id')
                            ->where('rekap_nilais.tema_id', $id)
                            ->orderBy('rekap_nilais.total_nilai', 'desc')
                            ->get();
        
        return view('pengajar.cat.hasil', compact('user','rekap'));
        

    }

}
