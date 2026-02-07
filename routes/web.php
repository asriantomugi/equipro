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
use App\Http\Controllers\Fasilitas\ExportLayananController;
use App\Http\Controllers\Logbook\RiwayatController;
use App\Http\Controllers\Logbook\ExportController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\LogAktivitas\LogAktivitasController;
use App\Http\Controllers\Dashboard\DashboardLaporanController;
use App\Http\Controllers\Dashboard\DashboardFasilitasController;

/*
Route::get('/', function () {
    return view('welcome');
});*/


// Menampilkan halaman login
Route::get('/login', [OtentikasiController::class, 'show']);

// Memproses login
Route::post('/login/process', [OtentikasiController::class, 'process']); 

// Memproses logout
Route::get('/logout', [OtentikasiController::class, 'logout']);

/**
 * ------------------------------------------------------------------------------------
 *       Group route untuk Controller yang bisa diakses oleh semua user
 * ------------------------------------------------------------------------------------
 */
Route::middleware(['role:super_admin,admin,teknisi'])->group(function () {

    // Mengakses halaman index (menampilkan module)
    /**
     * Menampilkan halaman index (module)
     * Method: GET
     * Name: index
     * URL: /
     */
    Route::get('/', [HomeController::class, 'index'])->name('index');

    /**
     * Menampilkan JSON data lokasi tingkat II berdasarkan lokasi tingkat I
     * Method: POST
     * Name: json.lokasi_tk_2.daftar
     * URL: /json/lokasi-tk-2/daftar
     */
    Route::post('/json/lokasi-tk-2/daftar', [LokasiTk2Controller::class, 'daftarJson'])->name('json.lokasi_tk_2.daftar');

    /**
     * Menampilkan JSON data lokasi tingkat III berdasarkan lokasi tingkat II
     * Method: POST
     * Name: json.lokasi_tk_3.daftar
     * URL: /json/lokasi-tk-3/daftar
     */
    Route::post('/json/lokasi-tk-3/daftar', [LokasiTk3Controller::class, 'daftarJson'])->name('json.lokasi_tk_3.daftar');
    
});


/** 
 * ------------------------------------------------------------------------------------
 *                                  MODULE MASTER DATA
 *                              (HANYA SUPER ADMIN & ADMIN)
 * ------------------------------------------------------------------------------------
 */ 

/**
 * Group route untuk module master data
 * URL: /master-data/... 
 */ 
