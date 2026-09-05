<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HasilDinasController;
use App\Http\Controllers\JadwalAbsensiController;
use App\Http\Controllers\JawabanDinasController;
use App\Http\Controllers\PaketDinasController;
use App\Http\Controllers\PelajarController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SoalDinasController;
use App\Http\Controllers\StafAdminController;
use App\Http\Controllers\SuperController;
use App\Http\Controllers\TesDinasController;
use App\Livewire\Admin\MasterAdmin;
use App\Livewire\Admin\MasterPelajar;
use App\Livewire\Admin\MasterPendidik;
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


Route::get('/',[AuthController::class, 'tampilLogin'])->name('landing');
// Dead routes: AuthController@tampilRegister / register were never implemented.
Route::get('/login',[AuthController::class, 'tampilLogin'])->name('login');
Route::post('/log',[AuthController::class, 'login'])->name('log');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/reset', [AuthController::class, 'reset'])->name('reset');
Route::post('/submit_email',[AuthController::class, 'kirimEmail'])->name('submit_email');
Route::get('/form_reset',[AuthController::class, 'formReset'])->name('form_reset');
Route::post('/upreset', [AuthController::class, 'upReset'])->name('upreset');

// Pendaftaran
Route::get('/petunjuk-pendaftaran',[PendaftarController::class, 'petunjuk'])->name('petunjuk');
Route::get('/register-email',[PendaftarController::class, 'registerEmail'])->name('register-email');
Route::post('/upload-register-email',[PendaftarController::class, 'uploadRegisterEmail'])->name('up-register-email');
Route::post('/up-formulir-pendaftaran',[PendaftarController::class, 'upFormulirPendaftar'])->name('pendaftar.up-formulir-pendaftaran');
    Route::get('/cetak-formulir/{id}',[PendaftarController::class, 'cetak'])->name('pendaftar.cetak-formulir');
    Route::get('/cetak-formulir-pdf/{id}',[PendaftarController::class, 'cetak_pdf'])->name('pendaftar.cetak-formulir-pdf');
    Route::get('/edit-pendaftar/{id}',[PendaftarController::class, 'editPendaftar'])->name('pendaftar.edit-pendaftar');
    Route::post('/update-pendaftar/{id}',[PendaftarController::class, 'updatePendaftar'])->name('pendaftar.update-pendaftar');
Route::get('/register-pendidik',[PendaftarController::class, 'registerPendidik'])->name('register-pendidik');
Route::post('/upload-register-pendidik',[PendaftarController::class, 'upRegisterPendidik'])->name('register-pendidik.upload');

Route::group(['prefix' => 'pendaftar','middleware' => ['auth','pendaftar-role']], function () {
    Route::get('/profil',[PendaftarController::class, 'profil'])->name('pendaftar.profil');


});

