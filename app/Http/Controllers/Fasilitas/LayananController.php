<?php

namespace App\Http\Controllers\Fasilitas;

/**
 * PeralatanController.php
 * Controller ini digunakan untuk menangani proses CRUD Layanan
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
 use App\Models\Fasilitas;
 use App\Models\Perusahaan;
 use App\Models\Layanan;
 use App\Models\JenisAlat;
 use App\Models\LokasiTk1;
 use App\Models\LokasiTk2;
 use App\Models\LokasiTk3;
 use App\Models\DaftarPeralatanLayanan;
 use App\Http\Controllers\Controller;

class LayananController extends Controller
{
    /**
     * Function untuk menampilkan daftar Layanan.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/daftar
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

        // ambil daftar layanan
        $daftar = Layanan::all();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = "/fasilitas/layanan/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan form tambah layanan step 1.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/tambah/step1
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep1()
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
        
        // ambil daftar fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        // ambil daftar lokasi tingkat I yang aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = "/fasilitas/layanan/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.tambah.step1')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('fasilitas', $fasilitas)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ;
    }


    /**
     * Function untuk menampilkan form tambah layanan step 1 (tombol Back).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/tambah/step1/back/{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep1Back($id)
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

        // ambil data layanan dengan status draft berdasarkan id
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }
        
        // ambil daftar fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        // ambil daftar lokasi tingkat I yang aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();

        // ambil daftar lokasi tingkat II yang aktif
        $lokasi_tk_2 = LokasiTk2::where('lokasi_tk_1_id', $layanan->lokasi_tk_1_id)
            ->where('status', 1)
            ->get();

        // ambil daftar lokasi tingkat III yang aktif
        $lokasi_tk_3 = LokasiTk3::where('lokasi_tk_2_id', $layanan->lokasi_tk_2_id)
            ->where('status', 1)
            ->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = "/fasilitas/layanan/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.tambah.step1_back')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('layanan', $layanan)
        ->with('fasilitas', $fasilitas)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ->with('lokasi_tk_2', $lokasi_tk_2)
        ->with('lokasi_tk_3', $lokasi_tk_3)
        ;
    }

    /**
     * Function untuk menambahkan layanan step 1.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/tambah/step1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tambahStep1(Request $request)
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

        // ===================== CEK DUPLIKASI KODE ============================
        // jika ada, cek apakah kode yang baru sudah terdaftar
        $cekKode = Layanan::where('kode', $request->kode)->first();
        // jika kode sudah terdaftar
        if($cekKode != null){
            // kembali ke halaman edit dan tampilkan pesan error
            return redirect()->back()->with('notif', 'kode_terdaftar')->withInput();
        }
        // ===================== END OF CEK DUPLIKASI KODE =====================

        try{
            // tambah row di tabel layanan
            $layanan = Layanan::create([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'fasilitas_id' => $request->fasilitas,
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'lokasi_tk_2_id' => $request->lokasi_tk_2,
                'lokasi_tk_3_id' => $request->lokasi_tk_3,
                'kondisi' => $request->kondisi,
                'status' => 2, // draft
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            // dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        return redirect('/fasilitas/layanan/tambah/step2/'.$layanan->id)->with('notif', 'simpan_sukses');
    }


    /**
     * Function untuk mengubah data layanan di step 1 (tombol back).
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/tambah/step1/back
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tambahStep1Back(Request $request)
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
        // ambil data layanan sebelumnya berdasarkan id
        $layanan = Layanan::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan kode
        if($layanan->kode != strtoupper($request->kode)){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = Layanan::where('kode', strtoupper($request->kode))->first();
            // jika kode sudah terdaftar
            if($cekKode != null){
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->with('notif', 'kode_terdaftar')->withInput();
            }
        }
        // ===================== END OF CEK DUPLIKASI KODE =====================
        
        try{
            // update data layanan di tabel layanan
            Layanan::where('id', $request->id)
            ->update([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'fasilitas_id' => $request->fasilitas,
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'lokasi_tk_2_id' => $request->lokasi_tk_2,
                'lokasi_tk_3_id' => $request->lokasi_tk_3,
                'kondisi' => $request->kondisi,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/tambah/step1/back/'.$layanan->id)->with('notif', 'simpan_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/layanan/tambah/step2/'.$layanan->id)->with('notif', 'simpan_sukses');
    }


    /**
     * Function untuk menampilkan form tambah layanan step 2 (daftar peralatan).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/tambah/step2/{$id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep2($id)
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

        // ambil data layanan dengan status draft dari form tambah 1
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->where('status', 1)
            ->get();

        // ambil daftar peralatan tersedia
        $daftar_tersedia = Peralatan::where('status', 1)->get();

        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();

        // ambil daftar jenis alat yang aktif
        $jenis = JenisAlat::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = "/fasilitas/layanan/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.tambah.step2')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('layanan', $layanan)
        ->with('daftar_peralatan', $daftar_peralatan)
        ->with('daftar_tersedia', $daftar_tersedia)
        ->with('perusahaan', $perusahaan)
        ->with('jenis', $jenis)
        ;
    }


    /**
     * Function untuk menampilkan form tambah layanan step 3 (review).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/tambah/step3/{$id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep3($id)
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

        // ambil data layanan dengan status draft dari form tambah 2
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->where('status', 1)
            ->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = "/fasilitas/layanan/daftar";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.tambah.step3')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('layanan', $layanan)
        ->with('daftar_peralatan', $daftar_peralatan)
        ;
    }


    /**
     * Function untuk memproses tambah layanan step 3 (review).
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/tambah/step3
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tambahStep3(Request $request)
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

        // ambil data layanan dengan status draft dari form tambah 2
        $layanan = Layanan::where('id', $request->id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }
        
        try{
            // update data layanan di tabel layanan
            Layanan::where('id', $request->id)
            ->update([
                'status' => config('constants.status_layanan.aktif'), // status layanan = aktif
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/tambah/step3/'.$layanan->id)->with('notif', 'tambah_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/layanan/daftar')->with('notif', 'tambah_sukses');
    }


    /**
     * Function untuk menghapus layanan yang berstatus DRAFT.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/hapus
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hapus(Request $request)
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

        // ambil data layanan dengan status draft dari modal hapus
        $layanan = Layanan::where('id', $request->id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan kirim notif
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }
        
        try{
            // Ambil semua ID peralatan dari tabel daftar_peralatan_layanan berdasarkan layanan_id
            $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $request->id)
                ->pluck('peralatan_id'); // hasil: Collection dari peralatan_id

            // Update flag_layanan di tabel peralatan berdasarkan ID yang diambil
            Peralatan::whereIn('id', $daftar_peralatan)
                ->update(['flag_layanan' => 0]); // peralatan sedang tidak terpasang di layanan


            // hapus data peralatan di tabel daftar peralatan layanan
            DaftarPeralatanLayanan::where('layanan_id', $request->id)
                ->delete();

            // hapus layanan dari tabel layanan
            Layanan::where('id', $request->id)
                ->delete();
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'hapus_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/layanan/daftar')->with('notif', 'hapus_sukses');
    }


    /**
     * Function untuk memproses filter daftar peralatan tersedia.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/peralatan/filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function peralatanFilter(Request $request)
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
        
        // buat query untuk mengambil daftar peralatan
        $query = Peralatan::query();
        // ambil peralatan dengan status aktif dan belum ditambahkan ke Layanan manapun
        $query->where('status', 1)->where('flag_layanan', 0);

        // jika field jenis alat terpilih
        if ($request->filled('jenis')) {
            $query->where('jenis_id', $request->jenis);
        }

        // jika field kondisi terpilih
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // jika field status kepemilikan terpilih
        if ($request->filled('sewa')) {
            $query->where('sewa', $request->sewa);
        }

        // jika field perusahaan pemilik terpilih
        if ($request->filled('perusahaan')) {
            $query->where('perusahaan_id', $request->perusahaan);
        }

        // proses query
        $daftar_tersedia = $query->get();

        // kirim hasil query ke view
        return view('fasilitas.layanan.tambah.modal_daftar_peralatan', compact('daftar_tersedia'));
    }


    /**
     * Function untuk memproses tambah peralatan ke layanan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/peralatan/tambah
     *
     * @param  layanan_id
     * @param  peralatan_id
     * @return \Illuminate\Http\Response
     */
    public function tambahPeralatan(Request $request)
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

        $validasi = $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'peralatan_id' => 'required|exists:peralatan,id',
        ]);

        // cek apakah status peralatan aktif
        $status = Peralatan::find($request->peralatan_id)->status;
        if (!$status || $status == 0) {
           // kirim pesan gagal melalui JSON
           return response()->json(['success' => false, 'reason' => 'Peralatan tidak aktif'], 400);
        }
        
        try{
            // tambah row di tabel daftar peralatan layanan
            DaftarPeralatanLayanan::create([
                'layanan_id' => strtoupper($request->layanan_id),
                'peralatan_id' => strtoupper($request->peralatan_id),
                'kondisi' => config('constants.kondisi_peralatan_layanan.beroperasi'), // default beroperasi
                'status' => 1, // default aktif
                'created_by' => session()->get('id')
            ]);

            // update flag_layanan menjadi 1, sebagai penanda bahwa peralatan sudah ditambahkan ke layanan
            Peralatan::where('id', $request->peralatan_id)
            ->update([
                'flag_layanan' => 1,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kirim pesan error ke file storage/logs/laravel.log
            // \Log::error('Gagal tambah peralatan ke layanan: '.$ex->getMessage());
            // kirim pesan gagal melalui JSON
            return response()->json(['success' => false, 'reason' => 'Gagal menambahkan peralatan'], 400);
        }

        return response()->json(['success' => true]);
    }


    /**
     * Function untuk menghapus peralatan dari daftar peralatan di layanan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/peralatan/hapus
     *
     * @param  peralatan_id
     * @param  layanan_id
     * @return \Illuminate\Http\Response
     */
    public function hapusPeralatan(Request $request)
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

        // ambil data layanan sebelumnya berdasarkan id
        $layanan = Layanan::where('id', $request->layanan_id)
            ->first();
        // cek apakah layanan ada
        if($layanan == null){
            return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
        }
        // cek apakah ada data peralatan tsb
        $peralatan = DaftarPeralatanLayanan::where('layanan_id', $request->layanan_id)
            ->where('peralatan_id', $request->peralatan_id)
            ->first();
        if($peralatan == null){
             return redirect('/fasilitas/layanan/tambah/step2/'.$request->layanan_id)->with('notif', 'item_null');
        }

        try{
            // hapus data peralatan di tabel daftar peralatan layanan
            DaftarPeralatanLayanan::where('layanan_id', $request->layanan_id)
                ->where('peralatan_id', $request->peralatan_id)
                ->delete();

            // update flag_layanan di tabel peralatan
            Peralatan::where('id', $request->peralatan_id)
            ->update([
                'flag_layanan' => 0, // peralatan sedang tidak terpasang pada layanan
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/tambah/step2/'.$request->layanan_id)->with('notif', 'hapus_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/layanan/tambah/step2/'.$request->layanan_id)->with('notif', 'hapus_sukses');
    }


    /**
     * Function untuk mengubah data peralatan di daftar peralatan di layanan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/peralatan/edit
     *
     * @param  id
     * @param  peralatan_id
     * @return \Illuminate\Http\Response
     */
    public function editPeralatan(Request $request)
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

        // cek apakah ada data peralatan tsb
        $peralatan = DaftarPeralatanLayanan::where('id', $request->id)
            ->where('peralatan_id', $request->peralatan_id)
            ->first();
        if($peralatan == null){
             return redirect('/fasilitas/layanan/tambah/step2/'.$request->layanan_id)->with('notif', 'item_null');
        }

        try{
            // update data peralatan di tabel peralatan layanan
            DaftarPeralatanLayanan::where('id', $request->id)
            ->update([
                'ip_address' => $request->ip_address,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect('/fasilitas/layanan/tambah/step2/'.$peralatan->layanan_id)->with('notif', 'edit_gagal');
        }

        // jika proses update berhasil
        return redirect('/fasilitas/layanan/tambah/step2/'.$peralatan->layanan_id)->with('notif', 'edit_sukses');
    }


    /**
     * Function untuk mengubah data layanan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/edit
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
        // ambil data layanan sebelumnya berdasarkan id
        $layanan = Layanan::where('id', $request->id)
            ->first();
        // cek apakah ada perubahan kode
        if($layanan->kode != $request->kode){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = Layanan::where('kode', $request->kode)->first();
            // jika kode sudah terdaftar
            if($cekKode != null){
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->with('notif', 'kode_terdaftar')->withInput();
            }
        }
        // ===================== END OF CEK DUPLIKASI KODE =====================
        
        try{

            // update data layanan di tabel layanan
            Peralatan::where('id', $request->id)
            ->update([
                'kode' => strtoupper($request->kode),
                'nama' => strtoupper($request->nama),
                'fasilitas_id' => $request->fasilitas,
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'lokasi_tk_2_id' => $request->lokasi_tk_2,
                'lokasi_tk_3_id' => $request->lokasi_tk_3,
                'kondisi' => $request->kondisi,
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
