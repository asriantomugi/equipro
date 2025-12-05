<?php

namespace App\Http\Controllers\MasterData;

/**
 * PerusahaanController.php
 * Controller ini digunakan untuk menangani proses CRUD perusahaan
 *
 * @author Mugi Asrianto
 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\QueryException;
use Illuminate\Support\MessageBag;
use App\Models\User;
use App\Models\Perusahaan;
use App\Http\Controllers\Controller;

class PerusahaanController extends Controller
{
    /**
     * Function untuk menampilkan daftar perusahaan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/perusahaan/daftar
     *
     * @return \Illuminate\Http\Response
     */
    public function daftar()
    {
        // ambil daftar perusahaan
        $daftar = Perusahaan::all();
       
        // variabel untuk dikirim ke halaman view
        $judul = "Perusahaan";
		$module = "Master Data";
        $menu = "Perusahaan";
        $menu_url = route('master_data.perusahaan.daftar');
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('master_data.perusahaan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah perusahaan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/perusahaan/tambah
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambah()
    {
        // variabel untuk dikirim ke halaman view
        $judul = "Perusahaan";
		$module = "Master Data";
        $menu = "Perusahaan";
        $menu_url = route('master_data.perusahaan.daftar');
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('master_data.perusahaan.tambah')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ;
    }

    /**
     * Function untuk menambahkan perusahaan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/perusahaan/tambah
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tambah(Request $request)
    {
        // melakukan validasi input dari form
        // jika tidak sesuai parameter, maka akan muncul error
        $validasi  = $request->validate([
            // parameter validasi
            'email' => 'unique:perusahaan,email',
            'nama' => 'unique:perusahaan,nama',
        ],[
            // pesan error
            'email.unique' => 'Email yang dimasukkan sudah terdaftar',
            'nama.unique' => 'Nama yang dimasukkan sudah terdaftar'
        ]);

        try{
            // tambah row di tabel perusahaan
            $perusahaan = Perusahaan::create([
                'email' => strtolower($request->email),
                'nama' => strtoupper($request->nama),
                'alamat' => strtoupper($request->alamat),
                'telepon' => strtoupper($request->telepon),
                'status' => 1, // aktif
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/master-data/perusahaan/daftar')->with('notif', 'tambah_gagal');
            return redirect()
                ->route('master_data.perusahaan.daftar')
                ->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        // return redirect('/master-data/perusahaan/daftar')->with('notif', 'tambah_sukses');
        return redirect()
                ->route('master_data.perusahaan.daftar')
                ->with('notif', 'tambah_sukses');
    }

    /**
     * Menampilkan JSON data perusahaan berdasarkan id
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/perusahaan/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil id perusahaan
        $id = $request->id;

        // ambil data perusahaan
        $perusahaan = Perusahaan::find($id);

        // ambil data relationship
        $created_by = User::find($perusahaan->created_by);
        $updated_by = User::find($perusahaan->updated_by);

        //dd($perusahaan->nama);

        //return response()->json($user);
        return response()->json([
            'perusahaan'=>$perusahaan,
            'created_by'=>$created_by, 
            'updated_by'=>$updated_by
        ]);
    }

    /**
     * Function untuk menampilkan halaman form edit perusahaan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /master-data/perusahaan/edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formEdit($id)
    {
        // ambil data perusahaan
        $perusahaan = Perusahaan::where('id', $id)
            ->first();

        // jika perusahaan dengan id tersebut tidak ada
        if($perusahaan == null){
            // kembali ke halaman daftar dan kirim notif
            // return redirect('/master-data/perusahaan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('master_data.perusahaan.daftar')
                ->with('notif', 'item_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Perusahaan";
		$module = "Master Data";
        $menu = "Perusahaan";
        $menu_url = route('master_data.perusahaan.daftar');
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('master_data.perusahaan.edit')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('perusahaan', $perusahaan)
        ;
    }

    /**
     * Function untuk mengubah data perusahaan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /master-data/perusahaan/edit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        // ===================== CEK DUPLIKASI NAMA DAN EMAIL ============================
        // ambil data perusahaan sebelumnya berdasarkan id
        $perusahaan = Perusahaan::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan nama
        if($perusahaan->nama != $request->nama){
            // jika ada, cek apakah nama yang baru sudah terdaftar
            $cekNama = Perusahaan::where('nama', $request->nama)->first();
            // jika nama sudah terdaftar
            if($cekNama != null){
                // buat pesan error
                $errors = new MessageBag(['nama' => 'Nama yang dimasukkan sudah terdaftar']);
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->withErrors($errors)->withInput();
            }
        }

        // cek apakah ada perubahan email
        if($perusahaan->email != $request->email){
            // jika ada, cek apakah email yang baru sudah terdaftar
            $cekEmail = Perusahaan::where('email', $request->email)->first();
            // jika email sudah terdaftar
            if($cekEmail != null){
                // buat pesan error
                $errors = new MessageBag(['email' => 'Email yang dimasukkan sudah terdaftar']);
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->withErrors($errors)->withInput();
            }
        }
        // ===================== END OF CEK DUPLIKASI NAMA DAN EMAIL =====================

        try{
            // update data perusahaan di tabel Perusahaan
            Perusahaan::where('id', $request->id)
            ->update([
                'nama' => strtoupper($request->nama),
                'email' => strtolower($request->email),
                'alamat' => strtoupper($request->alamat),
                'telepon' => strtoupper($request->telepon),
                'status' => $request->status,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/master-data/perusahaan/daftar')->with('notif', 'edit_gagal');
            return redirect()
                ->route('master_data.perusahaan.daftar')
                ->with('notif', 'edit_gagal');
        }

        // jika proses insert berhasil
        // return redirect('/master-data/perusahaan/daftar')->with('notif', 'edit_sukses');
        return redirect()
                ->route('master_data.perusahaan.daftar')
                ->with('notif', 'edit_sukses');
    }
}
