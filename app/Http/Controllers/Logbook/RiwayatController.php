<?php

namespace App\Http\Controllers\Logbook;

/**
 * RiwayatController.php
 * Controller ini digunakan untuk menangani riwayat dan ekspor
 *
 * @author Yanti Melani
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
 use App\Models\User;

class RiwayatController extends Controller
{

    /**
     * Function untuk menampilkan daftar riwayat.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/riwayat/daftar
     *
     * @return \Illuminate\Http\Response
     */
    public function daftar()
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
        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // Ambil hanya laporan dengan status CLOSED
        $daftar = Laporan::where('status', config('constants.status_laporan.closed'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Variabel untuk dikirim ke view
        $judul = "Riwayat";
        $module = "Riwayat";
        $menu = "Riwayat";
        $menu_url = "/logbook/riwayat/daftar";
        $submenu = "Daftar";

        return view('logbook.riwayat.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('daftar', $daftar);
        }
}