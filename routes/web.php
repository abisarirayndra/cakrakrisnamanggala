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

// Pendaftaran
Route::get('/petunjuk-pendaftaran','PendaftarController@petunjuk')->name('petunjuk');
Route::get('/register-email','PendaftarController@registerEmail')->name('register-email');
Route::post('/upload-register-email','PendaftarController@uploadRegisterEmail')->name('up-register-email');
Route::post('/up-formulir-pendaftaran','PendaftarController@upFormulirPendaftar')->name('pendaftar.up-formulir-pendaftaran');
    Route::get('/cetak-formulir/{id}','PendaftarController@cetak')->name('pendaftar.cetak-formulir');
    Route::get('/cetak-formulir-pdf/{id}','PendaftarController@cetak_pdf')->name('pendaftar.cetak-formulir-pdf');
    Route::get('/edit-pendaftar/{id}','PendaftarController@editPendaftar')->name('pendaftar.edit-pendaftar');
    Route::post('/update-pendaftar/{id}','PendaftarController@updatePendaftar')->name('pendaftar.update-pendaftar');
Route::get('/register-pendidik','PendaftarController@registerPendidik')->name('register-pendidik');
Route::post('/upload-register-pendidik','PendaftarController@upRegisterPendidik')->name('register-pendidik.upload');

Route::group(['prefix' => 'pendaftar','middleware' => ['auth','pendaftar-role']], function () {
    Route::get('/profil','PendaftarController@profil')->name('pendaftar.profil');


});

