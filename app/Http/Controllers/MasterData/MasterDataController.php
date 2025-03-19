<?php

namespace App\Http\Controllers\MasterData;

/**
 * MasterDataController.php
 * Controller ini digunakan untuk menangani akses ke halaman utama
 * module Master Data
 *
 * @author Mugi Asrianto
 */

use App\Models\Gse;
use App\Models\User;
use App\Models\Mohon;
use App\Models\Stiker;
use App\Models\Konstanta;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class MasterDataController extends Controller
{
	/**
     * Function untuk menampilkan halaman utama module Master Data
	 * 
     * Akses:
     * - Super Admin
	 * - Admin
     * 
	 * Method: GET
     * URL: /home/master-data
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
            && session()->get('role_id') != config('constants.role.admin')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================
		
        // ===================== PROSES PENGAMBILAN DATA USER =======================
        
        // Ambil jumlah user Super Admin
        $jlhSuperAdmin = User::where('status', 1) // status aktif
            ->where('role_id', config('constants.role.super_admin')) // role super admin
            ->get()->count();

        // Ambil jumlah user Admin
        $jlhAdmin = User::where('status', 1) // status aktif
            ->where('role_id', config('constants.role.admin')) // role admin
            ->get()->count();

        // Ambil jumlah user Teknisi
        $jlhTeknisi = User::where('status', 1) // status aktif
            ->where('role_id', config('constants.role.teknisi')) // role teknisi
            ->get()->count();

        // Buat array untuk data chart jumlah user berdasarkan role
        $dataChartRoleUser = [
            'labels' => ['Super Admin', 'Admin', 'Teknisi'],
            'data' => [$jlhSuperAdmin, $jlhAdmin, $jlhTeknisi]
        ];
        // ===================== AKHIR PROSES PENGAMBILAN DATA USER =======================

		// buat variabel untuk dikirim ke halaman view
		$judul = "Master Data";
		$menu = "Master Data";
        $page = "Home";
			
		// alihkan ke halaman view untuk user non BUAU/BUGH
		return view('master_data.home')
		->with('judul', $judul)
		->with('menu', $menu)
		->with('page', $page)		
        ->with('dataChartRoleUser', $dataChartRoleUser)
		;
    }	

}
