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
    ->middleware(['role:super_admin, admin']) // Akses oleh Super Admin dan Admin
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
 *                             END OF MODULE MASTER DATA
 * ------------------------------------------------------------------------------------
 */ 

 /** 
 * ------------------------------------------------------------------------------------
 *                             MODULE FASILITAS
 *                          (HANYA SUPER ADMIN & ADMIN)
 * ------------------------------------------------------------------------------------
 */ 

// Menampilkan halaman utama module Fasilitas
Route::get('/fasilitas/home', [FasilitasModuleController::class, 'home'])->middleware(['role:super_admin,admin']);

/* ============================== MENU PERALATAN ==================================== */
// Menampilkan daftar peralatan
Route::get('/fasilitas/peralatan/daftar', [PeralatanController::class, 'daftar'])->middleware(['role:super_admin,admin']);

// Menampilkan form tambah peralatan
Route::get('/fasilitas/peralatan/tambah', [PeralatanController::class, 'formTambah'])->middleware(['role:super_admin,admin']);

// Melakukan proses tambah peralatan
Route::post('/fasilitas/peralatan/tambah', [PeralatanController::class, 'tambah'])->middleware(['role:super_admin,admin']);

// Menampilkan form edit peralatan
Route::get('/fasilitas/peralatan/edit/{id}', [PeralatanController::class, 'formEdit'])->middleware(['role:super_admin,admin']);

// Melakukan proses edit peralatan
Route::post('/fasilitas/peralatan/edit', [PeralatanController::class, 'edit'])->middleware(['role:super_admin,admin']);

// Menampilkan JSON data peralatan
Route::post('/fasilitas/peralatan/detail', [PeralatanController::class, 'detail'])->middleware(['role:super_admin,admin']);

/* =========================== END OF MENU PERALATAN ============================== */

/* ============================== MENU LAYANAN ==================================== */
// Menampilkan daftar layanan
Route::get('/fasilitas/layanan/daftar', [LayananController::class, 'daftar'])->name('fasilitas.layanan.daftar')->middleware(['role:super_admin,admin']);

// Menampilkan form tambah layanan step 1
Route::get('/fasilitas/layanan/tambah/step1', [LayananController::class, 'formTambahStep1'])->name('fasilitas.layanan.tambah.step1.form')->middleware(['role:super_admin,admin']);

// Menampilkan form tambah layanan step 1 (tombol back)
Route::get('/fasilitas/layanan/tambah/step1/back/{id}', [LayananController::class, 'formTambahStep1Back'])->name('fasilitas.layanan.tambah.step1.back.form')->middleware(['role:super_admin,admin']);

// Melakukan proses tambah layanan step 1
Route::post('/fasilitas/layanan/tambah/step1', [LayananController::class, 'tambahStep1'])->name('fasilitas.layanan.tambah.step1')->middleware(['role:super_admin,admin']);

// Melakukan proses tambah layanan step 1 (tombol back)
Route::post('/fasilitas/layanan/tambah/step1/back', [LayananController::class, 'tambahStep1Back'])->name('fasilitas.layanan.tambah.step1.back')->middleware(['role:super_admin,admin']);

// Menampilkan form tambah layanan step 2
Route::get('/fasilitas/layanan/tambah/step2/{id}', [LayananController::class, 'formTambahStep2'])->name('fasilitas.layanan.tambah.step2.form')->middleware(['role:super_admin,admin']);

// Melakukan proses tambah layanan step 2
Route::post('/fasilitas/layanan/tambah/step2', [LayananController::class, 'tambahStep2'])->name('fasilitas.layanan.tambah.step2')->middleware(['role:super_admin,admin']);

// Menampilkan form tambah layanan step 3
Route::get('/fasilitas/layanan/tambah/step3/{id}', [LayananController::class, 'formTambahStep3'])->name('fasilitas.layanan.tambah.step3.form')->middleware(['role:super_admin,admin']);

// Melakukan proses tambah layanan step 3
Route::post('/fasilitas/layanan/tambah/step3', [LayananController::class, 'tambahStep3'])->name('fasilitas.layanan.tambah.step3')->middleware(['role:super_admin,admin']);

// Melakukan menghapus draft layanan
Route::post('/fasilitas/layanan/hapus', [LayananController::class, 'hapus'])->name('fasilitas.layanan.hapus')->middleware(['role:super_admin,admin']);

// Menampilkan form edit layanan step 1
Route::get('/fasilitas/layanan/edit/step1/{id}', [LayananController::class, 'formEditStep1'])->name('fasilitas.layanan.edit.step1.form')->middleware(['role:super_admin,admin']);

// Melakukan proses edit layanan step 1
Route::post('/fasilitas/layanan/edit/step1', [LayananController::class, 'editStep1'])->name('fasilitas.layanan.edit.step1')->middleware(['role:super_admin,admin']);

// Menampilkan form edit layanan step 2
Route::get('/fasilitas/layanan/edit/step2/{id}', [LayananController::class, 'formEditStep2'])->name('fasilitas.layanan.edit.step2.form')->middleware(['role:super_admin,admin']);