Route::group(['prefix' => 'super','middleware' => ['auth','super-role']], function(){



});

Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin-role']], function(){
    Route::get('/beranda','AdminController@index')->name('admin.beranda');

    Route::get('/paket','PaketDinasController@paket')->name('admin.dinas.paket');
    Route::get('/tambahpaket','PaketDinasController@tambah')->name('admin.dinas.tambahpaket');
    Route::post('/uppaket','PaketDinasController@up')->name('admin.dinas.uppaket');
    Route::get('/lihatpaket/{id}','PaketDinasController@lihat')->name('admin.dinas.lihatpaket');
    Route::get('/get_token_tes/{id}','PaketDinasController@getTesToken')->name('admin.dinas.get_token_tes');
    Route::get('/editpaket/{id}','PaketDinasController@editPaket')->name('admin.dinas.editpaket');
    Route::post('/updatepaket/{id}','PaketDinasController@updatePaket')->name('admin.dinas.updatepaket');
    Route::get('/admin/hapuspaket/{id}','PaketDinasController@hapusPaket')->name('admin.dinas.hapuspaket');
    Route::get('/daftar_arsip','ArsipController@daftarArsip')->name('admin.dinas.daftar_arsip');

    Route::post('/tambahkelas/{id}','PaketDinasController@tambahKelas')->name('admin.dinas.tambahkelas');
    Route::get('/hapuskelas/{id}','PaketDinasController@hapusKelas')->name('admin.dinas.hapuskelas');
    Route::post('/tambahtes/{id}','TesDinasController@tambahTes')->name('admin.dinas.tambahtes');
    Route::get('/hapustes/{id}','TesDinasController@hapusTes')->name('admin.dinas.hapustes');
    Route::get('/edittes/{id}','TesDinasController@editTes')->name('admin.dinas.edittes');
    Route::post('/updatetes/{id}','TesDinasController@updateTes')->name('admin.dinas.updatetes');
    Route::get('/hasildinas/{id}','HasilDinasController@hasilKedinasanAdmin')->name('admin.dinas.hasildinas');
    Route::get('/live_hasildinas/{id}','HasilDinasController@liveSkorKedinasan')->name('admin.dinas.live_hasildinas');
    Route::get('/hasiltnipolri/{id}','HasilDinasController@hasilTniPolriAdmin')->name('admin.dinas.hasiltnipolri');
    Route::get('/live_hasiltnipolri/{id}','HasilDinasController@liveSkorTniPolri')->name('admin.dinas.live_hasiltnipolri');
    Route::get('/cetakhasildinas/{id}','HasilDinasController@cetakKedinasanAdmin')->name('admin.dinas.cetakhasildinas');
    Route::get('/cetaktnipolri/{id}','HasilDinasController@cetakTniPolriAdmin')->name('admin.dinas.cetakhasiltnipolri');
    Route::get('/hasil_psikotes/{id}','HasilDinasController@hasilPsikotesAdmin')->name('admin.dinas.hasil_psikotes');
    Route::get('/cetak_hasil_psikotes/{id}','HasilDinasController@cetakPsikotesAdmin')->name('admin.dinas.cetak_hasil_psikotes');
    Route::get('/live_hasilpsikotes/{id}','HasilDinasController@liveSkorPsikotes')->name('admin.dinas.live_hasilpsikotes');
    Route::get('/arsipkan_paket/{id}', 'HasilDinasController@arsipkanPaket')->name('admin.dinas.arsipkan_paket');

    // Route::get('/monitor_tes','TesDinasController@monitor')->name('admin.monitor_tes');
    // Route::get('/monitor_tes/lihat/{id}','TesDinasController@monitorTes')->name('admin.monitor_tes.lihat');
    // Route::post('/monitor_tes/diskualifikasi/{id}','TesDinasController@diskualifikasi')->name('admin.monitor_tes.diskualifikasi');

    //Toefl
    Route::get('/paket-toefl','PaketToeflController@index')->name('admin.toefl.index');
    Route::post('/tambah-paket-toefl','PaketToeflController@create')->name('admin.toefl.create');
    Route::get('/perbarui-referal/{id}','PaketToeflController@perbaruiReferal')->name('admin.toefl.perbarui-referal');
    Route::get('/hapus-paket/{id}','PaketToeflController@hapusPaket')->name('admin.toefl.hapus-paket');
    Route::get('/edit-paket/{id}','PaketToeflController@editPaket')->name('admin.toefl.edit-paket');
    Route::post('/update-paket/{id}','PaketToeflController@updatePaket')->name('admin.toefl.update-paket');

    Route::get('/opsi_administrasi','SuperController@index')->name('super.administrasi');

    Route::get('/pengguna-pendaftar','PenggunaController@penggunaPendaftar')->name('super.penggunapendaftar');
    Route::get('/pengguna-pendaftar/lihat/{id}','PenggunaController@lihatPendaftar')->name('super.penggunapendaftar.lihat');
    Route::post('/pengguna-pendaftar/migrasi/{id}','PenggunaController@migrasiPendaftar')->name('super.penggunapendaftar.migrasi');
    Route::get('/pengguna-pendaftar/hapus/{id}','PenggunaController@hapusPendaftar')->name('super.penggunapendaftar.hapus');

    Route::get('/pengguna-pelajar','PenggunaController@penggunaPelajar')->name('super.penggunapelajar');
    Route::get('/pengguna-pelajar/cetak','PenggunaController@cetakPenggunaPelajar')->name('super.penggunapelajar.cetak');
    Route::get('/pengguna-pelajar/lihat/{id}','PenggunaController@lihatPelajar')->name('super.penggunapelajar.lihat');
    Route::get('/pengguna-pelajar/edit/{id}','PenggunaController@editPelajar')->name('super.penggunapelajar.edit');
    Route::post('/pengguna-pelajar/update/{id}','PenggunaController@updatePelajar')->name('super.penggunapelajar.update');
    Route::get('/pengguna-pelajar/cetak-pdf/{id}','PenggunaController@cetakPdfPelajar')->name('super.penggunapelajar.cetak-pdf');
    Route::get('/pengguna-pelajar/editdata/{id}','PenggunaController@editDataPelajar')->name('super.penggunapelajar.editdata');
    Route::post('/pengguna-pelajar/updatedata/{id}','PenggunaController@updateDataPelajar')->name('super.penggunapelajar.updatedata');
    Route::get('/pengguna-pelajar/suspend/{id}','PenggunaController@suspendPelajar')->name('super.penggunapelajar.suspend');
    Route::get('/pengguna-pelajar/hapus/{id}','PenggunaController@destroyPelajar')->name('super.penggunapelajar.hapus');
    Route::get('/pengguna-pelajar-suspended','PenggunaController@penggunaPelajarSuspend')->name('super.penggunasuspend');
    Route::get('/pengguna-pelajar-suspended/lihat/{id}','PenggunaController@lihatSuspended')->name('super.penggunasuspend.lihat');
    Route::get('/pengguna-pelajar-suspended/cabut-suspend-pelajar/{id}','PenggunaController@cabutSuspendPelajar')->name('super.penggunasuspend.cabutsuspendpelajar');

    Route::get('/pengguna-pendidik','PenggunaController@penggunaPendidik')->name('super.penggunapendidik');
    Route::post('/pengguna-pendidik/tambah','PenggunaController@tambahPendidik')->name('super.penggunapendidik.tambah');
    Route::get('/pengguna-pendidik/lihat/{id}','PenggunaController@lihatPendidik')->name('super.penggunapendidik.lihat');
    Route::get('/pengguna-pendidik/hapus/{id}','PenggunaController@hapusPendidik')->name('super.penggunapendidik.hapus');

    Route::get('/pengguna-staf-admin','PenggunaController@penggunaStafAdmin')->name('super.penggunastafadmin');
    Route::post('/pengguna-staf-admin/tambah','PenggunaController@tambahStafAdmin')->name('super.penggunastafadmin.tambah');
    Route::get('/pengguna-staf-admin/hapus/{id}','PenggunaController@destroyStafAdmin')->name('super.penggunastafadmin.hapus');
    Route::get('/pengguna-staf-admin/lihat/{id}','PenggunaController@lihatStafAdmin')->name('super.penggunastafadmin.lihat');

    Route::get('/pengguna-pelajar-suspended','PenggunaController@penggunaPelajarSuspend')->name('super.penggunasuspend');
    Route::get('/pengguna-pelajar-suspended/lihat/{id}','PenggunaController@lihatSuspended')->name('super.penggunasuspend.lihat');
    Route::get('/pengguna-pelajar-suspended/cabut-suspend-pelajar/{id}','PenggunaController@cabutSuspendPelajar')->name('super.penggunasuspend.cabutsuspendpelajar');

    Route::get('/cetak_soal/{id}','SoalDinasController@adminCetakSoalGandaPoin')->name('admin.cetak_soal');
});

