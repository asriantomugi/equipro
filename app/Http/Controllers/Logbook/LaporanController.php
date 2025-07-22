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
 use Illuminate\Support\Facades\Log;
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

        // Ambil daftar laporan hanya yang draft & open
        $daftar = Laporan::with([
            'layanan.fasilitas',
            'layanan.LokasiTk1',
            'layanan.LokasiTk2',
            'layanan.LokasiTk3',
        ])
        ->whereIn('status', [
            config('constants.status_laporan.draft'),
            config('constants.status_laporan.open')
        ])
        ->get();

        // Variabel untuk dikirim ke view
        $judul = "Laporan";
        $module = "Laporan";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/daftar";
        $submenu = "Daftar";

        return view('logbook.laporan.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('daftar', $daftar);
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

        // Ambil laporan terkait jika ada (misal via laporan_id di request, atau buat baru jika belum ada)
        $laporan = null;
        if ($request->has('laporan_id')) {
            $laporan = Laporan::find($request->laporan_id);
        }
        

        // Data tambahan untuk view
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
            ->with('laporan', $laporan)
            ->with('jenisLaporan', $jenisLaporan);
    }


    /**
     * Function untuk menampilkan form tambah layanan step 2 (tombol Back).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: URL: /logbook/laporan/tambah/step2/back{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep2Back(Request $request,$laporan_id)
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

    // Ambil laporan berdasarkan ID dari URL
        $laporan = Laporan::findOrFail($laporan_id);

        // Ambil data layanan terkait laporan
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->findOrFail($laporan->layanan_id);

        $jenisLaporan = config('constants.jenis_laporan');

        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->get();
        $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->first();

        $jenisLaporanString = $laporan->jenis == 1 ? 'gangguan_peralatan' : 'gangguan_non_peralatan';

        return view('logbook.laporan.tambah.step2_back')->with([
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step2',
            'submenu' => 'Tambah',
            'layanan' => $layanan,
            'jenisLaporan' => $jenisLaporan,
            'laporan' => $laporan,
            'selectedJenisLaporan' => old('jenis_laporan', $jenisLaporanString),
            'waktuGangguan' => old('waktu_gangguan', $laporan->waktu),
            'gangguanPeralatan' => $gangguanPeralatan,
            'gangguanNonPeralatan' => $gangguanNonPeralatan,
        ]);

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
            // ===================== AKHIR PROSES VERIFIKASI =======================

        /* ---------------- VALIDASI INPUT ---------------- */
        
            Log::info('Mulai proses simpanStep2', ['request' => $request->all()]);

        // ---------------- VALIDASI INPUT ----------------
        $jenisLaporan = config('constants.jenis_laporan');

        $rules = [
            'layanan_id'     => 'required|exists:layanan,id',
            'jenis_laporan'  => ['required', Rule::in(array_keys($jenisLaporan))],
            'waktu_gangguan' => 'required|date',
        ];

        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $rules += [
                'gangguan'              => 'required|array|min:1',
                'gangguan.*.id'         => 'required|exists:peralatan,id',
                'gangguan.*.kondisi'    => ['required', Rule::in(['0', '1'])],
                'gangguan.*.deskripsi'  => 'nullable|string',
            ];
        } else {
            $rules['deskripsi_gangguan'] = 'nullable|string';
        }

        $messages = [
            'layanan_id.*'          => 'Layanan tidak valid.',
            'jenis_laporan.*'       => 'Jenis laporan tidak valid.',
            'waktu_gangguan.*'      => 'Waktu gangguan wajib diisi & valid.',
            'gangguan.*.id.*'       => 'Peralatan wajib dipilih & valid.',
            'gangguan.*.kondisi.*'  => 'Kondisi gangguan wajib dipilih & valid.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
            Log::info('Validasi input Step 2 berhasil.', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi Step 2 gagal.', $e->errors());
            throw $e;
        }

        // ---------------- SIMPAN KE DATABASE ----------------
        $userId = Auth::id();
        DB::beginTransaction();

        try {
            $laporan = Laporan::create([
                'no_laporan'           => now()->format('YmdHis') . rand(100, 999),
                'layanan_id'           => $request->layanan_id,
                'jenis'                => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
                'waktu'                => $request->waktu_gangguan,
                'status'               => 1,
                'kondisi_layanan_temp' => false,
                'created_by'           => $userId,
            ]);

            Log::info("Laporan berhasil dibuat", ['laporan_id' => $laporan->id]);

            if ($request->jenis_laporan === 'gangguan_peralatan' && !empty($request->gangguan)) {
                foreach ($request->gangguan as $g) {
                    GangguanPeralatan::create([
                        'laporan_id'     => $laporan->id,
                        'layanan_id'     => $request->layanan_id,
                        'peralatan_id'   => $g['id'],
                        'waktu_gangguan' => $request->waktu_gangguan,
                        'kondisi'        => $g['kondisi'],
                        'deskripsi'      => $g['deskripsi'] ?? null,
                        'created_by'     => $userId,
                    ]);
                }

                Log::info('Data gangguan peralatan berhasil disimpan.', ['total' => count($request->gangguan)]);
            } elseif ($request->jenis_laporan === 'gangguan_non_peralatan') {
                GangguanNonPeralatan::create([
                    'laporan_id'     => $laporan->id,
                    'layanan_id'     => $request->layanan_id,
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'deskripsi'      => $request->deskripsi_gangguan ?? null,
                    'created_by'     => $userId,
                ]);

                Log::info('Data gangguan non-peralatan berhasil disimpan.');
            }

        DB::commit();

            Log::info("Proses Step 2 selesai. Redirect ke Step 3.", ['laporan_id' => $laporan->id]);
            return redirect()
                ->route('tambah.step3', ['laporan_id' => $laporan->id])
                ->with('notif', 'tambah_sukses');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan laporan Step 2.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['msg' => 'Gagal menyimpan laporan: ' . $e->getMessage()])
                ->with('notif', 'tambah_gagal');
        }
    }

    /**
     * Function untuk mengubah data di layanan step 2 (tombol Back).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: URL: /logbook/laporan/tambah/step2/back{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function tambahStep2Back(Request $request)
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

        /* ------------- VALIDASI INPUT ------------- */
        $jenisLaporan = config('constants.jenis_laporan');

        $rules = [
            'laporan_id'     => 'required|exists:laporan,id',
            'layanan_id'     => 'required|exists:layanan,id',
            'jenis_laporan'  => ['required', Rule::in(array_keys($jenisLaporan))],
            'waktu_gangguan' => 'required|date',
        ];

        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $rules += [
                'gangguan'            => 'required|array|min:1',
                'gangguan.*.id'       => 'required|exists:peralatan,id',
                'gangguan.*.kondisi' => ['required', Rule::in(['0', '1'])],
                'gangguan.*.deskripsi'=> 'nullable|string',
            ];
        } else {
            $rules['deskripsi_gangguan'] = 'nullable|string';
        }

        $messages = [
            'layanan_id.*'          => 'Layanan tidak valid.',
            'jenis_laporan.*'       => 'Jenis laporan tidak valid.',
            'waktu_gangguan.*'      => 'Waktu gangguan wajib diisi & valid.',
            'gangguan.*.id.*'       => 'Peralatan wajib dipilih & valid.',
            'gangguan.*.kondisi.*'  => 'Kondisi gangguan wajib dipilih & valid.',
        ];

        $validated = $request->validate($rules, $messages);
        /* ----------- AKHIR VALIDASI ----------- */

        /* ------------- SIMPAN PERUBAHAN ------------- */
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $laporan = Laporan::findOrFail($request->laporan_id);
            $laporan->update([
                'layanan_id' => $request->layanan_id,
                'jenis'      => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
                'waktu'      => $request->waktu_gangguan,
                'status'     => 1,
            ]);

            /* hapus gangguan lama */
            GangguanPeralatan::where('laporan_id', $laporan->id)->delete();
            GangguanNonPeralatan::where('laporan_id', $laporan->id)->delete();

            if ($request->jenis_laporan === 'gangguan_peralatan' && !empty($request->gangguan)) {
                foreach ($request->gangguan as $g) {
                    GangguanPeralatan::create([
                        'laporan_id'     => $laporan->id,
                        'layanan_id'     => $request->layanan_id,
                        'peralatan_id'   => $g['id'],
                        'waktu_gangguan' => $request->waktu_gangguan,
                        'kondisi'        => $g['kondisi'],
                        'deskripsi'      => $g['deskripsi'] ?? null,
                        'created_by'     => $userId,
                    ]);
                }
            } elseif ($request->jenis_laporan === 'gangguan_non_peralatan') {
                GangguanNonPeralatan::create([
                    'laporan_id'     => $laporan->id,
                    'layanan_id'     => $request->layanan_id,
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'deskripsi'      => $request->deskripsi_gangguan ?? null,
                    'created_by'     => $userId,
                ]);
            }

            DB::commit();
            return redirect()
                ->route('tambah.step3', ['laporan_id' => $laporan->id])
                ->with('notif', 'perubahan_tersimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['msg' => 'Gagal menyimpan perubahan: '.$e->getMessage()])
                ->with('notif', 'perubahan_gagal');
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
        if (!Auth::check()) return redirect('/login');
        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::findOrFail($laporan_id);
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($laporan->layanan_id);

        // Ambil gangguan
        $gangguanPeralatan = null;
        $gangguanNonPeralatan = null;
        $peralatanGangguanIds = [];

        if ($laporan->jenis == 1) {
            $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->get();
            $peralatanGangguanIds = $gangguanPeralatan->where('kondisi', 0)->pluck('peralatan_id')->toArray();
        } elseif ($laporan->jenis == 2) {
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        }

        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiTindaklanjut = config('constants.kondisi_tindaklanjut');

        return view('logbook.laporan.tambah.step3', [
            'judul'                 => 'Laporan',
            'module'                => 'Logbook',
            'menu'                  => 'Laporan',
            'menu_url'              => '/logbook/laporan/tambah/step3',
            'submenu'               => 'Tambah',
            'laporan'               => $laporan,
            'layanan'               => $layanan,
            'gangguanPeralatan'     => $gangguanPeralatan,
            'gangguanNonPeralatan'  => $gangguanNonPeralatan,
            'jenisTindakLanjut'     => $jenisTindakLanjut,
            'kondisiTindaklanjut'   => $kondisiTindaklanjut,
            'kondisiSetelah'        => config('constants.kondisi_layanan'),
            'peralatanGangguanIds'  => $peralatanGangguanIds,
            'step'                  => 3,
        ]);
    }


    /**
     * Function untuk menampilkan form tindak lanjut step 2 (tombol Back).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: URL: /logbook/laporan/tambah/step2/back{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep3Back($laporan_id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');
        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::findOrFail($laporan_id);
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($laporan->layanan_id);

        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiTindaklanjut = config('constants.kondisi_tindaklanjut');

        $tlPeralatan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->get()->groupBy('peralatan_id');
        $tlNon = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->first();

        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->get();
        $peralatanGangguanIds = $gangguanPeralatan->where('kondisi', 0)->pluck('peralatan_id')->toArray();

        return view('logbook.laporan.tambah.step3_back', [
            'judul'                 => 'Laporan',
            'module'                => 'Logbook',
            'menu'                  => 'Laporan',
            'menu_url'              => '/logbook/laporan/tambah/step3',
            'submenu'               => 'Tambah',
            'laporan'               => $laporan,
            'layanan'               => $layanan,
            'jenisTindakLanjut'     => $jenisTindakLanjut,
            'kondisiTindaklanjut'   => $kondisiTindaklanjut,
            'tindaklanjutPeralatan' => $tlPeralatan,
            'tindaklanjutNonPeralatan' => $tlNon,
            'kondisiSetelah'        => config('constants.kondisi_layanan'),
            'peralatanGangguanIds'  => $peralatanGangguanIds,
        ]);
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
        if (!Auth::check()) return redirect('/login');

        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (!in_array(session()->get('role_id'), [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi')
        ])) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI ======================= //

        // Ambil konfigurasi kondisi valid
        $validKondisi = array_values(config('constants.kondisi_tindaklanjut'));

        // Validasi umum
        $request->validate([
            'laporan_id' => 'required|integer|exists:laporan,id',
            'layanan_id' => 'required|integer|exists:layanan,id',
            'jenis_laporan' => ['required', Rule::in([1, 0])],
            'kondisi_setelah' => ['required', Rule::in(['1', '0', 1, 0])],
        ]);

        try {
            $userId = Auth::id();

            $laporan = Laporan::find($request->laporan_id);
            if (!$laporan) {
                return back()->withErrors(['msg' => 'Laporan tidak ditemukan.']);
            }
            $laporan->kondisi_layanan_temp = $request->kondisi_setelah;
            $laporan->save();

            \Log::info('Kondisi setelah:', [$request->kondisi_setelah]);
            \Log::info('Semua input:', $request->all());

            if ($request->jenis_laporan == 1) {
                // Validasi untuk gangguan peralatan
                $request->validate([
                    'tindaklanjut' => 'required|array',
                    'tindaklanjut.*.kondisi' => ['required', Rule::in(['1', '0', 1, 0])],
                    'tindaklanjut.*.jenis' => ['required', Rule::in(['1', '0', 1, 0])],
                    'tindaklanjut.*.waktu' => 'required|date',
                    'tindaklanjut.*.deskripsi' => 'nullable|string',
                ]);

                foreach ($request->tindaklanjut as $peralatanId => $tl) {
                    $gangguan = GangguanPeralatan::where('laporan_id', $request->laporan_id)
                        ->where('peralatan_id', $peralatanId)
                        ->latest()
                        ->first();

                    if (!$gangguan) {
                        return back()->withErrors(['msg' => "Data gangguan peralatan untuk ID $peralatanId tidak ditemukan."]);
                    }

                    TlGangguanPeralatan::create([
                        'gangguan_peralatan_id' => $gangguan->id,
                        'laporan_id' => $request->laporan_id,
                        'layanan_id' => $request->layanan_id,
                        'peralatan_id' => $peralatanId,
                        'waktu' => $tl['waktu'],
                        'deskripsi' => $tl['deskripsi'] ?? null,
                        'kondisi' => $tl['kondisi'],
                        'jenis_tindaklanjut' => $tl['jenis'],
                        'created_by' => $userId,
                    ]);
                }

            } else {
                // Validasi gangguan non-peralatan
                $request->validate([
                    'waktu' => 'required|date',
                    'deskripsi' => 'nullable|string',
                    'kondisi' => ['required', Rule::in($validKondisi)],
                ]);

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
                    'kondisi' => $request->kondisi,
                    'created_by' => $userId,
                ]);
            }

         // Redirect sesuai jenis tindak lanjut
        if ($request->jenis_laporan == 1) {
            $adaPenggantian = false;
            foreach ($request->tindaklanjut as $tl) {
                if ($tl['jenis'] == 0 || $tl['jenis'] === '0') {  // cek penggantian (0)
                    $adaPenggantian = true;
                    break;
                }
            }

            if ($adaPenggantian) {
                // Jika ada penggantian → ke Step 4
                return redirect()->route('tambah.step4', ['laporan_id' => $request->laporan_id])
                    ->with('notif', 'tambah_sukses');
            } else {
                // Jika semua perbaikan → langsung ke Step 5
                return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                    ->with('notif', 'tambah_sukses');
            }
        } else {
            // Non-peralatan langsung ke Step 5
            return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                ->with('notif', 'tambah_sukses');
        }



        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()])
                ->withInput()
                ->with('notif', 'tambah_gagal');
        }
    }



     /**
     * Function untuk mengubah data di layanan step 3 (tombol Back).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: URL: /logbook/laporan/tambah/step3/back{id}
     *
     * @return \Illuminate\Http\Response
     */

    public function tambahStep3Back(Request $request)
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


        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_laporan' => ['required', Rule::in([1, 0])],
            'kondisi_setelah' => ['required', Rule::in(['1', '0', 1, 0])],
        ]);

        try {
            $userId = Auth::id();

            $laporan = Laporan::find($request->laporan_id);
            if (!$laporan) {
                return back()->withErrors(['msg' => 'Laporan tidak ditemukan.']);
            }
            $laporan->kondisi_layanan_temp = $request->kondisi_setelah;
            $laporan->save();

            if ($request->jenis_laporan == 1) {
                foreach ($request->tindaklanjut as $peralatanId => $data) {
                    TlGangguanPeralatan::updateOrCreate(
                        [
                            'laporan_id' => $request->laporan_id,
                            'peralatan_id' => $peralatanId,
                        ],
                        [
                            'layanan_id' => $request->layanan_id,
                            'waktu' => $data['waktu'] ?? null,
                            'deskripsi' => $data['deskripsi'] ?? null,
                            'kondisi' => $data['kondisi'] ?? null,
                            'jenis_tindaklanjut' => $data['jenis'] ?? null,
                            'created_by' => $userId,
                        ]
                    );
                }
            } else {
                TlGangguanNonPeralatan::updateOrCreate(
                    ['laporan_id' => $request->laporan_id],
                    [
                        'layanan_id' => $request->layanan_id,
                        'waktu' => $request->waktu,
                        'deskripsi' => $request->deskripsi,
                        'kondisi' => 1,
                        'created_by' => $userId,
                    ]
                );
            }

          // Redirect sesuai jenis tindak lanjut
            if ($request->jenis_laporan == 1) {
                $adaPenggantian = false;
                foreach ($request->tindaklanjut as $tl) {
                    if ($tl['jenis'] == 0 || $tl['jenis'] === '0') {  // cek penggantian (0)
                        $adaPenggantian = true;
                        break;
                    }
                }

                if ($adaPenggantian) {
                    // Jika ada penggantian → ke Step 4
                    return redirect()->route('tambah.step4', ['laporan_id' => $request->laporan_id])
                        ->with('notif', 'tambah_sukses');
                } else {
                    // Jika semua perbaikan → langsung ke Step 5
                    return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                        ->with('notif', 'tambah_sukses');
                }
            } else {
                // Non-peralatan langsung ke Step 5
                return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                    ->with('notif', 'tambah_sukses');
            }


        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()])
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
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != true) {
            return redirect('/logout');
        }

        if (!in_array(session()->get('role_id'), [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi'),
        ])) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        /* ---------- Ambil Data Inti ---------- */
        $laporan  = Laporan::with('layanan')->findOrFail($laporan_id);
        $jenisTl  = null;
        $peralatanLama = collect();
        $peralatanTersedia = collect();

        $kodePenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');
        $kodeGangguan    = (int) config('constants.kondisi_gangguan_peralatan.gangguan');

        /* ---------- Map kondisi bool → label ---------- */
        $kondisiLabel = collect(config('constants.kondisi_peralatan'))
                        ->mapWithKeys(fn($v,$k)=>[$v => ucfirst($k)]);

        /* ---------- Jika laporan jenis 1 (gangguan peralatan) ---------- */
        if ($laporan->jenis == 1) {
            $tl = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tl) {
                $jenisTl = (int) $tl->jenis_tindaklanjut;

                if ($jenisTl === $kodePenggantian) {
                    // Ambil semua data gangguan untuk laporan ini
                    $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                                ->where('kondisi', $kodeGangguan) // hanya kondisi gangguan
                                ->get();

                    \Log::info('Gangguan:', $gangguan->toArray());

                    // Ambil data peralatan berdasarkan ID dari gangguan
                    $peralatanLama = $gangguan->map(function ($g) use ($kondisiLabel) {
                        $p = Peralatan::find($g->peralatan_id);
                        if (!$p) return null;

                        return (object)[
                            'id'    => $p->id,
                            'kode'  => $p->kode,
                            'nama'  => $p->nama,
                            'merk'  => $p->merk,
                            'tipe'  => $p->tipe,
                            'model' => $p->model,
                            'serial_number'=> $p->serial_number,
                            'status'=> $p->status ? 'Aktif':'Tidak Aktif',
                            'kondisi'=> $p->kondisi ? 'Normal' : 'Rusak',
                        ];
                    })->filter()->values();

                    // Log data peralatan gangguan
                    \Log::info('Peralatan Lama:', $peralatanLama->toArray());

                    // Ambil peralatan aktif yang belum digunakan
                    $peralatanTersedia = Peralatan::where('status', 1)
                        ->where('kondisi', 1) // hanya kondisi Normal
                        ->whereNotIn('id', $peralatanLama->pluck('id'))
                        ->get();
                }
            }
        } else {
            // gangguan non‑peralatan
            $tl = TlGangguanNonPeralatan::where('laporan_id',$laporan->id)->latest()->first();
            if ($tl) $jenisTl = (int) $tl->jenis_tindaklanjut;
        }

        // Kirim ke view
        return view('logbook.laporan.tambah.step4', [
            'judul'             => 'Laporan',
            'module'            => 'Logbook',
            'menu'              => 'Laporan',
            'menu_url'          => '/logbook/laporan/tambah/step4',
            'submenu'           => 'Tambah',
            'laporan'           => $laporan,
            'jenis_tindaklanjut'=> $jenisTl,
            'peralatanLama'     => $peralatanLama,
            'peralatanTersedia' => $peralatanTersedia,
            'jenis'             => JenisAlat::where('status',1)->get(),
            'perusahaan'        => Perusahaan::where('status',1)->get(),
        ]);
    }


    /**
     * Menampilkan form Step 4 (Penggantian) dengan prefill data sebelumnya.
     *
     * URL: /logbook/laporan/tambah/step4/back/{laporan_id}
     * Method: GET
     */
    public function formStep4Back($laporan_id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');

        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (!in_array(session()->get('role_id'), [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi'),
        ])) return redirect('/');
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan  = Laporan::with('layanan')->findOrFail($laporan_id);
        $jenisTl  = null;
        $peralatanLama = collect();
        $peralatanTersedia = collect();
        $peralatanBaru = [];

        $kodePenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');
        $kodeGangguan    = (int) config('constants.kondisi_gangguan_peralatan.gangguan');

        if ($laporan->jenis == 1) {
            $tl = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tl) {
                $jenisTl = (int) $tl->jenis_tindaklanjut;

                if ($jenisTl === $kodePenggantian) {
                    // Ambil semua gangguan pada peralatan (hanya yang kondisi gangguan)
                    $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                                ->where('kondisi', $kodeGangguan)
                                ->get();

                    // Ambil peralatan gangguan
                    $peralatanLama = $gangguan->map(function ($g) {
                        $p = Peralatan::find($g->peralatan_id);
                        if (!$p) return null;
                        return (object)[
                            'id'    => $p->id,
                            'kode'  => $p->kode,
                            'nama'  => $p->nama,
                            'merk'  => $p->merk,
                            'tipe'  => $p->tipe,
                            'model' => $p->model,
                            'serial_number'=> $p->serial_number,
                            'status'=> $p->status ? 'Aktif':'Tidak Aktif',
                            'kondisi'=> $p->kondisi ? 'Normal' : 'Rusak',
                        ];
                    })->filter()->values();

                    // Peralatan yang aktif dan normal, tidak termasuk peralatan gangguan
                    $peralatanTersedia = Peralatan::where('status', 1)
                        ->where('kondisi', 1)
                        ->whereNotIn('id', $peralatanLama->pluck('id'))
                        ->get();

                    // Ambil peralatan pengganti yang pernah dipilih sebelumnya
                    $pengganti = TlPenggantianPeralatan::where('laporan_id', $laporan->id)->get();

                    foreach ($pengganti as $idx => $pg) {
                        $pb = Peralatan::find($pg->peralatan_baru_id);
                        if ($pb) {
                            $peralatanBaru[$idx] = [
                                'id' => $pb->id,
                                'kode' => $pb->kode,
                                'nama' => $pb->nama,
                                'merk' => $pb->merk,
                                'tipe' => $pb->tipe,
                                'model' => $pb->model,
                                'serial_number' => $pb->serial_number,
                                'status' => $pb->status ? 'Aktif' : 'Tidak Aktif',
                                'kondisi' => $pb->kondisi ? 'Normal' : 'Rusak',
                            ];
                        }
                    }
                }
            }
        } else {
            $tl = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tl) $jenisTl = (int) $tl->jenis_tindaklanjut;
        }

        return view('logbook.laporan.tambah.step4_back', [
            'judul'             => 'Laporan',
            'module'            => 'Logbook',
            'menu'              => 'Laporan',
            'menu_url'          => '/logbook/laporan/tambah/step4/back',
            'submenu'           => 'Tambah',
            'laporan'           => $laporan,
            'jenis_tindaklanjut'=> $jenisTl,
            'peralatanLama'     => $peralatanLama,
            'peralatanTersedia' => $peralatanTersedia,
            'jenis'             => JenisAlat::where('status', 1)->get(),
            'perusahaan'        => Perusahaan::where('status', 1)->get(),
            'peralatanBaru'     => $peralatanBaru,
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

        if ($request->filled('sewa')) {
            $query->where('sewa', $request->sewa);
        }

        if ($request->filled('perusahaan')) {
            $query->where('perusahaan_id', $request->perusahaan);
        }

        // Hanya kondisi normal
        $query->where('kondisi', 1);

        // Ambil ID peralatan yang sudah dipakai
        $idTerpakai = GangguanPeralatan::pluck('peralatan_id')->toArray();

        // Filter agar hanya peralatan yang belum pernah dipakai
        $query->whereNotIn('id', $idTerpakai);

        // Ambil OBJEK peralatan, bukan hanya ID
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

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'laporan_id' => 'required|exists:laporan,id',
                'penggantian' => 'required|array|min:1',
                'penggantian.*.peralatan_lama_id' => 'required|exists:peralatan,id',
                'penggantian.*.peralatan_baru_id' => 'required|exists:peralatan,id|different:penggantian.*.peralatan_lama_id',
            ]);

            $laporanId = $validated['laporan_id'];
            $dataPenggantian = $validated['penggantian'];

            $laporan = Laporan::with('layanan')->findOrFail($laporanId);

            // Ambil tindak lanjut gangguan
            $tl = null;
            if ($laporan->jenis == 1) {
                $tl = TlGangguanPeralatan::where('laporan_id', $laporanId)->latest()->first();
            } else {
                $tl = TlGangguanNonPeralatan::where('laporan_id', $laporanId)->latest()->first();
            }

            if (!$tl || (int)$tl->jenis_tindaklanjut !== (int)config('constants.jenis_tindaklanjut.penggantian')) {
                return redirect()->back()->with('error', 'Jenis tindak lanjut bukan penggantian.');
            }

            // Hapus data lama
            TlPenggantianPeralatan::where('laporan_id', $laporanId)->delete();

            $saved = 0;

            foreach ($dataPenggantian as $i => $item) {
                if ($item['peralatan_lama_id'] === $item['peralatan_baru_id']) {
                    Log::warning("Step 4: Peralatan lama & baru sama (laporan_id: $laporanId, index: $i)");
                    continue;
                }

                // Ambil gangguan terkait peralatan_lama_id
                $gangguan = GangguanPeralatan::where('laporan_id', $laporanId)
                    ->where('peralatan_id', $item['peralatan_lama_id'])
                    ->first();

                TlPenggantianPeralatan::create([
                    'tl_gangguan_id'      => $tl->id,
                    'laporan_id'          => $laporanId,
                    'layanan_id'          => $laporan->layanan_id,
                    'peralatan_lama_id'   => $item['peralatan_lama_id'],
                    'peralatan_baru_id'   => $item['peralatan_baru_id'],
                    'created_by'          => auth()->user()->id,
                ]);

                $saved++;
            }

            DB::commit();

            if ($saved === 0) {
                Log::info("Step 4: Tidak ada data valid disimpan untuk laporan ID {$laporanId}");
                return redirect()->back()->with('warning', 'Tidak ada data yang disimpan. Periksa kembali isian Anda.');
            }

            return redirect()->route('tambah.step5', ['laporan_id' => $laporanId])
                            ->with('success', 'Data penggantian berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal simpan Step 4: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
    /**
     * Function untuk menyimpan data penggantian peralatan dari Step 4 Back.
     *
     * URL: /logbook/laporan/tambah/step4/back
     * Method: POST
     */
    public function tambahStep4Back(Request $request)
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

        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
            'jenis_tindaklanjut' => 'required|integer',
            'peralatan_baru' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $laporan = Laporan::findOrFail($request->laporan_id);

            // Ambil data tindaklanjut peralatan terbaru
            $tlGangguan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->get()->keyBy('peralatan_id');

            // Loop peralatan baru berdasarkan index
            foreach ($request->peralatan_baru as $index => $pb) {
                if (!empty($pb['id'])) {
                    // Ambil peralatan lama sesuai urutan layanan
                    $peralatanLama = $laporan->layanan->daftarPeralatanLayanan()->with('peralatan')->get()[$index] ?? null;

                    if ($peralatanLama && isset($tlGangguan[$peralatanLama->peralatan_id])) {
                        TlPenggantianPeralatan::updateOrCreate(
                            [
                                'laporan_id' => $laporan->id,
                                'tl_gangguan_id' => $tlGangguan[$peralatanLama->peralatan_id]->id,
                            ],
                            [
                                'layanan_id' => $laporan->layanan_id,
                                'peralatan_lama_id' => $peralatanLama->peralatan_id,
                                'peralatan_baru_id' => $pb['id'],
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('tambah.step5', ['laporan_id' => $laporan->id])
                            ->with('notif', 'tambah_sukses');
        } catch (\Exception $e) {
            DB::rollBack();

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
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::with([
            'layanan.fasilitas',
            'layanan.LokasiTk1',
            'layanan.LokasiTk2',
            'layanan.LokasiTk3',
            'gangguanNonPeralatan'
        ])->findOrFail($laporan_id);

        $detailGangguanPeralatan = collect();
        $penggantian = collect();
        $perbaikan = collect();
        $tindaklanjut = null;

        if ($laporan->jenis == 1) {
            // Ambil semua gangguan peralatan
            $detailGangguanPeralatan = GangguanPeralatan::with('peralatan')
                ->where('laporan_id', $laporan->id)
                ->get();

            // Cek apakah ada penggantian
            $penggantianCount = TlPenggantianPeralatan::where('laporan_id', $laporan->id)->count();
            
            if ($penggantianCount > 0) {
                // Jika ada penggantian, ambil data penggantian dengan tindaklanjut
                $penggantian = TlPenggantianPeralatan::with([
                    'peralatanLama',
                    'peralatanBaru',
                    'tindaklanjut' => function ($query) {
                        $query->select('id', 'jenis_tindaklanjut', 'deskripsi', 'waktu', 'kondisi');
                    },
                ])
                ->where('laporan_id', $laporan->id)
                ->get();
            } else {
                // Jika tidak ada penggantian, ambil tindaklanjut langsung
                $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                    ->latest('waktu')
                    ->first();
            }

            // Ambil semua data perbaikan untuk keperluan lain jika diperlukan
            $perbaikan = TlGangguanPeralatan::with(['peralatan'])
                ->where('laporan_id', $laporan->id)
                ->where('jenis_tindaklanjut', config('constants.jenis_tindaklanjut.perbaikan'))
                ->get();

        } else {
            // Untuk gangguan non-peralatan
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();
        }

        return view('logbook.laporan.tambah.step5', [
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step5',
            'submenu' => 'Tambah',
            'laporan' => $laporan,
            'detailGangguanPeralatan' => $detailGangguanPeralatan,
            'penggantian' => $penggantian,
            'perbaikan' => $perbaikan,
            'tindaklanjut' => $tindaklanjut,
        ]);
    }

    /**
     * Function untuk menyimpan data laporan 
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/simpanStep5
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanStep5(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }    

        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }

        if(session()->get('role_id') != config('constants.role.super_admin')
        && session()->get('role_id') != config('constants.role.admin')
        && session()->get('role_id') != config('constants.role.teknisi')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
        ]);

        DB::beginTransaction();
        try {
            $laporan = Laporan::findOrFail($request->laporan_id);

            // Update status berdasarkan kondisi layanan
            if ($laporan->kondisi_layanan_temp) { // SERVICEABLE
                $laporan->status = config('constants.status_laporan.closed');
            } else { // UNSERVICEABLE
                $laporan->status = config('constants.status_laporan.open');
            }

            // Update waktu selesai jika laporan ditutup
            if ($laporan->status == config('constants.status_laporan.closed')) {
                $laporan->waktu_selesai = now();
            }

            $laporan->save();

            DB::commit();

            // Redirect berdasarkan status laporan
            if ($laporan->status == config('constants.status_laporan.closed')) {
                return redirect()->route('logbook.riwayat.daftar')
                    ->with('notif', 'tambah_sukses')
                    ->with('message', 'Laporan berhasil disimpan dan ditutup.');
            } else {
                return redirect()->route('logbook.laporan.daftar')
                    ->with('notif', 'tambah_sukses')
                    ->with('message', 'Laporan berhasil disimpan dan masih dalam status terbuka.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ERROR SIMPAN STEP 5: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan laporan. Silakan coba lagi.');
        }
    }

    /**
     * Function untuk menghapus laporan yang berstatus DRAFT.
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/hapus
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hapus(Request $request)
    {
        $laporan = Laporan::where('id', $request->id)
            ->where('status', config('constants.status_laporan.draft'))
            ->first();

        if ($laporan == null) {
            return redirect('/logbook/laporan/daftar')->with('notif', 'item_null');
        }

        try {
            // Hapus data terkait jika perlu
            GangguanPeralatan::where('laporan_id', $request->id)->delete();

            // Hapus laporan
            $laporan->delete();
        } catch (QueryException $ex) {
            return redirect('/logbook/laporan/daftar')->with('notif', 'hapus_gagal');
        }

        return redirect('/logbook/laporan/daftar')->with('notif', 'hapus_sukses');
    }


    /**
     * Function untuk detail laporan
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        $laporan_id = $request->id;
        $laporan = Laporan::with([
            'layanan.fasilitas',
            'layanan.lokasiTk1',
            'layanan.lokasiTk2',
            'layanan.lokasiTk3',
            'gangguanNonPeralatan',
            'getCreatedName',      
            'getUpdatedName'
        ])->find($laporan_id);

        if (!$laporan) {
            return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
        }

        $detailGangguanPeralatan = collect();
        $penggantian = collect();
        $perbaikan = collect();
        $tindaklanjut = null;

        if ($laporan->jenis == 1) {
            $detailGangguanPeralatan = GangguanPeralatan::with('peralatan')
                ->where('laporan_id', $laporan->id)
                ->get();

            $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();

            $penggantian = TlPenggantianPeralatan::with([
                'peralatanLama',
                'peralatanBaru',
                'tindaklanjut'
            ])
                ->where('laporan_id', $laporan->id)
                ->get();

            $perbaikan = TlGangguanPeralatan::with('peralatan')
                ->where('laporan_id', $laporan->id)
                ->where('jenis_tindaklanjut', config('constants.jenis_tindaklanjut.perbaikan'))
                ->get();
        } else {
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();
        }

        return response()->json([
            'laporan' => $laporan,
            'detailGangguanPeralatan' => $detailGangguanPeralatan,
            'penggantian' => $penggantian,
            'perbaikan' => $perbaikan,
            'tindaklanjut' => $tindaklanjut,
            'gangguanNonPeralatan' => $laporan->gangguanNonPeralatan,
        ]);
    }

    /**
     * Function untuk menampilkan edit step 2 (hanya status "draft").
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: GET
     * URL: laporan/{id}/edit/step2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editStep2($id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }    
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        if(session()->get('role_id') != config('constants.role.super_admin')
        && session()->get('role_id') != config('constants.role.admin')
        && session()->get('role_id') != config('constants.role.teknisi')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // Ambil laporan berdasarkan ID
        $laporan = Laporan::findOrFail($id);

        // Validasi hanya bisa edit draft (status = 1)
        if ($laporan->status !== config('constants.status_laporan.draft')) {
            return redirect()->route('logbook.laporan.daftar')
                            ->with('notif', 'item_null');
        }

        // Ambil data layanan terkait laporan (sama seperti formStep2Back)
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->findOrFail($laporan->layanan_id);

        $jenisLaporan = config('constants.jenis_laporan');

        // Ambil data gangguan sebelumnya (sama seperti formStep2Back)
        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->get();
        $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->first();

        // Konversi jenis laporan ke string untuk konsistensi dengan formStep2Back
        $jenisLaporanString = $laporan->jenis == 1 ? 'gangguan_peralatan' : 'gangguan_non_peralatan';

        return view('logbook.laporan.edit.step2')->with([
            'judul' => 'Edit Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/daftar',
            'submenu' => 'Edit',
            'layanan' => $layanan,
            'jenisLaporan' => $jenisLaporan,
            'laporan' => $laporan,
            'selectedJenisLaporan' => old('jenis_laporan', $jenisLaporanString),
            'waktuGangguan' => old('waktu_gangguan', $laporan->waktu),
            'gangguanPeralatan' => $gangguanPeralatan,
            'gangguanNonPeralatan' => $gangguanNonPeralatan,
        ]);
    }

    /**
     * Function untuk menyimpan/update edit step 2 (hanya status "draft").
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: POST
     * URL: laporan/{id}/edit/step2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStep2(Request $request, $id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }    
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }
        if(session()->get('role_id') != config('constants.role.super_admin')
        && session()->get('role_id') != config('constants.role.admin')
        && session()->get('role_id') != config('constants.role.teknisi')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        Log::info('Mulai proses updateStep2', ['laporan_id' => $id, 'request' => $request->all()]);

        $laporan = Laporan::findOrFail($id);

        // Validasi hanya bisa edit draft (status = 1)
        if ($laporan->status !== config('constants.status_laporan.draft')) {
            return redirect()->route('logbook.laporan.daftar')
                            ->with('notif', 'item_null');
        }

        // ---------------- VALIDASI INPUT ----------------
        $rules = [
            'waktu_gangguan' => 'required|date',
        ];

        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $rules += [
                'gangguan'              => 'required|array|min:1',
                'gangguan.*.id'         => 'required|exists:peralatan,id',
                'gangguan.*.kondisi'    => ['required', Rule::in(['0', '1'])],
                'gangguan.*.deskripsi'  => 'nullable|string',
            ];
        } else {
            $rules['deskripsi_gangguan'] = 'nullable|string';
        }

        $messages = [
            'waktu_gangguan.*'      => 'Waktu gangguan wajib diisi & valid.',
            'gangguan.*.id.*'       => 'Peralatan wajib dipilih & valid.',
            'gangguan.*.kondisi.*'  => 'Kondisi gangguan wajib dipilih & valid.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
            Log::info('Validasi input update Step 2 berhasil.', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi update Step 2 gagal.', $e->errors());
            throw $e;
        }

        // ---------------- UPDATE KE DATABASE ----------------
        $userId = Auth::id();
        DB::beginTransaction();

        try {
            // Update waktu gangguan pada laporan
            $laporan->waktu = $request->waktu_gangguan;
            $laporan->updated_by = $userId;
            $laporan->save();

            Log::info("Laporan berhasil diupdate", ['laporan_id' => $laporan->id]);

            if ($request->jenis_laporan === 'gangguan_peralatan' && !empty($request->gangguan)) {
                // Hapus data gangguan peralatan yang lama
                GangguanPeralatan::where('laporan_id', $laporan->id)->delete();

                // Simpan data gangguan peralatan yang baru
                foreach ($request->gangguan as $g) {
                    GangguanPeralatan::create([
                        'laporan_id'     => $laporan->id,
                        'layanan_id'     => $laporan->layanan_id,
                        'peralatan_id'   => $g['id'],
                        'waktu_gangguan' => $request->waktu_gangguan,
                        'kondisi'        => $g['kondisi'],
                        'deskripsi'      => $g['deskripsi'] ?? null,
                        'created_by'     => $userId,
                    ]);
                }

                Log::info('Data gangguan peralatan berhasil diupdate.', ['total' => count($request->gangguan)]);

            } elseif ($request->jenis_laporan === 'gangguan_non_peralatan') {
                // Update atau create gangguan non-peralatan
                GangguanNonPeralatan::updateOrCreate(
                    ['laporan_id' => $laporan->id],
                    [
                        'layanan_id'     => $laporan->layanan_id,
                        'waktu_gangguan' => $request->waktu_gangguan,
                        'deskripsi'      => $request->deskripsi_gangguan ?? null,
                        'created_by'     => $userId,
                    ]
                );

                Log::info('Data gangguan non-peralatan berhasil diupdate.');
            }

            DB::commit();

            Log::info("Proses update Step 2 selesai.", ['laporan_id' => $laporan->id]);
            
            // PERBAIKAN: Tambahkan parameter $id ke route
            return redirect()
                ->route('logbook.laporan.edit.step3', ['id' => $id])
                ->with('notif', 'simpan_sukses');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal update laporan Step 2.', [
                'laporan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['msg' => 'Gagal update laporan: ' . $e->getMessage()])
                ->with('notif', 'simpan_gagal');
        }
    }

    /**
     * Function untuk menampilkan form edit step 3 (tindak lanjut).
     * 
     * Untuk status draft: edit dimulai dari step 3
     * Untuk status open: menambah tindak lanjut baru
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/edit/{laporan_id}/step3
     *
     * @param int $laporan_id
     * @return \Illuminate\Http\Response
     */
    public function editStep3($id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');
        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::findOrFail($id);
        
        // Validasi status laporan yang bisa diedit
        if (!in_array($laporan->status, [
            config('constants.status_laporan.draft'), 
            config('constants.status_laporan.open')
        ])) {
            return redirect()->route('logbook.laporan.daftar')
                ->with('notif', 'edit_gagal')
                ->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        // Untuk status open, pastikan kondisi layanan unserviceable
        if ($laporan->status == config('constants.status_laporan.open') && $laporan->kondisi_layanan != 0) {
            return redirect()->route('logbook.detail', $id)
                ->with('notif', 'edit_gagal')
                ->withErrors(['msg' => 'Hanya laporan dengan kondisi unserviceable yang dapat ditambah tindak lanjut.']);
        }

        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($laporan->layanan_id);

        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiTindaklanjut = config('constants.kondisi_tindaklanjut');

        // Ambil data gangguan peralatan untuk menentukan peralatan mana yang perlu tindak lanjut
        $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->get();
        $peralatanGangguanIds = $gangguanPeralatan->where('kondisi', 0)->pluck('peralatan_id')->toArray();

        // PERBAIKAN: Ambil data tindak lanjut yang sudah ada untuk semua status
        $existingTindakLanjut = collect();
        $tlPeralatan = collect();
        $tlNon = null;

        if ($laporan->jenis == 1) {
            // Untuk gangguan peralatan
            $existingTindakLanjut = TlGangguanPeralatan::with('peralatan')
                ->where('laporan_id', $laporan->id)
                ->orderBy('waktu', 'desc')
                ->get();
            
            // Group by peralatan_id untuk pre-fill form (ambil yang terbaru untuk setiap peralatan)
            $tlPeralatan = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->get()
                ->groupBy('peralatan_id')
                ->map(function ($items) {
                    return $items->sortByDesc('waktu')->first(); // Ambil yang terbaru
                });
        } else {
            // Untuk gangguan non-peralatan
            $existingTindakLanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->orderBy('waktu', 'desc')
                ->get();
            
            // Ambil yang terbaru untuk pre-fill form
            $tlNon = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();
        }

        return view('logbook.laporan.edit.step3', [
            'judul'                 => 'Edit Laporan',
            'module'                => 'Logbook',
            'menu'                  => 'Laporan',
            'menu_url'              => '/logbook/laporan/edit/step3',
            'submenu'               => 'Edit',
            'laporan'               => $laporan,
            'layanan'               => $layanan,
            'jenisTindakLanjut'     => $jenisTindakLanjut,
            'kondisiTindaklanjut'   => $kondisiTindaklanjut,
            'kondisiSetelah'        => config('constants.kondisi_layanan'),
            'peralatanGangguanIds'  => $peralatanGangguanIds,
            'existingTindakLanjut'  => $existingTindakLanjut,
            'tindaklanjutPeralatan' => $tlPeralatan,
            'tindaklanjutNonPeralatan' => $tlNon,
            'isEdit'                => true,
        ]);
    }

    /**
     * Function untuk menyimpan/update data edit step 3 (tindak lanjut).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: PUT
     * URL: /logbook/laporan/edit/{laporan_id}/step3
     *
     * @param Request $request
     * @param int $laporan_id
     * @return \Illuminate\Http\Response
     */
    public function updateStep3(Request $request, $id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');
        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::findOrFail($id);
        
        // Validasi status laporan
        if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
            \Log::info('Status laporan tidak valid:', ['status' => $laporan->status]);
            return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        // Validasi umum
        $request->validate([
            'laporan_id' => 'required|integer|exists:laporan,id',
            'layanan_id' => 'required|integer|exists:layanan,id',
            'jenis_laporan' => ['required', Rule::in([1, 0])],
            'kondisi_setelah' => ['required', Rule::in(['1', '0', 1, 0])],
        ]);

        try {
            $userId = Auth::id();
            DB::beginTransaction();

            // UPDATE KONDISI LAYANAN - HANYA kondisi_layanan_temp seperti di simpanStep3
            $laporan->kondisi_layanan_temp = $request->kondisi_setelah;
            $laporan->save();

            \Log::info('Kondisi setelah:', [$request->kondisi_setelah]);
            \Log::info('Semua input:', $request->all());

            if ((int)$request->jenis_laporan === 1) {
                // Validasi untuk gangguan peralatan
                $request->validate([
                    'tindaklanjut' => 'required|array',
                    'tindaklanjut.*.kondisi' => ['required', Rule::in(['1', '0', 1, 0])],
                    'tindaklanjut.*.jenis' => ['required', Rule::in(['1', '0', 1, 0])],
                    'tindaklanjut.*.waktu' => 'required|date',
                    'tindaklanjut.*.deskripsi' => 'nullable|string',
                ]);

                // JANGAN HAPUS DATA LAMA - hanya tambah yang baru
                foreach ($request->tindaklanjut as $peralatanId => $tl) {
                    $gangguan = GangguanPeralatan::where('laporan_id', $id)
                        ->where('peralatan_id', $peralatanId)
                        ->latest()
                        ->first();

                    if (!$gangguan) {
                        throw new \Exception("Data gangguan peralatan untuk ID $peralatanId tidak ditemukan.");
                    }

                    TlGangguanPeralatan::create([
                        'gangguan_peralatan_id' => $gangguan->id,
                        'laporan_id' => $id,
                        'layanan_id' => $request->layanan_id,
                        'peralatan_id' => $peralatanId,
                        'waktu' => $tl['waktu'],
                        'deskripsi' => $tl['deskripsi'] ?? null,
                        'kondisi' => $tl['kondisi'],
                        'jenis_tindaklanjut' => $tl['jenis'],
                        'created_by' => $userId,
                    ]);
                }

            } else {
                // Validasi gangguan non-peralatan
                $request->validate([
                    'waktu' => 'required|date',
                    'deskripsi' => 'nullable|string',
                    'kondisi' => ['required', Rule::in($validKondisi)],
                ]);

                $gangguan = GangguanNonPeralatan::where('laporan_id', $id)->latest()->first();

                if (!$gangguan) {
                    throw new \Exception('Data gangguan non-peralatan tidak ditemukan.');
                }

                TlGangguanNonPeralatan::create([
                    'gangguan_non_peralatan_id' => $gangguan->id,
                    'laporan_id' => $id,
                    'layanan_id' => $request->layanan_id,
                    'waktu' => $request->waktu,
                    'deskripsi' => $request->deskripsi,
                    'kondisi' => $request->kondisi,
                    'created_by' => $userId,
                ]);
            }

            DB::commit();

            // Redirect sesuai jenis tindak lanjut - sama seperti simpanStep3
            if ((int)$request->jenis_laporan === 1) {
                $adaPenggantian = false;
                foreach ($request->tindaklanjut as $tl) {
                    if ($tl['jenis'] == 0 || $tl['jenis'] === '0') {
                        $adaPenggantian = true;
                        break;
                    }
                }

                // Tambahkan log untuk debugging
                \Log::info('Redirecting to step:', [
                    'jenis_laporan' => $request->jenis_laporan,
                    'adaPenggantian' => $adaPenggantian,
                ]);

                if ($adaPenggantian) {
                    return redirect()->route('logbook.laporan.edit.step4', $id)
                        ->with('notif', 'edit_sukses');
                } else {
                    return redirect()->route('logbook.laporan.edit.step5', $id)
                        ->with('notif', 'edit_sukses');
                }
            } else {
                return redirect()->route('logbook.laporan.edit.step5', $id)
                    ->with('notif', 'edit_sukses');
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error occurred:', ['message' => $e->getMessage()]);
            return back()->withErrors(['msg' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()])
                ->withInput()
                ->with('notif', 'edit_gagal');
        }
    }

    /**
     * Function untuk menampilkan data edit step 4 (penggantian).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/edit/{laporan_id}/step4
     *
     * @param Request $request
     * @param int $laporan_id
     * @return \Illuminate\Http\Response
     */
    public function editStep4($id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');

        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (!in_array(session()->get('role_id'), [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi'),
        ])) return redirect('/');
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::with('layanan')->findOrFail($id);
        
       // Validasi status laporan
        if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
            \Log::info('Status laporan tidak valid:', ['status' => $laporan->status]);
            return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        $jenisTl = null;
        $peralatanLama = collect();
        $peralatanTersedia = collect();
        $penggantiPeralatan = collect();

        $kodePenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');
        $kodeGangguan = (int) config('constants.kondisi_gangguan_peralatan.gangguan');

        if ($laporan->jenis == 1) {
            $tl = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tl) {
                $jenisTl = (int) $tl->jenis_tindaklanjut;

                if ($jenisTl === $kodePenggantian) {
                    // Ambil semua gangguan pada peralatan (hanya yang kondisi gangguan)
                    $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                        ->where('kondisi', $kodeGangguan)
                        ->get();

                    // Ambil peralatan gangguan
                    $peralatanLama = $gangguan->map(function ($g) {
                        $p = Peralatan::find($g->peralatan_id);
                        if (!$p) return null;
                        return (object)[
                            'id' => $p->id,
                            'kode' => $p->kode,
                            'nama' => $p->nama,
                            'merk' => $p->merk,
                            'tipe' => $p->tipe,
                            'model' => $p->model,
                            'serial_number' => $p->serial_number,
                            'status' => $p->status,
                            'kondisi' => $p->kondisi,
                        ];
                    })->filter()->values();

                    // Peralatan yang aktif dan normal, tidak termasuk peralatan gangguan
                    $peralatanTersedia = Peralatan::where('status', 1)
                        ->where('kondisi', 1)
                        ->whereNotIn('id', $peralatanLama->pluck('id'))
                        ->get();

                    // Ambil peralatan pengganti yang sudah ada sebelumnya
                    $pengganti = TlPenggantianPeralatan::where('laporan_id', $laporan->id)
                        ->with('peralatanBaru')
                        ->get();

                    // Group by peralatan_lama_id untuk mudah diakses di blade
                    $penggantiPeralatan = $pengganti->keyBy('peralatan_lama_id');
                }
            }
        } else {
            $tl = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tl) $jenisTl = (int) $tl->jenis_tindaklanjut;
        }

        return view('logbook.laporan.edit.step4', [
            'judul' => 'Edit Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/edit/step4',
            'submenu' => 'Edit',
            'laporan' => $laporan,
            'jenis_tindaklanjut' => $jenisTl,
            'peralatanLama' => $peralatanLama,
            'peralatanTersedia' => $peralatanTersedia,
            'penggantiPeralatan' => $penggantiPeralatan,
            'isEdit' => true,
            'jenis'             => JenisAlat::where('status', 1)->get(),
            'perusahaan'        => Perusahaan::where('status', 1)->get(),
            'penggantiPeralatan'     => $penggantiPeralatan,
        ]);
    }

    /**
     * Function untuk menyimpan/update data edit step 4 (penggantian).
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/edit/{laporan_id}/step4
     *
     * @param Request $request
     * @param int $laporan_id
     * @return \Illuminate\Http\Response
     */
    public function updateStep4(Request $request, $id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) return redirect('/login');

        $status = User::find(session()->get('id'))->status;
        if (!$status) return redirect('/logout');

        if (!in_array(session()->get('role_id'), [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi'),
        ])) return redirect('/');
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::findOrFail($id);
        
       // Validasi status laporan - hanya draft dan open yang bisa diedit
        if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
            return redirect()->route('logbook.laporan.daftar')
                ->with('notif', 'edit_gagal')
                ->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'laporan_id' => 'required|exists:laporan,id',
                'penggantian' => 'required|array|min:1',
                'penggantian.*.peralatan_lama_id' => 'required|exists:peralatan,id',
                'penggantian.*.peralatan_baru_id' => 'required|exists:peralatan,id|different:penggantian.*.peralatan_lama_id',
            ]);

            $dataPenggantian = $validated['penggantian'];

            // Ambil tindak lanjut gangguan
            $tl = TlGangguanPeralatan::where('laporan_id', $id)->latest()->first();

            if (!$tl || (int)$tl->jenis_tindaklanjut !== (int)config('constants.jenis_tindaklanjut.penggantian')) {
                return redirect()->back()->with('error', 'Jenis tindak lanjut bukan penggantian.');
            }

            // Hapus data penggantian lama
            TlPenggantianPeralatan::where('laporan_id', $id)->delete();

            $saved = 0;

            foreach ($dataPenggantian as $i => $item) {
                if ($item['peralatan_lama_id'] === $item['peralatan_baru_id']) {
                    Log::warning("Step 4: Peralatan lama & baru sama (laporan_id: $id, index: $i)");
                    continue;
                }

                TlPenggantianPeralatan::create([
                    'tl_gangguan_id'      => $tl->id,
                    'laporan_id'          => $id,
                    'layanan_id'          => $laporan->layanan_id,
                    'peralatan_lama_id'   => $item['peralatan_lama_id'],
                    'peralatan_baru_id'   => $item['peralatan_baru_id'],
                    'created_by'          => Auth::id(),
                ]);

                $saved++;
            }

            DB::commit();

            if ($saved === 0) {
                Log::info("Step 4: Tidak ada data valid disimpan untuk laporan ID {$id}");
                return redirect()->back()->with('warning', 'Tidak ada data yang disimpan. Periksa kembali isian Anda.');
            }

            // Redirect ke step 5
            return redirect()->route('logbook.laporan.edit.step5', $id)
                ->with('notif', 'edit_sukses')
                ->with('message', 'Data penggantian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update Step 4: " . $e->getMessage());
            
            return back()
                ->withErrors(['msg' => 'Gagal menyimpan data penggantian: ' . $e->getMessage()])
                ->withInput()
                ->with('notif', 'edit_gagal');
        }
    }

    /**
     * Function untuk menampilkan review data edit
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/edit/step5/{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function editStep5($id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (
            session()->get('role_id') != config('constants.role.super_admin') &&
            session()->get('role_id') != config('constants.role.admin') &&
            session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $laporan = Laporan::with([
            'layanan.fasilitas',
            'layanan.LokasiTk1',
            'layanan.LokasiTk2',
            'layanan.LokasiTk3'
        ])->findOrFail($id);

        // Validasi status laporan - hanya draft dan open yang bisa diedit
        if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
            return redirect()->route('logbook.laporan.daftar')
                ->with('notif', 'edit_gagal')
                ->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        $detailGangguanPeralatan = collect();
        $penggantian = collect();
        $perbaikan = collect();
        $tindaklanjut = null;
        $gangguanNonPeralatan = null;

        if ($laporan->jenis == 1) {
            // Ambil semua gangguan peralatan
            $detailGangguanPeralatan = GangguanPeralatan::with('peralatan')
                ->where('laporan_id', $laporan->id)
                ->get();

            // Ambil tindaklanjut terakhir dari tl_gangguan_peralatan
            $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();

            // Ambil semua data penggantian jika ada
            if ($tindaklanjut && $tindaklanjut->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian')) {
                $penggantian = TlPenggantianPeralatan::with([
                    'peralatanLama',
                    'peralatanBaru',
                    'tindaklanjut' => function ($query) {
                        $query->select('id', 'jenis_tindaklanjut', 'deskripsi', 'waktu', 'kondisi');
                    },
                ])
                    ->where('laporan_id', $laporan->id)
                    ->get();
            }

            // Ambil semua data perbaikan
            $perbaikan = TlGangguanPeralatan::with([
                'peralatan',
            ])
                ->where('laporan_id', $laporan->id)
                ->where('jenis_tindaklanjut', config('constants.jenis_tindaklanjut.perbaikan'))
                ->get();

        } else {
            // Ambil gangguan non peralatan
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest()
                ->first();

            // Ambil tindaklanjut terakhir dari tl_gangguan_non_peralatan
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();
        }

        return view('logbook.laporan.edit.step5', [
            'judul' => 'Edit Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/edit/step5',
            'submenu' => 'Edit Step 5',
            'laporan' => $laporan,
            'detailGangguanPeralatan' => $detailGangguanPeralatan,
            'penggantian' => $penggantian,
            'perbaikan' => $perbaikan,
            'tindaklanjut' => $tindaklanjut,
            'gangguanNonPeralatan' => $gangguanNonPeralatan,
            'isEdit' => true,
        ]);
    }

    /**
     * Function untuk update data laporan step 5
     * 
     * Akses:
     * - Teknisi
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/edit/step5/{id}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStep5(Request $request, $id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }    

        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }

        if(session()->get('role_id') != config('constants.role.super_admin')
        && session()->get('role_id') != config('constants.role.admin')
        && session()->get('role_id') != config('constants.role.teknisi')){
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
        ]);

        DB::beginTransaction();
        try {
            $laporan = Laporan::with('layanan')->findOrFail($id);

            // Validasi status laporan
            if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
                return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
            }

            // Update status berdasarkan kondisi layanan
            if ($laporan->kondisi_layanan_temp) { // SERVICEABLE
                $laporan->status = config('constants.status_laporan.closed');
            } else { // UNSERVICEABLE
                $laporan->status = config('constants.status_laporan.open');
            }

            // Update kondisi layanan dari temp ke aktual - UPDATE DI TABEL LAYANAN
            if ($laporan->layanan) {
                $laporan->layanan->kondisi = $laporan->kondisi_layanan_temp;
                $laporan->layanan->save();
            }
            
            // Update user yang melakukan perubahan terakhir
            $laporan->updated_by = Auth::id();
            
            $laporan->save();

            DB::commit();

            // Redirect berdasarkan status akhir
            if ($laporan->status == config('constants.status_laporan.closed')) {
                return redirect()->route('logbook.riwayat.daftar')
                    ->with('notif', 'edit_sukses');
            } else {
                return redirect()->route('logbook.laporan.daftar')
                    ->with('notif', 'edit_sukses');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ERROR UPDATE STEP 5: ' . $e->getMessage());
            
            return back()
                ->withErrors(['msg' => 'Terjadi kesalahan saat memperbarui laporan: ' . $e->getMessage()])
                ->with('notif', 'edit_gagal');
        }
    }

}
        