<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Kelas;
use App\PaketSoal;
use App\Tema;


class CatController extends Controller
{
    public function index(){
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $kelas = Kelas::all();
        $paket = PaketSoal::select('paket_soals.id','users.nama as pengajar','paket_soals.nama_paket','paket_soals.status','paket_soals.kelas_id','paket_soals.created_at')
                    ->join('users','users.id','=','paket_soals.user_id')
                    ->where('user_id', $id)
                    ->get();

        return view('pengajar.cat.index', compact('user','kelas','paket'));

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
        Tema::create([
            'tema' => $request->tema,
            'durasi' => $request->durasi,
            'paket_id' => $request->paket_id,
        ]);

        return back();
    }

    public function tambahSoal(Request $request){
        $user = Auth::user()->nama;
        $nama = PaketSoal::select('paket_soals.nama_paket')->where('id', $id)->get();
    }
}
