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
     * Function untuk mengarahkan user ke halaman daftar module
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
    public function index(Request $request)
    {	
		// buat variabel untuk dikirim ke halaman view
        $judul = "Module";
		$module = "Module";
        $menu = "Daftar";
        $menu_url = "#";
			
		// alihkan ke halaman view untuk user non BUAU/BUGH
		return view('module')
		->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)		
		;
    }	

}