Route::group(['prefix' => 'super','middleware' => ['auth','super-role']], function(){



});

Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin-role']], function(){
    Route::get('/beranda',[AdminController::class, 'index'])->name('admin.beranda');

    Route::middleware('superadmin-role')->group(function () {
        Route::get('/paket',[PaketDinasController::class, 'paket'])->name('admin.dinas.paket');
        Route::get('/tambahpaket',[PaketDinasController::class, 'tambah'])->name('admin.dinas.tambahpaket');
        Route::post('/uppaket',[PaketDinasController::class, 'up'])->name('admin.dinas.uppaket');
        Route::get('/lihatpaket/{id}',[PaketDinasController::class, 'lihat'])->name('admin.dinas.lihatpaket');
        Route::get('/get_token_tes/{id}',[PaketDinasController::class, 'getTesToken'])->name('admin.dinas.get_token_tes');
        Route::get('/editpaket/{id}',[PaketDinasController::class, 'editPaket'])->name('admin.dinas.editpaket');
        Route::post('/updatepaket/{id}',[PaketDinasController::class, 'updatePaket'])->name('admin.dinas.updatepaket');
        Route::get('/admin/hapuspaket/{id}',[PaketDinasController::class, 'hapusPaket'])->name('admin.dinas.hapuspaket');
        Route::get('/daftar_arsip',[ArsipController::class, 'daftarArsip'])->name('admin.dinas.daftar_arsip');

        Route::post('/tambahkelas/{id}',[PaketDinasController::class, 'tambahKelas'])->name('admin.dinas.tambahkelas');
        Route::get('/hapuskelas/{id}',[PaketDinasController::class, 'hapusKelas'])->name('admin.dinas.hapuskelas');
        Route::post('/tambahtes/{id}',[TesDinasController::class, 'tambahTes'])->name('admin.dinas.tambahtes');
        Route::get('/hapustes/{id}',[TesDinasController::class, 'hapusTes'])->name('admin.dinas.hapustes');
        Route::get('/edittes/{id}',[TesDinasController::class, 'editTes'])->name('admin.dinas.edittes');
        Route::post('/updatetes/{id}',[TesDinasController::class, 'updateTes'])->name('admin.dinas.updatetes');
        Route::get('/hasildinas/{id}',[HasilDinasController::class, 'hasilKedinasanAdmin'])->name('admin.dinas.hasildinas');
        Route::get('/live_hasildinas/{id}',[HasilDinasController::class, 'liveSkorKedinasan'])->name('admin.dinas.live_hasildinas');
        Route::get('/hasiltnipolri/{id}',[HasilDinasController::class, 'hasilTniPolriAdmin'])->name('admin.dinas.hasiltnipolri');
        Route::get('/live_hasiltnipolri/{id}',[HasilDinasController::class, 'liveSkorTniPolri'])->name('admin.dinas.live_hasiltnipolri');
        Route::get('/cetakhasildinas/{id}',[HasilDinasController::class, 'cetakKedinasanAdmin'])->name('admin.dinas.cetakhasildinas');
        Route::get('/cetaktnipolri/{id}',[HasilDinasController::class, 'cetakTniPolriAdmin'])->name('admin.dinas.cetakhasiltnipolri');
        Route::get('/hasil_psikotes/{id}',[HasilDinasController::class, 'hasilPsikotesAdmin'])->name('admin.dinas.hasil_psikotes');
        Route::get('/cetak_hasil_psikotes/{id}',[HasilDinasController::class, 'cetakPsikotesAdmin'])->name('admin.dinas.cetak_hasil_psikotes');
        Route::get('/live_hasilpsikotes/{id}',[HasilDinasController::class, 'liveSkorPsikotes'])->name('admin.dinas.live_hasilpsikotes');
        Route::get('/arsipkan_paket/{id}', [HasilDinasController::class, 'arsipkanPaket'])->name('admin.dinas.arsipkan_paket');

        // Route::get('/monitor_tes',[TesDinasController::class, 'monitor'])->name('admin.monitor_tes');
        // Route::get('/monitor_tes/lihat/{id}',[TesDinasController::class, 'monitorTes'])->name('admin.monitor_tes.lihat');
        // Route::post('/monitor_tes/diskualifikasi/{id}',[TesDinasController::class, 'diskualifikasi'])->name('admin.monitor_tes.diskualifikasi');

        // TOEFL routes removed: PaketToeflController does not exist.

        Route::get('/opsi_administrasi',[SuperController::class, 'index'])->name('super.administrasi');

        Route::get('/pengguna-pendaftar',[PenggunaController::class, 'penggunaPendaftar'])->name('super.penggunapendaftar');
        Route::get('/pengguna-pendaftar/lihat/{id}',[PenggunaController::class, 'lihatPendaftar'])->name('super.penggunapendaftar.lihat');
        Route::post('/pengguna-pendaftar/migrasi/{id}',[PenggunaController::class, 'migrasiPendaftar'])->name('super.penggunapendaftar.migrasi');
        Route::get('/pengguna-pendaftar/hapus/{id}',[PenggunaController::class, 'hapusPendaftar'])->name('super.penggunapendaftar.hapus');
    });

    Route::get('/pengguna-pelajar', MasterPelajar::class)->name('admin.pengguna.pelajar');
    Route::middleware('superadmin-role')->group(function () {
        Route::get('/pengguna-pelajar/cetak',[PenggunaController::class, 'cetakPenggunaPelajar'])->name('super.penggunapelajar.cetak');
        Route::get('/pengguna-pelajar/lihat/{id}',[PenggunaController::class, 'lihatPelajar'])->name('super.penggunapelajar.lihat');
        Route::get('/pengguna-pelajar/edit/{id}',[PenggunaController::class, 'editPelajar'])->name('super.penggunapelajar.edit');
        Route::post('/pengguna-pelajar/update/{id}',[PenggunaController::class, 'updatePelajar'])->name('super.penggunapelajar.update');
        Route::get('/pengguna-pelajar/cetak-pdf/{id}',[PenggunaController::class, 'cetakPdfPelajar'])->name('super.penggunapelajar.cetak-pdf');
        Route::get('/pengguna-pelajar/editdata/{id}',[PenggunaController::class, 'editDataPelajar'])->name('super.penggunapelajar.editdata');
        Route::post('/pengguna-pelajar/updatedata/{id}',[PenggunaController::class, 'updateDataPelajar'])->name('super.penggunapelajar.updatedata');
        Route::get('/pengguna-pelajar/suspend/{id}',[PenggunaController::class, 'suspendPelajar'])->name('super.penggunapelajar.suspend');
        Route::get('/pengguna-pelajar/hapus/{id}',[PenggunaController::class, 'destroyPelajar'])->name('super.penggunapelajar.hapus');
        Route::get('/pengguna-pelajar-suspended',[PenggunaController::class, 'penggunaPelajarSuspend'])->name('super.penggunasuspend');
        Route::get('/pengguna-pelajar-suspended/lihat/{id}',[PenggunaController::class, 'lihatSuspended'])->name('super.penggunasuspend.lihat');
        Route::get('/pengguna-pelajar-suspended/cabut-suspend-pelajar/{id}',[PenggunaController::class, 'cabutSuspendPelajar'])->name('super.penggunasuspend.cabutsuspendpelajar');
    });

    Route::middleware('superadmin-role')->group(function () {
        Route::post('/pengguna-pendidik/tambah',[PenggunaController::class, 'tambahPendidik'])->name('super.penggunapendidik.tambah');
        Route::get('/pengguna-pendidik/lihat/{id}',[PenggunaController::class, 'lihatPendidik'])->name('super.penggunapendidik.lihat');
        Route::get('/pengguna-pendidik/hapus/{id}',[PenggunaController::class, 'hapusPendidik'])->name('super.penggunapendidik.hapus');

        Route::get('/cetak_soal/{id}',[SoalDinasController::class, 'adminCetakSoalGandaPoin'])->name('admin.cetak_soal');
    });

    Route::get('/pengguna-admin', MasterAdmin::class)->middleware('superadmin-role')->name('admin.pengguna.admin');
    Route::get('/pengguna-pendidik', MasterPendidik::class)->name('admin.pengguna.pendidik');
});

