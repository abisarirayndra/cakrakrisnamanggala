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
Route::get('/reset', 'AuthController@reset')->name('reset');
Route::post('/upreset', 'AuthController@upReset')->name('upreset');


Route::group(['middleware' => ['auth','super-role']], function(){
    
});

Route::group(['middleware' => ['auth','admin-role']], function(){
    Route::get('/admin/cat/paket','CatController@paket')->name('admin.cat.paket');
    Route::post('/admin/cat/buatpaket','CatController@buatPaket')->name('admin.cat.buatpaket');
    Route::get('/admin/cat/editpaket/{id}','CatController@editPaket')->name('admin.cat.editpaket');
    Route::post('/admin/cat/updatepaket/{id}','CatController@updatePaket')->name('admin.cat.updatepaket');
    Route::get('/admin/cat/hapuspaket/{id}','CatController@destroyPaket')->name('admin.cat.hapuspaket');
    Route::get('/admin/cat/tema/{id}','CatController@daftarTema')->name('admin.cat.tema');
    Route::post('/admin/cat/buattema','CatController@buatTema')->name('admin.cat.buattema');
    Route::get('/admin/cat/edittema/{id}','CatController@editTemaAdmin')->name('admin.cat.edittema');
    Route::post('/admin/cat/updatetema/{id}','CatController@updateTemaAdmin')->name('admin.cat.updatetema');
    Route::get('/admin/cat/hapustema/{id}','CatController@destroyTemaAdmin')->name('admin.cat.hapustema');
    Route::get('/admin/cat/hasil/{id}','CatController@hasilAdmin')->name('admin.cat.hasil');

    // Kedinasan
    Route::get('/admin/dinas/paket','PaketDinasController@paket')->name('admin.dinas.paket');
    Route::get('/admin/dinas/tambahpaket','PaketDinasController@tambah')->name('admin.dinas.tambahpaket');
    Route::post('/admin/dinas/uppaket','PaketDinasController@up')->name('admin.dinas.uppaket');
    Route::get('/admin/dinas/lihatpaket/{id}','PaketDinasController@lihat')->name('admin.dinas.lihatpaket');
    Route::post('/admin/dinas/tambahkelas/{id}','PaketDinasController@tambahKelas')->name('admin.dinas.tambahkelas');
    Route::get('/admin/dinas/hapuskelas/{id}','PaketDinasController@hapusKelas')->name('admin.dinas.hapuskelas');
    Route::post('/admin/dinas/tambahtes/{id}','TesDinasController@tambahTes')->name('admin.dinas.tambahtes');
    Route::get('/admin/dinas/hapustes/{id}','TesDinasController@hapusTes')->name('admin.dinas.hapustes');
    Route::get('/admin/dinas/edittes/{id}','TesDinasController@editTes')->name('admin.dinas.edittes');
    Route::post('/admin/dinas/updatetes/{id}','TesDinasController@updateTes')->name('admin.dinas.updatetes');






});

Route::group(['middleware' => ['auth','pengajar-role']], function(){
    Route::get('/pengajar/cat/index','CatController@index')->name('pengajar.cat.index');
    Route::get('/pengajar/cat/paket','CatController@paketPendidik')->name('pengajar.cat.paket');
    Route::get('/pengajar/cat/tema/{id}','CatController@tema')->name('pengajar.cat.tema');
    Route::post('/pengajar/cat/buattema','CatController@buatTema')->name('pangajar.cat.buattema');
    Route::get('/pengajar/cat/edit/{id}','CatController@editTema')->name('pengajar.cat.edit');
    Route::post('/pengajar/cat/updatetema/{id}','CatController@updateTema')->name('pengajar.cat.updatetema');
    Route::get('/pengajar/cat/hapustema/{id}','CatController@destroyTema')->name('pengajar.cat.hapustema');
    Route::get('/pengajar/cat/hasil/{id}','CatController@hasilPengajar')->name('pengajar.cat.hasil');
    Route::get('/pengajar/cat/soal/{id}','CatController@Soal')->name('pengajar.cat.soal');
    Route::post('/pengajar/cat/upjumlahsoal/{id}','CatController@upJumlahSoal')->name('pengajar.cat.upjumlahsoal');
    Route::post('/pengajar/cat/importsoal','CatController@importSoal')->name('pengajar.cat.importsoal');
    Route::get('/pengajar/cat/editsoal/{id}','CatController@editSoal')->name('pengajar.cat.editsoal');
    Route::post('/pengajar/cat/updatesoal/{id}','CatController@updateSoal')->name('pengajar.cat.updatesoal');
    Route::get('/pengajar/cat/hapussoal/{id}','CatController@hapusSoal')->name('pengajar.cat.hapussoal');
    Route::get('/pengajar/cat/tambahgambar/{id}','CatController@tambahGambar')->name('pengajar.cat.tambahgambar');
    Route::post('/pengajar/cat/upgambar/{id}','CatController@upGambar')->name('pengajar.cat.upgambar');
    Route::get('/pengajar/cat/editgambar/{id}','CatController@editGambar')->name('pengajar.cat.editgambar');
    Route::post('/pengajar/cat/updategambar/{id}','CatController@updateGambar')->name('pengajar.cat.updategambar');
    Route::get('/pengajar/cat/hapusgambar/{id}','CatController@hapusGambar')->name('pengajar.cat.hapusgambar');


});

Route::group(['middleware' => ['auth','pelajar-role']], function(){
    Route::get('/pelajar/index','PelajarController@index')->name('pelajar.index');
    // Route::get('/pelajar/cat/tema/{id}','CatController@temaSoal')->name('pelajar.cat.tema');
    Route::get('/pelajar/cat/paket','CatController@paketPelajar')->name('pelajar.cat.paket');
    Route::get('/pelajar/cat/tema/{id}','CatController@temaSoal')->name('pelajar.cat.tema');
    Route::post('/pengajar/cat/upjawaban','CatController@upJawaban')->name('pelajar.cat.upjawaban');
    Route::get('/pelajar/cat/soal/{id}','CatController@lembarSoal')->name('pelajar.cat.soal');
    Route::get('/pelajar/cat/kumpulkan/{id}','CatController@reviewJawaban')->name('pelajar.cat.kumpulkan');
    Route::get('/pelajar/cat/jawaban', 'CatController@jawaban')->name('pelajar.cat.jawaban');
    Route::get('/pelajar/cat/skoring/{id}','CatController@skoring')->name('pelajar.cat.skoring');
    Route::get('/pelajar/cat/hasil', 'CatController@hasilPelajar')->name('pelajar.cat.hasil');

    //Kedinasan
    Route::get('/pelajar/dinas/index','KedinasanController@index')->name('pelajar.dinas.index');

});