Route::group(['prefix' => 'pendidik','middleware' => ['auth','pengajar-role']], function(){
    Route::get('/beranda','PengajarController@index')->name('pendidik.dinas.beranda');
    Route::get('/edit-profil','PengajarController@edit')->name('pendidik.dinas.edit');
    Route::post('/update-profil','PengajarController@update')->name('pendidik.dinas.updateprofil');
    Route::get('/paket','PaketDinasController@pendidikPaket')->name('pendidik.dinas.paket');
    Route::get('/tes/{id}','TesDinasController@pendidikTes')->name('pendidik.dinas.tes');
    Route::get('/tipesoal/{id}','SoalDinasController@pendidikPilihTipe')->name('pendidik.dinas.tipesoal');
    Route::get('/hapusganda/{id}','SoalDinasController@pendidikHapusGanda')->name('pendidik.dinas.hapusganda');
    Route::get('/hapusgandapoin/{id}','SoalDinasController@pendidikHapusGandaPoin')->name('pendidik.dinas.hapusgandapoin');
    Route::get('/hapusessay/{id}','SoalDinasController@pendidikHapusEssay')->name('pendidik.dinas.hapusessay');
    Route::get('/soalganda/{id}','SoalDinasController@pendidikSoalGanda')->name('pendidik.dinas.soalganda');
    Route::get('/cetaksoalganda/{id}', 'SoalDinasController@pendidikCetakSoalGanda')->name('pendidik.dinas.cetaksoalganda');
    Route::post('/upsoalganda/{id}','SoalDinasController@pendidikUpSoalGanda')->name('pendidik.dinas.upsoalganda');
    Route::get('/editsoalganda/{id}','SoalDinasController@pendidikEditSoalGanda')->name('pendidik.dinas.editsoalganda');
    Route::post('/updatesoalganda/{id}','SoalDinasController@pendidikUpdateSoalGanda')->name('pendidik.dinas.updatesoalganda');
    Route::get('/soalgandapoin/{id}','SoalDinasController@pendidikSoalGandaPoin')->name('pendidik.dinas.soalgandapoin');
    Route::get('/cetaksoalgandapoin/{id}', 'SoalDinasController@pendidikCetakSoalGandaPoin')->name('pendidik.dinas.cetaksoalgandapoin');
    Route::post('/upsoalgandapoin/{id}','SoalDinasController@pendidikUpSoalGandaPoin')->name('pendidik.dinas.upsoalgandapoin');
    Route::get('/editsoalgandapoin/{id}','SoalDinasController@pendidikEditSoalGandaPoin')->name('pendidik.dinas.editsoalgandapoin');
    Route::post('/updatesoalgandapoin/{id}','SoalDinasController@pendidikUpdateSoalGandaPoin')->name('pendidik.dinas.updatesoalgandapoin');
    Route::get('/soalessay/{id}','SoalDinasController@pendidikSoalEssay')->name('pendidik.dinas.soalessay');
    Route::post('/upsoalessay/{id}','SoalDinasController@pendidikUpSoalEssay')->name('pendidik.dinas.upsoalessay');
    Route::get('/editsoalessay/{id}','SoalDinasController@pendidikEditSoalEssay')->name('pendidik.dinas.editsoalessay');
    Route::post('/updatesoalessay/{id}','SoalDinasController@pendidikUpdateSoalEssay')->name('pendidik.dinas.updatesoalessay');
    Route::get('/hapussoalganda/{id}','SoalDinasController@pendidikHapusSoalGanda')->name('pendidik.dinas.hapussoalganda');
    Route::get('/hapussoalgandapoin/{id}','SoalDinasController@pendidikHapusSoalGandaPoin')->name('pendidik.dinas.hapussoalgandapoin');
    Route::get('/hapussoalessay/{id}','SoalDinasController@pendidikHapusSoalEssay')->name('pendidik.dinas.hapussoalessay');
    Route::get('/penilaian/{id}', 'HasilDinasController@hasilPendidik')->name('pendidik.dinas.penilaian');
    Route::get('/cetak_hasil/{id}', 'HasilDinasController@cetakPdfHasil')->name('pendidik.dinas.cetak_hasil');
    Route::post('/arsipkan/{id}','HasilDinasController@arsipkan')->name('pendidik.dinas.arsipkan');
    Route::get('/analisis','ArsipController@analisis')->name('pendidik.dinas.analisis');
    Route::get('/hasil','ArsipController@hasil')->name('pendidik.dinas.hasil');
    Route::get('/cetakhasil','ArsipController@cetakHasil')->name('pendidik.dinas.cetakhasil');
    Route::get('/analisispelajar','ArsipController@pelajar')->name('pendidik.dinas.analisispelajar');
    Route::get('/jawabanpelajar','ArsipController@jawabanPelajar')->name('pendidik.dinas.jawabanpelajar');
    Route::get('/analisissoal','ArsipController@soal')->name('pendidik.dinas.analisissoal');
    Route::post('/importganda','SoalDinasController@pendidikImportSoalGanda')->name('pendidik.dinas.importganda');
    Route::post('/importgandapoin','SoalDinasController@pendidikImportSoalGandaPoin')->name('pendidik.dinas.importgandapoin');
    Route::get('/cetakjawaban','ArsipController@cetakJawaban')->name('pendidik.dinas.cetakjawaban');
    Route::get('/cetakjawabanpoin','ArsipController@cetakJawabanPoin')->name('pendidik.dinas.cetakjawabanpoin');

    // Absensi
    Route::get('/absensi','JadwalAbsensiController@scanAbsensiPendidik')->name('pendidik.absensi');
    Route::get('/absensi/jurnal/{id}','JadwalAbsensiController@jurnalPendidik')->name('pendidik.absensi.jurnal');
    Route::post('/absensi/up-jurnal/{id}','JadwalAbsensiController@upJurnalPendidik')->name('pendidik.absensi.up-jurnal');
    Route::post('/absensi/selesai/{id}','JadwalAbsensiController@selesaiPendidik')->name('pendidik.absensi.selesai');
    Route::get('/absensi/histori-mengajar','JadwalAbsensiController@historiMengajar')->name('pendidik.absensi.histori-mengajar');
    // Absensi Jasmani
    Route::get('/jadwal_jasmani','JadwalAbsensiController@jadwalAbsensiJasmani')->name('pendidik.absensi.jadwal_jasmani');
    Route::get('/jadwal_jasmani/absensi/{id}','JadwalAbsensiController@absensiJasmani')->name('pendidik.absensi.jadwal_jasmani.absensi');
    Route::post('/absensi_jasmani/upload_absensi_jasmani/pelajar','JadwalAbsensiController@uploadAbsensiPelajarJasmani')->name('pendidik.absensi.upload_absensi_jasmani.pelajar');
    Route::post('/absensi_jasmani/upload_absensi_jasmani','JadwalAbsensiController@uploadAbsensiPendidikJasmani')->name('pendidik.absensi.upload_absensi_jasmani');
    Route::get('/absensi_jasmani/hapus/izin-pelajar/{id}','JadwalAbsensiController@hapusIzinPelajar')->name('pendidik.absensi.hapus-izin-pelajar');
    Route::post('/absensi_jasmani/upload-absensi/izin-pelajar','JadwalAbsensiController@uploadAbsensiIzinPelajar')->name('pendidik.absensi.upload-izin-pelajar');
    Route::post('/absensi_jasmani/upload-absensi/izin-pendidik','JadwalAbsensiController@uploadAbsensiIzinPendidik')->name('pendidik.absensi.upload-izin-pendidik');
    Route::get('/absensi_jasmani/hapus/izin-pendidik/{id}','JadwalAbsensiController@hapusIzinPendidik')->name('pendidik.absensi.hapus-izin-pendidik');

});



