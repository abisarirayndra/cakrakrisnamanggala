<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class SuperController extends Controller
{
    public function index(){

        $user = Auth::user()->nama;
        $data_user = User::where('role_id', 3)->get();

        return view('super.index', compact('user','data_user'));
    }
}