Route::group(['prefix' => 'pendidik','middleware' => ['auth','pengajar-role']], function(){
    Route::get('/beranda',[PengajarController::class, 'index'])->name('pendidik.dinas.beranda');
    Route::get('/edit-profil',[PengajarController::class, 'edit'])->name('pendidik.dinas.edit');
    Route::post('/update-profil',[PengajarController::class, 'update'])->name('pendidik.dinas.updateprofil');
    Route::get('/paket',[PaketDinasController::class, 'pendidikPaket'])->name('pendidik.dinas.paket');
    Route::get('/tes/{id}',[TesDinasController::class, 'pendidikTes'])->name('pendidik.dinas.tes');
    Route::get('/tipesoal/{id}',[SoalDinasController::class, 'pendidikPilihTipe'])->name('pendidik.dinas.tipesoal');
    Route::get('/hapusganda/{id}',[SoalDinasController::class, 'pendidikHapusGanda'])->name('pendidik.dinas.hapusganda');
    Route::get('/hapusgandapoin/{id}',[SoalDinasController::class, 'pendidikHapusGandaPoin'])->name('pendidik.dinas.hapusgandapoin');
    Route::get('/hapusessay/{id}',[SoalDinasController::class, 'pendidikHapusEssay'])->name('pendidik.dinas.hapusessay');
    Route::get('/soalganda/{id}',[SoalDinasController::class, 'pendidikSoalGanda'])->name('pendidik.dinas.soalganda');
    Route::get('/cetaksoalganda/{id}', [SoalDinasController::class, 'pendidikCetakSoalGanda'])->name('pendidik.dinas.cetaksoalganda');
    Route::post('/upsoalganda/{id}',[SoalDinasController::class, 'pendidikUpSoalGanda'])->name('pendidik.dinas.upsoalganda');
    Route::get('/editsoalganda/{id}',[SoalDinasController::class, 'pendidikEditSoalGanda'])->name('pendidik.dinas.editsoalganda');
    Route::post('/updatesoalganda/{id}',[SoalDinasController::class, 'pendidikUpdateSoalGanda'])->name('pendidik.dinas.updatesoalganda');
    Route::get('/soalgandapoin/{id}',[SoalDinasController::class, 'pendidikSoalGandaPoin'])->name('pendidik.dinas.soalgandapoin');
    Route::get('/cetaksoalgandapoin/{id}', [SoalDinasController::class, 'pendidikCetakSoalGandaPoin'])->name('pendidik.dinas.cetaksoalgandapoin');
    Route::post('/upsoalgandapoin/{id}',[SoalDinasController::class, 'pendidikUpSoalGandaPoin'])->name('pendidik.dinas.upsoalgandapoin');
    Route::get('/editsoalgandapoin/{id}',[SoalDinasController::class, 'pendidikEditSoalGandaPoin'])->name('pendidik.dinas.editsoalgandapoin');
    Route::post('/updatesoalgandapoin/{id}',[SoalDinasController::class, 'pendidikUpdateSoalGandaPoin'])->name('pendidik.dinas.updatesoalgandapoin');
    Route::get('/soalessay/{id}',[SoalDinasController::class, 'pendidikSoalEssay'])->name('pendidik.dinas.soalessay');
    Route::post('/upsoalessay/{id}',[SoalDinasController::class, 'pendidikUpSoalEssay'])->name('pendidik.dinas.upsoalessay');
    Route::get('/editsoalessay/{id}',[SoalDinasController::class, 'pendidikEditSoalEssay'])->name('pendidik.dinas.editsoalessay');
    Route::post('/updatesoalessay/{id}',[SoalDinasController::class, 'pendidikUpdateSoalEssay'])->name('pendidik.dinas.updatesoalessay');
    Route::get('/hapussoalganda/{id}',[SoalDinasController::class, 'pendidikHapusSoalGanda'])->name('pendidik.dinas.hapussoalganda');
    Route::get('/hapussoalgandapoin/{id}',[SoalDinasController::class, 'pendidikHapusSoalGandaPoin'])->name('pendidik.dinas.hapussoalgandapoin');
    Route::get('/hapussoalessay/{id}',[SoalDinasController::class, 'pendidikHapusSoalEssay'])->name('pendidik.dinas.hapussoalessay');
    Route::get('/penilaian/{id}', [HasilDinasController::class, 'hasilPendidik'])->name('pendidik.dinas.penilaian');
    Route::get('/cetak_hasil/{id}', [HasilDinasController::class, 'cetakPdfHasil'])->name('pendidik.dinas.cetak_hasil');
    Route::post('/arsipkan/{id}',[HasilDinasController::class, 'arsipkan'])->name('pendidik.dinas.arsipkan');
    Route::get('/analisis',[ArsipController::class, 'analisis'])->name('pendidik.dinas.analisis');
    Route::get('/hasil',[ArsipController::class, 'hasil'])->name('pendidik.dinas.hasil');
    Route::get('/cetakhasil',[ArsipController::class, 'cetakHasil'])->name('pendidik.dinas.cetakhasil');
    Route::get('/analisispelajar',[ArsipController::class, 'pelajar'])->name('pendidik.dinas.analisispelajar');
    Route::get('/jawabanpelajar',[ArsipController::class, 'jawabanPelajar'])->name('pendidik.dinas.jawabanpelajar');
    Route::get('/analisissoal',[ArsipController::class, 'soal'])->name('pendidik.dinas.analisissoal');
    Route::post('/importganda',[SoalDinasController::class, 'pendidikImportSoalGanda'])->name('pendidik.dinas.importganda');
    Route::post('/importgandapoin',[SoalDinasController::class, 'pendidikImportSoalGandaPoin'])->name('pendidik.dinas.importgandapoin');
    Route::get('/cetakjawaban',[ArsipController::class, 'cetakJawaban'])->name('pendidik.dinas.cetakjawaban');
    Route::get('/cetakjawabanpoin',[ArsipController::class, 'cetakJawabanPoin'])->name('pendidik.dinas.cetakjawabanpoin');

    // Absensi
    Route::get('/absensi',[JadwalAbsensiController::class, 'scanAbsensiPendidik'])->name('pendidik.absensi');
    Route::get('/absensi/jurnal/{id}',[JadwalAbsensiController::class, 'jurnalPendidik'])->name('pendidik.absensi.jurnal');
    Route::post('/absensi/up-jurnal/{id}',[JadwalAbsensiController::class, 'upJurnalPendidik'])->name('pendidik.absensi.up-jurnal');
    Route::post('/absensi/selesai/{id}',[JadwalAbsensiController::class, 'selesaiPendidik'])->name('pendidik.absensi.selesai');
    Route::get('/absensi/histori-mengajar',[JadwalAbsensiController::class, 'historiMengajar'])->name('pendidik.absensi.histori-mengajar');
    // Absensi Jasmani
    Route::get('/jadwal_jasmani',[JadwalAbsensiController::class, 'jadwalAbsensiJasmani'])->name('pendidik.absensi.jadwal_jasmani');
    Route::get('/jadwal_jasmani/absensi/{id}',[JadwalAbsensiController::class, 'absensiJasmani'])->name('pendidik.absensi.jadwal_jasmani.absensi');
    Route::post('/absensi_jasmani/upload_absensi_jasmani/pelajar',[JadwalAbsensiController::class, 'uploadAbsensiPelajarJasmani'])->name('pendidik.absensi.upload_absensi_jasmani.pelajar');
    Route::post('/absensi_jasmani/upload_absensi_jasmani',[JadwalAbsensiController::class, 'uploadAbsensiPendidikJasmani'])->name('pendidik.absensi.upload_absensi_jasmani');
    Route::get('/absensi_jasmani/hapus/izin-pelajar/{id}',[JadwalAbsensiController::class, 'hapusIzinPelajar'])->name('pendidik.absensi.hapus-izin-pelajar');
    Route::post('/absensi_jasmani/upload-absensi/izin-pelajar',[JadwalAbsensiController::class, 'uploadAbsensiIzinPelajar'])->name('pendidik.absensi.upload-izin-pelajar');
    Route::post('/absensi_jasmani/upload-absensi/izin-pendidik',[JadwalAbsensiController::class, 'uploadAbsensiIzinPendidik'])->name('pendidik.absensi.upload-izin-pendidik');
    Route::get('/absensi_jasmani/hapus/izin-pendidik/{id}',[JadwalAbsensiController::class, 'hapusIzinPendidik'])->name('pendidik.absensi.hapus-izin-pendidik');

});



