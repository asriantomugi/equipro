<?php

namespace App\Http\Controllers\MasterData;

/**
 * JenisAlatController.php
 * Controller ini digunakan untuk menangani proses CRUD Jenis Alat
 *
 * @author Mugi Asrianto
 */

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Database\QueryException;
 use Illuminate\Support\MessageBag;
 use App\Models\User;
 use App\Models\JenisAlat;
 use App\Http\Controllers\Controller;

class JenisAlatController extends Controller
{
    /**
     * Function untuk menampilkan daftar jenis alat.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/jenis-alat/daftar
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
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar jenis alat
        $daftar = JenisAlat::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "Jenis Alat";
        $module = "Master Data";
        $menu = "Jenis Alat";
        $menu_url = "/master-data/jenis-alat/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('master_data.jenis_alat.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah jenis alat.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/jneis-alat/tambah
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
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // variabel untuk dikirim ke halaman view
        $judul = "Jenis Alat";
        $module = "Master Data";
        $menu = "Jenis Alat";
        $menu_url = "/master-data/jenis-alat/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('master_data.jenis_alat.tambah')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('submenu', $submenu)
        ->with('menu_url', $menu_url)
        ;
    }

    /**
     * Function untuk menambahkan jenis alat.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/jenis-alat/tambah
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
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // melakukan validasi input dari form
        // jika tidak sesuai parameter, maka akan muncul error
        $validasi  = $request->validate([
            // parameter validasi
            'kode' => 'unique:fasilitas,kode',
            'nama' => 'unique:fasilitas,nama',
        ],[
            // pesan error
            'kode.unique' => 'Kode yang dimasukkan sudah terdaftar',
            'nama.unique' => 'Nama yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel jenis alat
            $jenis_alat = JenisAlat::create([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'status' => 1, // aktif
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/jenis-alat/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/jenis-alat/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data jenis alat berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/jenis-alat/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id jenis alat
        $id = $request->id;

        // ambil data jenis alat
        $jenis_alat = JenisAlat::find($id);

        // ambil data relationship
        $created_by = User::find($jenis_alat->created_by);
        $updated_by = User::find($jenis_alat->updated_by);

        //dd($jenis_alat->nama);

        //return response()->json($user);
        return response()->json([
            'jenis_alat'=>$jenis_alat,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit jenis alat.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/jenis-alat/edit
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
        // cek apakah status user = aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        // cek role user, hanya bisa diakses oleh super admin dan admin
        if(session()->get('role_id') != config('constants.role.super_admin')
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil data jenis alat
        $jenis_alat = JenisAlat::where('id', $id)
            ->first();

        // jika jenis alat dengan id tersebut tidak ada
        if($jenis_alat == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/jenis-alat/daftar')->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Jenis Alat";
        $module = "Master Data";
        $menu = "Jenis Alat";
        $menu_url = "/master-data/jenis-alat/daftar";
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('master_data.jenis_alat.edit')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('jenis_alat', $jenis_alat)
        ;
    }

    /**
     * Function untuk mengubah data jenis alat.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/jenis-alat/edit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ===================== CEK DUPLIKASI KODE ============================
        // ambil data jenis alat sebelumnya berdasarkan id
        $jenis_alat = JenisAlat::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan kode
        if($jenis_alat->kode != $request->kode){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = JenisAlat::where('kode', $request->kode)->first();
            // jika kode sudah terdaftar
            if($cekKode != null){
                // buat pesan error
                $errors = new MessageBag(['kode' => 'Kode yang dimasukkan sudah terdaftar']);
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->withErrors($errors)->withInput();
            }
        }
        // ===================== END OF CEK DUPLIKASI KODE =====================

        try{
            // update data jenis alat di tabel Jenis alat
            JenisAlat::where('id', $request->id)
            ->update([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'status' => $request->status,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/jenis-alat/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/jenis-alat/daftar')->with('notif', 'edit_sukses');
    }
}
