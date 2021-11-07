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


Route::get('/','AuthController@tampilLogin')->middleware('guest')->name('landing');
Route::get('/register','AuthController@tampilRegister')->middleware('guest')->name('auth.register');
Route::post('/reg','AuthController@register')->middleware('guest')->name('reg');
Route::get('/login','AuthController@tampilLogin')->middleware('guest')->name('login');
Route::post('/log','AuthController@login')->middleware('guest')->name('log');
Route::get('/logout', 'AuthController@logout')->name('logout');
Route::get('/reset', 'AuthController@reset')->name('reset');
Route::post('/upreset', 'AuthController@upReset')->name('upreset');


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
    Route::get('/admin/paket','PaketDinasController@paket')->name('admin.dinas.paket');
    Route::get('/admin/tambahpaket','PaketDinasController@tambah')->name('admin.dinas.tambahpaket');
    Route::post('/admin/uppaket','PaketDinasController@up')->name('admin.dinas.uppaket');
    Route::get('/admin/lihatpaket/{id}','PaketDinasController@lihat')->name('admin.dinas.lihatpaket');
    Route::get('/admin/editpaket/{id}','PaketDinasController@editPaket')->name('admin.dinas.editpaket');
    Route::post('/admin/updatepaket/{id}','PaketDinasController@updatePaket')->name('admin.dinas.updatepaket');
    Route::get('/admin/hapuspaket/{id}','PaketDinasController@hapusPaket')->name('admin.dinas.hapuspaket');

    Route::post('/admin/tambahkelas/{id}','PaketDinasController@tambahKelas')->name('admin.dinas.tambahkelas');
    Route::get('/admin/hapuskelas/{id}','PaketDinasController@hapusKelas')->name('admin.dinas.hapuskelas');
    Route::post('/admin/tambahtes/{id}','TesDinasController@tambahTes')->name('admin.dinas.tambahtes');
    Route::get('/admin/hapustes/{id}','TesDinasController@hapusTes')->name('admin.dinas.hapustes');
    Route::get('/admin/edittes/{id}','TesDinasController@editTes')->name('admin.dinas.edittes');
    Route::post('/admin/updatetes/{id}','TesDinasController@updateTes')->name('admin.dinas.updatetes');
    Route::get('/admin/hasildinas/{id}','HasilDinasController@hasilKedinasanAdmin')->name('admin.dinas.hasildinas');
    Route::get('/admin/hasiltnipolri/{id}','HasilDinasController@hasilTniPolriAdmin')->name('admin.dinas.hasiltnipolri');
    Route::get('/admin/cetakhasildinas/{id}','HasilDinasController@cetakKedinasanAdmin')->name('admin.dinas.cetakhasildinas');
    Route::get('/admin/cetaktnipolri/{id}','HasilDinasController@cetakTniPolriAdmin')->name('admin.dinas.cetakhasiltnipolri');
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

    // Kedinasan
    Route::get('/pendidik/paket','PaketDinasController@pendidikPaket')->name('pendidik.dinas.paket');
    Route::get('/pendidik/tes/{id}','TesDinasController@pendidikTes')->name('pendidik.dinas.tes');
    Route::get('/pendidik/tipesoal/{id}','SoalDinasController@pendidikPilihTipe')->name('pendidik.dinas.tipesoal');
    Route::get('/pendidik/hapusganda/{id}','SoalDinasController@pendidikHapusGanda')->name('pendidik.dinas.hapusganda');
    Route::get('/pendidik/hapusgandapoin/{id}','SoalDinasController@pendidikHapusGandaPoin')->name('pendidik.dinas.hapusgandapoin');
    Route::get('/pendidik/hapusessay/{id}','SoalDinasController@pendidikHapusEssay')->name('pendidik.dinas.hapusessay');
    Route::get('/pendidik/soalganda/{id}','SoalDinasController@pendidikSoalGanda')->name('pendidik.dinas.soalganda');
    Route::get('/pendidik/cetaksoalganda/{id}', 'SoalDinasController@pendidikCetakSoalGanda')->name('pendidik.dinas.cetaksoalganda');
    Route::post('/pendidik/upsoalganda/{id}','SoalDinasController@pendidikUpSoalGanda')->name('pendidik.dinas.upsoalganda');
    Route::get('/pendidik/editsoalganda/{id}','SoalDinasController@pendidikEditSoalGanda')->name('pendidik.dinas.editsoalganda');
    Route::post('/pendidik/updatesoalganda/{id}','SoalDinasController@pendidikUpdateSoalGanda')->name('pendidik.dinas.updatesoalganda');
    Route::get('/pendidik/soalgandapoin/{id}','SoalDinasController@pendidikSoalGandaPoin')->name('pendidik.dinas.soalgandapoin');
    Route::get('/pendidik/cetaksoalgandapoin/{id}', 'SoalDinasController@pendidikCetakSoalGandaPoin')->name('pendidik.dinas.cetaksoalgandapoin');
    Route::post('/pendidik/upsoalgandapoin/{id}','SoalDinasController@pendidikUpSoalGandaPoin')->name('pendidik.dinas.upsoalgandapoin');
    Route::get('/pendidik/editsoalgandapoin/{id}','SoalDinasController@pendidikEditSoalGandaPoin')->name('pendidik.dinas.editsoalgandapoin');
    Route::post('/pendidik/updatesoalgandapoin/{id}','SoalDinasController@pendidikUpdateSoalGandaPoin')->name('pendidik.dinas.updatesoalgandapoin');
    Route::get('/pendidik/soalessay/{id}','SoalDinasController@pendidikSoalEssay')->name('pendidik.dinas.soalessay');
    Route::post('/pendidik/upsoalessay/{id}','SoalDinasController@pendidikUpSoalEssay')->name('pendidik.dinas.upsoalessay');
    Route::get('/pendidik/editsoalessay/{id}','SoalDinasController@pendidikEditSoalEssay')->name('pendidik.dinas.editsoalessay');
    Route::post('/pendidik/updatesoalessay/{id}','SoalDinasController@pendidikUpdateSoalEssay')->name('pendidik.dinas.updatesoalessay');
    Route::get('/pendidik/hapussoalganda/{id}','SoalDinasController@pendidikHapusSoalGanda')->name('pendidik.dinas.hapussoalganda');
    Route::get('/pendidik/hapussoalgandapoin/{id}','SoalDinasController@pendidikHapusSoalGandaPoin')->name('pendidik.dinas.hapussoalgandapoin');
    Route::get('/pendidik/hapussoalessay/{id}','SoalDinasController@pendidikHapusSoalEssay')->name('pendidik.dinas.hapussoalessay');
    Route::get('/pendidik/penilaian/{id}', 'HasilDinasController@hasilPendidik')->name('pendidik.dinas.penilaian');
    Route::get('/pendidik/cetak_hasil/{id}', 'HasilDinasController@cetakPdfHasil')->name('pendidik.dinas.cetak_hasil');
    Route::post('/pendidik/arsipkan/{id}','HasilDinasController@arsipkan')->name('pendidik.dinas.arsipkan');
    Route::get('/pendidik/analisis','ArsipController@analisis')->name('pendidik.dinas.analisis');
    Route::get('/pendidik/hasil','ArsipController@hasil')->name('pendidik.dinas.hasil');
    Route::get('/pendidik/cetakhasil','ArsipController@cetakHasil')->name('pendidik.dinas.cetakhasil');
    Route::get('/pendidik/analisispelajar','ArsipController@pelajar')->name('pendidik.dinas.analisispelajar');
    Route::get('/pendidik/jawabanpelajar','ArsipController@jawabanPelajar')->name('pendidik.dinas.jawabanpelajar');
    Route::get('/pendidik/analisissoal','ArsipController@soal')->name('pendidik.dinas.analisissoal');
    Route::post('/pendidik/importganda','SoalDinasController@pendidikImportSoalGanda')->name('pendidik.dinas.importganda');
    Route::post('/pendidik/importgandapoin','SoalDinasController@pendidikImportSoalGandaPoin')->name('pendidik.dinas.importgandapoin');
    Route::get('/pendidik/cetakjawaban','ArsipController@cetakJawaban')->name('pendidik.dinas.cetakjawaban');
    Route::get('/pendidik/cetakjawabanpoin','ArsipController@cetakJawabanPoin')->name('pendidik.dinas.cetakjawabanpoin');
});



