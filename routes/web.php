<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtentikasiController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\MasterData\UserController;

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
