<?php

namespace App\Http\Controllers\Fasilitas;

/**
 * FasilitasModuleController.php
 * Controller ini digunakan untuk menangani akses ke halaman utama
 * module Fasilitas
 *
 * @author Mugi Asrianto
 */

use App\Models\User;
use App\Models\Konstanta;
use App\Models\Perusahaan;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FasilitasModuleController extends Controller
{
	/**
     * Function untuk menampilkan halaman utama module Fasilitas
	 * 
     * Akses:
     * - Super Admin
	 * - Admin
     * 
	 * Method: GET
     * URL: /fasilitas/home
     * 
     * @param  Request  $request
     * @return void
     */
    public function home(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }

        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }

        // cek role user, hanya bisa diakses oleh admin AP1
        if(session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // mengambil daftar fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        //dd($fasilitas);
		
        // ===================== PROSES PENGAMBILAN DATA USER =======================
        // Buat array sementara untuk data chart jumlah serviceable dan unserviceable
        $dataChart = [
            'labels' => ['Serviceable', 'Unserviceable'],
            'data' => [0, 0]
        ];
        // ===================== AKHIR PROSES PENGAMBILAN DATA USER =======================

		// buat variabel untuk dikirim ke halaman view
		$judul = "Home";
		$module = "Fasilitas";
        $menu = "Home";
        $menu_url = "#";
			
		// alihkan ke halaman view
		return view('fasilitas.home')
		->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('fasilitas', $fasilitas)
        ->with('dataChart', $dataChart)
		;
    }	

}