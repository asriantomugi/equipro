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
 use Illuminate\Validation\Rule;
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
 use App\Models\TlPenggantianPeralatan;
 use App\Models\TlGangguanNonPeralatan;
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
        $daftar = Laporan::with([
            'layanan.fasilitas',
            'layanan.LokasiTk1',
            'layanan.LokasiTk2',
            'layanan.LokasiTk3',
            'gangguanPeralatan.peralatan'
        ])->get();
       
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
        $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3'])
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
        $judul = "Laporan";
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
     * Function untuk memproses filter daftar layanan.
     * 
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
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

        $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3'])
                        ->where('status', 1);

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

        return response()->view('logbook.laporan.modal_daftar_layanan', compact('layanan'));
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
        $judul = "Laporan";
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
         // Simpan ke tabel laporan 
        $laporan = Laporan::create([
            'no_laporan' => now()->format('YmdHis') . rand(100, 999), // contoh format nomor unik
            'layanan_id' => $request->layanan_id,
            'jenis' => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2, // 1 = peralatan, 2 = non-peralatan
            'waktu' => $request->waktu_gangguan,
            'status' => 1, // default status
            'kondisi_layanan_temp' => false, // default kondisi
            'created_by' => auth()->id(),
        ]);

        // 2. Gangguan berdasarkan jenis
        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $request->validate([
                'peralatan' => 'required|array|min:1',
                'peralatan.*.id' => 'required|exists:peralatan,id',
                'peralatan.*.kondisi' => 'required|in:0,1',
                'peralatan.*.deskripsi' => 'required|string',
            ]);

            foreach ($request->peralatan as $alat) {
                GangguanPeralatan::create([
                    'laporan_id' => $laporan->id,
                    'layanan_id' => $request->layanan_id,
                    'peralatan_id' => $alat['id'],
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'kondisi' => $alat['kondisi'],
                    'deskripsi' => $alat['deskripsi'],
                    'created_by' => auth()->id(),
                ]);
            }

        } else {
            $request->validate([
                'deskripsi_gangguan' => 'required|string',
            ]);

            GangguanNonPeralatan::create([
                'laporan_id' => $laporan->id,
                'layanan_id' => $request->layanan_id,
                'waktu_gangguan' => $request->waktu_gangguan,
                'deskripsi' => $request->deskripsi_gangguan,
                'created_by' => auth()->id(),
            ]);
        }

        DB::commit();

        // Arahkan ke step 3 sambil kirim laporan_id
        return redirect()->route('tambah.step3', ['laporan_id' => $laporan->id])
            ->with('notif', 'tambah_sukses');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan laporan: ' . $e->getMessage()])
            ->with('notif', 'tambah_gagal');;
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
    public function formStep3($laporan_id, Request $request)
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

        // Ambil data laporan
        $laporan = Laporan::findOrFail($laporan_id);

        // Ambil gangguan sesuai jenis laporan
        $gangguanPeralatan = null;
        $gangguanNonPeralatan = null;

        if ($laporan->jenis_laporan  == 1) {
        // Jenis: Gangguan Peralatan
        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        } elseif ($laporan->jenis_laporan  == 2) {
            // Jenis: Gangguan Non-Peralatan
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        }

        //constants
        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiSetelahPerbaikan = config('constants.kondisi_peralatan');

        // variabel untuk dikirim ke halaman view
        $judul = "Laporan";
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
            ->with('gangguanNonPeralatan', $gangguanNonPeralatan)
            ->with('laporan', $laporan)
            ->with('step', 3)
            ->with('jenisTindakLanjut', $jenisTindakLanjut)
            ->with('kondisiSetelahPerbaikan', $kondisiSetelahPerbaikan);
            
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
            'laporan_id' => 'required|integer|exists:laporan,id',
            'layanan_id' => 'required|integer|exists:layanan,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'deskripsi' => 'required|string',
            'jenis_laporan' => ['required', Rule::in([1, 0])],
        ]);

        try {
            $userId = Auth::id();

            if ($request->jenis_laporan == 1) {
                // validasi tambahan untuk gangguan peralatan
                $request->validate([
                    'jenis_tindaklanjut' => ['required', Rule::in([1, 0])],
                    'kondisi' => ['required', Rule::in([1, 0])],
                ]);

                $gangguan = GangguanPeralatan::where('laporan_id', $request->laporan_id)->latest()->first();

                if (!$gangguan) {
                    return back()->withErrors(['msg' => 'Data gangguan peralatan tidak ditemukan.']);
                }

                TlGangguanPeralatan::create([
                    'gangguan_peralatan_id' => $gangguan->id,
                    'laporan_id' => $request->laporan_id,
                    'layanan_id' => $request->layanan_id,
                    'peralatan_id' => $gangguan->peralatan_id,
                    'waktu' => $request->waktu,
                    'deskripsi' => $request->deskripsi,
                    'kondisi' => $request->kondisi,
                    'jenis_tindaklanjut' => $request->jenis_tindaklanjut,
                    'created_by' => $userId,
                ]);
            } else {
                $gangguan = GangguanNonPeralatan::where('laporan_id', $request->laporan_id)->latest()->first();

                if (!$gangguan) {
                    return back()->withErrors(['msg' => 'Data gangguan non-peralatan tidak ditemukan.']);
                }

                TlGangguanNonPeralatan::create([
                    'gangguan_non_peralatan_id' => $gangguan->id,
                    'laporan_id' => $request->laporan_id,
                    'layanan_id' => $request->layanan_id,
                    'waktu' => $request->waktu,
                    'deskripsi' => $request->deskripsi,
                    'kondisi' => 1, // default atau ubah sesuai logikamu
                    'created_by' => $userId,
                ]);
            }

            return redirect()->route('tambah.step4', ['laporan_id' => $request->laporan_id])
                            ->with('notif', 'tambah_sukses');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()])
                        ->withInput()
                        ->with('notif', 'tambah_gagal');
        }
    }

    /**
     * Function untuk menampilkan form jenis tindak lanjut penggantian & perbaikan (Gangguan Peralatan)
     * dan Input kondisi Layanan setelah dilakukan perbaikan(Gangguan Non Peralatan).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step4
     *
     * @return \Illuminate\Http\Response
     */
    public function step4($laporan_id)
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

        
        $laporan = Laporan::with('layanan')->findOrFail($laporan_id);
        $jenis = JenisAlat::where('status', 1)->get();
        $perusahaan = Perusahaan::where('status', 1)->get();

        $jenis_tindaklanjut = null;
        $peralatanTersedia = [];
        $peralatanLama = [];

        $kondisiSetelah = config('constants.kondisi_layanan');
        $constPenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');

        if ($laporan->jenis == 1) {
            $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tindaklanjut) {
                $jenis_tindaklanjut = (int) $tindaklanjut->jenis_tindaklanjut;

                if ($jenis_tindaklanjut === $constPenggantian) {
                    // Ambil semua peralatan dari layanan terkait laporan
                    $peralatanLama = $laporan->layanan
                        ->daftarPeralatanLayanan // relasi hasMany
                        ->load('peralatan')      // eager load relasi peralatan
                        ->pluck('peralatan')     // ambil peralatan-nya saja
                        ->filter()               // buang null
                        ->values();              // reset index jadi 0,1,2,...
                    
                    $peralatanTersedia = Peralatan::where('status', 1)
                        ->whereNotIn('id', $peralatanLama->pluck('id'))
                        ->get();
                }
            }
        } else {
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tindaklanjut) {
                $jenis_tindaklanjut = (int) $tindaklanjut->jenis_tindaklanjut;
            }
        }

        return view('logbook.laporan.tambah.step4', [
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step4',
            'submenu' => 'Tambah',
            'laporan' => $laporan,
            'jenis_tindaklanjut' => $jenis_tindaklanjut,
            'peralatanLama' => $peralatanLama,
            'peralatanTersedia' => $peralatanTersedia,
            'kondisiSetelah' => $kondisiSetelah,
            'jenis' => JenisAlat::all(), // <- ini untuk dropdown filter
            'perusahaan' => Perusahaan::all(), 
        ]);
    }

    /**
     * Function untuk memproses filter peralatan pengganti.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: GET
     * URL: /logbook/laporan/peralatan/filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterPeralatanPengganti(Request $request)
    {
        $query = Peralatan::query();

        if ($request->filled('jenis')) {
            $query->where('jenis_id', $request->jenis); 
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('sewa')) {
            $query->where('sewa', $request->sewa);
        }

        if ($request->filled('perusahaan')) {
            $query->where('perusahaan_id', $request->perusahaan);
        }

        $query->where('status', 1);

        $peralatan = $query->get();

        return view('logbook.laporan.modal_penggantian_peralatan', compact('peralatan'));
    }


    /**
     * Function untuk menyimpan data jenis tindak lanjut penggantian & perbaikan (Gangguan Peralatan)
     * dan Input kondisi Layanan setelah dilakukan perbaikan(Gangguan Non Peralatan).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step4
     *
     * @return \Illuminate\Http\Response
     */
    public function simpanStep4(Request $request)
{
    $request->validate([
        'laporan_id' => 'required|exists:laporan,id',
        'kondisi_setelah' => 'required|boolean',
        'jenis_tindaklanjut' => 'required|integer',
    ]);

    try {
        $laporan = Laporan::findOrFail($request->laporan_id);

        // Update kondisi sementara layanan
        $laporan->kondisi_layanan_temp = $request->kondisi_setelah;
        $laporan->updated_by = Auth::id();
        $laporan->save();

        // Simpan data penggantian peralatan (jika penggantian dan peralatan baru tersedia)
        if (
            $laporan->jenis == 1 &&
            $request->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian') &&
            $request->filled('peralatan_baru_id')
        ) {
            // Ambil data tindak lanjut peralatan terbaru
            $tlGangguan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();

            if ($tlGangguan) {
                TlPenggantianPeralatan::updateOrCreate(
                    [
                        'laporan_id' => $laporan->id,
                        'tl_gangguan_id' => $tlGangguan->id,
                    ],
                    [
                        'layanan_id' => $laporan->layanan_id,
                        'peralatan_lama_id' => $tlGangguan->peralatan_id,
                        'peralatan_baru_id' => $request->peralatan_baru_id,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }
        }

        return redirect()->route('tambah.step5', ['laporan_id' => $laporan->id])
                         ->with('notif', 'tambah_sukses');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                         ->withInput();
    }
}

    /**
     * Function untuk menampilkan review data
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step5
     *
     * @return \Illuminate\Http\Response
     */
    public function step5($laporan_id)
{
    // Ambil data laporan dan relasi layanan + lokasi
    $laporan = Laporan::with([
        'layanan.fasilitas',
        'layanan.LokasiTk1',
        'layanan.LokasiTk2',
        'layanan.LokasiTk3',
        'gangguanNonPeralatan'
    ])->findOrFail($laporan_id);

    $detailGangguanPeralatan = [];
    $tindaklanjut = null;
    $penggantian = null;

    if ($laporan->jenis == 1) {
        $detailGangguanPeralatan = TlGangguanPeralatan::with('peralatan')
            ->where('laporan_id', $laporan->id)->get();

        $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();

        if ($tindaklanjut && $tindaklanjut->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian')) {
            $penggantian = TlPenggantianPeralatan::with(['peralatanLama', 'peralatanBaru'])
                ->where('laporan_id', $laporan->id)
                ->latest()->first();
        }
    } else {
        $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
    }

    return view('logbook.laporan.tambah.step5', [
        'judul' => 'Laporan',
        'module' => 'Logbook',
        'menu' => 'Laporan',
        'menu_url' => '/logbook/laporan/tambah/step5',
        'submenu' => 'Tambah',
        'laporan' => $laporan,
        'detailGangguanPeralatan' => $detailGangguanPeralatan,
        'tindaklanjut' => $tindaklanjut,
        'penggantian' => $penggantian,
    ]);
}

    public function simpanStep5(Request $request)
{
    $request->validate([
        'laporan_id' => 'required|exists:laporan,id',
    ]);

    try {
        $laporan = Laporan::findOrFail($request->laporan_id);
        $laporan->status = 1; // Anggap 1 = laporan sudah final
        $laporan->updated_by = auth()->id();
        $laporan->save();

        return redirect()->route('logbook.laporan.daftar')->with('notif', 'tambah_sukses');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
    }
}




}