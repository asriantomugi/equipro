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
        // ambil daftar layanan
        $daftar = Layanan::all();

        // ambil data untuk form filter
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ->with('fasilitas', $fasilitas)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ->with('fasilitas_id', '')
        ->with('lokasi_tk_1_id', '')
        ->with('lokasi_tk_2_id', '')
        ->with('lokasi_tk_3_id', '')
        ->with('kondisi', '')
        ->with('status', '')
        ;
    }


    /**
     * Function untuk memproses filter daftar layanan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {        
        // ambil parameter
        $fasilitas_id = $request->fasilitas;
        $lokasi_tk_1_id = $request->lokasi_tk_1;
        $lokasi_tk_2_id = $request->lokasi_tk_2;
        $lokasi_tk_3_id = $request->lokasi_tk_3;
        $kondisi = $request->kondisi;
        $status = $request->status;

        // buat query untuk mengambil daftar layanan
        $query = Layanan::query();

        // jika field fasilitas terpilih
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas_id', $fasilitas_id);
        }

        // jika field lokasi tingkat I terpilih
        if ($request->filled('lokasi_tk_1')) {
            $query->where('lokasi_tk_1_id', $lokasi_tk_1_id);
        }

        // jika field lokasi tingkat II terpilih
        if ($request->filled('lokasi_tk_2')) {
            $query->where('lokasi_tk_2_id', $lokasi_tk_2_id);
        }

        // jika field lokasi tingkat III terpilih
        if ($request->filled('lokasi_tk_3')) {
            $query->where('lokasi_tk_3_id', $lokasi_tk_3_id);
        }

        // jika field kondisi terpilih
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $kondisi);
        }

        // jika field status terpilih
        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        // proses query
        $daftar = $query->get();

        // ambil data untuk form filter
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
        $submenu = "Daftar";

        // menampilkan halaman view
        return view('fasilitas.layanan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ->with('fasilitas', $fasilitas)
        ->with('lokasi_tk_1', $lokasi_tk_1)
        ->with('fasilitas_id', $fasilitas_id)
        ->with('lokasi_tk_1_id', $lokasi_tk_1_id)
        ->with('lokasi_tk_2_id', $lokasi_tk_2_id)
        ->with('lokasi_tk_3_id', $lokasi_tk_3_id)
        ->with('kondisi', $kondisi)
        ->with('status', $status)
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
        // ambil daftar fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        // ambil daftar lokasi tingkat I yang aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
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
        // ambil data layanan dengan status draft berdasarkan id
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
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
        $menu_url = route('fasilitas.layanan.daftar');
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
        // ===================== CEK DUPLIKASI KODE ============================
        // jika ada, cek apakah kode yang baru sudah terdaftar
        $cekKode = Layanan::where('kode', $request->kode)->first();
        // jika kode sudah terdaftar
        if($cekKode != null){
            // kembali ke halaman tambah step 1 dan tampilkan pesan error
            return redirect()->back()->with('notif', 'kode_terdaftar');
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
                'status' => config('constants.status_layanan.draft'), // status layanan draft
                'created_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            // dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'tambah_gagal');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'tambah_gagal');
        }

        // jika proses insert berhasil
        // diteruskan ke halaman tambah step 2 dan tampilkan pesan sukses
        // return redirect('/fasilitas/layanan/tambah/step2/'.$layanan->id)->with('notif', 'simpan_sukses');
        return redirect()
            ->route('fasilitas.layanan.tambah.step2.form', $layanan->id)
            ->with('notif', 'simpan_sukses');
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
        // ambil data layanan dengan status draft berdasarkan id
        $layanan = Layanan::where('id', $request->id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ===================== CEK DUPLIKASI KODE ============================
        // cek apakah ada perubahan kode
        if($layanan->kode != strtoupper($request->kode)){
            // jika ada, cek apakah kode yang baru sudah terdaftar
            $cekKode = Layanan::where('kode', strtoupper($request->kode))->first();
            // jika kode sudah terdaftar
            if($cekKode != null){
                // kembali ke halaman edit dan tampilkan pesan error
                return redirect()->back()->with('notif', 'kode_terdaftar');
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
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman tambah step 1 back dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/tambah/step1/back/'.$layanan->id)->with('notif', 'simpan_gagal');
            return redirect()
                ->route('fasilitas.layanan.tambah.step1.back.form', $layanan->id)
                ->with('notif', 'simpan_gagal');
        }

        // jika proses update berhasil dan tampilkan pesan sukses
        // diteruskan ke halaman tambah step 2
        // return redirect('/fasilitas/layanan/tambah/step2/'.$layanan->id)->with('notif', 'simpan_sukses');
        return redirect()
            ->route('fasilitas.layanan.tambah.step2.form', $layanan->id)
            ->with('notif', 'simpan_sukses');
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
        // ambil data layanan dengan status draft dari form tambah 1
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();

        // ambil daftar jenis alat yang aktif
        $jenis = JenisAlat::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
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
        // ambil data layanan dengan status draft dari form tambah 2
        $layanan = Layanan::where('id', $id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // jika daftar peralatan masih kosong
        if($daftar_peralatan->isEmpty()){
            // kembali ke halaman layanan tambah step 2 dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.tambah.step2.form', $layanan->id)
                ->with('notif', 'peralatan_null');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
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
        // ambil data layanan dengan status draft dari form tambah 2
        $layanan = Layanan::where('id', $request->id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // jika daftar peralatan masih kosong
        if($daftar_peralatan->isEmpty()){
            // kembali ke halaman layanan tambah step 2 dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.tambah.step2.form', $layanan->id)
                ->with('notif', 'peralatan_null');
        }
        
        try{
            // update status layanan di tabel layanan
            Layanan::where('id', $request->id)
            ->update([
                'status' => config('constants.status_layanan.aktif'), // status layanan = aktif
                'status' => config('constants.kondisi_layanan.serviceable'), // kondisi layanan = serviceable
                'updated_by' => session()->get('id')
            ]);

            // update data peralatan dari yang baru ditambahkan
            $daftar_peralatan_id = DaftarPeralatanLayanan::where('layanan_id', $request->id)
                ->update(['kondisi' => config('constants.kondisi_peralatan_layanan.beroperasi')]); // kondisi beroperasi
        }
        // jika proses update gagal
        catch(QueryException $ex){
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/tambah/step3/'.$layanan->id)->with('notif', 'tambah_gagal');
            return redirect()
                ->route('fasilitas.layanan.tambah.step3.form', $layanan->id)
                ->with('notif', 'aktif_gagal');
        }

        // jika proses update berhasil
        // kembali ke halaman daftar dan tampilkan pesan sukses
        // return redirect('/fasilitas/layanan/daftar')->with('notif', 'tambah_sukses');
        return redirect()
            ->route('fasilitas.layanan.daftar')
            ->with('notif', 'aktif_sukses');
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
        // ambil data layanan dengan status draft dari modal hapus
        $layanan = Layanan::where('id', $request->id)
            ->where('status', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }
        
        try{
            // Ambil semua ID peralatan dari tabel daftar_peralatan_layanan berdasarkan layanan_id
            $daftar_peralatan_id = DaftarPeralatanLayanan::where('layanan_id', $request->id)
                ->pluck('peralatan_id'); // hasil: Collection dari peralatan_id

            // Update flag_layanan di tabel peralatan berdasarkan ID yang diambil
            Peralatan::whereIn('id', $daftar_peralatan_id)
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
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'hapus_gagal');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'hapus_gagal');
        }

        // jika proses update berhasil
        // kembali ke halaman daftar dan tampilkan pesan sukses
        // return redirect('/fasilitas/layanan/daftar')->with('notif', 'hapus_sukses');
        return redirect()
            ->route('fasilitas.layanan.daftar')
            ->with('notif', 'hapus_sukses');
    }


    /**
     * Menampilkan JSON data layanan berdasarkan id layanan
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        // ambil data layanan dengan status draft dari modal hapus
        $layanan = Layanan::with ([
            'fasilitas',
            'lokasiTk1',
            'lokasiTk2',
            'lokasiTk3',
            'getCreatedName',
            'getUpdatedName'
            ])
            ->where('id', $request->id)
            ->first();
            
        // Ambil data peralatan berdasarkan layanan_id
        $daftarPeralatan = DaftarPeralatanLayanan::with([
                'peralatan.jenis'
            ])
            ->where('layanan_id', $layanan->id)
            ->get();

        //return response()->json($user);
        return response()->json([
            'layanan'=>$layanan,
            'daftarPeralatan'=>$daftarPeralatan
        ]);
    }


    /**
     * Function untuk menampilkan form edit layanan step 1.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/edit/step1/{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formEditStep1($id)
    {
        // ambil data layanan dengan status aktif atau tidak aktif berdasarkan id
        $layanan = Layanan::where('id', $id)
            ->where('status', '!=', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
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
        $menu_url = route('fasilitas.layanan.daftar');
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.edit.step1')
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
     * Function untuk memproses edit data layanan di step 1.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/edit/step1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editStep1(Request $request)
    {
        // ambil data layanan sebelumnya berdasarkan id
        $layanan = Layanan::where('id', $request->id)
            ->where('status', '!=', config('constants.status_layanan.draft'))
            ->first();
        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }
                
        // ===================== CEK DUPLIKASI KODE ============================        
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
                'lokasi_tk_1_id' => $request->lokasi_tk_1,
                'lokasi_tk_2_id' => $request->lokasi_tk_2,
                'lokasi_tk_3_id' => $request->lokasi_tk_3,
                'status' => $request->status,
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // dd($ex->getMessage());
            // kembali ke halaman edit step 1 dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/edit/step1/'.$layanan->id)->with('notif', 'simpan_gagal');
            return redirect()
                ->route('fasilitas.layanan.edit.step1.form', $layanan->id)
                ->with('notif', 'simpan_gagal');
        }

        // jika proses update berhasil
        // kembali ke halaman edit step 2 dan tampilkan pesan sukses
        // return redirect('/fasilitas/layanan/edit/step2/'.$layanan->id)->with('notif', 'simpan_sukses');
        return redirect()
            ->route('fasilitas.layanan.edit.step2.form', $layanan->id)
            ->with('notif', 'simpan_sukses');
    }


    /**
     * Function untuk menampilkan form edit layanan step 2 (daftar peralatan).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/edit/step2/{$id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formEditStep2($id)
    {
        // ambil data layanan dengan status aktif atau tidak aktif dari form tambah 1
        $layanan = Layanan::where('id', $id)
            ->where('status', '!=', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();

        // ambil daftar jenis alat yang aktif
        $jenis = JenisAlat::where('status', 1)->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.edit.step2')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('layanan', $layanan)
        ->with('daftar_peralatan', $daftar_peralatan)
        ->with('perusahaan', $perusahaan)
        ->with('jenis', $jenis)
        ;
    }


    /**
     * Function untuk menampilkan form edit layanan step 3 (review).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /fasilitas/layanan/edit/step3/{$id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formEditStep3($id)
    {
        // ambil data layanan dengan status aktif atau tidak aktif dari form tambah 2
        $layanan = Layanan::where('id', $id)
            ->where('status', '!=', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // jika daftar peralatan kosong
        if($daftar_peralatan->isEmpty()){
            // dan status layanan aktif
            if($layanan->status == config('constants.status_layanan.aktif')){
                // kembali ke halaman layanan tambah step 2 dan tampilkan pesan error
                return redirect()
                    ->route('fasilitas.layanan.edit.step2.form', $layanan->id)
                    ->with('notif', 'peralatan_null');
            }
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Layanan";
		$module = "Fasilitas";
        $menu = "Layanan";
        $menu_url = route('fasilitas.layanan.daftar');
        $submenu = "Edit Data";
        
        // menampilkan halaman view
        return view('fasilitas.layanan.edit.step3')
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
     * Function untuk memproses edit layanan step 3 (review).
     * Pada proses ini, hanya menampilkan halaman review hasil perubahan data layanan.
     * Semua proses update data telah dilakukan pada step 1 dan step 2.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/edit/step3
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editStep3(Request $request)
    {
        // ambil data layanan dengan status aktif dari form tambah 2
        $layanan = Layanan::where('id', $request->id)
            ->where('status', '!=', config('constants.status_layanan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if($layanan == null){
            // kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
        }

        // kembali ke halaman daftar dan tampilkan pesan sukses
        // return redirect('/fasilitas/layanan/daftar')->with('notif', 'simpan_sukses');
        return redirect()
            ->route('fasilitas.layanan.daftar')
            ->with('notif', 'simpan_sukses');
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
        // buat query untuk mengambil daftar peralatan
        $query = Peralatan::query();
        // ambil peralatan dengan status aktif dan belum ditambahkan ke Layanan manapun
        $query->where('status', 1)
              ->where('kondisi', config('constants.kondisi_peralatan.normal')) // kondisi peralatan normal
              ->where('flag_layanan', 0);

        // jika field jenis alat terpilih
        if ($request->filled('jenis')) {
            $query->where('jenis_id', $request->jenis);
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
        return view('fasilitas.layanan.modal_daftar_peralatan', compact('daftar_tersedia'));
    }


    /**
     * Function untuk memproses tambah peralatan ke layanan baru.
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
                //'kondisi' => config('constants.kondisi_peralatan_layanan.beroperasi')
                'created_by' => session()->get('id')
            ]);

            // update flag_layanan menjadi 1, sebagai penanda bahwa peralatan sudah ditambahkan ke layanan
            Peralatan::where('id', $request->peralatan_id)
            ->update([
                'flag_layanan' => 1, // peralatan diberi tanda bahwa sedang terpasang di layanan
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
            // kirim pesan error ke file storage/logs/laravel.log
            // \Log::error('Gagal tambah peralatan ke layanan: '.$ex->getMessage());
            // kirim pesan gagal melalui JSON
            return response()->json(['success' => false, 'reason' => 'Gagal menambahkan peralatan'], 400);
        }

        return response()->json(['success' => true]);
    }


    /**
     * Function untuk memproses tambah peralatan ke layanan lama (edit layanan).
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/peralatan/edit/tambah
     *
     * @param  layanan_id
     * @param  peralatan_id
     * @return \Illuminate\Http\Response
     */
    public function editTambahPeralatan(Request $request)
    {
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
                'kondisi' => config('constants.kondisi_peralatan_layanan.beroperasi'),
                'created_by' => session()->get('id')
            ]);

            // update flag_layanan menjadi 1, sebagai penanda bahwa peralatan sudah ditambahkan ke layanan
            Peralatan::where('id', $request->peralatan_id)
            ->update([
                'flag_layanan' => 1, // peralatan diberi tanda bahwa sedang terpasang di layanan
                'updated_by' => session()->get('id')
            ]);
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
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
        // ambil data layanan sebelumnya berdasarkan id
        $layanan = Layanan::where('id', $request->layanan_id)
            ->first();
        // cek apakah layanan ada
        if($layanan == null){
            // jika tidak ada, kembali ke halaman daftar dan tampilkan pesan error
            // return redirect('/fasilitas/layanan/daftar')->with('notif', 'item_null');
            return redirect()
                ->route('fasilitas.layanan.daftar')
                ->with('notif', 'item_null');
            
        }
        // cek apakah ada data peralatan tsb
        $peralatan = DaftarPeralatanLayanan::where('layanan_id', $request->layanan_id)
            ->where('peralatan_id', $request->peralatan_id)
            ->first();
        // jika tidak ada
        if($peralatan == null){
            // kembali ke ahlaman daftar dan tampilkan pesan error
             return redirect()->back()->with('notif', 'item_null')->withInput();
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
            return redirect()->back()->with('notif', 'hapus_gagal');
        }

        // jika proses update berhasil
        // kembali ke halaman daftar dan tampilkan pesan sukses
        return redirect()->back()->with('notif', 'hapus_sukses');
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
        // cek apakah ada data peralatan tsb
        $peralatan = DaftarPeralatanLayanan::where('id', $request->id)
            ->where('peralatan_id', $request->peralatan_id)
            ->first();
        // jika tidak ada
        if($peralatan == null){
            // kembali ke halaman sebelumnya dan tampilkan pesan error
            return redirect()->back()->with('notif', 'item_null');
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
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()->back()->with('notif', 'edit_gagal');
        }

        // jika proses update berhasil
        return redirect()->back()->with('notif', 'edit_sukses');
    }


    /**
     * Menampilkan JSON data peralatan berdasarkan id layanan dan id peralatan
     *
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /fasilitas/layanan/peralatan/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detailPeralatan(Request $request)
    {
        // ambil data peralatan
        $peralatan = Peralatan::find($request->peralatan_id);
        // ambil data dari tabel daftar peralatan layanan
        $satuPeralatan = DaftarPeralatanLayanan::where('id', $request->id)
            ->where('peralatan_id', $request->peralatan_id)
            ->first();

        //return response()->json($user);
        return response()->json([
            'peralatan'=>$peralatan,
            'satuPeralatan'=>$satuPeralatan
        ]);
    }
}
