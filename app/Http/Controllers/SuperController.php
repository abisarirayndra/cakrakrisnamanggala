<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class SuperController extends Controller
{
    public function index(){

        $user = Auth::user()->nama;

        return view('super.beranda', compact('user'));
    }
}