Route::group(['prefix' => 'pelajar','middleware' => ['auth','pelajar-role']], function(){

    //Kedinasan
    Route::get('/beranda','PelajarController@index')->name('pelajar.dinas.beranda');
    Route::get('/paket','PaketDinasController@pelajarPaket')->name('pelajar.dinas.paket');
    Route::get('/tes/{id}','TesDinasController@pelajarTes')->name('pelajar.dinas.tes');
    Route::get('/persiapan/{id}','SoalDinasController@pelajarPersiapan')->name('pelajar.dinas.persiapan');
    Route::get('/soalganda/{id}','SoalDinasController@pelajarSoalGanda')->name('pelajar.dinas.soalganda');
    Route::post('/upjawabanganda/{id}','JawabanDinasController@upJawabanGanda')->name('pelajar.dinas.upjawabanganda');
    // Route::get('/review/{id}','JawabanDinasController@review')->name('pelajar.dinas.review');
    Route::get('/kumpulkan/{id}','JawabanDinasController@kumpulkan')->name('pelajar.dinas.kumpulkan');
    Route::get('/nilai/{id}','JawabanDinasController@nilai')->name('pelajar.dinas.nilai');
    Route::get('/soalgandapoin/{id}','SoalDinasController@pelajarSoalGandaPoin')->name('pelajar.dinas.soalgandapoin');
    Route::post('/upjawabangandapoin/{id}','JawabanDinasController@upJawabanGandaPoin')->name('pelajar.dinas.upjawabangandapoin');
    // Route::get('/reviewgandapoin/{id}','JawabanDinasController@reviewGandaPoin')->name('pelajar.dinas.reviewgandapoin');
    // Route::post('/kumpulkangandapoin/{id}','JawabanDinasController@kumpulkanGandaPoin')->name('pelajar.dinas.kumpulkangandapoin');

    Route::get('/absensi','JadwalAbsensiController@scanAbsensiPelajar')->name('pelajar.absensi');
    Route::get('/absensi/histori-pembelajaran','JadwalAbsensiController@historiPelajar')->name('pelajar.absensi.histori-pembelajaran');
    Route::get('/masukkan_token','TesDinasController@masukToken')->name('pelajar.masukkan_token');
    Route::post('/submit_token','TesDinasController@submitToken')->name('pelajar.submit_token');

});

