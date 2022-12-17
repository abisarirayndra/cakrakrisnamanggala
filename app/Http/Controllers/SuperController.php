<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Markas;
class SuperController extends Controller
{
    public function index(){

        $user = Auth::user()->nama;
        $markas = Markas::select('markas')->get();
        foreach($markas as $item){
            $categories[] = $item->markas;
        }
        $jumlah_pelajar_genteng[] = User::join('adm_pelajars','adm_pelajars.pelajar_id','users.id')
                                        ->where('adm_pelajars.markas_id', 1)
                                        ->where('users.role_id', 4)
                                        ->count();
        $jumlah_pelajar_banyuwangi[] = User::join('adm_pelajars','adm_pelajars.pelajar_id','users.id')
                                        ->where('adm_pelajars.markas_id', 2)
                                        ->where('users.role_id', 4)
                                        ->count();
        $jumlah_pelajar_jember[] = User::join('adm_pelajars','adm_pelajars.pelajar_id','users.id')
                                        ->where('adm_pelajars.markas_id', 3)
                                        ->where('users.role_id', 4)
                                        ->count();
        $jumlah_pelajar_smadatara[] = User::join('adm_pelajars','adm_pelajars.pelajar_id','users.id')
                                        ->where('adm_pelajars.markas_id', 4)
                                        ->where('users.role_id', 4)
                                        ->count();
        $jumlah_pelajar = array_merge($jumlah_pelajar_genteng, $jumlah_pelajar_banyuwangi, $jumlah_pelajar_jember, $jumlah_pelajar_smadatara);

        $jumlah_pendidik_genteng[] = User::join('adm_pendidik','adm_pendidik.pendidik_id','users.id')
                                        ->where('adm_pendidik.markas_id', 1)
                                        ->where('users.role_id', 3)
                                        ->count();
        $jumlah_pendidik_banyuwangi[] = User::join('adm_pendidik','adm_pendidik.pendidik_id','users.id')
                                        ->where('adm_pendidik.markas_id', 2)
                                        ->where('users.role_id', 3)
                                        ->count();
        $jumlah_pendidik_jember[] = User::join('adm_pendidik','adm_pendidik.pendidik_id','users.id')
                                        ->where('adm_pendidik.markas_id', 3)
                                        ->where('users.role_id', 3)
                                        ->count();
        $jumlah_pendidik = array_merge($jumlah_pendidik_genteng, $jumlah_pendidik_banyuwangi, $jumlah_pendidik_jember);


        return view('admin.administrasi.index', compact('user','categories','jumlah_pelajar','jumlah_pendidik'));
    }
}
