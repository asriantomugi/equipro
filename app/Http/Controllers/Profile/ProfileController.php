<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Function untuk menampilkan form ubah password user sendiri.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /profil/ubah_password
     *
     * @return \Illuminate\Http\Response
     */
    public function formUbahPassword()
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
        
        // semua role yang sudah login bisa mengubah password sendiri
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user yang sedang login
        $user = User::find(session()->get('id'));
        
        // jika user tidak ditemukan
        if($user == null){
            return redirect('/')->with('notif', 'user_null');
        }
        
        // variabel untuk dikirim ke halaman view
        $module = "Profile";
        $menu = "Profile";
        $menu_url = "/profile";
        $submenu = "Ubah Password";
        
        // menampilkan halaman view
        return view('profile.ubah_password')
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('user', $user);
    }

    /**
     * Function untuk mengubah password user sendiri.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /profil/ubah_password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ubahPassword(Request $request)
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
        
        // semua role yang sudah login bisa mengubah password sendiri
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // pastikan yang di-reset password bukan user Super Admin
        // ambil data user
        $user = User::where('id', $id)
			->where('role_id', '!=', config('constants.role.super_admin'))
            ->first();
        
        // jika user tidak ditemukan
        if($user == null){
            return redirect('/profile/ubah_password')->with('notif', 'user_null');
        }

        // pastikan user hanya bisa mengubah password sendiri
        if($request->id != session()->get('id')){
            return redirect('/profile/ubah_password')->with('notif', 'unauthorized');
        }

        // melakukan validasi input dari form
        $validasi = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:5'
            ]
        ], [
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Password yang dimasukkan tidak sama',
            'password.min' => 'Password minimal 5 karakter'
        ]);
        
        try {
            // update data password user di tabel User
            User::where('id', $user->id)
                ->update([
                    'password' => bcrypt($request->password),
                    'updated_by' => session()->get('id'),
                    'updated_at' => now()
                ]);

            // opsional: paksa user untuk login ulang setelah ganti password
            // Auth::logout();
            // return redirect('/login')->with('notif', 'password_sukses_logout');
            
        } catch(QueryException $ex) {
            // jika proses update gagal
            return redirect('/profile/ubah_password')->with('notif', 'password_gagal');
        }

        // jika proses update berhasil
        return redirect('/profile/ubah_password')->with('notif', 'password_sukses');
    }

    /**
     * Function untuk menampilkan halaman profil user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /profil
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            return redirect('/login');
        }    
        
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user yang sedang login
        $user = User::find(session()->get('id'));
        
        // variabel untuk dikirim ke halaman view
        $judul = "Profil";
        $module = "Profil";
        $menu = "Profil";
        $menu_url = "/profil";
        $submenu = "Lihat Profil";
        
        // menampilkan halaman view
        return view('profile.ubah_profile')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('user', $user);
    }

    /**
     * Function untuk menampilkan form edit profil user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /profil/edit
     *
     * @return \Illuminate\Http\Response
     */
    public function formEditProfil()
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            return redirect('/login');
        }    
        
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user yang sedang login
        $user = User::find(session()->get('id'));
        
        // variabel untuk dikirim ke halaman view
        $judul = "Profile";
        $module = "Profile";
        $menu = "Profil";
        $menu_url = "/profil";
        $submenu = "Edit Profil";
        
        // menampilkan halaman view
        return view('profile.edit')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('user', $user);
    }

    /**
     * Function untuk update profil user.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /profil/update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfil(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user yang sedang login
        $user = User::find(session()->get('id'));
        
        // pastikan user hanya bisa mengubah profil sendiri
        if($request->id != session()->get('id')){
            return redirect('/profil')->with('notif', 'unauthorized');
        }

        // melakukan validasi input dari form
        $validasi = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan'
        ]);
        
        try {
            // update data profil user di tabel User
            User::where('id', $user->id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_by' => session()->get('id'),
                    'updated_at' => now()
                ]);
            
        } catch(QueryException $ex) {
            // jika proses update gagal
            return redirect('/profile/edit')->with('notif', 'profil_gagal');
        }

        // jika proses update berhasil
        return redirect('/profile')->with('notif', 'profil_sukses');
    }
}
