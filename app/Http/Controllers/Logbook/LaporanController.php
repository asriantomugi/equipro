<?php

namespace App\Http\Controllers\Logbook;

/**
 * LaporanController.php
 * Controller ini digunakan untuk menangani proses CRUD laporan
 *
 * @author Yanti Melani
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
 use App\Models\Fasilitas;
 use App\Models\LokasiTk1;
 use App\Models\LokasiTk2;
 use App\Models\LokasiTk3;
 use App\Models\Layanan;
 use App\Models\DaftarPeralatanLayanan;
 use App\Models\Laporan;
 use App\Models\GangguanPeralatan;
 use App\Models\GangguanNonPeralatan;
 use App\Models\TlGangguanPeralatan;
 use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    /**
     * Function untuk menampilkan daftar layanan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/daftar
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar laporan
        $daftar = Laporan::all();
       
        // variabel untuk dikirim ke halaman view
		$judul = "Laporan";
		$module = "Laporan";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/daftar";
        $submenu = "Daftar";
        
        // menampilkan halaman view
        return view('logbook.laporan.daftar')
        ->with('judul', $judul)
		->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)	
        ->with('daftar', $daftar)
        ;
    }

    /**
     * Function untuk menampilkan daftar layanan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step1
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep1(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ambil daftar Fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        // ambil daftar Lokasi yang aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        $lokasi_tk_2 = LokasiTk2::where('status', 1)->get();
        $lokasi_tk_3= LokasiTk3::where('status', 1)->get();

        // ambil daftar Layanan yang aktif
        $query = Layanan::with(['fasilitas', 'getLokasiTk1', 'getLokasiTk2', 'getLokasiTk3'])
                  ->where('status', 1);
                  
        // Filter berdasarkan input
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas_id', $request->fasilitas);
        }

        if ($request->filled('LokasiTk1')) {
            $query->where('lokasi_tk_1_id', $request->LokasiTk1);
        }

        if ($request->filled('LokasiTk2')) {
            $query->where('lokasi_tk_2_id', $request->LokasiTk2);
        }

        if ($request->filled('LokasiTk3')) {
            $query->where('lokasi_tk_3_id', $request->LokasiTk3);
        }

        $layanan = $query->get();

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
		$module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/step1";
        $submenu = "Tambah";
        
        // menampilkan halaman view
        return view('logbook.laporan.tambah.step1')
        ->with('judul', $judul)
        ->with('module', $module)
		->with('menu', $menu)
        ->with('menu_url', $menu_url)
        ->with('submenu', $submenu)
        ->with('fasilitas', $fasilitas)
        ->with('layanan', $layanan)
        ->with('LokasiTk1', $lokasi_tk_1)
        ->with('LokasiTk2', $lokasi_tk_2)
        ->with('LokasiTk3', $lokasi_tk_3)
        ;
    }


    /**
     * Function untuk memilih layanan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step1
     *
     * @return \Illuminate\Http\Response
     */
    public function Step1(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $validated = $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
        ]);

        try {
            session([
                'selected_layanan_id' => $request->layanan_id,
                'gangguan' => session('gangguan', []), // Pastikan gangguan tetap tersimpan
            ]);

            return redirect()->route('tambah.step2', ['layanan_id' => $request->layanan_id])
                ->with('notif', 'tambah_sukses');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Function untuk // Menampilkan halaman step 2 yang berisi form jenis laporan 
     * + form gangguan (dinamis)
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step2
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep2(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // Validasi bahwa ID layanan tersedia
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
        ]);

        // Ambil data layanan dan jenis laporan dari config
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($request->layanan_id);
        $jenisLaporan = config('constants.jenis_laporan');

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/step2";
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step2')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('jenisLaporan', $jenisLaporan); 
    }

    /**
     * Function untuk menambah jenis laporan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step2
     *
     * @return \Illuminate\Http\Response
     */
    public function simpanStep2(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_laporan' => 'required|in:gangguan_peralatan,gangguan_non_peralatan',
            'waktu_gangguan' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            if ($request->jenis_laporan === 'gangguan_peralatan') {
                // Validasi tambahan
                $request->validate([
                    'peralatan' => 'required|array|min:1',
                    'peralatan.*.id' => 'required|exists:peralatan,id',
                    'peralatan.*.kondisi' => 'required|in:0,1', // Asumsikan 0: Rusak, 1: Normal
                    'peralatan.*.deskripsi' => 'required|string',
                ]);

                foreach ($request->peralatan as $alat) {
                    GangguanPeralatan::create([
                        'layanan_id' => $request->layanan_id,
                        'peralatan_id' => $alat['id'],
                        'waktu_gangguan' => $request->waktu_gangguan,
                        'kondisi' => $alat['kondisi'],
                        'deskripsi' => $alat['deskripsi'],
                        'created_by' => auth()->id(),
                    ]);
                }

            } else {
                // gangguan_non_peralatan
                $request->validate([
                    'deskripsi_gangguan' => 'required|string',
                ]);

                GangguanNonPeralatan::create([
                    'layanan_id' => $request->layanan_id,
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'deskripsi' => $request->deskripsi_gangguan,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('tambah.step3')->with('notif', 'tambah_sukses');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan laporan: ' . $e->getMessage()]);
        }
    }

    /**
     * Function untuk menampilkan form tindak lanjut.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step3
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep3()
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        // Ambil data gangguan terakhir berdasarkan user
        $userId = auth()->id();
        $gangguanPeralatan = GangguanPeralatan::where('created_by', $userId)->latest()->first();
        $gangguanNonPeralatan = GangguanNonPeralatan::where('created_by', $userId)->latest()->first();

        return view('logbook.step3', compact('gangguanPeralatan', 'gangguanNonPeralatan'));

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/step3";
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step3')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('gangguanPeralatan', $gangguanPeralatan)
            ->with('gangguanNonPeralatan', $gangguanNonPeralatan); 
    }

    /**
     * Function untuk menyimpan data tindak lanjut.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step3
     *
     * @return \Illuminate\Http\Response
     */
    public function simpanStep3(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $request->validate([
            'laporan_id' => 'required|integer',
            'layanan_id' => 'required|integer',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'deskripsi' => 'required|string',
            'kondisi' => 'required|boolean',
            'jenis_laporan' => 'required|string|in:peralatan,non_peralatan'
        ]);

        try {
            $userId = Auth::id();

            if ($request->jenis_laporan === 'peralatan') {
                TlGangguanPeralatan::create([
                    'gangguan_peralatan_id' => $request->gangguan_peralatan_id,
                    'laporan_id'            => $request->laporan_id,
                    'layanan_id'            => $request->layanan_id,
                    'peralatan_id'          => $request->peralatan_id,
                    'tanggal'               => $request->tanggal,
                    'waktu'                 => $request->waktu,
                    'deskripsi'             => $request->deskripsi,
                    'kondisi'               => $request->kondisi,
                    'jenis_tindaklanjut'    => $request->jenis_tindaklanjut,
                    'created_by'            => $userId
                ]);
            } else {
                TlGangguanNonPeralatan::create([
                    'gangguan_non_peralatan_id' => $request->gangguan_non_peralatan_id,
                    'laporan_id'                => $request->laporan_id,
                    'layanan_id'                => $request->layanan_id,
                    'tanggal'                   => $request->tanggal,
                    'waktu'                     => $request->waktu,
                    'deskripsi'                 => $request->deskripsi,
                    'kondisi'                   => $request->kondisi,
                    'created_by'                => $userId
                ]);
            }

            return redirect()->route('tambah.step4')->with('success', 'Tindak lanjut berhasil disimpan. Lanjut ke Step 4.');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()]);
        }
    }


    /**
     * Function untuk menampilkan form gangguan peralatan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/gangguan/form
     *
     * @return \Illuminate\Http\Response
     */
    public function formGangguanPeralatan(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        //mengambil data 
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($request->layanan_id);
        $peralatan = $request->peralatan_id;
        $jenisLaporan = config('constants.jenis_laporan');

        if (!$layanan) {
            return abort(404, 'Layanan tidak ditemukan.');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/gangguan/form";
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.gangguan.form')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('peralatan', $peralatan)
            ->with('jenisLaporan', $jenisLaporan); 

    }

    /**
     * Function untuk menyimpan data gangguan peralatan
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/gangguan/submit
     *
     * @return \Illuminate\Http\Response
     */
    public function GangguanPeralatan(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        // Cek semua input masuk dulu
        logger('Request masuk GangguanPeralatan', $request->all());

        // Validasi input dari form
        $request->validate([
            'layanan_id'     => 'required|integer',
            'peralatan'   => 'required|array',
            'waktu_gangguan' => 'required|date',
            'peralatan.*.kondisi' => 'required',
            'peralatan.*.deskripsi' => 'required|string',
            'waktu_gangguan' => 'required|date',
        ]);

        // Format waktu gangguan
        $waktu = date('Y-m-d H:i:s', strtotime($request->waktu_gangguan));

        // Simpan ke tabel laporan
        $laporan = Laporan::create([
            'no_laporan'    => now()->format('YmdHis') ,
            'layanan_id'    => $request->layanan_id,
            'jenis'         => config('constants.jenis_laporan.gangguan_peralatan'),
            'waktu'         => $request->waktu_gangguan,
            'status'        => config('constants.status_laporan.open'),
            'kondisi_layanan_temp'  => $request->kondisi,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
        ]);

        logger('Laporan berhasil disimpan ID: ' . $laporan->id);

        // Simpan gangguan peralatan satu per satu
        foreach ($request->peralatan as $item) {
            GangguanPeralatan::create([
                'laporan_id'   => $laporan->id,
                'layanan_id'   => $request->layanan_id,
                'peralatan_id' => $item['id'],
                'kondisi' => $item['kondisi'] == '1' ? true : false,
                'deskripsi' => $item['deskripsi'],
                'waktu' => $waktu,
                'created_by'   => auth()->id(),
                'updated_by'   => auth()->id(),
            ]);
        }

        if ($errors = $request->getSession()->get('errors')) {
            logger('VALIDATION ERROR', $errors->toArray());
        }


         // Redirect ke form tindaklanjut gangguan peralatan (step selanjutnya)
        return redirect()->route('tambah.gangguan.tindaklanjut', ['laporan_id' => $laporan->id]);
    }


    /**
     * Function untuk menampilkan form tindaklanjut gangguan peralatan
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/gangguan/tindaklanjut
     *
     * @return \Illuminate\Http\Response
     */
    public function formTindaklanjut($laporan_id)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $laporan = Laporan::findOrFail($laporan_id); // ini bisa gagal kalau ID tidak ditemukan
        $jenisTindaklanjut = config('constants.jenis_tindaklanjut');

        // Ambil gangguan_peralatan berdasarkan laporan_id
        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan_id)->first();

        // Validasi jika tidak ada data
        if (!$gangguanPeralatan) {
            abort(404, 'Data gangguan peralatan tidak ditemukan.');
        }

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/gangguan/tindaklanjut";
        $submenu = "Tindaklanjut";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.gangguan.tindaklanjut', [
            'judul' => $judul,
            'module' => $module,
            'menu' => $menu,
            'menu_url' => $menu_url,
            'submenu' => $submenu,
            'laporan' => $laporan,
            'jenisTindaklanjut' => config('constants.jenis_tindaklanjut'),
            'gangguan_peralatan_id' => $gangguanPeralatan->id,
        ]);
    }

    public function simpanTindaklanjut(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        // Validasi input
        $request->validate([
            'laporan_id'             => 'required|integer|exists:laporan,id',
            'gangguan_peralatan_id'  => 'required|integer|exists:gangguan_peralatan,id',
            'jenis_tindaklanjut'     => 'required|string',
            'waktu'                  => 'required|date',
            'deskripsi'              => 'required|string',
        ]);

        // Ambil data gangguan untuk referensi (misalnya perlu informasi peralatan_id, layanan_id)
        $gangguan = GangguanPeralatan::findOrFail($request->gangguan_peralatan_id);

        $datetime = \Carbon\Carbon::parse($request->waktu);
        
        // Simpan data ke tabel tindak lanjut
        $tindaklanjut = TlGangguanPeralatan::create([
            'laporan_id'             => $request->laporan_id,
            'gangguan_peralatan_id' => $request->gangguan_peralatan_id,
            'layanan_id'             => $gangguan->layanan_id,
            'peralatan_id'           => $gangguan->peralatan_id,
            'tanggal'                => $datetime->toDateString(), // "2025-05-28"
            'waktu'                  => $datetime->toTimeString(), // "02:54:00"
            'deskripsi'              => $request->deskripsi,
            'kondisi'                => $gangguan->kondisi, // kondisi setelah perbaikan nanti bisa ubah
            'jenis_tindaklanjut'     => $request->jenis_tindaklanjut,
            'created_by'             => auth()->id(),
            'updated_by'             => auth()->id(),
        ]);

        $jenisTindaklanjut = filter_var($request->jenis_tindaklanjut, FILTER_VALIDATE_BOOLEAN);

        // Redirect ke form berikutnya sesuai jenis_tindaklanjut
        if ($request->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian')) {
            return redirect()->route('tambah.gangguan.penggantian', [
                'laporan_id' => $request->laporan_id
            ]);
        } elseif ($request->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.perbaikan')) {
            return redirect()->route('tambah.gangguan.perbaikan', [
                'laporan_id' => $request->laporan_id
            ]);
        } else {
            abort(400, 'Jenis tindaklanjut tidak dikenali.');
        }

    }

    public function formPenggantian($laporan_id)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $laporan = Laporan::findOrFail($laporan_id);
        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan_id)->firstOrFail();

        // Ambil peralatan yang tersedia (flag_layanan = 0)
        $peralatanList = Peralatan::where(function ($query) {
            $query->where('flag_layanan', 0)
                ->orWhereNull('flag_layanan');
        })->get();

        // Ambil peralatan lama yang digunakan layanan
        $peralatanLama = DaftarPeralatanLayanan::with('peralatan')
            ->where('layanan_id', $laporan->layanan_id)
            ->get()
            ->pluck('peralatan')
            ->filter() // buang yang null
            ->values(); // reset keys agar konsisten

        $peralatanLamaIds = $peralatanLama ? $peralatanLama->pluck('id')->toArray() : [];

        $peralatanPengganti = Peralatan::whereNotIn('id', $peralatanLamaIds)
            ->where('status', true) // misal hanya yang aktif
            ->get();

        $peralatanBaru = Peralatan::all();

        // variabel untuk dikirim ke halaman view
        $judul = "Tambah Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/tambah/gangguan/penggantian";
        $submenu = "Penggantian";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.gangguan.penggantian', [
            'judul' => $judul,
            'module' => $module,
            'menu' => $menu,
            'menu_url' => $menu_url,
            'submenu' => $submenu,
            'laporan' => $laporan,
            'jenisTindaklanjut' => config('constants.jenis_tindaklanjut'),
            'gangguan_peralatan_id' => $gangguanPeralatan->id,
            'peralatanList' => $peralatanList, // peralatan dengan flag_layanan = 0
            'peralatanLama' => $peralatanLama ?? collect(), // relasi dari laporan ke peralatan lama
            'peralatanPengganti' => collect(),
        ]);
    }

    public function simpanPenggantian(Request $request)
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
         && session()->get('role_id') != config('constants.role.admin')
         && session()->get('role_id') != config('constants.role.teknisi')){
            // jika bukan
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
            'gangguan_peralatan_id' => 'required|exists:gangguan_peralatan,id',
            'peralatan_id' => 'required|exists:peralatan,id',
            'kondisi' => 'required|boolean',
            'kondisi_layanan_temp' => 'required|boolean',
        ]);

        // Simpan data tindaklanjut penggantian
        TlGangguanPeralatan::create([
            'gangguan_peralatan_id' => $request->gangguan_peralatan_id,
            'laporan_id' => $request->laporan_id,
            'layanan_id' => Laporan::find($request->laporan_id)->layanan_id,
            'peralatan_id' => $request->peralatan_id,
            'tanggal' => now()->toDateString(),
            'waktu' => now()->toTimeString(),
            'deskripsi' => 'Penggantian Peralatan',
            'kondisi' => $request->kondisi,
            'jenis_tindaklanjut' => config('constants.jenis_tindaklanjut.penggantian'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Update kondisi layanan sementara
        Laporan::where('id', $request->laporan_id)->update([
            'kondisi_layanan_temp' => $request->kondisi_layanan_temp,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('tambah.review', ['laporan_id' => $request->laporan_id])
                        ->with('success', 'Data penggantian berhasil disimpan.');
    }

}
