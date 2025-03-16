<?php

namespace App\Http\Controllers\MasterData;

/**
 * UserController.php
 * Controller ini digunakan untuk menangani proses CRUD user
 *
 * @author Mugi Asrianto
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Role;
use App\Models\DetailUser;
use App\Models\Perusahaan;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Function untuk menampilkan daftar user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/user/daftar
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
        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar user
        $daftar = User::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "User";
        $menu = "User";
        $page = "Daftar";
        
        // menampilkan halaman view
        return view('master_data.user.daftar')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('daftar', $daftar)
        ;
    }


    /**
     * Function untuk menampilkan form tambah user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/user/tambah
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambah()
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }

        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar role untuk form tambah user
        $roles = Role::where('id', '!=', config('constants.role.super_admin'))
            ->get();

        // ambil daftar perusahaan
        $perusahaan = Perusahaan::all();
    

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah User";
        $menu = "User";
        $page = "Tambah User";
        
        // menampilkan halaman view
        return view('master_data.user.tambah')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('roles', $roles)
        ->with('perusahaan', $perusahaan)
        ;
    }

    /**
     * Function untuk menambahkan user.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/user/tambah
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tambah(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // melakukan validasi input dari form
        // jika tidak sesuai parameter, maka akan muncul error
        $validasi  = $request->validate([
            // parameter validasi
            'email' => 'unique:users,email',
            'password' => 'confirmed'
        ],[
            // pesan error
            'email.unique' => 'Email yang dimasukkan sudah terdaftar.',
            'password.confirmed' => 'Password yang dimasukkan tidak sama.'
        ]);

        try{
            // tambah row di tabel user
            $user = User::create([
                'name' => strtoupper($request->nama),
                'email' => $request->email,
                'status' => 1, // aktif
                'role_id' => $request->role,
                'password' => bcrypt($request->password),
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/user/daftar')->with('notif', 'tambah_gagal');
        }

        try{
            // tambah row di tabel detail user
            $detailUser = DetailUser::create([
                'user_id' => $user->id,
                'perusahaan_id' => $request->perusahaan,
                'alamat' => strtoupper($request->alamat),
                'telepon' => $request->telepon,
                'jabatan' => $request->jabatan,
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            dd($ex->getMessage());
            // hapus user di tabel user
            User::where('id', $user->id)->delete();
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/user/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/user/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data user admin berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/user/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id user
        $id = $request->id;

        // ambil data user
        $user = User::find($id);

        // ambil data relationship
        $role = $user->role;
        $detail = $user->detail;
        $perusahaan = Perusahaan::find($detail->perusahaan_id);
        $created_by = User::find($user->created_by);
        $updated_by = User::find($user->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'user'=>$user, 
            'perusahaan'=>$perusahaan,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit user.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/user/edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formEdit($id)
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }

        // cek role user, hanya bisa diakses oleh super user dan operation
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user
        $user = User::where('id', $id)
            ->where('role_id', '!=', config('constants.role.super_admin'))
            ->first();

        // jika user dengan id tersebut tidak ada
        if($user == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/user/daftar')->with('notif', 'user_null');
        }

        // ambil daftar role untuk form tambah user
        $roles = Role::where('id', '!=', config('constants.role.super_admin'))
            ->get();   

        // ambil daftar perusahaan
        $perusahaan = Perusahaan::all();

        // variabel untuk dikirim ke halaman view
        $judul = "Edit Data User";
        $menu = "User";
        $page = "Edit Data User";
        
        // menampilkan halaman view
        return view('master_data.user.edit')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('roles', $roles)
        ->with('perusahaan', $perusahaan)
        ->with('user', $user)
        ;
    }

    /**
     * Function untuk mengubah data user admin.
     * 
     * Akses:
     * - Super Admin
     * - Airport Operation
     * 
     * Method: POST
     * URL: /master-data/user/admin/edit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editAdmin(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        try{
            // update data user di tabel User
            User::where('id', $request->id)
            ->update([
                'name' => $request->nama,
                'status' => $request->status,
                'role_id' => $request->role,
                'updated_by' => session()->get('id')
            ]);

            // update data user di tabel detail user
            DetailUser::where('user_id', $request->id)
            ->update([
                'perusahaan_id' => $request->perusahaan,
                'jabatan' => $request->jabatan,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/user/admin/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/user/admin/daftar')->with('notif', 'edit_sukses');
    }
	
	/**
     * Function untuk menampilkan form reset password user admin.
     *
     * Akses:
     * - Super Admin
     * - Airport Operation
     * 
     * Method: GET
     * URL: /master-data/user/admin/password/reset/{id}
     *
	 * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formResetPasswordAdmin($id)
    {
		// ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }

        // cek role user, hanya bisa diakses oleh super user dan operation
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.operation')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user admin AP1
        $user = User::where('id', $id)
			->where('role_id', '!=', config('constants.role.gh'))
            ->first();
			
		// jika user dengan id tersebut tidak ada
        if($user == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/user/admin/daftar')->with('notif', 'user_null');
        }
		// ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar role
        $roles = Role::all();
		
		// variabel untuk dikirim ke halaman view
        $judul = "User Admin";
        $menu = "User";
        $page = "Admin";
        $page_url = "/master-data/user/admin/daftar";
        $subpage = "Reset Password";
        
        // menampilkan halaman view
        return view('user.admin.password')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('menuAktif', 0) // menu utama tidak aktif (menu dropdown)
        ->with('page', $page)
        ->with('page_url', $page_url)
        ->with('subpage', $subpage)
		->with('user', $user)
		->with('roles', $roles)
        ;
    }
	
	
	/**
     * Function untuk me-reset password user admin.
     * 
     * Akses:
     * - Super Admin
     * - Airport Operation
     * 
     * Method: POST
     * URL: /master-data/user/admin/password/reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPasswordAdmin(Request $request)
    {
		// ========================= PROSES VERIFIKASI ========================
        // cek session user
        if (!Auth::check()) {
            // jika tidak ada session user
            return redirect('/login');
        }
		
		// cek role user, hanya bisa diakses oleh super user dan operation
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.operation')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data user admin AP1
        $user = User::where('id', $request->id)
			->where('role_id', '!=', config('constants.role.gh'))
            ->first();
			
		// jika user dengan id tersebut tidak ada
        if($user == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/user/admin/daftar')->with('notif', 'user_null');
        }
		// ===================== AKHIR PROSES VERIFIKASI =======================

        // melakukan validasi input dari form
        // jika tidak sesuai parameter, maka akan muncul error
        $validasi  = $request->validate([
            // parameter validasi
            'password' => 'confirmed'
        ],[
            // pesan error
            'password.confirmed' => 'Password yang dimasukkan tidak sama.'
        ]);
		
		try{
            // update data user di tabel User
            User::where('id', $user->id)
            ->update([
                'password' => bcrypt($request->password),
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/user/admin/daftar')->with('notif', 'password_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/user/admin/daftar')->with('notif', 'password_sukses');
    }
}