Route::group(['middleware' => ['auth','pelajar-role']], function(){

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
    Route::get('/pelajar/beranda','PelajarController@index')->name('pelajar.dinas.beranda');
    Route::get('/pelajar/paket','PaketDinasController@pelajarPaket')->name('pelajar.dinas.paket');
    Route::get('/pelajar/tes/{id}','TesDinasController@pelajarTes')->name('pelajar.dinas.tes');
    Route::get('/pelajar/persiapan/{id}','SoalDinasController@pelajarPersiapan')->name('pelajar.dinas.persiapan');
    Route::get('/pelajar/soalganda/{id}','SoalDinasController@pelajarSoalGanda')->name('pelajar.dinas.soalganda');
    Route::post('pelajar/upjawabanganda/{id}','JawabanDinasController@upJawabanGanda')->name('pelajar.dinas.upjawabanganda');
    Route::get('/pelajar/review/{id}','JawabanDinasController@review')->name('pelajar.dinas.review');
    Route::post('/pelajar/kumpulkan/{id}','JawabanDinasController@kumpulkan')->name('pelajar.dinas.kumpulkan');
    Route::get('/pelajar/nilai/{id}','JawabanDinasController@nilai')->name('pelajar.dinas.nilai');

    Route::get('/pelajar/soalgandapoin/{id}','SoalDinasController@pelajarSoalGandaPoin')->name('pelajar.dinas.soalgandapoin');
    Route::post('pelajar/upjawabangandapoin/{id}','JawabanDinasController@upJawabanGandaPoin')->name('pelajar.dinas.upjawabangandapoin');
    Route::get('/pelajar/reviewgandapoin/{id}','JawabanDinasController@reviewGandaPoin')->name('pelajar.dinas.reviewgandapoin');
    Route::post('/pelajar/kumpulkangandapoin/{id}','JawabanDinasController@kumpulkanGandaPoin')->name('pelajar.dinas.kumpulkangandapoin');

});
