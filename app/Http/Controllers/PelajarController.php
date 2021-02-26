<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\PaketSoal;
use App\Pelajar;

class PelajarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user()->nama;
        $id = Auth::user()->id;
        $kelas = Pelajar::select('kelas_id')->where('user_id', $id)->get();
        $kelas_id = [];
        foreach($kelas as $item){
            $kelas_id = $item->kelas_id;
        }
        $paket = PaketSoal::select('paket_soals.id','users.nama as pengajar','paket_soals.nama_paket','paket_soals.kelas_id','paket_soals.created_at')
                        ->join('users','users.id','=','paket_soals.user_id')
                        ->where('kelas_id', $kelas_id)
                        ->where('status', 1)
                        ->get();
        // return $paket;
        return view('pelajar.index', compact('user','paket'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
