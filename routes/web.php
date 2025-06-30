<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtentikasiController;
use App\Http\Controllers\MasterData\MasterDataModuleController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\MasterData\PerusahaanController;
use App\Http\Controllers\MasterData\FasilitasController;
use App\Http\Controllers\MasterData\JenisAlatController;
use App\Http\Controllers\MasterData\LokasiTk1Controller;
use App\Http\Controllers\MasterData\LokasiTk2Controller;
use App\Http\Controllers\MasterData\LokasiTk3Controller;
use App\Http\Controllers\Fasilitas\FasilitasModuleController;
use App\Http\Controllers\Fasilitas\PeralatanController;
use App\Http\Controllers\Logbook\LogbookModuleController;
use App\Http\Controllers\Logbook\LaporanController;
use App\Http\Controllers\Fasilitas\LayananController;
use App\Http\Controllers\LogAktivitas\LogAktivitasController;

/*
Route::get('/', function () {
    return view('welcome');
});*/

// Mengakses index
Route::get('/', [HomeController::class, 'index']);

// Menampilkan halaman module
Route::get('/module', [HomeController::class, 'module']);

// Menampilkan halaman login
Route::get('/login', [OtentikasiController::class, 'show']);

// Memproses login
Route::post('/login/process', [OtentikasiController::class, 'process']); 

// Memproses logout
Route::get('/logout', [OtentikasiController::class, 'logout']);


/** 
 * ------------------------------------------------------------------------------------
 *                             MODULE MASTER DATA
 * ------------------------------------------------------------------------------------
 */ 

// Menampilkan halaman utama module Master Data
Route::get('/master-data/home', [MasterDataModuleController::class, 'home']);

/* ============================== MENU USER ==================================== */
// Menampilkan daftar user
Route::get('/master-data/user/daftar', [UserController::class, 'daftar']);

// Menampilkan form tambah user
Route::get('/master-data/user/tambah', [UserController::class, 'formTambah']);

// Melakukan proses tambah user
Route::post('/master-data/user/tambah', [UserController::class, 'tambah']);

// Menampilkan form edit user
Route::get('/master-data/user/edit/{id}', [UserController::class, 'formEdit']);

// Melakukan proses edit user
Route::post('/master-data/user/edit', [UserController::class, 'edit']);

// Menampilkan form reset password user
Route::get('/master-data/user/password/reset/{id}', [UserController::class, 'formResetPassword']);

// Melakukan proses reset password user
Route::post('/master-data/user/password/reset', [UserController::class, 'resetPassword']);

// Menampilkan JSON data user
Route::post('/master-data/user/detail', [UserController::class, 'detail']);

/* =========================== END OF MENU USER ================================ */

/* ============================== MENU PERUSAHAAN ==================================== */
// Menampilkan daftar perusahaan
Route::get('/master-data/perusahaan/daftar', [PerusahaanController::class, 'daftar']);

// Menampilkan form tambah perusahaan
Route::get('/master-data/perusahaan/tambah', [PerusahaanController::class, 'formTambah']);

// Melakukan proses tambah perusahaan
Route::post('/master-data/perusahaan/tambah', [PerusahaanController::class, 'tambah']);

// Menampilkan form edit perusahaan
Route::get('/master-data/perusahaan/edit/{id}', [PerusahaanController::class, 'formEdit']);

// Melakukan proses edit perusahaan
Route::post('/master-data/perusahaan/edit', [PerusahaanController::class, 'edit']);

// Menampilkan JSON data perusahaan
Route::post('/master-data/perusahaan/detail', [PerusahaanController::class, 'detail']);

/* =========================== END OF MENU PERUSAHAAN ================================ */

/* ============================== MENU FASILITAS ==================================== */
// Menampilkan daftar fasilitas
Route::get('/master-data/fasilitas/daftar', [FasilitasController::class, 'daftar']);

// Menampilkan form tambah fasilitas
Route::get('/master-data/fasilitas/tambah', [FasilitasController::class, 'formTambah']);

// Melakukan proses tambah fasilitas
Route::post('/master-data/fasilitas/tambah', [FasilitasController::class, 'tambah']);

// Menampilkan form edit fasilitas
Route::get('/master-data/fasilitas/edit/{id}', [FasilitasController::class, 'formEdit']);

// Melakukan proses edit fasilitas
Route::post('/master-data/fasilitas/edit', [FasilitasController::class, 'edit']);