Route::group(['prefix' => 'staf-admin', 'middleware' => ['auth','stafadmin-role']], function(){
    Route::get('/beranda','StafAdminController@index')->name('staf-admin.beranda');
    Route::post('/update-profil/{id}','StafAdminController@update')->name('staf-admin.update-profil');
    Route::get('/jadwal','JadwalAbsensiController@index')->name('staf-admin.jadwal');
    Route::post('/jadwal/tambah','JadwalAbsensiController@tambahJadwal')->name('staf-admin.jadwal.tambah');
    Route::get('/jadwal/hapus/{id}','JadwalAbsensiController@hapusJadwal')->name('staf-admin.jadwal.hapus');
    Route::get('/jadwal/edit/{id}','JadwalAbsensiController@editJadwal')->name('staf-admin.jadwal.edit');
    Route::post('/jadwal/update/{id}','JadwalAbsensiController@updateJadwal')->name('staf-admin.jadwal.update');
    Route::get('/absensi/beranda','JadwalAbsensiController@berandaAbsensi')->name('staf-admin.absensi.beranda');
    Route::get('/absensi/{id}','JadwalAbsensiController@absensi')->name('staf-admin.absen');
    Route::get('/absensi-pulang/{id}','JadwalAbsensiController@absensiPulang')->name('staf-admin.absen-pulang');
    Route::post('/absensi-pulang/selesai','JadwalAbsensiController@selesaiPelajar')->name('staf-admin.absen-pulang.selesai');
    Route::get('/absen/staf','JadwalAbsensiController@absenStaf')->name('staf-admin.absen.staf');
    Route::post('/absensi/upload-absensi','JadwalAbsensiController@uploadAbsensi')->name('staf-admin.absensi.upload-absensi');
    Route::post('/absensi/upload-absensi/staf','JadwalAbsensiController@uploadAbsensiStaf')->name('staf-admin.absensi.upload-absensi.staf');
    Route::post('/absensi/upload-absensi/izin-staf','JadwalAbsensiController@uploadAbsensiIzinStaf')->name('staf-admin.absensi.upload-izin-staf');
    Route::get('/absensi/hapus/izin-staf/{id}','JadwalAbsensiController@hapusIzinStaf')->name('staf-admin.absensi.hapus-izin-staf');

    Route::get('/absensi/hapus/izin-pelajar/{id}','JadwalAbsensiController@hapusIzinPelajar')->name('staf-admin.absensi.hapus-izin-pelajar');
    Route::post('/absensi/upload-absensi/izin-pelajar','JadwalAbsensiController@uploadAbsensiIzinPelajar')->name('staf-admin.absensi.upload-izin-pelajar');
    Route::post('/absensi/upload-absensi/izin-pendidik','JadwalAbsensiController@uploadAbsensiIzinPendidik')->name('staf-admin.absensi.upload-izin-pendidik');
    Route::get('/absensi/hapus/izin-pendidik/{id}','JadwalAbsensiController@hapusIzinPendidik')->name('staf-admin.absensi.hapus-izin-pendidik');
    Route::get('/absen/rekap-pembelajaran/','JadwalAbsensiController@rekapAbsensiPembelajaran')->name('staf-admin.absensi.rekap-pembelajaran');
    Route::get('/absen/rekap-pembelajaran/lihat/{id}','JadwalAbsensiController@lihatRekapAbsensiPembelajaran')->name('staf-admin.absensi.rekap-pembelajaran.lihat');
    Route::get('/absen/rekap-pembelajaran/cetak/{id}','JadwalAbsensiController@cetakJurnalHarian')->name('staf-admin.absensi.rekap-pembelajaran.cetak');
    Route::get('/absen/rekap-staf/','JadwalAbsensiController@rekapAbsensiStaf')->name('staf-admin.absensi.rekap-staf');
    Route::get('/absen/rekap-staf/cetak-jurnal','JadwalAbsensiController@cetakJurnalStaf')->name('staf-admin.absensi.rekap-staf.cetak');

    Route::get('/pengguna-pelajar','PenggunaController@penggunaPelajar')->name('staf-admin.penggunapelajar');
    Route::get('/pengguna-pelajar/cetak','PenggunaController@cetakPenggunaPelajar')->name('staf-admin.penggunapelajar.cetak');
    Route::get('/pengguna-pelajar/lihat/{id}','PenggunaController@lihatPelajar')->name('staf-admin.penggunapelajar.lihat');
    Route::get('/pengguna-pelajar/edit/{id}','PenggunaController@editPelajar')->name('staf-admin.penggunapelajar.edit');
    Route::post('/pengguna-pelajar/update/{id}','PenggunaController@updatePelajar')->name('staf-admin.penggunapelajar.update');
    Route::get('/pengguna-pelajar/cetak-pdf/{id}','PenggunaController@cetakPdfPelajar')->name('staf-admin.penggunapelajar.cetak-pdf');
    Route::get('/pengguna-pelajar/editdata/{id}','PenggunaController@editDataPelajar')->name('staf-admin.penggunapelajar.editdata');
    Route::post('/pengguna-pelajar/updatedata/{id}','PenggunaController@updateDataPelajar')->name('staf-admin.penggunapelajar.updatedata');
    // Route::get('/pengguna-pelajar/suspend/{id}','PenggunaController@suspendPelajar')->name('staf-admin.penggunapelajar.suspend');
    // Route::get('/pengguna-pelajar/hapus/{id}','PenggunaController@destroyPelajar')->name('staf-admin.penggunapelajar.hapus');
});
