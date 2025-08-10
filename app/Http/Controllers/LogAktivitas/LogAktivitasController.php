<?php

namespace App\Http\Controllers\LogAktivitas;

/**
 * LogAktivitasController.php
 * Controller ini digunakan untuk menampilkan daftar log aktivitas user
 *
 * Akses: SUPER ADMIN dan ADMIN
 *
 * @author Faldy
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LogAktivitas;

class LogAktivitasController extends Controller
{
    /**
     * Function untuk menampilkan daftar log aktivitas user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /log_aktivitas/daftar
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

        // ambil data user dari session
        $user = User::find(session()->get('id'));

        // cek apakah user valid dan aktif
        if (!$user || !$user->status) {
            return redirect('/logout');
        }

        // ✅ PERBAIKAN: cek role user, bisa diakses oleh super admin DAN admin
        $userRoleId = session()->get('role_id');
        $allowedRoles = [
            config('constants.role.super_admin'),
            config('constants.role.admin')
        ];
        
        if (!in_array($userRoleId, $allowedRoles)) {
            // Bisa juga menggunakan abort(403) untuk forbidden access
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ========================= AMBIL DATA LOG ============================
        // ambil data log aktivitas beserta relasi user (hanya ambil id dan name)
        $daftar = LogAktivitas::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->latest()->get();

        // ========================= PENGIRIMAN DATA KE VIEW ===================
        // variabel untuk dikirim ke halaman view
        $judul     = "Log Aktivitas";
        $module    = "Log Aktivitas";
        $menu      = "Log Aktivitas";
        $menu_url  = "/log_aktivitas/daftar";  // ✅ Sesuaikan dengan route yang benar
        $submenu   = "Daftar";

        // tampilkan view
        return view('log_aktivitas.daftar', compact(
            'judul', 'module', 'menu', 'menu_url', 'submenu', 'daftar'
        ));
    }
}