// Menampilkan JSON data fasilitas
Route::post('/master-data/fasilitas/detail', [FasilitasController::class, 'detail']);

/* =========================== END OF MENU FASILITAS ================================ */

/* ============================== MENU JENIS ALAT ==================================== */
// Menampilkan daftar jenis alat
Route::get('/master-data/jenis-alat/daftar', [JenisAlatController::class, 'daftar']);

// Menampilkan form tambah jenis alat
Route::get('/master-data/jenis-alat/tambah', [JenisAlatController::class, 'formTambah']);

// Melakukan proses tambah jenis alat
Route::post('/master-data/jenis-alat/tambah', [JenisAlatController::class, 'tambah']);

// Menampilkan form edit jenis alat
Route::get('/master-data/jenis-alat/edit/{id}', [JenisAlatController::class, 'formEdit']);

// Melakukan proses edit jenis alat
Route::post('/master-data/jenis-alat/edit', [JenisAlatController::class, 'edit']);

// Menampilkan JSON data jenis alat
Route::post('/master-data/jenis-alat/detail', [JenisAlatController::class, 'detail']);

/* =========================== END OF MENU JENIS ALAT ================================ */

/* ============================== MENU LOKASI TINGKAT I ==================================== */
// Menampilkan daftar lokasi tingkat I
Route::get('/master-data/lokasi-tk-1/daftar', [LokasiTk1Controller::class, 'daftar']);

// Menampilkan form tambah lokasi tingkat I
Route::get('/master-data/lokasi-tk-1/tambah', [LokasiTk1Controller::class, 'formTambah']);

// Melakukan proses tambah lokasi tingkat I
Route::post('/master-data/lokasi-tk-1/tambah', [LokasiTk1Controller::class, 'tambah']);

// Menampilkan form edit lokasi tingkat I
Route::get('/master-data/lokasi-tk-1/edit/{id}', [LokasiTk1Controller::class, 'formEdit']);

// Melakukan proses edit lokasi tingkat I
Route::post('/master-data/lokasi-tk-1/edit', [LokasiTk1Controller::class, 'edit']);

// Menampilkan JSON data lokasi tingkat I
Route::post('/master-data/lokasi-tk-1/detail', [LokasiTk1Controller::class, 'detail']);

/* =========================== END OF MENU LOKASI TINGKAT I ================================ */

/* ============================== MENU LOKASI TINGKAT II ==================================== */
// Menampilkan daftar lokasi tingkat II
Route::get('/master-data/lokasi-tk-2/daftar', [LokasiTk2Controller::class, 'daftar']);

// Menampilkan form tambah lokasi tingkat II
Route::get('/master-data/lokasi-tk-2/tambah', [LokasiTk2Controller::class, 'formTambah']);

// Melakukan proses tambah lokasi tingkat II
Route::post('/master-data/lokasi-tk-2/tambah', [LokasiTk2Controller::class, 'tambah']);

// Menampilkan form edit lokasi tingkat II
Route::get('/master-data/lokasi-tk-2/edit/{id}', [LokasiTk2Controller::class, 'formEdit']);

// Melakukan proses edit lokasi tingkat II
Route::post('/master-data/lokasi-tk-2/edit', [LokasiTk2Controller::class, 'edit']);

// Menampilkan JSON data lokasi tingkat II
Route::post('/master-data/lokasi-tk-2/detail', [LokasiTk2Controller::class, 'detail']);

// Menampilkan JSON data lokasi tingkat II berdasarkan lokasi tingkat I
Route::post('/json/lokasi-tk-2/daftar', [LokasiTk2Controller::class, 'daftarJson']);

/* =========================== END OF MENU LOKASI TINGKAT II ================================ */

/* ============================== MENU LOKASI TINGKAT III ==================================== */
// Menampilkan daftar lokasi tingkat III
Route::get('/master-data/lokasi-tk-3/daftar', [LokasiTk3Controller::class, 'daftar']);

// Menampilkan form tambah lokasi tingkat IIi
Route::get('/master-data/lokasi-tk-3/tambah', [LokasiTk3Controller::class, 'formTambah']);

// Melakukan proses tambah lokasi tingkat II
Route::post('/master-data/lokasi-tk-3/tambah', [LokasiTk3Controller::class, 'tambah']);

// Menampilkan form edit lokasi tingkat III
Route::get('/master-data/lokasi-tk-3/edit/{id}', [LokasiTk3Controller::class, 'formEdit']);