Route::group(['prefix' => 'pelajar','middleware' => ['auth','pelajar-role']], function(){

    //Kedinasan
    Route::get('/beranda',[PelajarController::class, 'index'])->name('pelajar.dinas.beranda');
    Route::get('/paket',[PaketDinasController::class, 'pelajarPaket'])->name('pelajar.dinas.paket');
    Route::get('/tes/{id}',[TesDinasController::class, 'pelajarTes'])->name('pelajar.dinas.tes');
    Route::get('/persiapan/{id}',[SoalDinasController::class, 'pelajarPersiapan'])->name('pelajar.dinas.persiapan');
    Route::get('/soalganda/{id}',[SoalDinasController::class, 'pelajarSoalGanda'])->name('pelajar.dinas.soalganda');
    Route::post('/upjawabanganda/{id}',[JawabanDinasController::class, 'upJawabanGanda'])->name('pelajar.dinas.upjawabanganda');
    // Route::get('/review/{id}',[JawabanDinasController::class, 'review'])->name('pelajar.dinas.review');
    Route::get('/kumpulkan/{id}',[JawabanDinasController::class, 'kumpulkan'])->name('pelajar.dinas.kumpulkan');
    Route::get('/nilai/{id}',[JawabanDinasController::class, 'nilai'])->name('pelajar.dinas.nilai');
    Route::get('/soalgandapoin/{id}',[SoalDinasController::class, 'pelajarSoalGandaPoin'])->name('pelajar.dinas.soalgandapoin');
    Route::post('/upjawabangandapoin/{id}',[JawabanDinasController::class, 'upJawabanGandaPoin'])->name('pelajar.dinas.upjawabangandapoin');
    // Route::get('/reviewgandapoin/{id}',[JawabanDinasController::class, 'reviewGandaPoin'])->name('pelajar.dinas.reviewgandapoin');
    // Route::post('/kumpulkangandapoin/{id}',[JawabanDinasController::class, 'kumpulkanGandaPoin'])->name('pelajar.dinas.kumpulkangandapoin');

    Route::get('/absensi',[JadwalAbsensiController::class, 'scanAbsensiPelajar'])->name('pelajar.absensi');
    Route::get('/absensi/histori-pembelajaran',[JadwalAbsensiController::class, 'historiPelajar'])->name('pelajar.absensi.histori-pembelajaran');
    Route::get('/masukkan_token',[TesDinasController::class, 'masukToken'])->name('pelajar.masukkan_token');
    Route::post('/submit_token',[TesDinasController::class, 'submitToken'])->name('pelajar.submit_token');
    Route::get('/capaian_tes', [HasilDinasController::class, 'capaian'])->name('pelajar.capaian');
});

