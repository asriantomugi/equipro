<?php

namespace App\Http\Controllers\Fasilitas;

/**
 * PeralatanController.php
 * Controller ini digunakan untuk menangani proses CRUD peralatan
 *
 * @author Mugi Asrianto
 */

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Database\QueryException;
 use Illuminate\Support\MessageBag;
 use App\Models\User;
 use App\Models\Peralatan;
 use App\Models\JenisAlat;
 use App\Models\Perusahaan;
 use App\Http\Controllers\Controller;

class PeralatanController extends Controller
{
    /**
     * Function untuk menampilkan daftar peralatan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/peralatan/daftar
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

        // ambil daftar peralatan
        $daftar = Peralatan::all();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Peralatan";
		$module = "Fasilitas";
        $menu = "Peralatan";
        $menu_url = "/fasilitas/peralatan/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('fasilitas.peralatan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah peralatan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/peralatan/tambah
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

        // ambil daftar jenis alat yang aktif
        $jenis_alat = JenisAlat::where('status', 1)->get();

        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Peralatan";
		$module = "Fasilitas";
        $menu = "Peralatan";
        $menu_url = "/fasilitas/peralatan/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('fasilitas.peralatan.tambah')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('jenis_alat', $jenis_alat)
        ->with('perusahaan', $perusahaan)
        ;
    }


    /**
     * Function untuk menambahkan peralatan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/peralatan/tambah
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
            'kode' => 'unique:peralatan,kode'
        ],[
            // pesan error
            'kode.unique' => 'Kode yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel peralatan
            $peralatan = Peralatan::create([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'merk' => strtoupper($request->merk),
                'tipe' => strtoupper($request->tipe),
                'model' => strtoupper($request->model),
                'serial_number' => strtoupper($request->serial_number),
                'thn_produksi' => $request->thn_produksi,
                'thn_pengadaan' => $request->thn_pengadaan,
                'keterangan' => strtoupper($request->keterangan),
                'sewa' => $request->sewa,
                'jenis_id' => $request->jenis,
                'perusahaan_id' => $request->perusahaan,
                'kondisi' => $request->kondisi,
                'status' => 1, // aktif
                'flag_layanan' => 0, // default = 0
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/peralatan/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/fasilitas/peralatan/daftar')->with('notif', 'tambah_sukses');
    }


    /**
     * Menampilkan JSON data peralatan berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/peralatan/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id peralatan
        $id = $request->id;

        // ambil data peralatan
        $peralatan = Peralatan::find($id);

        // ambil data relationship
        $jenis = JenisAlat::find($peralatan->jenis_id);
        $perusahaan = Perusahaan::find($peralatan->perusahaan_id);
        $created_by = User::find($peralatan->created_by);
        $updated_by = User::find($peralatan->updated_by);

        //dd($peralatan->jenis->nama);

        //return response()->json($user);
        return response()->json([
            'peralatan'=>$peralatan,
            'jenis'=>$jenis,
            'perusahaan'=>$perusahaan,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }


    /**
     * Function untuk menampilkan halaman form edit peralatan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/peralatan/edit/{id}
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

        // ambil data peralatan
        $peralatan = Peralatan::where('id', $id)
            ->first();

        // jika peralatan dengan id tersebut tidak ada
        if($peralatan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/peralatan/daftar')->with('notif', 'item_null');
        }

        // ambil daftar jenis alat yang aktif
        $jenis_alat = JenisAlat::where('status', 1)->get();

        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Peralatan";
		$module = "Fasilitas";
        $menu = "Peralatan";
        $menu_url = "/fasilitas/peralatan/daftar";
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('fasilitas.peralatan.edit')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('peralatan', $peralatan)
        ->with('jenis_alat', $jenis_alat)
        ->with('perusahaan', $perusahaan)
        ;
    }


    /**
     * Function untuk mengubah data peralatan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/peralatan/edit
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
        // ambil data peralatan sebelumnya berdasarkan id
        $peralatan = Peralatan::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan kode
        if($peralatan->kode != strtoupper($request->kode)){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = Peralatan::where('kode', strtoupper($request->kode))->first();
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
            // update data peralatan di tabel peralatan
            Peralatan::where('id', $request->id)
            ->update([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'merk' => strtoupper($request->merk),
                'tipe' => strtoupper($request->tipe),
                'model' => strtoupper($request->model),
                'serial_number' => strtoupper($request->serial_number),
                'thn_produksi' => $request->thn_produksi,
                'thn_pengadaan' => $request->thn_pengadaan,
                'keterangan' => strtoupper($request->keterangan),
                'sewa' => $request->sewa,
                'jenis_id' => $request->jenis,
                'perusahaan_id' => $request->perusahaan,
                'kondisi' => $request->kondisi,
                'status' => $request->status,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/peralatan/daftar')->with('notif', 'edit_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/peralatan/daftar')->with('notif', 'edit_sukses');
    }

}