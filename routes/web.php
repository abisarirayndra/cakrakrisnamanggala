<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('landing');
// });

Route::get('/','Controller@landing')->middleware('guest')->name('landing');
Route::get('/register','AuthController@tampilRegister')->middleware('guest')->name('auth.register');
Route::post('/reg','AuthController@register')->middleware('guest')->name('reg');
Route::get('/login','AuthController@tampilLogin')->middleware('guest')->name('login');
Route::post('/log','AuthController@login')->middleware('guest')->name('log');
Route::get('/logout', 'AuthController@logout')->name('logout');

Route::group(['middleware' => ['auth','super-role']], function(){
    Route::get('/super/index','SuperController@index')->name('super.index');
    Route::post('/super/tambahpengajar','PengajarController@store')->name('super.tambahpengajar');
});

Route::group(['middleware' => ['auth','pengajar-role']], function(){
    Route::get('/pengajar/cat/index','CatController@index')->name('pengajar.cat.index');
    Route::post('pengajar/cat/tambahpaket','CatController@store')->name('pengajar.cat.tambahpaket');
    Route::get('/pengajar/cat/edit/{id}','CatController@edit')->name('pengajar.cat.edit');
    Route::post('/pengajar/cat/buattema','CatController@buatTema')->name('pangajar.cat.buattema');
});