// Melakukan proses edit lokasi tingkat III
Route::post('/master-data/lokasi-tk-3/edit', [LokasiTk3Controller::class, 'edit']);

// Menampilkan JSON data lokasi tingkat III
Route::post('/master-data/lokasi-tk-3/detail', [LokasiTk3Controller::class, 'detail']);

// Menampilkan JSON data lokasi tingkat III berdasarkan lokasi tingkat II
Route::post('/json/lokasi-tk-3/daftar', [LokasiTk3Controller::class, 'daftarJson']);

/* =========================== END OF MENU LOKASI TINGKAT II ================================ */

/** 
 * ------------------------------------------------------------------------------------
 *                             END OF MODULE MASTER DATA
 * ------------------------------------------------------------------------------------
 */ 

 /** 
 * ------------------------------------------------------------------------------------
 *                             MODULE FASILITAS
 * ------------------------------------------------------------------------------------
 */ 

// Menampilkan halaman utama module Fasilitas
Route::get('/fasilitas/home', [FasilitasModuleController::class, 'home']);

/* ============================== MENU PERALATAN ==================================== */
// Menampilkan daftar peralatan
Route::get('/fasilitas/peralatan/daftar', [PeralatanController::class, 'daftar']);

// Menampilkan form tambah peralatan
Route::get('/fasilitas/peralatan/tambah', [PeralatanController::class, 'formTambah']);

// Melakukan proses tambah peralatan
Route::post('/fasilitas/peralatan/tambah', [PeralatanController::class, 'tambah']);

// Menampilkan form edit peralatan
Route::get('/fasilitas/peralatan/edit/{id}', [PeralatanController::class, 'formEdit']);

// Melakukan proses edit peralatan
Route::post('/fasilitas/peralatan/edit', [PeralatanController::class, 'edit']);

// Menampilkan JSON data peralatan
Route::post('/fasilitas/peralatan/detail', [PeralatanController::class, 'detail']);

/* =========================== END OF MENU PERALATAN ================================ */

/* ============================== MENU LAYANAN ==================================== */
Route::prefix('/fasilitas/layanan')->name('fasilitas.layanan.')->group(function () {
    // Menampilkan daftar layanan
    Route::get('/daftar', [LayananController::class, 'daftar'])->name('daftar');

    // Menampilkan form tambah layanan step 1
    Route::get('/tambah/step1', [LayananController::class, 'formTambahStep1'])->name('tambah.step1.form');

    // Menampilkan form tambah layanan step 1 (tombol back)
    Route::get('/tambah/step1/back/{id}', [LayananController::class, 'formTambahStep1Back'])->name('tambah.step1.back.form');

    // Melakukan proses tambah layanan step 1
    Route::post('/tambah/step1', [LayananController::class, 'tambahStep1'])->name('tambah.step1.form');

    // Melakukan proses tambah layanan step 1 (tombol back)
    Route::post('/tambah/step1/back', [LayananController::class, 'tambahStep1Back'])->name('tambah.step1.back');

    // Menampilkan form tambah layanan step 2
    Route::get('/tambah/step2/{id}', [LayananController::class, 'formTambahStep2'])->name('tambah.step2.form');

    // Melakukan proses tambah layanan step 2
    Route::post('/tambah/step2', [LayananController::class, 'tambahStep2'])->name('tambah.step2');

    // Menampilkan form tambah layanan step 3
    Route::get('/tambah/step3/{id}', [LayananController::class, 'formTambahStep3'])->name('tambah.step3.form');

    // Melakukan proses tambah layanan step 3
    Route::post('/tambah/step3', [LayananController::class, 'tambahStep3'])->name('tambah.step3');

    // Melakukan menghapus draft layanan
    Route::post('/hapus', [LayananController::class, 'hapus'])->name('hapus');

    // Menampilkan form edit layanan step 1
    Route::get('/edit/step1/{id}', [LayananController::class, 'formEditStep1'])->name('edit.step1.form');

    // Melakukan proses edit layanan step 1
    Route::post('/edit/step1', [LayananController::class, 'editStep1'])->name('edit.step1');

    // Menampilkan form edit layanan step 2
    Route::get('/edit/step2/{id}', [LayananController::class, 'formEditStep2'])->name('edit.step2.form');

    // Menampilkan form edit layanan step 3
    Route::get('/edit/step3/{id}', [LayananController::class, 'formEditStep3'])->name('edit.step3.form');

    // Melakukan proses edit layanan step 3
    Route::post('/edit/step3', [LayananController::class, 'editStep3'])->name('edit.step3');

    // Melakukan proses filter layanan
    Route::post('/filter', [LayananController::class, 'filter'])->name('filter');

    // Menampilkan JSON data layanan
    Route::post('/detail', [LayananController::class, 'detail'])->name('detail');

    // Menampilkan daftar peralatan tersedia berdasarkan filter
    Route::post('/peralatan/filter', [LayananController::class, 'peralatanFilter'])->name('peralatan.filter');

    // Melakukan proses tambah peralatan ke layanan
    Route::post('/peralatan/tambah', [LayananController::class, 'tambahPeralatan'])->name('peralatan.tambah');

    // Menampilkan halaman edit ip address peralatan
    Route::post('/peralatan/edit', [LayananController::class, 'editPeralatan'])->name('peralatan.edit');

    // Menghapus peralatan dari layanan
    Route::post('/peralatan/hapus', [LayananController::class, 'hapusPeralatan'])->name('peralatan.hapus');

    // Menampilkan JSON data layanan
    Route::post('/peralatan/detail', [LayananController::class, 'detailPeralatan'])->name('peralatan.detail');
});
/* =========================== END OF MENU LAYANAN ================================ */

