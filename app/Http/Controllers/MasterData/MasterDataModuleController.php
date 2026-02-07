<?php

namespace App\Http\Controllers\MasterData;

/**
 * MasterDataModuleController.php
 * Controller ini digunakan untuk menangani akses ke halaman utama
 * module Master Data
 *
 * @author Mugi Asrianto
 */

use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Fasilitas;
use App\Models\JenisAlat;
use App\Models\LokasiTk1;
use App\Models\LokasiTk2;
use App\Models\LokasiTk3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class MasterDataModuleController extends Controller
{
	/**
     * Function untuk menampilkan halaman utama module Master Data
	 * 
     * Akses:
     * - Super Admin
	 * - Admin
     * 
	 * Method: GET
     * URL: /master-data/home
     * 
     * @param  Request  $request
     * @return void
     */
    public function home(Request $request)
    {		
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

        // Ambil jumlah perusahaan
        $jlhPerusahaan = Perusahaan::where('status', 1) // status aktif
            ->get()->count();

        // Ambil jumlah fasilitas
        $jlhFasilitas = Fasilitas::where('status', 1) // status aktif
            ->get()->count();
        
        // Ambil jumlah jenis alat
        $jlhJenisAlat = JenisAlat::where('status', 1) // status aktif
            ->get()->count();

        // Ambil jumlah lokasi tingkat I
        $jlhLokasiTk1 = LokasiTk1::where('status', 1) // status aktif
            ->get()->count();

        // Ambil jumlah lokasi tingkat II
        $jlhLokasiTk2= LokasiTk2::where('status', 1) // status aktif
            ->get()->count();

        // Ambil jumlah lokasi tingkat III
        $jlhLokasiTk3= LokasiTk3::where('status', 1) // status aktif
            ->get()->count();

        // Buat array untuk data chart jumlah user berdasarkan role
        $dataChartRoleUser = [
            'labels' => ['Super Admin', 'Admin', 'Teknisi'],
            'data' => [$jlhSuperAdmin, $jlhAdmin, $jlhTeknisi]
        ];

        // Buat array untuk data chart jumlah perusahaan, fasilitas, jenis alat
        $dataChartInfo = [
            'labels' => ['Perusahaan', 'Fasilitas', 'Jenis Alat'],
            'data' => [$jlhPerusahaan, $jlhFasilitas, $jlhJenisAlat]
        ];

        // Buat array untuk data chart jumlah lokasi tingkat I, II, dan III
        $dataChartLokasi = [
            'labels' => ['Lokasi Tingkat I', 'Lokasi Tingkat II', 'Lokasi Tingkat III'],
            'data' => [$jlhLokasiTk1, $jlhLokasiTk2, $jlhLokasiTk3]
        ];
        // ===================== AKHIR PROSES PENGAMBILAN DATA USER =======================

		// buat variabel untuk dikirim ke halaman view
		$judul = "Home";
		$module = "Master Data";
        $menu = "Home";
        $menu_url = "#";
			
		// alihkan ke halaman view
		return view('master_data.home')
		->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('dataChartRoleUser', $dataChartRoleUser)
        ->with('dataChartInfo', $dataChartInfo)
        ->with('dataChartLokasi', $dataChartLokasi)
		;
    }	

}