Route::prefix('/master-data')->name('master_data.')
    ->middleware(['role:super_admin,admin']) // Akses oleh Super Admin dan Admin
    ->group(function () {

    /**
     * Menampilkan halaman utama module Master Data
     * Method: GET
     * Name: master_data.home
     * URL: /master-data/home 
     */ 
    Route::get('/home', [MasterDataModuleController::class, 'home'])->name('home');

    /* ==================================== MENU USER ==================================== */
    /**
     * Menampilkan daftar user
     * Method: GET
     * Name: master_data.user.daftar
     * URL: /master-data/user/daftar 
     */
    Route::get('/user/daftar', [UserController::class, 'daftar'])->name('user.daftar');

    /**
     * Menampilkan form tambah user
     * Method: GET
     * Name: master_data.user.tambah.form
     * URL: /master-data/user/tambah 
     */
    Route::get('/user/tambah', [UserController::class, 'formTambah'])->name('user.tambah.form');

    // Melakukan proses tambah user
    /**
     * Menampilkan daftar user
     * Method: POST
     * Name: master_data.user.tambah
     * URL: /master-data/user/tambah 
     */
    Route::post('/user/tambah', [UserController::class, 'tambah'])->name('user.tambah');

    /**
     * Menampilkan form edit user
     * Method: GET
     * Name: master_data.user.edit.form
     * URL: /master-data/user/edit/{id} 
     */
    Route::get('/user/edit/{id}', [UserController::class, 'formEdit'])->name('user.edit.form');

    /**
     * Melakukan proses edit user
     * Method: POST
     * Name: master_data.user.edit
     * URL: /master-data/user/edit 
     */
    Route::post('/user/edit', [UserController::class, 'edit'])->name('user.edit');

    /**
     * Menampilkan form reset password user
     * Method: GET
     * Name: master_data.user.password.form
     * URL: /master-data/user/password/reset/{id} 
     */
    Route::get('/user/password/reset/{id}', [UserController::class, 'formResetPassword'])->name('user.password.form');

    /**
     * Melakukan proses reset password user
     * Method: POST
     * Name: master_data.user.password
     * URL: /master-data/user/password/reset 
     */
    Route::post('/user/password/reset', [UserController::class, 'resetPassword'])->name('user.password');

    /**
     * Menampilkan JSON data user
     * Method: POST
     * Name: master_data.user.detail
     * URL: /master-data/user/detail
     */
    Route::post('/user/detail', [UserController::class, 'detail'])->name('user.detail');

    /* ================================= END OF MENU USER ================================ */

    /* ================================= MENU PERUSAHAAN ================================= */
    /**
     * Menampilkan daftar perusahaan
     * Method: GET
     * Name: master_data.perusahaan.daftar
     * URL: /master-data/perusahaan/daftar
     */
    Route::get('/perusahaan/daftar', [PerusahaanController::class, 'daftar'])->name('perusahaan.daftar');

    /**
     * Menampilkan form tambah perusahaan
     * Method: GET
     * Name: master_data.perusahaan.tambah.form
     * URL: /master-data/perusahaan/tambah
     */
    Route::get('/perusahaan/tambah', [PerusahaanController::class, 'formTambah'])->name('perusahaan.tambah.form');

    /**
     * Melakukan proses tambah perusahaan
     * Method: POST
     * Name: master_data.perusahaan.tambah
     * URL: /master-data/perusahaan/tambah
     */
    Route::post('/perusahaan/tambah', [PerusahaanController::class, 'tambah'])->name('perusahaan.tambah');

    /**
     * Menampilkan form edit perusahaan
     * Method: GET
     * Name: master_data.perusahaan.edit.form
     * URL: /master-data/perusahaan/edit/{id}
     */
    Route::get('/perusahaan/edit/{id}', [PerusahaanController::class, 'formEdit'])->name('perusahaan.edit.form');

    /**
     * Melakukan proses edit perusahaan
     * Method: POST
     * Name: master_data.perusahaan.edit
     * URL: /master-data/perusahaan/edit
     */
    Route::post('/perusahaan/edit', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');

    /**
     * Menampilkan JSON data perusahaan
     * Method: POST
     * Name: master_data.perusahaan.detail
     * URL: /master-data/perusahaan/detail
     */
    Route::post('/perusahaan/detail', [PerusahaanController::class, 'detail'])->name('perusahaan.detail');

    /* =========================== END OF MENU PERUSAHAAN ================================ */

    /* =============================== MENU FASILITAS ==================================== */
    /**
     * Menampilkan daftar fasilitas
     * Method: GET
     * Name: master_data.fasilitas.daftar
     * URL: /master-data/fasilitas/daftar
     */
    Route::get('/fasilitas/daftar', [FasilitasController::class, 'daftar'])->name('fasilitas.daftar');

    /**
     * Menampilkan form tambah fasilitas
     * Method: GET
     * Name: master_data.fasilitas.tambah.form
     * URL: /master-data/fasilitas/tambah
     */
    Route::get('/fasilitas/tambah', [FasilitasController::class, 'formTambah'])->name('fasilitas.tambah.form');

    /**
     * Melakukan proses tambah fasilitas
     * Method: POST
     * Name: master_data.fasilitas.tambah
     * URL: /master-data/fasilitas/tambah
     */
    Route::post('/fasilitas/tambah', [FasilitasController::class, 'tambah'])->name('fasilitas.tambah');

    /**
     * Menampilkan form edit fasilitas
     * Method: GET
     * Name: master_data.fasilitas.edit.form
     * URL: /master-data/fasilitas/edit/{id}
     */
    Route::get('/fasilitas/edit/{id}', [FasilitasController::class, 'formEdit'])->name('fasilitas.edit.form');

    /**
     * Melakukan proses edit fasilitas
     * Method: POST
     * Name: master_data.fasilitas.edit
     * URL: /master-data/fasilitas/edit
     */
    Route::post('/fasilitas/edit', [FasilitasController::class, 'edit'])->name('fasilitas.edit');

    /**
     * Menampilkan JSON data fasilitas
     * Method: POST
     * Name: master_data.fasilitas.detail
     * URL: /master-data/fasilitas/detail
     */
    Route::post('/fasilitas/detail', [FasilitasController::class, 'detail'])->name('fasilitas.detail');

    /* =========================== END OF MENU FASILITAS ================================ */

    /* ============================== MENU JENIS ALAT =================================== */
    /**
     * Menampilkan daftar jenis alat
     * Method: GET
     * Name: master_data.jenis_alat.daftar
     * URL: /master-data/jenis-alat/daftar
     */
    Route::get('/jenis-alat/daftar', [JenisAlatController::class, 'daftar'])->name('jenis_alat.daftar');

    /**
     * Menampilkan form tambah jenis alat
     * Method: GET
     * Name: master_data.jenis_alat.tambah.form
     * URL: /master-data/jenis-alat/tambah
     */
    Route::get('/jenis-alat/tambah', [JenisAlatController::class, 'formTambah'])->name('jenis_alat.tambah.form');

    /**
     * Melakukan proses tambah jenis alat
     * Method: POST
     * Name: master_data.jenis_alat.tambah
     * URL: /master-data/jenis-alat/tambah
     */
    Route::post('/jenis-alat/tambah', [JenisAlatController::class, 'tambah'])->name('jenis_alat.tambah');

    /**
     * Menampilkan form edit jenis alat
     * Method: GET
     * Name: master_data.jenis_alat.edit.form
     * URL: /master-data/jenis-alat/edit/{id}
     */
    Route::get('/jenis-alat/edit/{id}', [JenisAlatController::class, 'formEdit'])->name('jenis_alat.edit.form');

    /**
     * Melakukan proses edit jenis alat
     * Method: POST
     * Name: master_data.jenis_alat.edit
     * URL: /master-data/jenis-alat/edit
     */
    Route::post('/jenis-alat/edit', [JenisAlatController::class, 'edit'])->name('jenis_alat.edit');

    /**
     * Menampilkan JSON data jenis alat
     * Method: POST
     * Name: master_data.jenis_alat.detail
     * URL: /master-data/jenis-alat/detail
     */
    Route::post('/jenis-alat/detail', [JenisAlatController::class, 'detail'])->name('jenis_alat.detail');

    /* =========================== END OF MENU JENIS ALAT ================================ */

    /* ============================ MENU LOKASI TINGKAT I ================================ */
    /**
     * Menampilkan daftar lokasi tingkat I
     * Method: GET
     * Name: master_data.lokasi_tk_1.daftar
     * URL: /master-data/lokasi-tk-1/daftar
     */
    Route::get('/lokasi-tk-1/daftar', [LokasiTk1Controller::class, 'daftar'])->name('lokasi_tk_1.daftar');

    /**
     * Menampilkan form tambah lokasi tingkat I
     * Method: GET
     * Name: master_data.lokasi_tk_1.tambah.form
     * URL: /master-data/lokasi-tk-1/tambah
     */
    Route::get('/lokasi-tk-1/tambah', [LokasiTk1Controller::class, 'formTambah'])->name('lokasi_tk_1.tambah.form');

    /**
     * Melakukan proses tambah lokasi tingkat I
     * Method: POST
     * Name: master_data.lokasi_tk_1.tambah
     * URL: /master-data/lokasi-tk-1/tambah
     */
    Route::post('/lokasi-tk-1/tambah', [LokasiTk1Controller::class, 'tambah'])->name('lokasi_tk_1.tambah');

    /**
     * Menampilkan form edit lokasi tingkat I
     * Method: GET
     * Name: master_data.lokasi_tk_1.edit.form
     * URL: /master-data/lokasi-tk-1/edit/{id}
     */
    Route::get('/lokasi-tk-1/edit/{id}', [LokasiTk1Controller::class, 'formEdit'])->name('lokasi_tk_1.edit.form');

    /**
     * Melakukan proses edit lokasi tingkat I
     * Method: POST
     * Name: master_data.lokasi_tk_1.edit
     * URL: /master-data/lokasi-tk-1/edit
     */
    Route::post('/lokasi-tk-1/edit', [LokasiTk1Controller::class, 'edit'])->name('lokasi_tk_1.edit');

    /**
     * Menampilkan JSON data lokasi tingkat I
     * Method: POST
     * Name: master_data.lokasi_tk_1.detail
     * URL: /master-data/lokasi-tk-1/detail
     */
    Route::post('/lokasi-tk-1/detail', [LokasiTk1Controller::class, 'detail'])->name('lokasi_tk_1.detail');

    /* ========================= END OF MENU LOKASI TINGKAT I ============================= */

    /* ============================= MENU LOKASI TINGKAT II =============================== */
    /**
     * Menampilkan daftar lokasi tingkat II
     * Method: GET
     * Name: master_data.lokasi_tk_2.daftar
     * URL: /master-data/lokasi-tk-2/daftar
     */
    Route::get('/lokasi-tk-2/daftar', [LokasiTk2Controller::class, 'daftar'])->name('lokasi_tk_2.daftar');

    /**
     * Menampilkan form tambah lokasi tingkat II
     * Method: GET
     * Name: master_data.lokasi_tk_2.tambah.form
     * URL: /master-data/lokasi-tk-2/tambah
     */
    Route::get('/lokasi-tk-2/tambah', [LokasiTk2Controller::class, 'formTambah'])->name('lokasi_tk_2.tambah.form');

    /**
     * Melakukan proses tambah lokasi tingkat II
     * Method: POST
     * Name: master_data.lokasi_tk_2.tambah
     * URL: /master-data/lokasi-tk-2/tambah
     */
    Route::post('/lokasi-tk-2/tambah', [LokasiTk2Controller::class, 'tambah'])->name('lokasi_tk_2.tambah');

    /**
     * Menampilkan form edit lokasi tingkat II
     * Method: GET
     * Name: master_data.lokasi_tk_2.edit.form
     * URL: /master-data/lokasi-tk-2/edit/{id}
     */
    Route::get('/lokasi-tk-2/edit/{id}', [LokasiTk2Controller::class, 'formEdit'])->name('lokasi_tk_2.edit.form');

    /**
     * Melakukan proses edit lokasi tingkat II
     * Method: POST
     * Name: master_data.lokasi_tk_2.edit
     * URL: /master-data/lokasi-tk-2/edit
     */
    Route::post('/lokasi-tk-2/edit', [LokasiTk2Controller::class, 'edit'])->name('lokasi_tk_2.edit');

    /**
     * Menampilkan JSON data lokasi tingkat II
     * Method: POST
     * Name: master_data.lokasi_tk_2.detail
     * URL: /master-data/lokasi-tk-2/detail
     */
    Route::post('/lokasi-tk-2/detail', [LokasiTk2Controller::class, 'detail'])->name('lokasi_tk_2.detail');

    /* =========================== END OF MENU LOKASI TINGKAT II ========================= */

    /* ============================== MENU LOKASI TINGKAT III ============================ */
    /**
     * Menampilkan daftar lokasi tingkat III
     * Method: GET
     * Name: master_data.lokasi_tk_3.daftar
     * URL: /master-data/lokasi-tk-3/daftar
     */
    Route::get('/lokasi-tk-3/daftar', [LokasiTk3Controller::class, 'daftar'])->name('lokasi_tk_3.daftar');

    /**
     * Menampilkan form tambah lokasi tingkat III
     * Method: GET
     * Name: master_data.lokasi_tk_3.tambah.form
     * URL: /master-data/lokasi-tk-3/tambah
     */
    Route::get('/lokasi-tk-3/tambah', [LokasiTk3Controller::class, 'formTambah'])->name('lokasi_tk_3.tambah.form');

    /**
     * Melakukan proses tambah lokasi tingkat III
     * Method: POST
     * Name: master_data.lokasi_tk_3.tambah
     * URL: /master-data/lokasi-tk-3/tambah
     */
    Route::post('/lokasi-tk-3/tambah', [LokasiTk3Controller::class, 'tambah'])->name('lokasi_tk_3.tambah');

    /**
     * Menampilkan form edit lokasi tingkat III
     * Method: GET
     * Name: master_data.lokasi_tk_3.edit.form
     * URL: /master-data/lokasi-tk-3/edit/{id}
     */
    Route::get('/lokasi-tk-3/edit/{id}', [LokasiTk3Controller::class, 'formEdit'])->name('lokasi_tk_3.edit.form');

    /**
     * Melakukan proses edit lokasi tingkat III
     * Method: POST
     * Name: master_data.lokasi_tk_3.edit
     * URL: /master-data/lokasi-tk-3/edit
     */
    Route::post('/lokasi-tk-3/edit', [LokasiTk3Controller::class, 'edit'])->name('lokasi_tk_3.edit');

    /**
     * Menampilkan JSON data lokasi tingkat III
     * Method: POST
     * Name: master_data.lokasi_tk_3.detail
     * URL: /master-data/lokasi-tk-3/detail
     */
    Route::post('/lokasi-tk-3/detail', [LokasiTk3Controller::class, 'detail'])->name('lokasi_tk_3.detail');

    /* ======================== END OF MENU LOKASI TINGKAT III ============================ */

});

/** 
 * ------------------------------------------------------------------------------------
 *                               END OF MODULE MASTER DATA
 * ------------------------------------------------------------------------------------
 */ 




/** 
 * ------------------------------------------------------------------------------------
 *                                  MODULE FASILITAS
 *                            (SUPER ADMIN, ADMIN, TEKNISI)
 * ------------------------------------------------------------------------------------
 */ 

/**
 * Group route untuk module fasilitas
 * URL: /fasilitas/... 
 */ 
Route::prefix('/fasilitas')->name('fasilitas.')
    ->middleware(['role:super_admin,admin']) // Akses oleh Super Admin dan Admin
    ->group(function () {

    /**
     * Menampilkan halaman utama module Fasilitas
     * Method: GET
     * Name: fasilitas.home
     * URL: /fasilitas/home 
     */ 
    Route::get('/home', [FasilitasModuleController::class, 'home'])->name('home');

    /* ==================================== MENU PERALATAN ==================================== */
    /**
     * Menampilkan daftar peralatan
     * Method: GET
     * Name: fasilitas.peralatan.daftar
     * URL: /fasilitas/peralatan/daftar 
     */
    Route::get('/peralatan/daftar', [PeralatanController::class, 'daftar'])->name('peralatan.daftar');

    /**
     * Menampilkan form tambah peralatan
     * Method: GET
     * Name: fasilitas.peralatan.tambah.form
     * URL: /fasilitas/peralatan/tambah
     */
    Route::get('/peralatan/tambah', [PeralatanController::class, 'formTambah'])->name('peralatan.tambah.form');

    /**
     * Melakukan proses tambah peralatan
     * Method: POST
     * Name: fasilitas.peralatan.tambah
     * URL: /fasilitas/peralatan/tambah
     */
    Route::post('/peralatan/tambah', [PeralatanController::class, 'tambah'])->name('peralatan.tambah');

    /**
     * Menampilkan form edit peralatan
     * Method: GET
     * Name: fasilitas.peralatan.edit.form
     * URL: /fasilitas/peralatan/edit/{id}
     */
    Route::get('/peralatan/edit/{id}', [PeralatanController::class, 'formEdit'])->name('peralatan.edit.form');

    /**
     * Melakukan proses edit peralatan
     * Method: POST
     * Name: fasilitas.peralatan.edit
     * URL: /fasilitas/peralatan/edit
     */
    Route::post('/peralatan/edit', [PeralatanController::class, 'edit'])->name('peralatan.edit');

    /**
     * Menampilkan JSON data peralatan
     * Method: POST
     * Name: fasilitas.peralatan.detail
     * URL: /fasilitas/peralatan/detail
     */
    Route::post('/peralatan/detail', [PeralatanController::class, 'detail'])->name('peralatan.detail');

    /* ================================ END OF MENU PERALATAN ================================ */

    /* ===================================== MENU LAYANAN ==================================== */

    /**
     * Menampilkan daftar layanan
     * Method: GET
     * Name: fasilitas.layanan.daftar
     * URL: /fasilitas/layanan/daftar
     */
    Route::get('/layanan/daftar', [LayananController::class, 'daftar'])->name('layanan.daftar');

    /**
     * Menampilkan form layanan step 1
     * Method: GET
     * Name: fasilitas.layanan.tambah.step1.form
     * URL: /fasilitas/layanan/tambah/step1
     */
    Route::get('/layanan/tambah/step1', [LayananController::class, 'formTambahStep1'])->name('layanan.tambah.step1.form');

    /**
     * Menampilkan form tambah layanan step 1 (tombol back)
     * Method: GET
     * Name: fasilitas.layanan.tambah.step1.back.form
     * URL: /fasilitas/layanan/tambah/step1.back/{id}
     */
    Route::get('/layanan/tambah/step1/back/{id}', [LayananController::class, 'formTambahStep1Back'])->name('layanan.tambah.step1.back.form');

    /**
     * Melakukan proses tambah layanan step 1
     * Method: POST
     * Name: fasilitas.layanan.tambah.step1
     * URL: /fasilitas/layanan/tambah/step1
     */
    Route::post('/layanan/tambah/step1', [LayananController::class, 'tambahStep1'])->name('layanan.tambah.step1');

    /**
     * Melakukan proses tambah layanan step 1 (tombol back)
     * Method: POST
     * Name: fasilitas.layanan.tambah.step1.back
     * URL: /fasilitas/layanan/tambah/step1/back
     */
    Route::post('/layanan/tambah/step1/back', [LayananController::class, 'tambahStep1Back'])->name('layanan.tambah.step1.back');

    /**
     * Menampilkan form tambah layanan step 2
     * Method: GET
     * Name: fasilitas.layanan.tambah.step2.form
     * URL: /fasilitas/layanan/tambah/step2/{id}
     */
    Route::get('/layanan/tambah/step2/{id}', [LayananController::class, 'formTambahStep2'])->name('layanan.tambah.step2.form');

    /**
     * Melakukan proses tambah layanan step 2
     * Method: POST
     * Name: fasilitas.layanan.tambah.step2
     * URL: /fasilitas/layanan/tambah/step2
     */
    Route::post('/layanan/tambah/step2', [LayananController::class, 'tambahStep2'])->name('fasilitas.layanan.tambah.step2');

    /**
     * Menampilkan form tambah layanan step 3
     * Method: GET
     * Name: fasilitas.layanan.tambah.step3.form
     * URL: /fasilitas/layanan/tambah/step3/{id}
     */
    Route::get('/layanan/tambah/step3/{id}', [LayananController::class, 'formTambahStep3'])->name('layanan.tambah.step3.form');

    /**
     * Melakukan proses tambah layanan step 3
     * Method: POST
     * Name: fasilitas.layanan.tambah.step3
     * URL: /fasilitas/layanan/tambah/step3
     */
    Route::post('/layanan/tambah/step3', [LayananController::class, 'tambahStep3'])->name('layanan.tambah.step3');

    /**
     * Melakukan menghapus draft layanan
     * Method: POST
     * Name: fasilitas.layanan.hapus
     * URL: /fasilitas/layanan/hapus
     */
    Route::post('/layanan/hapus', [LayananController::class, 'hapus'])->name('layanan.hapus');

    /**
     * Menampilkan form edit layanan step 1
     * Method: GET
     * Name: fasilitas.layanan.edit.step1.form
     * URL: /fasilitas/layanan/edit/step1/{id}
     */
    Route::get('/layanan/edit/step1/{id}', [LayananController::class, 'formEditStep1'])->name('layanan.edit.step1.form');

    /**
     * Melakukan proses edit layanan step 1
     * Method: POST
     * Name: fasilitas.layanan.edit.step1
     * URL: /fasilitas/layanan/edit/step1
     */
    Route::post('/layanan/edit/step1', [LayananController::class, 'editStep1'])->name('layanan.edit.step1');

    /**
     * Menampilkan form edit layanan step 2
     * Method: GET
     * Name: fasilitas.layanan.edit.step2.form
     * URL: /fasilitas/layanan/edit/step2/{id}
     */
    Route::get('/layanan/edit/step2/{id}', [LayananController::class, 'formEditStep2'])->name('layanan.edit.step2.form');

    /**
     * Menampilkan form edit layanan step 3
     * Method: GET
     * Name: fasilitas.layanan.edit.step3.form
     * URL: /fasilitas/layanan/edit/step3/{id}
     */
    Route::get('/layanan/edit/step3/{id}', [LayananController::class, 'formEditStep3'])->name('layanan.edit.step3.form');

    /**
     * Melakukan proses edit layanan step 3
     * Method: POST
     * Name: fasilitas.layanan.edit.step3
     * URL: /fasilitas/layanan/edit/step3
     */
    Route::post('/layanan/edit/step3', [LayananController::class, 'editStep3'])->name('layanan.edit.step3');

    /**
     * Melakukan proses filter layanan
     * Method: POST
     * Name: fasilitas.layanan.filter
     * URL: /fasilitas/layanan/filter
     */
    Route::post('/layanan/filter', [LayananController::class, 'filter'])->name('layanan.filter');

    /**
     * Menampilkan JSON data layanan
     * Method: POST
     * Name: fasilitas.layanan.detail
     * URL: /fasilitas/layanan/detail
     */
    Route::post('/layanan/detail', [LayananController::class, 'detail'])->name('layanan.detail');

    /**
     * Menampilkan daftar peralatan tersedia berdasarkan filter
     * Method: POST
     * Name: fasilitas.layanan.peralatan.filter
     * URL: /fasilitas/layanan/peralatan/filter
     */
    Route::post('/layanan/peralatan/filter', [LayananController::class, 'peralatanFilter'])->name('layanan.peralatan.filter');

    /**
     * Melakukan proses tambah peralatan ke layanan baru
     * Method: POST
     * Name: fasilitas.layanan.peralatan.tambah
     * URL: /fasilitas/layanan/peralatan/tambah
     */
    Route::post('/layanan/peralatan/tambah', [LayananController::class, 'tambahPeralatan'])->name('layanan.peralatan.tambah');

    /**
     * Melakukan proses tambah peralatan ke layanan lama (edit layanan)
     * Method: POST
     * Name: fasilitas.layanan.peralatan.edit.tambah
     * URL: /fasilitas/layanan/peralatan/edit/tambah
     */
    Route::post('/layanan/peralatan/edit/tambah', [LayananController::class, 'editTambahPeralatan'])->name('layanan.peralatan.edit.tambah');

    /**
     * Menampilkan halaman edit ip address peralatan
     * Method: POST
     * Name: fasilitas.layanan.peralatan.edit
     * URL: /fasilitas/layanan/peralatan/edit
     */
    Route::post('/layanan/peralatan/edit', [LayananController::class, 'editPeralatan'])->name('layanan.peralatan.edit');

    /**
     * Menghapus peralatan dari layanan
     * Method: POST
     * Name: fasilitas.layanan.peralatan.hapus
     * URL: /fasilitas/layanan/peralatan/hapus
     */
    Route::post('/layanan/peralatan/hapus', [LayananController::class, 'hapusPeralatan'])->name('layanan.peralatan.hapus');

    /**
     * Menampilkan JSON data peralatan layanan
     * Method: POST
     * Name: fasilitas.layanan.peralatan.detail
     * URL: /fasilitas/layanan/peralatan/detail
     */
    Route::post('/layanan/peralatan/detail', [LayananController::class, 'detailPeralatan'])->name('layanan.peralatan.detail');

    /* ================================= END OF MENU LAYANAN ================================= */

});

/* =========================== MENU EXPORT LAYANAN ================================ */
// Menampilkan halaman daftar export layanan
Route::get('/fasilitas/layanan/export/daftar', [ExportLayananController::class, 'daftar'])->name('fasilitas.layanan.export.daftar')->middleware(['role:super_admin,admin']);

// AJAX: Mengambil data layanan dengan filter
Route::get('/fasilitas/layanan/export/data', [ExportLayananController::class, 'getData'])->name('fasilitas.layanan.export.data')->middleware(['role:super_admin,admin']);

// Export layanan ke Excel
Route::post('/fasilitas/layanan/export', [ExportLayananController::class, 'export'])->name('fasilitas.layanan.export')->middleware(['role:super_admin,admin']);

// AJAX: Mendapatkan Lokasi Tk 2 berdasarkan Tk 1
Route::get('/fasilitas/layanan/export/lokasi-tk2', [ExportLayananController::class, 'getLokasiTk2ByTk1'])->name('fasilitas.layanan.lokasi-tk2')->middleware(['role:super_admin,admin']);

// AJAX: Mendapatkan Lokasi Tk 3 berdasarkan Tk 2
Route::get('/fasilitas/layanan/export/lokasi-tk3', [ExportLayananController::class, 'getLokasiTk3ByTk2'])->name('fasilitas.layanan.lokasi-tk3')->middleware(['role:super_admin,admin']);

/* ======================== END OF MENU EXPORT LAYANAN ============================= */

/** 
 * ------------------------------------------------------------------------------------
 *                             END OF MODULE FASILITAS
 * ------------------------------------------------------------------------------------
 */ 

/** 
 * ------------------------------------------------------------------------------------
 *                             MODULE LOGBOOK
 *                    (SUPER ADMIN, ADMIN, & TEKNISI)
 * ------------------------------------------------------------------------------------
 */ 

/**
 * Group route untuk module logbook
 * URL: /logbook/... 
 */ 
Route::prefix('/logbook')->name('logbook.')
    ->middleware(['role:super_admin,admin,teknisi']) // Akses oleh Super Admin, Admin, Teknisi
    ->group(function () {

    /**
     * Menampilkan halaman utama module Logbook
     * Method: GET
     * Name: logbook.home
     * URL: /logbook/home 
     */ 
    Route::get('/home', [LogbookModuleController::class, 'home'])->name('home');


    /* ============================== MENU LAPORAN ==================================== */
    
    /**
     * Menampilkan daftar laporan
     * Method: GET
     * Name: logbook.laporan.daftar
     * URL: /logbook/laporan/daftar
     */
    Route::get('/laporan/daftar', [LaporanController::class, 'daftar'])->name('laporan.daftar');

    /**
     * Menampilkan form tambah laporan step 1
     * Method: GET
     * Name: logbook.laporan.tambah.step1.form
     * URL: /logbook/laporan/tambah/step1
     */
    Route::get('/laporan/tambah/step1', [LaporanController::class, 'formTambahStep1'])->name('laporan.tambah.step1.form');

    /**
     * Menampilkan hasil filter pada form tambah laporan step 1
     * Method: GET
     * Name: logbook.laporan.tambah.step1.filter
     * URL: /logbook/laporan/tambah/step1/filter
     */
    Route::post('/laporan/tambah/step1/filter', [LaporanController::class, 'formTambahStep1Filter'])->name('laporan.tambah.step1.filter');
    
    /**
     * Menampilkan form tambah laporan step 2
     * Method: GET
     * Name: logbook.laporan.tambah.step2.form
     * URL: /logbook/laporan/tambah/step2/{layanan_id}
     */
    Route::get('/laporan/tambah/step2/{layanan_id}', [LaporanController::class, 'formTambahStep2'])->name('laporan.tambah.step2.form');

    /**
     * Memproses form tambah laporan step 2
     * Method: POST
     * Name: logbook.laporan.tambah.step2
     * URL: /logbook/laporan/tambah/step2
     */
    Route::post('/laporan/tambah/step2', [LaporanController::class, 'tambahStep2'])->name('laporan.tambah.step2');

    /**
     * Menampilkan form tambah laporan step 3
     * Method: GET
     * Name: logbook.laporan.tambah.step3.form
     * URL: /logbook/laporan/tambah/step3/{id}
     */
    Route::get('/laporan/tambah/step3/{id}', [LaporanController::class, 'formTambahStep3'])->name('laporan.tambah.step3.form');

    /**
     * Memproses form tambah laporan step 3
     * Method: POST
     * Name: logbook.laporan.tambah.step3
     * URL: /logbook/laporan/tambah/step3
     */
    Route::post('/laporan/tambah/step3', [LaporanController::class, 'tambahStep3'])->name('laporan.tambah.step3');


});

// Logbook Module Routes
Route::group(['prefix' => 'logbook', 'middleware' => ['auth']], function () {
    
    // Menampilkan halaman laporan logbook
    Route::get('/laporan', [LogbookModuleController::class, 'laporan'])
        ->name('logbook.laporan')
        ->middleware(['role:super_admin,admin,teknisi']);
    
    // Melakukan proses filter logbook
    Route::post('/filter', [LogbookModuleController::class, 'filter'])
        ->name('logbook.filter')
        ->middleware(['role:super_admin,admin,teknisi']);
    
    // Menampilkan halaman fasilitas logbook
    Route::get('/fasilitas', [LogbookModuleController::class, 'fasilitas'])
        ->name('logbook.fasilitas')
        ->middleware(['role:super_admin,admin,teknisi']);
  
});


/* ============================== MENU LAPORAN ==================================== */

// Menampilkan form Step 2 saat kembali dari Step 3
Route::get('/logbook/laporan/tambah/step2_back/{laporan_id}', [LaporanController::class, 'formStep2Back'])->name('tambah.step2.back')->middleware(['role:super_admin,admin,teknisi']);

// Menyimpan Step 2 dari mode kembali Step 3
Route::post('/logbook/laporan/tambah/step2/back/simpan', [LaporanController::class, 'tambahStep2Back'])->name('tambah.step2.back.simpan')->middleware(['role:super_admin,admin,teknisi']);

// Route untuk menampilkan form step3 back
Route::get('/logbook/laporan/tambah/step3/back/{laporan_id}', [LaporanController::class, 'formStep3Back'])->name('tambah.step3.form.back')->middleware(['role:super_admin,admin,teknisi']);

// Route untuk memproses update step3 back
Route::post('/logbook/laporan/tambah/step3/back/{laporan_id}', [LaporanController::class, 'tambahStep3Back'])->name('tambah.step3.back')->middleware(['role:super_admin,admin,teknisi']);

// Step 4 - Menampilkan form step 4
Route::get('/logbook/laporan/tambah/step4/{laporan_id}', [LaporanController::class, 'step4'])->name('tambah.step4')->middleware(['role:super_admin,admin,teknisi']);

// Step 4 - Simpan data step 4 (pakai parameter laporan_id)
Route::post('/logbook/laporan/tambah/step4/{laporan_id}/simpan', [LaporanController::class, 'simpanStep4'])->name('tambah.simpanStep4')->middleware(['role:super_admin,admin,teknisi']);

// Step 4 - Filter peralatan pengganti
Route::post('/logbook/laporan/peralatan/filter', [LaporanController::class, 'filterPeralatanPengganti'])->name('laporan.filterPeralatan')->middleware(['role:super_admin,admin,teknisi']);

// Menampilkan form Step 4 saat kembali dari Step 5
Route::get('/logbook/laporan/tambah/step4/back/{laporan_id}', [LaporanController::class, 'formStep4Back'])->name('tambah.step4.back')->middleware(['role:super_admin,admin,teknisi']);

// Menyimpan data dari Step 4 Back
Route::post('/logbook/laporan/tambah/step4/back', [LaporanController::class, 'tambahStep4Back'])->name('tambah.step4.back.store')->middleware(['role:super_admin,admin,teknisi']);

// Step 5 - Menampilkan review
Route::get('/logbook/laporan/tambah/step5/{laporan_id}', [LaporanController::class, 'step5'])->name('tambah.step5')->middleware(['role:super_admin,admin,teknisi']);

// Step 5 - Simpan laporan
Route::post('/logbook/laporan/tambah/step5/simpan', [LaporanController::class, 'simpanStep5'])->name('tambah.simpanStep5')->middleware(['role:super_admin,admin,tehnisi']);

// Untuk menghapus laporan yang berstatus DRAFT
Route::post('/logbook/laporan/hapus', [LaporanController::class, 'hapus'])->name('logbook.laporan.hapus')->middleware(['role:super_admin,admin,teknisi']);

// Untuk menampilkan detail data laporan
Route::post('/logbook/laporan/detail', [LaporanController::class, 'detail'])->name('logbook.laporan.detail')->middleware(['role:super_admin,admin,teknisi']);

/* ====== EDIT LAPORAN ====== */

// GET - Tampilkan form edit step2 untuk laporan draft
Route::get('/laporan/{id}/edit/step2', [LaporanController::class, 'editStep2'])->name('laporan.edit.step2')->middleware(['role:super_admin,admin,teknisi']);

// POST - Simpan hasil edit step2
Route::post('/laporan/{id}/edit/step2', [LaporanController::class, 'updateStep2'])->name('laporan.edit.step2.update')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 3 - Tindak Lanjut (GET)
Route::get('/laporan/edit/{id}/step3', [LaporanController::class, 'editStep3'])->name('logbook.laporan.edit.step3')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 3 - Tindak Lanjut (POST)
Route::post('/laporan/edit/{id}/step3', [LaporanController::class, 'updateStep3'])->name('logbook.laporan.edit.step3.update')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 4 - Penggantian (GET)
Route::get('/laporan/edit/{id}/step4', [LaporanController::class, 'editStep4'])->name('logbook.laporan.edit.step4')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 4 - Penggantian (POST)
Route::post('/laporan/edit/{id}/step4', [LaporanController::class, 'updateStep4'])->name('logbook.laporan.edit.step4.update')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 5 - Review (GET)
Route::get('/laporan/edit/{id}/step5', [LaporanController::class, 'editStep5'])->name('logbook.laporan.edit.step5')->middleware(['role:super_admin,admin,teknisi']);

// Edit Step 5 - Review (POST)
Route::post('/laporan/edit/{id}/step5', [LaporanController::class, 'updateStep5'])->name('logbook.laporan.edit.step5.update')->middleware(['role:super_admin,admin,teknisi']);

// Route untuk AJAX hapus tindak lanjut
Route::post('/logbook/laporan/edit/{id}/step3/delete', [LaporanController::class, 'deleteTindakLanjut'])->name('logbook.laporan.edit.step3.delete')->middleware(['role:super_admin,admin,teknisi']);

/* =========================== END OF MENU LAPORAN ================================ */

/* ============================== MENU RIWAYAT ==================================== */

// Halaman riwayat laporan
Route::get('/logbook/riwayat/daftar', [RiwayatController::class, 'daftar'])->name('logbook.riwayat.daftar')->middleware(['role:super_admin,admin,teknisi']);

// Untuk menampilkan detail data riwayat
Route::post('/logbook/riwayat/detail', [RiwayatController::class, 'detail'])->name('logbook.riwayat.detail')->middleware(['role:super_admin,admin,teknisi']);

/* =========================== END OF MENU RIWAYAT ================================ */

/* ============================== MENU EXPORT LOGBOOK ==================================== */

// Menampilkan daftar export
// Route ini digunakan untuk menampilkan halaman daftar laporan yang dapat diekspor
Route::get('/logbook/export/daftar', [ExportController::class, 'daftar'])->name('export.daftar')->middleware(['role:super_admin,admin,teknisi']);

// Menghandle proses export data laporan ke file Excel
// Route ini digunakan untuk mengekspor data laporan berdasarkan filter yang dipilih
Route::get('/logbook/export/export', [ExportController::class, 'export'])->name('laporan.export')->middleware(['role:super_admin,admin,teknisi']);

// Mengambil layanan berdasarkan fasilitas yang dipilih
// Route ini digunakan untuk mendapatkan daftar layanan yang terkait dengan fasilitas tertentu
Route::get('/logbook/export/get-layanan', [ExportController::class, 'getLayananByFasilitas'])->name('get.layanan.by.fasilitas')->middleware(['role:super_admin,admin,teknisi']);

// Mengambil data laporan dengan filter yang diterapkan
// Route ini digunakan untuk mendapatkan data laporan dengan pagination dan filter yang diterapkan
Route::get('/logbook/export/get-data', [ExportController::class, 'getData'])->name('export.getData')->middleware(['role:super_admin,admin,teknisi']);

/* ============================== END OF MENU EXPORT LOGBOOK ==================================== */

/** 
 * ------------------------------------------------------------------------------------
 *                             END OF MODULE LOGBOOK
 * ------------------------------------------------------------------------------------
 */ 

/** 
 * ------------------------------------------------------------------------------------
 *                             MODULE LOG AKTIVITAS
 *                          (HANYA SUPER ADMIN & ADMIN)
 * ------------------------------------------------------------------------------------
 */ 

// Menampilkan daftar log aktivitas
Route::get('/log_aktivitas/daftar', [LogAktivitasController::class, 'daftar'])->name('log-aktivitas.daftar')->middleware(['role:super_admin,admin']);

/** 
 * ------------------------------------------------------------------------------------
 *                            END OF MODULE LOG AKTIVITAS
 * ------------------------------------------------------------------------------------
 */ 

/** 
 * ------------------------------------------------------------------------------------
 *                             MODULE DASHBOARD
 *                    (SUPER ADMIN, ADMIN, & TEKNISI)
 * ------------------------------------------------------------------------------------
 */ 

/* ============================== DASHBOARD LAPORAN ==================================== */

// Rute untuk menampilkan dashboard laporan
Route::get('/dashboard/laporan', [DashboardLaporanController::class, 'laporan'])->name('dashboard.laporan')->middleware(['role:super_admin,admin,teknisi']);

// Rute untuk memfilter laporan dashboard (POST)
Route::post('/dashboard/laporan/filter', [DashboardLaporanController::class, 'filter'])->name('dashboard.laporan.filter')->middleware(['role:super_admin,admin,teknisi']);

// Rute untuk menampilkan laporan fasilitas dashboard
Route::get('/dashboard/fasilitas-laporan', [DashboardLaporanController::class, 'fasilitas'])->name('dashboard.fasilitas.laporan')->middleware(['role:super_admin,admin,teknisi']);

/* =========================== END OF DASHBOARD LAPORAN ================================ */

/* ============================== DASHBOARD FASILITAS ==================================== */

// Rute untuk menampilkan semua fasilitas dashboard
Route::get('/dashboard/fasilitas', [DashboardFasilitasController::class, 'index'])->name('dashboard.fasilitas')->middleware(['role:super_admin,admin,teknisi']);

// Rute untuk menampilkan daftar layanan fasilitas dashboard
Route::get('/dashboard/dashboarddaftar', [DashboardFasilitasController::class, 'dashboarddaftarIndex'])->name('dashboard.dashboarddaftar.index')->middleware(['role:super_admin,admin,teknisi']);

// Rute untuk memfilter daftar layanan fasilitas dashboard (POST)
Route::post('/dashboard/dashboarddaftar/filter', [DashboardFasilitasController::class, 'dashboarddaftarFilter'])->name('dashboard.dashboarddaftar.filter')->middleware(['role:super_admin,admin,teknisi']);

// Rute untuk menampilkan daftar layanan berdasarkan fasilitas_id
Route::get('/dashboard/dashboarddaftar/{fasilitas_id}', [DashboardFasilitasController::class, 'dashboarddaftar'])->name('dashboard.dashboarddaftar')->middleware(['role:super_admin,admin,teknisi']);

/* =========================== END OF DASHBOARD FASILITAS ================================ */

/** 
 * ------------------------------------------------------------------------------------
 *                            END OF MODULE DASHBOARD
 * ------------------------------------------------------------------------------------
 */ 

/** 
 * ------------------------------------------------------------------------------------
 *                             MENU PROFIL
 *                      (SEMUA USER YANG SUDAH LOGIN)
 * ------------------------------------------------------------------------------------
 */ 

// Halaman utama profil
Route::get('/profile', [ProfileController::class, 'profile'])->middleware(['auth']);

// Menampilkan form edit profil
Route::get('/profile/edit', [ProfileController::class, 'formEditProfil'])->middleware(['auth']);

// Melakukan update profil
Route::post('/profile/update', [ProfileController::class, 'updateProfil'])->middleware(['auth']);

// Menampilkan form ubah password
Route::get('/profile/ubah_password', [ProfileController::class, 'formUbahPassword'])->middleware(['auth']);

// Melakukan proses ubah password
Route::post('/profile/ubah_password', [ProfileController::class, 'ubahPassword'])->middleware(['auth']);

/** 
 * ------------------------------------------------------------------------------------
 *                            END OF MENU PROFIL
 * ------------------------------------------------------------------------------------
 */