// Menampilkan form edit layanan step 3
Route::get('/fasilitas/layanan/edit/step3/{id}', [LayananController::class, 'formEditStep3'])->name('fasilitas.layanan.edit.step3.form')->middleware(['role:super_admin,admin']);

// Melakukan proses edit layanan step 3
Route::post('/fasilitas/layanan/edit/step3', [LayananController::class, 'editStep3'])->name('fasilitas.layanan.edit.step3')->middleware(['role:super_admin,admin']);

// Melakukan proses filter layanan
Route::post('/fasilitas/layanan/filter', [LayananController::class, 'filter'])->name('fasilitas.layanan.filter')->middleware(['role:super_admin,admin']);

// Menampilkan JSON data layanan
Route::post('/fasilitas/layanan/detail', [LayananController::class, 'detail'])->name('fasilitas.layanan.detail')->middleware(['role:super_admin,admin']);

// Menampilkan daftar peralatan tersedia berdasarkan filter
Route::post('/fasilitas/layanan/peralatan/filter', [LayananController::class, 'peralatanFilter'])->name('fasilitas.layanan.peralatan.filter')->middleware(['role:super_admin,admin']);

// Melakukan proses tambah peralatan ke layanan
Route::post('/fasilitas/layanan/peralatan/tambah', [LayananController::class, 'tambahPeralatan'])->name('fasilitas.layanan.peralatan.tambah')->middleware(['role:super_admin,admin']);

// Menampilkan halaman edit ip address peralatan
Route::post('/fasilitas/layanan/peralatan/edit', [LayananController::class, 'editPeralatan'])->name('fasilitas.layanan.peralatan.edit')->middleware(['role:super_admin,admin']);

// Menghapus peralatan dari layanan
Route::post('/fasilitas/layanan/peralatan/hapus', [LayananController::class, 'hapusPeralatan'])->name('fasilitas.layanan.peralatan.hapus')->middleware(['role:super_admin,admin']);

// Menampilkan JSON data peralatan layanan
Route::post('/fasilitas/layanan/peralatan/detail', [LayananController::class, 'detailPeralatan'])->name('fasilitas.layanan.peralatan.detail')->middleware(['role:super_admin,admin']);

/* =========================== END OF MENU LAYANAN ================================ */

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

// Logbook Module Routes
Route::group(['prefix' => 'logbook', 'middleware' => ['auth']], function () {
    // Menampilkan halaman utama module Logbook
    Route::get('/home', [LogbookModuleController::class, 'home'])
        ->name('logbook.home')
        ->middleware(['role:super_admin,admin,teknisi']);
    
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

// Menampilkan daftar laporan
Route::get('/logbook/laporan/daftar', [LaporanController::class, 'daftar'])->name('logbook.laporan.daftar')->middleware(['role:super_admin,admin,teknisi']);

/* ====== TAMBAH LAPORAN ====== */

// Step 1 - Menampilkan form filter
Route::get('/logbook/laporan/tambah/step1', [LaporanController::class, 'formStep1'])->name('tambah.step1')->middleware(['role:super_admin,admin,teknisi']);

// Step 1 - Melakukan proses filter
Route::post('/logbook/laporan/filter', [LaporanController::class, 'filter'])->name('logbook.laporan.filter')->middleware(['role:super_admin,admin,teknisi']);

// Step 2 - Menampilkan form pilih jenis laporan dan input gangguan
Route::get('/logbook/laporan/tambah/step2', [LaporanController::class, 'formStep2'])->name('tambah.step2')->middleware(['role:super_admin,admin,teknisi']);

// Step 2 - Menyimpan jenis laporan dan input gangguan
Route::post('/logbook/laporan/tambah/step2/simpan', [LaporanController::class, 'simpanStep2'])->name('tambah.step2.simpan')->middleware(['role:super_admin,admin,teknisi']);

// Menampilkan form Step 2 saat kembali dari Step 3
Route::get('/logbook/laporan/tambah/step2_back/{laporan_id}', [LaporanController::class, 'formStep2Back'])->name('tambah.step2.back')->middleware(['role:super_admin,admin,teknisi']);

// Menyimpan Step 2 dari mode kembali Step 3
Route::post('/logbook/laporan/tambah/step2/back/simpan', [LaporanController::class, 'tambahStep2Back'])->name('tambah.step2.back.simpan')->middleware(['role:super_admin,admin,teknisi']);

// Step 3 - Menampilkan form tindaklanjut
Route::get('/logbook/laporan/tambah/step3/{laporan_id}', [LaporanController::class, 'formStep3'])->name('tambah.step3')->middleware(['role:super_admin,admin,teknisi']);

// Step 3 - Menyimpan tindaklanjut
Route::post('/logbook/laporan/tambah/step3/simpan', [LaporanController::class, 'simpanStep3'])->name('tambah.simpanStep3')->middleware(['role:super_admin,admin,teknisi']);

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
