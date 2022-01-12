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


Route::get('/','AuthController@tampilLogin')->name('landing');
Route::get('/register','AuthController@tampilRegister')->name('auth.register');
Route::post('/reg','AuthController@register')->name('reg');
Route::get('/login','AuthController@tampilLogin')->name('login');
Route::post('/log','AuthController@login')->name('log');
Route::get('/logout', 'AuthController@logout')->name('logout');
Route::get('/reset', 'AuthController@reset')->name('reset');
Route::post('/upreset', 'AuthController@upReset')->name('upreset');

Route::group(['prefix' => 'super','middleware' => ['auth','super-role']], function(){
    Route::get('/beranda','SuperController@index')->name('super.beranda');

    Route::get('/pengguna-pendaftar','PenggunaController@penggunaPendaftar')->name('super.penggunapendaftar');
    Route::get('/pengguna-pendaftar/lihat/{id}','PenggunaController@lihatPendaftar')->name('super.penggunapendaftar.lihat');
    Route::post('/pengguna-pendaftar/migrasi/{id}','PenggunaController@migrasiPendaftar')->name('super.penggunapendaftar.migrasi');
    Route::get('/pengguna-pendaftar/hapus/{id}','PenggunaController@hapusPendaftar')->name('super.penggunapendaftar.hapus');

    Route::get('/pengguna-pelajar','PenggunaController@penggunaPelajar')->name('super.penggunapelajar');
    Route::get('/pengguna-pelajar/lihat/{id}','PenggunaController@lihatPelajar')->name('super.penggunapelajar.lihat');
    Route::get('/pengguna-pelajar/edit/{id}','PenggunaController@editPelajar')->name('super.penggunapelajar.edit');
    Route::post('/pengguna-pelajar/update/{id}','PenggunaController@updatePelajar')->name('super.penggunapelajar.update');
    Route::get('/pengguna-pelajar/editdata/{id}','PenggunaController@editDataPelajar')->name('super.penggunapelajar.editdata');
    Route::post('/pengguna-pelajar/updatedata/{id}','PenggunaController@updateDataPelajar')->name('super.penggunapelajar.updatedata');
    Route::get('/pengguna-pelajar/suspend/{id}','PenggunaController@suspendPelajar')->name('super.penggunapelajar.suspend');
    Route::get('/pengguna-pelajar/hapus/{id}','PenggunaController@destroyPelajar')->name('super.penggunapelajar.hapus');
    Route::get('/pengguna-pelajar-suspended','PenggunaController@penggunaPelajarSuspend')->name('super.penggunasuspend');
    Route::get('/pengguna-pelajar-suspended/lihat/{id}','PenggunaController@lihatSuspended')->name('super.penggunasuspend.lihat');
    Route::get('/pengguna-pelajar-suspended/cabut-suspend-pelajar/{id}','PenggunaController@cabutSuspendPelajar')->name('super.penggunasuspend.cabutsuspendpelajar');

    Route::get('/pengguna-pendidik','PenggunaController@penggunaPendidik')->name('super.penggunapendidik');
    Route::post('/pengguna-pendidik/tambah','PenggunaController@tambahPendidik')->name('super.penggunapendidik.tambah');

    Route::get('/pengguna-staf-admin','PenggunaController@penggunaStafAdmin')->name('super.penggunastafadmin');
    Route::post('/pengguna-staf-admin/tambah','PenggunaController@tambahStafAdmin')->name('super.penggunastafadmin.tambah');
    Route::get('/pengguna-staf-admin/hapus/{id}','PenggunaController@destroyStafAdmin')->name('super.penggunastafadmin.hapus');
    Route::get('/pengguna-staf-admin/lihat/{id}','PenggunaController@lihatStafAdmin')->name('super.penggunastafadmin.lihat');

    Route::get('/pengguna-pelajar-suspended','PenggunaController@penggunaPelajarSuspend')->name('super.penggunasuspend');
    Route::get('/pengguna-pelajar-suspended/lihat/{id}','PenggunaController@lihatSuspended')->name('super.penggunasuspend.lihat');
    Route::get('/pengguna-pelajar-suspended/cabut-suspend-pelajar/{id}','PenggunaController@cabutSuspendPelajar')->name('super.penggunasuspend.cabutsuspendpelajar');

});

Route::group(['middleware' => ['auth','admin-role']], function(){
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
    // Kedinasan
    Route::get('/pendidik/beranda','PengajarController@index')->name('pendidik.dinas.beranda');
    Route::get('/pendidik/edit-profil','PengajarController@edit')->name('pendidik.dinas.edit');
    Route::post('/pendidik/update-profil','PengajarController@update')->name('pendidik.dinas.updateprofil');
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
