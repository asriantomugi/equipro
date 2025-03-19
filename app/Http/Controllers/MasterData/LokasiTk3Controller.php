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
use App\Models\LokasiTk3;
use App\Http\Controllers\Controller;

class LokasiTk3Controller extends Controller
{
    /**
     * Function untuk menampilkan daftar lokasi tingkat III.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-3/daftar/
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

        // ambil daftar lokasi tingkat III
        $daftar = LokasiTk3::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat III";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat III";
        $subpage = "Daftar";
        $page_url = "#";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_3.daftar')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah lokasi tingkat III.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-3/tambah
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
        $judul = "Lokasi Tingkat III";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat III";
        $subpage = "Tambah";
        $page_url = "/master-data/lokasi-tk-3/daftar";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_3.tambah')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ;
    }

    /**
     * Function untuk menambahkan Lokasi Tingkat III.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-3/tambah
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
            'kode' => 'unique:lokasi_tk_3,kode'
        ],[
            // pesan error
            'kode.unique' => 'Kode yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel lokasi tingkat III
            $lokasi_tk_3 = LokasiTk3::create([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'status' => 1, // aktif
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'lokasi_tk_2_id' => $request->lokasi_tk_2,
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/master-data/lokasi-tk-3/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-3/daftar')->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data Lokasi Tingkat III berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-3/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id Lokasi Tingkat III
        $id = $request->id;

        // ambil data Lokasi Tingkat III
        $lokasi_tk_3 = LokasiTk3::find($id);

        // ambil data relationship
        $lokasi_tk_1 = LokasiTk1::find($lokasi_tk_3->lokasi_tk_1_id);
        $lokasi_tk_2 = LokasiTk2::find($lokasi_tk_3->lokasi_tk_2_id);
        $created_by = User::find($lokasi_tk_2->created_by);
        $updated_by = User::find($lokasi_tk_2->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'lokasi_tk_1'=>$lokasi_tk_1,
            'lokasi_tk_2'=>$lokasi_tk_2,
            'lokasi_tk_3'=>$lokasi_tk_3,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit Lokasi Tingkat III.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/lokasi-tk-3/edit
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

        // ambil data lokasi tingkat III
        $lokasi_tk_3 = LokasiTk3::where('id', $id)
            ->first();

        // jika lokasi tingkat II dengan id tersebut tidak ada
        if($lokasi_tk_3 == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/master-data/lokasi-tk-3/daftar')->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Lokasi Tingkat III";
        $menu = "Lokasi";
        $page = "Lokasi Tingkat III";
        $subpage = "Edit Data";
        $page_url = "/master-data/lokasi-tk-3/daftar";
        
        // menampilkan halaman view
        return view('master_data.lokasi_tk_3.edit')
        ->with('judul', $judul)
        ->with('menu', $menu)
        ->with('page', $page)
        ->with('subpage', $subpage)
        ->with('page_url', $page_url)
        ->with('lokasi_tk_3', $lokasi_tk_3)
        ;
    }

    /**
     * Function untuk mengubah data lokasi tingkat III.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/lokasi-tk-3/edit
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
            // update data lokasi tingkat III di tabel lokasi_tk_3
            LokasiTk3::where('id', $request->id)
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
            return redirect('/master-data/lokasi-tk-3/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        return redirect('/master-data/lokasi-tk-3/daftar')->with('notif', 'edit_sukses');
    }
}
