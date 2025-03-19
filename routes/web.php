<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtentikasiController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\MasterData\PerusahaanController;
use App\Http\Controllers\MasterData\FasilitasController;
use App\Http\Controllers\MasterData\JenisAlatController;
use App\Http\Controllers\MasterData\LokasiTk1Controller;
use App\Http\Controllers\MasterData\LokasiTk2Controller;
use App\Http\Controllers\MasterData\LokasiTk3Controller;

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
Route::get('/master-data/home', [MasterDataController::class, 'home']);

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

/* =========================== END OF MENU LOKASI TINGKAT II ================================ */

/** 
 * ------------------------------------------------------------------------------------
 *                             END OF MODULE MASTER DATA
 * ------------------------------------------------------------------------------------
 */ 