/** 
 * ------------------------------------------------------------------------------------
 *                             END OF MODULE FASILITAS
 * ------------------------------------------------------------------------------------
 */ 


/** 
 * ------------------------------------------------------------------------------------
 *                             MODULE LOGBOOK
 * ------------------------------------------------------------------------------------
 */ 

// Menampilkan halaman utama module Logbook
Route::get('/logbook/home', [LogbookModuleController::class, 'home']);

/* ============================== MENU LAPORAN ==================================== */

// Menampilkan daftar laporan
Route::get('/logbook/laporan/daftar', [LaporanController::class, 'daftar'])->name('logbook.laporan.daftar');

// Step 1 -  Melakukan proses filter
Route::get('/logbook/laporan/tambah/step1', [LaporanController::class, 'formStep1'])->name('tambah.step1');

// Step 1 -  menampilkan filter
Route::post('/logbook/laporan/filter', [LaporanController::class, 'filter'])->name('logbook.laporan.filter');

// Step 2 - Pilih Jenis Laporan dan Input Gangguan
Route::get('/logbook/laporan/tambah/step2', [LaporanController::class, 'formStep2'])->name('tambah.step2');

// Step 2 - Menyimpan Jenis Laporan dan Input Gangguan
Route::post('/logbook/laporan/tambah/step2/simpan', [LaporanController::class, 'simpanStep2'])->name('tambah.step2.simpan');

// Step 3 - Menampilkan Form Tindaklanjut
Route::get('/logbook/laporan/tambah/step3/{laporan_id}', [LaporanController::class, 'formStep3'])->name('tambah.step3');

// Step 3 - Menyimpan Tindaklanjut
Route::post('/logbook/laporan/tambah/step3/simpan', [LaporanController::class, 'simpanStep3'])->name('tambah.simpanStep3');

// Step 4 - Menampilkan Form Step 4
Route::get('/logbook/laporan/tambah/step4/{laporan_id}', [LaporanController::class, 'step4'])->name('tambah.step4');

// Step 4 - Simpan Data Step 4 (pakai parameter laporan_id)
Route::post('/logbook/laporan/tambah/step4/{laporan_id}/simpan', [LaporanController::class, 'simpanStep4'])->name('tambah.simpanStep4');

// Step 4 - filter peralatan
Route::post('/logbook/laporan/peralatan/filter', [LaporanController::class, 'filterPeralatanPengganti'])->name('laporan.filterPeralatan');

// Step 5 Menampilkan Review
Route::get('/logbook/laporan/tambah/step5/{laporan_id}', [LaporanController::class, 'step5'])->name('tambah.step5');

// Step 5 Simpan Laporan
Route::post('/logbook/laporan/tambah/step5/simpan', [LaporanController::class, 'simpanStep5'])->name('tambah.simpanStep5');


/* ============================== MENU LAPORAN ==================================== */
Route::get('/log_aktivitas/daftar', [LogAktivitasController::class, 'daftar'])->name('log-aktivitas.daftar');



/** 
 * ------------------------------------------------------------------------------------
 *                            END OF MODULE LOGBOOK
 * ------------------------------------------------------------------------------------
 */ 