Route::group(['prefix' => 'staf-admin', 'middleware' => ['auth','admin-role']], function(){
    Route::get('/beranda',[StafAdminController::class, 'index'])->name('staf-admin.beranda');
    Route::post('/update-profil/{id}',[StafAdminController::class, 'update'])->name('staf-admin.update-profil');
    Route::get('/jadwal',[JadwalAbsensiController::class, 'index'])->name('staf-admin.jadwal');
    Route::post('/jadwal/tambah',[JadwalAbsensiController::class, 'tambahJadwal'])->name('staf-admin.jadwal.tambah');
    Route::get('/jadwal/hapus/{id}',[JadwalAbsensiController::class, 'hapusJadwal'])->name('staf-admin.jadwal.hapus');
    Route::get('/jadwal/edit/{id}',[JadwalAbsensiController::class, 'editJadwal'])->name('staf-admin.jadwal.edit');
    Route::post('/jadwal/update/{id}',[JadwalAbsensiController::class, 'updateJadwal'])->name('staf-admin.jadwal.update');
    Route::get('/absensi/beranda',[JadwalAbsensiController::class, 'berandaAbsensi'])->name('staf-admin.absensi.beranda');
    Route::get('/absensi/{id}',[JadwalAbsensiController::class, 'absensi'])->name('staf-admin.absen');
    Route::get('/absensi-pulang/{id}',[JadwalAbsensiController::class, 'absensiPulang'])->name('staf-admin.absen-pulang');
    Route::post('/absensi-pulang/selesai',[JadwalAbsensiController::class, 'selesaiPelajar'])->name('staf-admin.absen-pulang.selesai');
    Route::get('/absen/staf',[JadwalAbsensiController::class, 'absenStaf'])->name('staf-admin.absen.staf');
    Route::post('/absensi/upload-absensi',[JadwalAbsensiController::class, 'uploadAbsensi'])->name('staf-admin.absensi.upload-absensi');
    Route::post('/absensi/upload-absensi/staf',[JadwalAbsensiController::class, 'uploadAbsensiStaf'])->name('staf-admin.absensi.upload-absensi.staf');
    Route::post('/absensi/upload-absensi/izin-staf',[JadwalAbsensiController::class, 'uploadAbsensiIzinStaf'])->name('staf-admin.absensi.upload-izin-staf');
    Route::get('/absensi/hapus/izin-staf/{id}',[JadwalAbsensiController::class, 'hapusIzinStaf'])->name('staf-admin.absensi.hapus-izin-staf');
    Route::get('/absen/staf/pulang',[JadwalAbsensiController::class, 'absenPulangStaf'])->name('staf-admin.absen.staf-pulang');
    Route::post('/absen/staf/pulang_upload', [JadwalAbsensiController::class, 'uploadStafPulang'])->name('staf-admin.absen.upload-staf-pulang');

    Route::get('/absensi/hapus/izin-pelajar/{id}',[JadwalAbsensiController::class, 'hapusIzinPelajar'])->name('staf-admin.absensi.hapus-izin-pelajar');
    Route::post('/absensi/upload-absensi/izin-pelajar',[JadwalAbsensiController::class, 'uploadAbsensiIzinPelajar'])->name('staf-admin.absensi.upload-izin-pelajar');
    Route::post('/absensi/upload-absensi/izin-pendidik',[JadwalAbsensiController::class, 'uploadAbsensiIzinPendidik'])->name('staf-admin.absensi.upload-izin-pendidik');
    Route::get('/absensi/hapus/izin-pendidik/{id}',[JadwalAbsensiController::class, 'hapusIzinPendidik'])->name('staf-admin.absensi.hapus-izin-pendidik');
    Route::get('/absen/rekap-pembelajaran/',[JadwalAbsensiController::class, 'rekapAbsensiPembelajaran'])->name('staf-admin.absensi.rekap-pembelajaran');
    Route::get('/absen/rekap-pembelajaran/lihat/{id}',[JadwalAbsensiController::class, 'lihatRekapAbsensiPembelajaran'])->name('staf-admin.absensi.rekap-pembelajaran.lihat');
    Route::get('/absen/rekap-pembelajaran/cetak/{id}',[JadwalAbsensiController::class, 'cetakJurnalHarian'])->name('staf-admin.absensi.rekap-pembelajaran.cetak');
    Route::get('/absen/rekap-staf/',[JadwalAbsensiController::class, 'rekapAbsensiStaf'])->name('staf-admin.absensi.rekap-staf');
    Route::get('/absen/rekap-staf/cetak-jurnal',[JadwalAbsensiController::class, 'cetakJurnalStaf'])->name('staf-admin.absensi.rekap-staf.cetak');

    Route::get('/pengguna-pendaftar',[PenggunaController::class, 'penggunaPendaftar'])->name('staf-admin.penggunapendaftar');
    Route::get('/pengguna-pendaftar/lihat/{id}',[PenggunaController::class, 'lihatPendaftar'])->name('staf-admin.penggunapendaftar.lihat');
    Route::post('/pengguna-pendaftar/migrasi/{id}',[PenggunaController::class, 'migrasiPendaftar'])->name('staf-admin.penggunapendaftar.migrasi');
    Route::get('/pengguna-pendaftar/hapus/{id}',[PenggunaController::class, 'hapusPendaftar'])->name('staf-admin.penggunapendaftar.hapus');

    Route::get('/pengguna-pelajar',[PenggunaController::class, 'penggunaPelajar'])->name('staf-admin.penggunapelajar');
    Route::get('/pengguna-pelajar/cetak',[PenggunaController::class, 'cetakPenggunaPelajar'])->name('staf-admin.penggunapelajar.cetak');
    Route::get('/pengguna-pelajar/lihat/{id}',[PenggunaController::class, 'lihatPelajar'])->name('staf-admin.penggunapelajar.lihat');
    Route::get('/pengguna-pelajar/edit/{id}',[PenggunaController::class, 'editPelajar'])->name('staf-admin.penggunapelajar.edit');
    Route::post('/pengguna-pelajar/update/{id}',[PenggunaController::class, 'updatePelajar'])->name('staf-admin.penggunapelajar.update');
    Route::get('/pengguna-pelajar/cetak-pdf/{id}',[PenggunaController::class, 'cetakPdfPelajar'])->name('staf-admin.penggunapelajar.cetak-pdf');
    Route::get('/pengguna-pelajar/editdata/{id}',[PenggunaController::class, 'editDataPelajar'])->name('staf-admin.penggunapelajar.editdata');
    Route::post('/pengguna-pelajar/updatedata/{id}',[PenggunaController::class, 'updateDataPelajar'])->name('staf-admin.penggunapelajar.updatedata');
});
