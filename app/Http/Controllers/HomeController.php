<?php

namespace App\Http\Controllers;

/**
 * HomeController.php
 * Controller ini digunakan untuk menangani akses index.
 *
 * @author Mugi Asrianto
 */

use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	/**
     * Function untuk mengarahkan user ke halaman daftar module atau halaman login.
	 * 
     * Akses:
     * - All User
     * 
	 * Method: GET
     * URL: /
     * 
     * @param  Request  $request
     * @return 
     */
	public function index(Request $request){
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

		// cek role user, apabila admin AP1, arahkan ke home admin
        if(session()->get('role_id') == config('constants.role.super_admin') 
            || session()->get('role_id') == config('constants.role.admin')
			|| session()->get('role_id') == config('constants.role.teknisi')){
            return redirect('/module');
        }

		// jika tidak memenuhi persyaratan, paksa logout
		return redirect('/logout');
	}
	
	
    /**
     * Function untuk mengarahkan user ke halaman daftar module
	 * 
     * Akses:
     * - Super Admin
	 * - Admin
	 * - Teknisi
     * 
	 * Method: GET
     * URL: /module
     * 
     * @param  Request  $request
     * @return 
     */
    public function module(Request $request)
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

        // cek role user
        if(session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
			&& session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================
		
		// buat variabel untuk dikirim ke halaman view
		$judul = "Module";
		$menu = "Module";
        $page = "Daftar";
			
		// alihkan ke halaman view untuk user non BUAU/BUGH
		return view('module')
		->with('judul', $judul)
		->with('menu', $menu)
		->with('page', $page)		
		;
    }	

}
