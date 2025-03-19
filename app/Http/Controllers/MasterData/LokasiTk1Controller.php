<?php

namespace App\Http\Controllers\MasterData;

/**
 * LokasiTk1Controller.php
 * Controller ini digunakan untuk menangani proses CRUD lokasi tingkat I
 *
 * @author Mugi Asrianto
 */

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use \Illuminate\Database\QueryException;
 use App\Models\User;
 use App\Models\LokasiTk1;
 use App\Http\Controllers\Controller;

class LokasiTk1Controller extends Controller
{
    /**
     * Function untuk menampilkan daftar lokasi tingkat I.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-1/daftar
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

        // ambil daftar lokasi tingkat I
        $daftar = LokasiTk1::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat I";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat I";
        $subpage = "Daftar";
        $page_url = "#";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_1.daftar')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah lokasi tingkat I.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-1/tambah
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
        $judul = "Lokasi Tingkat I";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat I";
        $subpage = "Tambah";
        $page_url = "/master-data/lokasi-tk-1/daftar";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_1.tambah')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ;
    }

    /**
     * Function untuk menambahkan Lokasi Tingkat I.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-1/tambah
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
            'kode' => 'unique:lokasi_tk_1,kode'
        ],[
            // pesan error
            'kode.unique' => 'Kode yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel lokasi tingkat I
            $lokasi_tk_1 = LokasiTk1::create([
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
            return redirect('/master-data/lokasi-tk-1/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-1/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data Lokasi Tingkat I berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-1/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id Lokasi Tingkat I
        $id = $request->id;

        // ambil data Lokasi Tingkat I
        $lokasi_tk_1 = LokasiTk1::find($id);

        // ambil data relationship
        $created_by = User::find($lokasi_tk_1->created_by);
        $updated_by = User::find($lokasi_tk_1->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'lokasi_tk_1'=>$lokasi_tk_1,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit Lokasi Tingkat I.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-1/edit
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

        // ambil data lokasi tingkat I
        $lokasi_tk_1 = LokasiTk1::where('id', $id)
            ->first();

        // jika lokasi tingkat I dengan id tersebut tidak ada
        if($lokasi_tk_1 == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/lokasi-tk-1/daftar')->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat I";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat I";
        $subpage = "Edit Data";
        $page_url = "/master-data/lokasi-tk-1/daftar";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_1.edit')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ;
    }

    /**
     * Function untuk mengubah data lokasi tingkat I.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-1/edit
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

        try{
            // update data lokasi tingkat I di tabel lokasi_tk_1
            LokasiTk1::where('id', $request->id)
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
            return redirect('/master-data/lokasi-tk-1/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-1/daftar')->with('notif', 'edit_sukses');
    }
}
