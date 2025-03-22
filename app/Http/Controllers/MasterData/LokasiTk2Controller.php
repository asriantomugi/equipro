<?php

namespace App\Http\Controllers\MasterData;

/**
 * LokasiTk2Controller.php
 * Controller ini digunakan untuk menangani proses CRUD lokasi tingkat II
 *
 * @author Mugi Asrianto
 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\LokasiTk1;
use App\Models\LokasiTk2;
use App\Http\Controllers\Controller;

class LokasiTk2Controller extends Controller
{
    /**
     * Function untuk menampilkan daftar lokasi tingkat II.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-2/daftar/
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

        // ambil daftar lokasi tingkat II
        $daftar = LokasiTk2::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat II";
        $module = "Master Data";
        $menu = "Lokasi Tingkat II";
        $menu_url = "/master-data/lokasi-tk-2/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_2.daftar')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('submenu', $submenu)
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah lokasi tingkat II.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-2/tambah
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

        // ambil daftar lokasi tingkat I yang berstatus aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat II";
        $module = "Master Data";
        $menu = "Lokasi Tingkat II";
        $menu_url = "/master-data/lokasi-tk-2/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_2.tambah')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('submenu', $submenu)
        ->with('menu_url', $menu_url)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ;
    }

    /**
     * Function untuk menambahkan Lokasi Tingkat II.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-2/tambah
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
            'kode' => 'unique:lokasi_tk_2,kode'
        ],[
            // pesan error
            'kode.unique' => 'Kode yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel lokasi tingkat II
            $lokasi_tk_2 = LokasiTk2::create([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'status' => 1, // aktif
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/lokasi-tk-2/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-2/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data Lokasi Tingkat II berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-2/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id Lokasi Tingkat II
        $id = $request->id;

        // ambil data Lokasi Tingkat II
        $lokasi_tk_2 = LokasiTk2::find($id);

        // ambil data relationship
        $lokasi_tk_1 = LokasiTk1::find($lokasi_tk_2->lokasi_tk_1_id);
        $created_by = User::find($lokasi_tk_2->created_by);
        $updated_by = User::find($lokasi_tk_2->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'lokasi_tk_1'=>$lokasi_tk_1,
            'lokasi_tk_2'=>$lokasi_tk_2,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit Lokasi Tingkat II.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-2/edit
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

        // ambil data lokasi tingkat II
        $lokasi_tk_2 = LokasiTk2::where('id', $id)
            ->first();

        // jika lokasi tingkat II dengan id tersebut tidak ada
        if($lokasi_tk_2 == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/lokasi-tk-2/daftar')->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat II";
        $module = "Master Data";
        $menu = "Lokasi Tingkat II";
        $menu_url = "/master-data/lokasi-tk-2/daftar";
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_2.edit')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('submenu', $submenu)
        ->with('menu_url', $menu_url)
        ->with('lokasi_tk_2', $lokasi_tk_2)
        ;
    }

    /**
     * Function untuk mengubah data lokasi tingkat II.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-2/edit
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
            // update data lokasi tingkat II di tabel lokasi_tk_2
            LokasiTk2::where('id', $request->id)
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
            return redirect('/master-data/lokasi-tk-2/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-2/daftar')->with('notif', 'edit_sukses');
    }

    /**
     * Menampilkan JSON daftar lokasi tingkat II berdasarkan id lokasi tingkat I
     * 
     * Akses:
     * - Super Admin
     * - Airport Operation
     * 
     * Method: POST
     * URL: /json/lokasi-tk-2/daftar
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function daftarJson(Request $request)
    {
        // ambil id lokasi tingkat I
        $lokasi_tk_1_id = $request->input('lokasi_tk_1_id');

        // ambil daftar lokasi tingkat II berdasarkan id lokasi tingkat I
        $daftar = LokasiTk2::where('lokasi_tk_1_id', $lokasi_tk_1_id)
            ->where('status', 1) //status aktif
            ->orderBy('kode', 'asc')
            ->get();

        return response()->json($daftar);
    }

}
