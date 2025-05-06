<?php

namespace App\Http\Controllers\MasterData;

/**
 * FasilitasController.php
 * Controller ini digunakan untuk menangani proses CRUD fasilitas
 *
 * @author Mugi Asrianto
 */

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Database\QueryException;
 use Illuminate\Support\MessageBag;
 use App\Models\User;
 use App\Models\Fasilitas;
 use App\Http\Controllers\Controller;

class FasilitasController extends Controller
{
    /**
     * Function untuk menampilkan daftar fasilitas.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/fasilitas/daftar
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

        // ambil daftar fasilitas
        $daftar = Fasilitas::all();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Fasilitas";
		$module = "Master Data";
        $menu = "Fasilitas";
        $menu_url = "/master-data/fasilitas/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('master_data.fasilitas.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah fasilitas.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/fasilitas/tambah
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
        $judul = "Fasilitas";
		$module = "Master Data";
        $menu = "Fasilitas";
        $menu_url = "/master-data/fasilitas/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('master_data.fasilitas.tambah')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ;
    }

    /**
     * Function untuk menambahkan fasilitas.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/fasilitas/tambah
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
            // tambah row di tabel fasilitas
            $fasilitas = Fasilitas::create([
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
            return redirect('/master-data/fasilitas/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/fasilitas/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data fasilitas berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/fasilitas/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id fasilitas
        $id = $request->id;

        // ambil data fasilitas
        $fasilitas = Fasilitas::find($id);

        // ambil data relationship
        $created_by = User::find($fasilitas->created_by);
        $updated_by = User::find($fasilitas->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'fasilitas'=>$fasilitas,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit fasilitas.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/fasilitas/edit
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

        // ambil data fasilitas
        $fasilitas = Fasilitas::where('id', $id)
            ->first();

        // jika fasilitas dengan id tersebut tidak ada
        if($fasilitas == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/fasilitas/daftar')->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Fasilitas";
		$module = "Master Data";
        $menu = "Fasilitas";
        $menu_url = "/master-data/fasilitas/daftar";
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('master_data.fasilitas.edit')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('fasilitas', $fasilitas)
        ;
    }

    /**
     * Function untuk mengubah data fasilitas.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/fasilitas/edit
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
        // ambil data fasilitas sebelumnya berdasarkan id
        $fasilitas = Fasilitas::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan kode
        if($fasilitas->kode != $request->kode){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = Fasilitas::where('kode', $request->kode)->first();
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
            // update data fasilitas di tabel Fasilitas
            Fasilitas::where('id', $request->id)
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
            return redirect('/master-data/fasilitas/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/fasilitas/daftar')->with('notif', 'edit_sukses');
    }
}
