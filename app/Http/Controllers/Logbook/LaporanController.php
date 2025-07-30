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
 use Carbon\Carbon;
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
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = "/logbook/laporan/daftar";

        return view('logbook.laporan.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
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

    // KOSONGKAN daftar layanan di load awal - user harus klik filter terlebih dahulu
    $layanan = collect(); // collection kosong

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
     && session()->get('role_id') != config('constants.role.admin')
     && session()->get('role_id') != config('constants.role.teknisi')){
        return redirect('/');
    }
    // ===================== AKHIR PROSES VERIFIKASI =======================

    // ambil ID layanan yang sudah memiliki laporan dengan status "open"
    $layanan_open_ids = DB::table('laporan')
                         ->where('status', config('constants.status_laporan.open'))
                         ->pluck('layanan_id')
                         ->toArray();

    // ambil daftar Layanan yang aktif dan TIDAK memiliki laporan dengan status "open"
    $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3'])
              ->where('status', 1);
    
    // Exclude layanan yang sudah ada laporan open
    if (!empty($layanan_open_ids)) {
        $query->whereNotIn('id', $layanan_open_ids);
    }

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
     * Function untuk menampilkan form tambah layanan step 2.
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
     * URL: /logbook/laporan/tambah/step2/back{id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formStep2Back(Request $request, $laporan_id)
{
    // ========================= PROSES VERIFIKASI ========================
    if (!Auth::check()) return redirect('/login');
    
    $status = User::find(session()->get('id'))->status;
    if(!$status) return redirect('/logout');
    
    if(!in_array(session()->get('role_id'), [
        config('constants.role.super_admin'),
        config('constants.role.admin'),
        config('constants.role.teknisi')
    ])) {
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

    // AMBIL WAKTU GANGGUAN DARI TABEL LAPORAN (waktu_open)
    $waktuGangguanFormatted = '';
    
    if ($laporan->waktu_open) {
        try {
            // Ambil dari waktu_open di tabel laporan
            $waktuGangguanFormatted = Carbon::parse($laporan->waktu_open)->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            \Log::error('Error formatting waktu_open: ' . $e->getMessage());
        }
    } else {
        // Fallback: ambil dari tabel gangguan jika waktu_open kosong
        if ($laporan->jenis == 1) {
            $firstGangguan = $gangguanPeralatan->first();
            if ($firstGangguan && $firstGangguan->waktu_gangguan) {
                try {
                    $waktuGangguanFormatted = Carbon::parse($firstGangguan->waktu_gangguan)->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    \Log::error('Error formatting waktu gangguan peralatan: ' . $e->getMessage());
                }
            }
        } else {
            if ($gangguanNonPeralatan && $gangguanNonPeralatan->waktu_gangguan) {
                try {
                    $waktuGangguanFormatted = Carbon::parse($gangguanNonPeralatan->waktu_gangguan)->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    \Log::error('Error formatting waktu gangguan non-peralatan: ' . $e->getMessage());
                }
            }
        }
    }

    // Debug log untuk melihat data yang dikirim
    \Log::info('Step2Back Debug:', [
        'laporan_jenis' => $laporan->jenis,
        'laporan_waktu_open' => $laporan->waktu_open, // UBAH dari waktu ke waktu_open
        'laporan_waktu_close' => $laporan->waktu_close,
        'gangguan_peralatan_count' => $gangguanPeralatan->count(),
        'gangguan_peralatan_waktu' => $gangguanPeralatan->first()?->waktu_gangguan,
        'gangguan_non_peralatan' => $gangguanNonPeralatan ? 'exists' : 'null',
        'gangguan_non_peralatan_waktu' => $gangguanNonPeralatan?->waktu_gangguan,
        'waktu_formatted' => $waktuGangguanFormatted,
    ]);

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
        'waktuGangguan' => old('waktu_gangguan', $waktuGangguanFormatted),
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
        // FORMAT WAKTU SEBELUM DISIMPAN
        $waktuGangguan = Carbon::parse($request->waktu_gangguan);
        
        $laporan = Laporan::create([
            'no_laporan'           => now()->format('YmdHis') . rand(100, 999),
            'layanan_id'           => $request->layanan_id,
            'jenis'                => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
            'waktu_open'           => $waktuGangguan, // UBAH dari 'waktu' ke 'waktu_open'
            'waktu_close'          => null, // Belum ditutup
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
                    'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object yang sama
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
                'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object yang sama
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
 * URL: /logbook/laporan/tambah/step2/back{id}
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
            'gangguan.*.kondisi'  => ['required', Rule::in(['0', '1'])],
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

    try {
        $validated = $request->validate($rules, $messages);
        Log::info('Validasi input Step 2 Back berhasil.', $validated);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validasi Step 2 Back gagal.', $e->errors());
        throw $e;
    }
    /* ----------- AKHIR VALIDASI ----------- */

    /* ------------- SIMPAN PERUBAHAN ------------- */
    DB::beginTransaction();
    try {
        $userId = Auth::id();
        
        // FORMAT WAKTU SEBELUM UPDATE
        $waktuGangguan = Carbon::parse($request->waktu_gangguan);

        $laporan = Laporan::findOrFail($request->laporan_id);
        $laporan->update([
            'layanan_id' => $request->layanan_id,
            'jenis'      => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
            'waktu_open' => $waktuGangguan, // UBAH dari 'waktu' ke 'waktu_open'
            'status'     => 1,
            'updated_by' => $userId, // Tambahkan updated_by
        ]);

        Log::info("Laporan berhasil diupdate", ['laporan_id' => $laporan->id]);

        /* hapus gangguan lama */
        GangguanPeralatan::where('laporan_id', $laporan->id)->delete();
        GangguanNonPeralatan::where('laporan_id', $laporan->id)->delete();

        Log::info("Data gangguan lama berhasil dihapus", ['laporan_id' => $laporan->id]);

        if ($request->jenis_laporan === 'gangguan_peralatan' && !empty($request->gangguan)) {
            foreach ($request->gangguan as $g) {
                GangguanPeralatan::create([
                    'laporan_id'     => $laporan->id,
                    'layanan_id'     => $request->layanan_id,
                    'peralatan_id'   => $g['id'],
                    'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object yang sama
                    'kondisi'        => $g['kondisi'],
                    'deskripsi'      => $g['deskripsi'] ?? null,
                    'created_by'     => $userId,
                ]);
            }

            Log::info('Data gangguan peralatan berhasil diupdate.', ['total' => count($request->gangguan)]);

        } elseif ($request->jenis_laporan === 'gangguan_non_peralatan') {
            GangguanNonPeralatan::create([
                'laporan_id'     => $laporan->id,
                'layanan_id'     => $request->layanan_id,
                'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object yang sama
                'deskripsi'      => $request->deskripsi_gangguan ?? null,
                'created_by'     => $userId,
            ]);

            Log::info('Data gangguan non-peralatan berhasil diupdate.');
        }

        DB::commit();

        Log::info("Proses Step 2 Back selesai. Redirect ke Step 3.", ['laporan_id' => $laporan->id]);
        
        return redirect()
            ->route('tambah.step3', ['laporan_id' => $laporan->id])
            ->with('notif', 'perubahan_tersimpan');

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Gagal menyimpan perubahan Step 2 Back.', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()
            ->withErrors(['msg' => 'Gagal menyimpan perubahan: ' . $e->getMessage()])
            ->withInput()
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
        'peralatanGangguanIds'  => $peralatanGangguanIds,
        'step'                  => 3,
    ]);
}

/**
 * Function untuk menampilkan form tindak lanjut step 3 (tombol Back).
 *
 * Akses:
 * - Admin
 * - Teknisi
 * 
 * Method: GET
 * URL: /logbook/laporan/tambah/step3/back{id}
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

    // Ambil data tindak lanjut peralatan
    $tlPeralatan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->get()->groupBy('peralatan_id');
    $tlNon = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->first();

    // Ambil data gangguan peralatan
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

    // Validasi umum
    $request->validate([
        'laporan_id' => 'required|integer|exists:laporan,id',
        'layanan_id' => 'required|integer|exists:layanan,id',
        'jenis_laporan' => ['required', Rule::in([1, 0])],
    ]);

    try {
        $userId = Auth::id();
        $laporan = Laporan::find($request->laporan_id);
        if (!$laporan) {
            return back()->withErrors(['msg' => 'Laporan tidak ditemukan.']);
        }

        // Proses penyimpanan data tindak lanjut
        if ($request->jenis_laporan == 1) {
            // Validasi untuk gangguan peralatan
            $request->validate([
                'tindaklanjut' => 'required|array',
                'tindaklanjut.*.kondisi' => 'required|in:0,1',
                'tindaklanjut.*.jenis' => 'required|in:0,1',
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

                // Simpan atau update data tindak lanjut peralatan
                TlGangguanPeralatan::updateOrCreate(
                    [
                        'laporan_id' => $request->laporan_id,
                        'peralatan_id' => $peralatanId,
                    ],
                    [
                        'gangguan_peralatan_id' => $gangguan->id,
                        'layanan_id' => $request->layanan_id,
                        'waktu' => $tl['waktu'],
                        'deskripsi' => $tl['deskripsi'] ?? null,
                        'kondisi' => $tl['kondisi'],
                        'jenis_tindaklanjut' => $tl['jenis'],
                        'updated_by' => $userId,
                    ]
                );
            }

            // Cek kondisi peralatan untuk menentukan kondisi layanan
            $kondisiLayanan = TlGangguanPeralatan::where('laporan_id', $request->laporan_id)
                ->pluck('kondisi')
                ->toArray();

            // Jika ada peralatan yang masih dalam kondisi GANGGUAN (0), set kondisi layanan menjadi UNSERVICEABLE
            $isServiceable = !in_array(0, $kondisiLayanan); // 0 = GANGGUAN

            // Update kondisi layanan di laporan
            $laporan->kondisi_layanan_temp = $isServiceable ? 1 : 0; // 1 = SERVICEABLE, 0 = UNSERVICEABLE
            $laporan->save();

        } else {
            // Validasi gangguan non-peralatan
            $request->validate([
                'waktu' => 'required|date',
                'deskripsi' => 'nullable|string',
                'kondisi' => 'required|in:0,1',
            ]);

            $gangguan = GangguanNonPeralatan::where('laporan_id', $request->laporan_id)->latest()->first();

            if (!$gangguan) {
                return back()->withErrors(['msg' => 'Data gangguan non-peralatan tidak ditemukan.']);
            }

            // Simpan atau update data tindak lanjut non-peralatan
            TlGangguanNonPeralatan::updateOrCreate(
                ['laporan_id' => $request->laporan_id],
                [
                    'gangguan_non_peralatan_id' => $gangguan->id,
                    'layanan_id' => $request->layanan_id,
                    'waktu' => $request->waktu,
                    'deskripsi' => $request->deskripsi,
                    'kondisi' => $request->kondisi,
                    'updated_by' => $userId,
                ]
            );

            // Update kondisi layanan untuk non-peralatan
            $laporan->kondisi_layanan_temp = $request->kondisi; // Langsung ambil dari kondisi tindak lanjut
            $laporan->save();
        }

        // Redirect sesuai jenis tindak lanjut
        if ($request->jenis_laporan == 1) {
            // PERBAIKAN: Cek dari data yang baru disimpan di database
            $tindakLanjutPenggantian = TlGangguanPeralatan::where('laporan_id', $request->laporan_id)
                ->where('jenis_tindaklanjut', 0) // 0 = penggantian berdasarkan konstanta
                ->exists();

            if ($tindakLanjutPenggantian) {
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
 * URL: /logbook/laporan/tambah/step3/back{id}
 *
 * @return \Illuminate\Http\Response
 */
public function tambahStep3Back(Request $request)
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
    // ===================== AKHIR PROSES VERIFIKASI =======================
    
    // Validasi input
    $request->validate([
        'laporan_id' => 'required|exists:laporan,id',
        'layanan_id' => 'required|exists:layanan,id',
        'jenis_laporan' => ['required', Rule::in([1, 0])],
    ]);

    try {
        $userId = Auth::id();
        $laporan = Laporan::find($request->laporan_id);
        if (!$laporan) {
            return back()->withErrors(['msg' => 'Laporan tidak ditemukan.']);
        }

        // UPDATE DATA TINDAK LANJUT (bukan insert baru)
        if ($request->jenis_laporan == 1) {
            // Validasi untuk gangguan peralatan
            $request->validate([
                'tindaklanjut' => 'required|array',
                'tindaklanjut.*.kondisi' => 'required|in:0,1',
                'tindaklanjut.*.jenis' => 'required|in:0,1',
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

                // UPDATE data tindak lanjut peralatan yang sudah ada
                TlGangguanPeralatan::updateOrCreate(
                    [
                        'laporan_id' => $request->laporan_id,
                        'peralatan_id' => $peralatanId,
                    ],
                    [
                        'gangguan_peralatan_id' => $gangguan->id,
                        'layanan_id' => $request->layanan_id,
                        'waktu' => $tl['waktu'],
                        'deskripsi' => $tl['deskripsi'] ?? null,
                        'kondisi' => $tl['kondisi'],
                        'jenis_tindaklanjut' => $tl['jenis'],
                        'updated_by' => $userId,
                    ]
                );
            }

            // TAMBAHAN: Cek kondisi peralatan untuk menentukan kondisi layanan
            $kondisiLayanan = TlGangguanPeralatan::where('laporan_id', $request->laporan_id)
                ->pluck('kondisi')
                ->toArray();

            // Jika ada peralatan yang masih dalam kondisi GANGGUAN (0), set kondisi layanan menjadi UNSERVICEABLE
            $isServiceable = !in_array(0, $kondisiLayanan); // 0 = GANGGUAN

            // Update kondisi layanan di laporan
            $laporan->kondisi_layanan_temp = $isServiceable ? 1 : 0; // 1 = SERVICEABLE, 0 = UNSERVICEABLE
            $laporan->save();

        } else {
            // Validasi gangguan non-peralatan
            $request->validate([
                'waktu' => 'required|date',
                'deskripsi' => 'nullable|string',
                'kondisi' => 'required|in:0,1',
            ]);

            $gangguan = GangguanNonPeralatan::where('laporan_id', $request->laporan_id)->latest()->first();

            if (!$gangguan) {
                return back()->withErrors(['msg' => 'Data gangguan non-peralatan tidak ditemukan.']);
            }

            // UPDATE data tindak lanjut non-peralatan yang sudah ada
            TlGangguanNonPeralatan::updateOrCreate(
                ['laporan_id' => $request->laporan_id],
                [
                    'gangguan_non_peralatan_id' => $gangguan->id,
                    'layanan_id' => $request->layanan_id,
                    'waktu' => $request->waktu,
                    'deskripsi' => $request->deskripsi,
                    'kondisi' => $request->kondisi,
                    'updated_by' => $userId,
                ]
            );

            // TAMBAHAN: Update kondisi layanan untuk non-peralatan
            $laporan->kondisi_layanan_temp = $request->kondisi; // Langsung ambil dari kondisi tindak lanjut
            $laporan->save();
        }

        // Redirect sesuai jenis tindak lanjut
        if ($request->jenis_laporan == 1) {
            // PERBAIKAN: Cek dari data yang baru disimpan di database
            $tindakLanjutPenggantian = TlGangguanPeralatan::where('laporan_id', $request->laporan_id)
                ->where('jenis_tindaklanjut', 0) // 0 = penggantian berdasarkan konstanta
                ->exists();

            if ($tindakLanjutPenggantian) {
                // Jika ada penggantian → ke Step 4
                return redirect()->route('tambah.step4.back', ['laporan_id' => $request->laporan_id])
                    ->with('notif', 'update_sukses');
            } else {
                // Jika semua perbaikan → langsung ke Step 5
                return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                    ->with('notif', 'update_sukses');
            }
        } else {
            // Non-peralatan langsung ke Step 5
            return redirect()->route('tambah.step5', ['laporan_id' => $request->laporan_id])
                ->with('notif', 'update_sukses');
        }

    } catch (\Exception $e) {
        return back()->withErrors(['msg' => 'Gagal mengupdate tindak lanjut: ' . $e->getMessage()])
            ->withInput()
            ->with('notif', 'update_gagal');
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
    $laporan = Laporan::with('layanan')->findOrFail($laporan_id);
    $jenisTl = null;
    $peralatanLama = collect();
    $peralatanTersedia = collect();

    $kodePenggantian = 0; // PERBAIKAN: Sesuai konstanta penggantian = FALSE = 0
    $kodeGangguan = 0;    // PERBAIKAN: Sesuai konstanta gangguan = FALSE = 0

    /* ---------- Jika laporan jenis 1 (gangguan peralatan) ---------- */
    if ($laporan->jenis == 1) {
        // PERBAIKAN: Ambil semua tindak lanjut yang jenisnya penggantian
        $tindakLanjutPenggantian = TlGangguanPeralatan::where('laporan_id', $laporan->id)
            ->where('jenis_tindaklanjut', $kodePenggantian) // 0 = penggantian
            ->get();

        if ($tindakLanjutPenggantian->isNotEmpty()) {
            $jenisTl = $kodePenggantian;

            // Ambil peralatan yang perlu diganti
            $peralatanIds = $tindakLanjutPenggantian->pluck('peralatan_id')->unique();
            
            $peralatanLama = Peralatan::whereIn('id', $peralatanIds)->get()->map(function ($p) {
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
            });

            // Ambil peralatan aktif yang belum digunakan
            $peralatanTersedia = Peralatan::where('status', 1)
                ->where('kondisi', 1) // hanya kondisi Normal
                ->whereNotIn('id', $peralatanLama->pluck('id'))
                ->get();
        }
    }

    // Kirim ke view
    return view('logbook.laporan.tambah.step4', [
        'judul' => 'Laporan',
        'module' => 'Logbook',
        'menu' => 'Laporan',
        'menu_url' => '/logbook/laporan/tambah/step4',
        'submenu' => 'Tambah',
        'laporan' => $laporan,
        'jenis_tindaklanjut' => $jenisTl,
        'peralatanLama' => $peralatanLama,
        'peralatanTersedia' => $peralatanTersedia,
        'jenis' => JenisAlat::where('status', 1)->get(),
        'perusahaan' => Perusahaan::where('status', 1)->get(),
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

    $laporan = Laporan::with('layanan')->findOrFail($laporan_id);
    $jenisTl = null;
    $peralatanLama = collect();
    $peralatanTersedia = collect();
    $dataPenggantianTersimpan = collect();

    $kodePenggantian = 0; // 0 = penggantian sesuai konstanta FALSE
    $kodeGangguan = 0;    // 0 = gangguan sesuai konstanta FALSE

    /* ---------- Jika laporan jenis 1 (gangguan peralatan) ---------- */
    if ($laporan->jenis == 1) {
        // Ambil semua tindak lanjut yang jenisnya penggantian
        $tindakLanjutPenggantian = TlGangguanPeralatan::where('laporan_id', $laporan->id)
            ->where('jenis_tindaklanjut', $kodePenggantian) // 0 = penggantian
            ->get();

        if ($tindakLanjutPenggantian->isNotEmpty()) {
            $jenisTl = $kodePenggantian;

            // Ambil peralatan yang perlu diganti
            $peralatanIds = $tindakLanjutPenggantian->pluck('peralatan_id')->unique();
            
            $peralatanLama = Peralatan::whereIn('id', $peralatanIds)->get()->map(function ($p) {
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
            });

            // Ambil data penggantian yang sudah tersimpan sebelumnya
            $dataPenggantianTersimpan = TlPenggantianPeralatan::where('laporan_id', $laporan->id)
                ->with(['peralatanLama', 'peralatanBaru'])
                ->get()
                ->keyBy('peralatan_lama_id'); // Index by peralatan_lama_id untuk akses mudah

            // Ambil peralatan aktif yang belum digunakan
            $excludeIds = $peralatanLama->pluck('id')->toArray();
            
            // Jika ada data tersimpan, jangan exclude peralatan baru yang sudah dipilih
            if ($dataPenggantianTersimpan->isNotEmpty()) {
                $peralatanBaruTerpilih = $dataPenggantianTersimpan->pluck('peralatan_baru_id')->toArray();
                $excludeIds = array_diff($excludeIds, $peralatanBaruTerpilih);
            }

            $peralatanTersedia = Peralatan::where('status', 1)
                ->where('kondisi', 1) // hanya kondisi Normal
                ->whereNotIn('id', $excludeIds)
                ->get();
        }
    } else {
        // Untuk gangguan non-peralatan
        $tl = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        if ($tl) {
            $jenisTl = (int) $tl->jenis_tindaklanjut;
        }
    }

    // Kirim ke view step4_back
    return view('logbook.laporan.tambah.step4_back', [
        'judul' => 'Laporan',
        'module' => 'Logbook',
        'menu' => 'Laporan',
        'menu_url' => '/logbook/laporan/tambah/step4/back',
        'submenu' => 'Tambah',
        'laporan' => $laporan,
        'jenis_tindaklanjut' => $jenisTl,
        'peralatanLama' => $peralatanLama,
        'peralatanTersedia' => $peralatanTersedia,
        'dataPenggantianTersimpan' => $dataPenggantianTersimpan,
        'isBack' => true, // Flag untuk menandai ini adalah halaman back
        'jenis' => JenisAlat::where('status', 1)->get(),
        'perusahaan' => Perusahaan::where('status', 1)->get(),
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

        // PERBAIKAN: Cek tindak lanjut dengan konstanta yang benar
        $kodePenggantian = 0; // FALSE = 0 untuk penggantian

        if ($laporan->jenis == 1) {
            // Cek apakah ada tindak lanjut penggantian untuk laporan ini
            $tindakLanjutPenggantian = TlGangguanPeralatan::where('laporan_id', $laporanId)
                ->where('jenis_tindaklanjut', $kodePenggantian) // 0 = penggantian
                ->get();

            if ($tindakLanjutPenggantian->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada tindak lanjut penggantian untuk laporan ini.');
            }

            // Hapus data penggantian lama jika ada
            TlPenggantianPeralatan::where('laporan_id', $laporanId)->delete();

            $saved = 0;

            foreach ($dataPenggantian as $i => $item) {
                // Skip jika peralatan lama dan baru sama
                if ($item['peralatan_lama_id'] === $item['peralatan_baru_id']) {
                    Log::warning("Step 4: Peralatan lama & baru sama (laporan_id: $laporanId, index: $i)");
                    continue;
                }

                // Cari tindak lanjut yang sesuai dengan peralatan_lama_id
                $tl = $tindakLanjutPenggantian->where('peralatan_id', $item['peralatan_lama_id'])->first();
                
                if (!$tl) {
                    Log::warning("Step 4: Tindak lanjut tidak ditemukan untuk peralatan_id: {$item['peralatan_lama_id']}");
                    continue;
                }

                // Ambil gangguan terkait peralatan_lama_id
                $gangguan = GangguanPeralatan::where('laporan_id', $laporanId)
                    ->where('peralatan_id', $item['peralatan_lama_id'])
                    ->first();

                if (!$gangguan) {
                    Log::warning("Step 4: Gangguan tidak ditemukan untuk peralatan_id: {$item['peralatan_lama_id']}");
                    continue;
                }

                // Validasi peralatan baru (harus aktif dan normal)
                $peralatanBaru = Peralatan::find($item['peralatan_baru_id']);
                if (!$peralatanBaru || $peralatanBaru->status != 1 || $peralatanBaru->kondisi != 1) {
                    Log::warning("Step 4: Peralatan baru tidak valid (ID: {$item['peralatan_baru_id']})");
                    continue;
                }

                // Simpan data penggantian
                TlPenggantianPeralatan::create([
                    'tl_gangguan_id'      => $tl->id,
                    'laporan_id'          => $laporanId,
                    'layanan_id'          => $laporan->layanan_id,
                    'peralatan_lama_id'   => $item['peralatan_lama_id'],
                    'peralatan_baru_id'   => $item['peralatan_baru_id'],
                    'created_by'          => auth()->user()->id,
                ]);

                // OPSIONAL: Update status peralatan
                // Peralatan lama menjadi tidak aktif
                Peralatan::where('id', $item['peralatan_lama_id'])
                    ->update([
                        'status' => 0, // tidak aktif
                        'kondisi' => 0, // rusak
                    ]);

                // Peralatan baru tetap aktif (sudah aktif sebelumnya)
                // Atau bisa ditambahkan log penggunaan jika diperlukan

                $saved++;
            }

            DB::commit();

            if ($saved === 0) {
                Log::info("Step 4: Tidak ada data valid disimpan untuk laporan ID {$laporanId}");
                return redirect()->back()
                    ->with('warning', 'Tidak ada data yang disimpan. Periksa kembali isian Anda.')
                    ->withInput();
            }

            Log::info("Step 4: Berhasil menyimpan {$saved} data penggantian untuk laporan ID {$laporanId}");

            return redirect()->route('tambah.step5', ['laporan_id' => $laporanId])
                            ->with('success', "Data penggantian berhasil disimpan ({$saved} item).");

        } else {
            // Jika bukan gangguan peralatan, redirect ke step 5
            return redirect()->route('tambah.step5', ['laporan_id' => $laporanId])
                            ->with('info', 'Laporan non-peralatan tidak memerlukan penggantian.');
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error("Validasi gagal Step 4: " . json_encode($e->errors()));
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('error', 'Data yang dimasukkan tidak valid.');

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error("Gagal simpan Step 4: " . $e->getMessage());
        Log::error("Stack trace: " . $e->getTraceAsString());
        
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
            ->withInput();
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

    // Cek kondisi layanan berdasarkan peralatan
    $kondisiLayanan = TlGangguanPeralatan::where('laporan_id', $laporan->id)
        ->pluck('kondisi')
        ->toArray();

    // Jika ada peralatan yang masih dalam kondisi GANGGUAN (0), set kondisi layanan menjadi UNSERVICEABLE
    $isServiceable = !in_array(0, $kondisiLayanan); // 0 = GANGGUAN
    $laporan->kondisi_layanan_temp = $isServiceable ? 1 : 0; // 1 = SERVICEABLE, 0 = UNSERVICEABLE

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

        // Update kondisi layanan dan status
        $laporan->kondisi_layanan_temp = $request->input('kondisi_layanan_temp'); // Ambil dari request
        $laporan->status = $laporan->kondisi_layanan_temp ? config('constants.status_laporan.closed') : config('constants.status_laporan.open');
        $laporan->waktu_close = $laporan->status == config('constants.status_laporan.closed') ? now() : null; // Set waktu_close jika ditutup

        $laporan->save();

        // Update kondisi layanan di tabel layanan
        $layanan = Layanan::find($laporan->layanan_id);
        if ($layanan) {
            $layanan->kondisi = $laporan->kondisi_layanan_temp; // Update kondisi layanan
            $layanan->save();
        }

        DB::commit();

        // Redirect berdasarkan status laporan
        if ($laporan->status == config('constants.status_laporan.closed')) {
            return redirect()->route('logbook.riwayat.daftar')
                ->with('notif', 'tambah_sukses');
        } else {
            return redirect()->route('logbook.laporan.daftar')
                ->with('notif', 'tambah_sukses');
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
    try {
        $laporan_id = $request->id;
        
        // Validasi input
        if (!$laporan_id || !is_numeric($laporan_id)) {
            return response()->json(['error' => 'ID laporan tidak valid'], 400);
        }
        
        // Gunakan relasi yang SAMA PERSIS dengan step5
        $laporan = Laporan::with([
            'layanan.fasilitas',
            'layanan.LokasiTk1',
            'layanan.LokasiTk2',
            'layanan.LokasiTk3',
            'gangguanNonPeralatan',
            'getCreatedName',      
            'getUpdatedName'
        ])->findOrFail($laporan_id);

        // Inisialisasi variabel SAMA PERSIS seperti step5
        $detailGangguanPeralatan = collect();
        $penggantian = collect();
        $perbaikan = collect();
        $tindaklanjut = null;
        $semuaTindakLanjutNonPeralatan = collect();

        if ($laporan->jenis == 1) {
            // GANGGUAN PERALATAN - SAMA PERSIS seperti step5
            
            // Ambil semua gangguan peralatan - PASTIKAN FIELD WAKTU ADA
            $detailGangguanPeralatan = GangguanPeralatan::with(['peralatan'])
                ->where('laporan_id', $laporan->id)
                ->orderBy('created_at', 'asc') // Urutkan berdasarkan created_at jika waktu tidak ada
                ->get();

            // Debug log untuk melihat struktur data gangguan
            \Log::info('Debug Gangguan Peralatan:', [
                'laporan_id' => $laporan_id,
                'count' => $detailGangguanPeralatan->count(),
                'sample_data' => $detailGangguanPeralatan->first() ? $detailGangguanPeralatan->first()->toArray() : null
            ]);

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

                // Format data penggantian untuk memastikan struktur yang konsisten
                $penggantian = $penggantian->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'laporan_id' => $item->laporan_id,
                        'peralatan_lama_id' => $item->peralatan_lama_id,
                        'peralatan_baru_id' => $item->peralatan_baru_id,
                        'tindaklanjut_id' => $item->tindaklanjut_id,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'peralatan_lama' => $item->peralatanLama ? [
                            'id' => $item->peralatanLama->id,
                            'kode' => $item->peralatanLama->kode,
                            'nama' => $item->peralatanLama->nama,
                            'merk' => $item->peralatanLama->merk,
                            'tipe' => $item->peralatanLama->tipe,
                            'model' => $item->peralatanLama->model,
                            'serial_number' => $item->peralatanLama->serial_number,
                        ] : null,
                        'peralatan_baru' => $item->peralatanBaru ? [
                            'id' => $item->peralatanBaru->id,
                            'kode' => $item->peralatanBaru->kode,
                            'nama' => $item->peralatanBaru->nama,
                            'merk' => $item->peralatanBaru->merk,
                            'tipe' => $item->peralatanBaru->tipe,
                            'model' => $item->peralatanBaru->model,
                            'serial_number' => $item->peralatanBaru->serial_number,
                        ] : null,
                        'tindaklanjut' => $item->tindaklanjut ? [
                            'id' => $item->tindaklanjut->id,
                            'jenis_tindaklanjut' => $item->tindaklanjut->jenis_tindaklanjut,
                            'deskripsi' => $item->tindaklanjut->deskripsi,
                            'waktu' => $item->tindaklanjut->waktu,
                            'kondisi' => $item->tindaklanjut->kondisi,
                        ] : null,
                    ];
                });
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

            // Cek kondisi layanan berdasarkan peralatan
            $kondisiLayanan = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->pluck('kondisi')
                ->toArray();

            // Jika ada peralatan yang masih dalam kondisi GANGGUAN (0), set kondisi layanan menjadi UNSERVICEABLE
            $isServiceable = !in_array(0, $kondisiLayanan) && !in_array(false, $kondisiLayanan);
            $laporan->kondisi_layanan_temp = $isServiceable ? 1 : 0;

        } else {
            // GANGGUAN NON-PERALATAN - SAMA seperti step5
            
            // Untuk gangguan non-peralatan
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu')
                ->first();

            // Ambil semua tindak lanjut non-peralatan
            $semuaTindakLanjutNonPeralatan = TlGangguanNonPeralatan::with([
                'getCreatedName' => function($query) {
                    $query->select('id', 'name');
                },
                'getUpdatedName' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->where('laporan_id', $laporan->id)
            ->orderBy('waktu', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

            // Untuk non-peralatan, cek dari tindak lanjut terakhir
            if ($tindaklanjut) {
                $laporan->kondisi_layanan_temp = ($tindaklanjut->kondisi == 1 || $tindaklanjut->kondisi === true) ? 1 : 0;
            } else {
                $laporan->kondisi_layanan_temp = 0; // Default UNSERVICEABLE jika belum ada tindak lanjut
            }
        }

        // Log untuk debugging
        \Log::info('Detail Laporan Response:', [
            'laporan_id' => $laporan_id,
            'jenis_laporan' => $laporan->jenis,
            'total_gangguan_peralatan' => $detailGangguanPeralatan->count(),
            'total_penggantian' => $penggantian->count(),
            'total_perbaikan' => $perbaikan->count(),
            'kondisi_layanan_temp' => $laporan->kondisi_layanan_temp,
            'penggantian_count' => $penggantianCount ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'laporan' => $laporan,
            'detailGangguanPeralatan' => $detailGangguanPeralatan,
            'penggantian' => $penggantian,
            'perbaikan' => $perbaikan,
            'tindaklanjut' => $tindaklanjut,
            'semuaTindakLanjutNonPeralatan' => $semuaTindakLanjutNonPeralatan,
            'gangguanNonPeralatan' => $laporan->gangguanNonPeralatan,
        ]);

    } catch (\Exception $e) {
        // Log error untuk debugging
        \Log::error('Error in detail function:', [
            'laporan_id' => $request->id ?? 'null',
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'error' => 'Terjadi kesalahan saat memuat detail laporan',
            'message' => $e->getMessage()
        ], 500);
    }
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

    // PERBAIKAN: Ambil waktu gangguan dengan cara yang sama seperti formStep2Back
    $waktuGangguanFormatted = '';
    
    if ($laporan->waktu_open) {
        try {
            // Ambil dari waktu_open di tabel laporan (sama seperti formStep2Back)
            $waktuGangguanFormatted = Carbon::parse($laporan->waktu_open)->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            \Log::error('Error formatting waktu_open: ' . $e->getMessage());
        }
    } else {
        // Fallback: ambil dari tabel gangguan jika waktu_open kosong (sama seperti formStep2Back)
        if ($laporan->jenis == 1) {
            $firstGangguan = $gangguanPeralatan->first();
            if ($firstGangguan && $firstGangguan->waktu_gangguan) {
                try {
                    $waktuGangguanFormatted = Carbon::parse($firstGangguan->waktu_gangguan)->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    \Log::error('Error formatting waktu gangguan peralatan: ' . $e->getMessage());
                }
            }
        } else {
            if ($gangguanNonPeralatan && $gangguanNonPeralatan->waktu_gangguan) {
                try {
                    $waktuGangguanFormatted = Carbon::parse($gangguanNonPeralatan->waktu_gangguan)->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    \Log::error('Error formatting waktu gangguan non-peralatan: ' . $e->getMessage());
                }
            }
        }
    }

    // Debug log untuk membantu troubleshooting (sama seperti formStep2Back)
    \Log::info('Edit Step2 Debug:', [
        'laporan_jenis' => $laporan->jenis,
        'laporan_waktu_open' => $laporan->waktu_open,
        'laporan_waktu_close' => $laporan->waktu_close,
        'gangguan_peralatan_count' => $gangguanPeralatan->count(),
        'gangguan_peralatan_waktu' => $gangguanPeralatan->first()?->waktu_gangguan,
        'gangguan_non_peralatan' => $gangguanNonPeralatan ? 'exists' : 'null',
        'gangguan_non_peralatan_waktu' => $gangguanNonPeralatan?->waktu_gangguan,
        'waktu_formatted' => $waktuGangguanFormatted,
    ]);

    // Debug khusus untuk gangguan peralatan
    if ($gangguanPeralatan->count() > 0) {
        \Log::info('Edit Step 2 - Gangguan Peralatan Detail', [
            'laporan_id' => $laporan->id,
            'gangguan_data' => $gangguanPeralatan->toArray()
        ]);
    }

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
        'waktuGangguan' => old('waktu_gangguan', $waktuGangguanFormatted), // Menggunakan waktu yang sudah diformat dengan benar
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
        // PERBAIKAN: Gunakan Carbon untuk konsistensi format waktu
        $waktuGangguan = \Carbon\Carbon::parse($request->waktu_gangguan);

        // PERBAIKAN: Update waktu gangguan ke field yang benar
        // Konsisten dengan editStep2 yang menggunakan waktu_open
        $laporan->waktu_open = $waktuGangguan; // Ubah dari 'waktu' ke 'waktu_open'
        $laporan->updated_by = $userId;
        $laporan->save();

        Log::info("Laporan berhasil diupdate", [
            'laporan_id' => $laporan->id,
            'waktu_open' => $waktuGangguan->format('Y-m-d H:i:s'), // Ubah log juga
            'field_updated' => 'waktu_open' // Tambah info field yang diupdate
        ]);

        if ($request->jenis_laporan === 'gangguan_peralatan' && !empty($request->gangguan)) {
            // Hapus data gangguan peralatan yang lama
            GangguanPeralatan::where('laporan_id', $laporan->id)->delete();

            // Simpan data gangguan peralatan yang baru
            foreach ($request->gangguan as $g) {
                GangguanPeralatan::create([
                    'laporan_id'     => $laporan->id,
                    'layanan_id'     => $laporan->layanan_id,
                    'peralatan_id'   => $g['id'],
                    'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object
                    'kondisi'        => $g['kondisi'],
                    'deskripsi'      => $g['deskripsi'] ?? null,
                    'created_by'     => $userId,
                ]);
            }

            Log::info('Data gangguan peralatan berhasil diupdate.', ['total' => count($request->gangguan)]);

        } elseif ($request->jenis_laporan === 'gangguan_non_peralatan') {
            // PERBAIKAN: Hapus data gangguan peralatan yang mungkin ada sebelumnya
            // Karena jenis laporan tidak bisa diubah, tapi untuk safety
            GangguanPeralatan::where('laporan_id', $laporan->id)->delete();
            
            // Update atau create gangguan non-peralatan
            GangguanNonPeralatan::updateOrCreate(
                ['laporan_id' => $laporan->id],
                [
                    'layanan_id'     => $laporan->layanan_id,
                    'waktu_gangguan' => $waktuGangguan, // Gunakan Carbon object
                    'deskripsi'      => $request->deskripsi_gangguan ?? null,
                    'created_by'     => $userId,
                ]
            );

            Log::info('Data gangguan non-peralatan berhasil diupdate.');
        }

        DB::commit();

        Log::info("Proses update Step 2 selesai.", ['laporan_id' => $laporan->id]);
        
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
 * Untuk status draft: menampilkan form input tindak lanjut
 * Untuk status open: menampilkan data tindak lanjut dengan kemampuan menambah
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
        config('constants.status_laporan.open'),
        'draft',
        'open'
    ])) {
        return redirect()->route('logbook.laporan.daftar')
            ->with('notif', 'edit_gagal')
            ->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
    }

    // Untuk status open, pastikan kondisi layanan unserviceable
    if (($laporan->status == config('constants.status_laporan.open') || $laporan->status == 'open') && 
        $laporan->kondisi_layanan != 0) {
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

    // Ambil data tindak lanjut yang sudah ada
    $existingTindakLanjut = collect();
    $tlPeralatan = collect();
    $tlNon = null;

    //Cek kondisi terbaru setiap peralatan/layanan
    $kondisiTerbaru = [];

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

        // Cek kondisi terbaru setiap peralatan bermasalah
        foreach ($peralatanGangguanIds as $peralatanId) {
            $tindakLanjutTerbaru = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->where('peralatan_id', $peralatanId)
                ->latest('waktu')
                ->first();
            
            // Default kondisi gangguan (0) jika belum ada tindak lanjut
            $kondisiTerbaru[$peralatanId] = $tindakLanjutTerbaru ? $tindakLanjutTerbaru->kondisi : 0;
        }
    } else {
        // Untuk gangguan non-peralatan
        $existingTindakLanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
            ->orderBy('waktu', 'desc')
            ->get();
        
        // Ambil yang terbaru untuk pre-fill form
        $tlNon = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
            ->latest('waktu')
            ->first();

        // TAMBAHAN: Kondisi terbaru untuk non-peralatan
        $kondisiTerbaru['non_peralatan'] = $tlNon ? $tlNon->kondisi : 0;
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
        'kondisiTerbaru'        => $kondisiTerbaru, //Data kondisi terbaru
        'isEdit'                => true,
    ]);
}

/**
 * Function untuk menyimpan/update data edit step 3 (tindak lanjut).
 * 
 * Untuk status draft: menyimpan form biasa dengan multiple tindak lanjut
 * Untuk status open: menyimpan via AJAX untuk single tindak lanjut
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
 */
public function updateStep3(Request $request, $id)
{
    // ========================= PROSES VERIFIKASI ========================
    if (!Auth::check()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        return redirect('/login');
    }
    
    $status = User::find(session()->get('id'))->status;
    if (!$status) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'User not active'], 401);
        }
        return redirect('/logout');
    }

    if (
        session()->get('role_id') != config('constants.role.super_admin') &&
        session()->get('role_id') != config('constants.role.admin') &&
        session()->get('role_id') != config('constants.role.teknisi')
    ) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }
        return redirect('/');
    }
    // ===================== AKHIR PROSES VERIFIKASI =======================

    $laporan = Laporan::findOrFail($id);
    
    // Validasi status laporan dengan normalisasi
    $allowedStatuses = [
        'draft', 
        'open', 
        config('constants.status_laporan.draft'), 
        config('constants.status_laporan.open')
    ];
    
    if (!in_array($laporan->status, $allowedStatuses)) {
        \Log::info('Status laporan tidak valid:', ['status' => $laporan->status]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false, 
                'message' => 'Laporan dengan status ini tidak dapat diedit.'
            ], 400);
        }
        return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
    }

    try {
        DB::beginTransaction();
        
        $userId = Auth::id();
        
        // Tentukan jenis form berdasarkan form_type atau kondisi lainnya
        $formType = $request->input('form_type', '');
        
        \Log::info('Form processing:', [
            'form_type' => $formType,
            'laporan_status' => $laporan->status,
            'is_ajax' => $request->ajax(),
            'has_tindak_lanjut_id' => $request->has('tindak_lanjut_id')
        ]);
        
        // Cek apakah ini request untuk status draft (form biasa)
        if ($formType === 'draft' || 
            ($laporan->status == 'draft' || $laporan->status == config('constants.status_laporan.draft'))) {
            
            $result = $this->handleDraftFormTindakLanjut($request, $laporan, $userId, $id);
            DB::commit();
            return $result;
        }
        
        // Cek apakah ini request AJAX untuk status open
        if ($formType === 'ajax' || $request->ajax() || $request->has('tindak_lanjut_id')) {
            $result = $this->handleAjaxTindakLanjut($request, $laporan, $userId, $id);
            DB::commit();
            return $result;
        }

        // Fallback: handle berdasarkan status laporan
        if ($laporan->status == 'open' || $laporan->status == config('constants.status_laporan.open')) {
            $result = $this->handleAjaxTindakLanjut($request, $laporan, $userId, $id);
        } else {
            $result = $this->handleDraftFormTindakLanjut($request, $laporan, $userId, $id);
        }
        
        DB::commit();
        return $result;

    } catch (\Exception $e) {
        DB::rollback();
        
        \Log::error('Error occurred in updateStep3:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()
            ], 500);
        }
        
        return back()->withErrors(['msg' => 'Gagal menyimpan tindak lanjut: ' . $e->getMessage()])
            ->withInput()
            ->with('notif', 'edit_gagal');
    }
}
/**
 * Menentukan URL redirect selanjutnya berdasarkan jenis tindak lanjut terbaru
 */
private function getNextStepUrl($laporan, $jenisTindakLanjutTerbaru, $id)
{
    // Untuk gangguan non-peralatan, selalu ke step 5 (tidak ada penggantian)
    if ($laporan->jenis != 1) {
        return route('logbook.laporan.edit.step5', $id);
    }
    
    // Untuk gangguan peralatan, tergantung jenis tindak lanjut terbaru
    if ($jenisTindakLanjutTerbaru == 0) {
        // Jenis 0 = Penggantian, lanjut ke step 4
        return route('logbook.laporan.edit.step4', $id);
    } else {
        // Jenis 1 = Perbaikan, skip ke step 5
        return route('logbook.laporan.edit.step5', $id);
    }
}

/**
 * Handle form tindak lanjut untuk status draft - DIPERBAIKI dengan Dynamic Next Step
 * 
 * @param Request $request
 * @param Laporan $laporan
 * @param int $userId
 * @param int $id
 * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
 */
private function handleDraftFormTindakLanjut(Request $request, $laporan, $userId, $id)
{
    try {
        \Log::info('Processing draft form tindak lanjut:', [
            'laporan_id' => $id,
            'jenis' => $laporan->jenis,
            'request_data' => $request->all()
        ]);
        
        $jenisTindakLanjutTerbaru = null; // Variable untuk menyimpan jenis tindak lanjut terbaru
        
        if ($laporan->jenis == 1) {
            // Validasi untuk gangguan peralatan
            $request->validate([
                'tindaklanjut' => 'required|array',
                'tindaklanjut.*.kondisi' => 'required|in:0,1',
                'tindaklanjut.*.jenis_tindaklanjut' => 'required|in:0,1',
                'tindaklanjut.*.waktu' => 'required|date',
                'tindaklanjut.*.deskripsi' => 'nullable|string|max:1000',
            ]);

            // Hapus tindak lanjut yang sudah ada untuk laporan ini
            TlGangguanPeralatan::where('laporan_id', $laporan->id)->delete();

            $semuaBeroperasi = true;
            $waktuTerbaru = null;

            // Simpan tindak lanjut baru
            foreach ($request->tindaklanjut as $peralatanId => $tindakData) {
                // Cari gangguan_peralatan_id
                $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                    ->where('peralatan_id', $peralatanId)
                    ->latest()
                    ->first();

                if (!$gangguan) {
                    throw new \Exception("Data gangguan peralatan untuk ID $peralatanId tidak ditemukan.");
                }

                TlGangguanPeralatan::create([
                    'laporan_id' => $laporan->id,
                    'peralatan_id' => $peralatanId,
                    'gangguan_peralatan_id' => $gangguan->id,
                    'layanan_id' => $laporan->layanan_id,
                    'jenis_tindaklanjut' => (int)$tindakData['jenis_tindaklanjut'],
                    'waktu' => $tindakData['waktu'],
                    'kondisi' => (int)$tindakData['kondisi'],
                    'deskripsi' => $tindakData['deskripsi'] ?? null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                // Cek apakah semua beroperasi
                if ((int)$tindakData['kondisi'] === 0) {
                    $semuaBeroperasi = false;
                }

                // PERBAIKAN: Cari jenis tindak lanjut dengan waktu terbaru
                $waktuInput = \Carbon\Carbon::parse($tindakData['waktu']);
                if ($waktuTerbaru === null || $waktuInput->greaterThan($waktuTerbaru)) {
                    $waktuTerbaru = $waktuInput;
                    $jenisTindakLanjutTerbaru = (int)$tindakData['jenis_tindaklanjut'];
                }
            }

            // Update kondisi layanan berdasarkan kondisi tindak lanjut
            $kondisiLayanan = $semuaBeroperasi ? 1 : 0;
            
        } else {
            // Validasi untuk gangguan non-peralatan
            $request->validate([
                'waktu' => 'required|date',
                'kondisi' => 'required|in:0,1',
                'deskripsi' => 'nullable|string|max:1000',
            ]);

            // Cari gangguan_non_peralatan_id yang benar
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->first();
            
            if (!$gangguanNonPeralatan) {
                throw new \Exception('Data gangguan non-peralatan tidak ditemukan untuk laporan ini');
            }

            // Hapus tindak lanjut yang sudah ada untuk laporan ini
            TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->delete();

            // Simpan tindak lanjut baru
            TlGangguanNonPeralatan::create([
                'laporan_id' => $laporan->id,
                'layanan_id' => $laporan->layanan_id,
                'gangguan_non_peralatan_id' => $gangguanNonPeralatan->id,
                'waktu' => $request->waktu,
                'kondisi' => (int)$request->kondisi,
                'deskripsi' => $request->deskripsi,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Update kondisi layanan berdasarkan kondisi tindak lanjut
            $kondisiLayanan = (int)$request->kondisi;
            
            // Untuk non-peralatan, tidak ada jenis tindak lanjut (selalu ke step 5)
            $jenisTindakLanjutTerbaru = 1; // Set sebagai perbaikan agar ke step 5
        }

        // UPDATE KONDISI LAYANAN DAN JENIS TINDAK LANJUT TERBARU
        $laporan->kondisi_layanan_temp = $kondisiLayanan;
        
        // PERBAIKAN: Simpan jenis tindak lanjut terbaru untuk menentukan step selanjutnya
        // Anda bisa menambah field baru atau menggunakan field existing
        // Misalnya menggunakan field yang sudah ada atau menambah field temp
        if ($laporan->jenis == 1) {
            // Simpan informasi jenis tindak lanjut terbaru ke field temp atau session
            session(['jenis_tindaklanjut_terbaru_' . $id => $jenisTindakLanjutTerbaru]);
        }
        
        $laporan->save();

        \Log::info('Kondisi layanan dan jenis tindak lanjut updated:', [
            'laporan_id' => $id,
            'kondisi_layanan_temp_baru' => $kondisiLayanan,
            'jenis_tindaklanjut_terbaru' => $jenisTindakLanjutTerbaru
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tindak lanjut berhasil disimpan',
                'redirect' => $this->getNextStepUrl($laporan, $jenisTindakLanjutTerbaru, $id)
            ]);
        }

        // Tentukan redirect berdasarkan jenis tindak lanjut terbaru
        $redirectUrl = $this->getNextStepUrl($laporan, $jenisTindakLanjutTerbaru, $id);
        
        return redirect($redirectUrl)
            ->with('notif', 'edit_sukses')
            ->with('message', 'Tindak lanjut berhasil disimpan');

    } catch (\Exception $e) {
        \Log::error('Error in handleDraftFormTindakLanjut:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

/**
 * Handle AJAX tindak lanjut untuk status open - DIPERBAIKI dengan Dynamic Next Step
 */
private function handleAjaxTindakLanjut(Request $request, $laporan, $userId, $id)
{
    try {
        $jenisTindakLanjutTerbaru = null;
        
        if ($laporan->jenis == 1) {
            // Validasi untuk gangguan peralatan
            $request->validate([
                'peralatan_id' => 'required|integer|exists:peralatan,id',
                'jenis_tindaklanjut' => 'required|in:0,1',
                'waktu' => 'required|date',
                'kondisi' => 'required|in:0,1',
                'deskripsi' => 'nullable|string|max:1000',
            ]);

            // Cari gangguan_peralatan_id
            $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                ->where('peralatan_id', $request->peralatan_id)
                ->latest()
                ->first();

            if (!$gangguan) {
                throw new \Exception("Data gangguan peralatan untuk ID {$request->peralatan_id} tidak ditemukan.");
            }

            // Simpan tindak lanjut baru
            $tindakLanjutBaru = TlGangguanPeralatan::create([
                'laporan_id' => $laporan->id,
                'peralatan_id' => $request->peralatan_id,
                'gangguan_peralatan_id' => $gangguan->id,
                'layanan_id' => $laporan->layanan_id,
                'jenis_tindaklanjut' => (int)$request->jenis_tindaklanjut,
                'waktu' => $request->waktu,
                'kondisi' => (int)$request->kondisi,
                'deskripsi' => $request->deskripsi,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // AUTO-UPDATE KONDISI LAYANAN UNTUK GANGGUAN PERALATAN
            $peralatanGangguanIds = GangguanPeralatan::where('laporan_id', $laporan->id)
                ->where('kondisi', 0)
                ->pluck('peralatan_id')
                ->toArray();

            // Cek kondisi terbaru setiap peralatan bermasalah
            $semuaBeroperasi = true;
            foreach ($peralatanGangguanIds as $peralatanId) {
                $tindakLanjutTerbaru = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                    ->where('peralatan_id', $peralatanId)
                    ->latest('waktu')
                    ->first();

                if (!$tindakLanjutTerbaru || $tindakLanjutTerbaru->kondisi == 0) {
                    $semuaBeroperasi = false;
                    break;
                }
            }

            // PERBAIKAN UTAMA: Cari jenis tindak lanjut dengan waktu TERBARU dari SEMUA peralatan
            // Ini yang akan menentukan step selanjutnya
            $tindakLanjutTerakhirGlobal = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->latest('waktu') // Urutkan berdasarkan waktu terbaru
                ->latest('id')    // Jika waktu sama, urutkan berdasarkan ID terbaru
                ->first();
            
            // Gunakan jenis dari tindak lanjut yang baru saja ditambahkan atau yang terbaru
            if ($tindakLanjutTerakhirGlobal && $tindakLanjutTerakhirGlobal->id == $tindakLanjutBaru->id) {
                // Jika yang baru ditambahkan adalah yang terbaru berdasarkan waktu
                $jenisTindakLanjutTerbaru = (int)$request->jenis_tindaklanjut;
            } else if ($tindakLanjutTerakhirGlobal) {
                // Jika ada tindak lanjut lain yang lebih baru berdasarkan waktu
                $jenisTindakLanjutTerbaru = $tindakLanjutTerakhirGlobal->jenis_tindaklanjut;
            } else {
                // Fallback ke yang baru ditambahkan
                $jenisTindakLanjutTerbaru = (int)$request->jenis_tindaklanjut;
            }

            // Update kondisi layanan temp
            $kondisiLayananBaru = $semuaBeroperasi ? 1 : 0;
            $laporan->kondisi_layanan_temp = $kondisiLayananBaru;
            
            // PERBAIKAN: Simpan jenis tindak lanjut terbaru berdasarkan waktu terbaru
            session(['jenis_tindaklanjut_terbaru_' . $id => $jenisTindakLanjutTerbaru]);
            
            $laporan->save();

            \Log::info('Auto-update kondisi layanan peralatan:', [
                'laporan_id' => $id,
                'peralatan_id' => $request->peralatan_id,
                'kondisi_tindaklanjut' => $request->kondisi,
                'jenis_tindaklanjut_input' => $request->jenis_tindaklanjut,
                'tindaklanjut_terbaru_id' => $tindakLanjutTerakhirGlobal->id ?? null,
                'jenis_tindaklanjut_terbaru_final' => $jenisTindakLanjutTerbaru,
                'kondisi_layanan_temp_baru' => $kondisiLayananBaru,
                'semua_beroperasi' => $semuaBeroperasi,
                'waktu_tindaklanjut_baru' => $request->waktu,
                'waktu_tindaklanjut_terbaru_global' => $tindakLanjutTerakhirGlobal->waktu ?? null
            ]);

        } else {
            // Validasi untuk gangguan non-peralatan
            $request->validate([
                'waktu' => 'required|date',
                'kondisi' => 'required|in:0,1',
                'deskripsi' => 'nullable|string|max:1000',
            ]);

            // Cari gangguan_non_peralatan_id yang benar
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->first();
            
            if (!$gangguanNonPeralatan) {
                throw new \Exception('Data gangguan non-peralatan tidak ditemukan untuk laporan ini');
            }

            // Simpan tindak lanjut baru
            TlGangguanNonPeralatan::create([
                'laporan_id' => $laporan->id,
                'layanan_id' => $laporan->layanan_id,
                'gangguan_non_peralatan_id' => $gangguanNonPeralatan->id,
                'waktu' => $request->waktu,
                'kondisi' => (int)$request->kondisi,
                'deskripsi' => $request->deskripsi,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // AUTO-UPDATE KONDISI LAYANAN UNTUK GANGGUAN NON-PERALATAN
            $kondisiLayananBaru = (int)$request->kondisi;
            $laporan->kondisi_layanan_temp = $kondisiLayananBaru;
            $laporan->save();

            // Untuk non-peralatan, selalu ke step 5
            $jenisTindakLanjutTerbaru = 1;

            \Log::info('Auto-update kondisi layanan non-peralatan:', [
                'laporan_id' => $id,
                'kondisi_tindaklanjut' => $request->kondisi,
                'kondisi_layanan_temp_baru' => $kondisiLayananBaru
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tindak lanjut berhasil ditambahkan dan kondisi layanan telah diperbarui',
            'next_step_info' => [
                'jenis_tindaklanjut_terbaru' => $jenisTindakLanjutTerbaru,
                'next_step' => $jenisTindakLanjutTerbaru == 0 ? 'step4' : 'step5',
                'next_step_url' => $this->getNextStepUrl($laporan, $jenisTindakLanjutTerbaru, $id),
                'debug_info' => [
                    'input_jenis' => $request->jenis_tindaklanjut ?? null,
                    'final_jenis' => $jenisTindakLanjutTerbaru,
                    'waktu_input' => $request->waktu ?? null
                ]
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in handleAjaxTindakLanjut:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

/**
 *  Method untuk mendapatkan jenis tindak lanjut terbaru
 * Digunakan untuk memastikan konsistensi penentuan step selanjutnya
 */
private function getLatestJenisTindakLanjut($laporanId, $jenisLaporan)
{
    if ($jenisLaporan == 1) {
        // Untuk gangguan peralatan
        $tindakLanjutTerbaru = TlGangguanPeralatan::where('laporan_id', $laporanId)
            ->latest('waktu')
            ->latest('id')  // Jika waktu sama, ambil ID terbesar
            ->first();
            
        return $tindakLanjutTerbaru ? $tindakLanjutTerbaru->jenis_tindaklanjut : 1;
    } else {
        // Untuk non-peralatan, selalu perbaikan (tidak ada penggantian)
        return 1;
    }
}

/**
 * Method editStep4 yang sudah diperbarui dengan pengecekan yang lebih akurat
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
        return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
    }

    // Cek jenis tindak lanjut terbaru berdasarkan waktu
    $jenisTindakLanjutTerbaru = $this->getLatestJenisTindakLanjut($laporan->id, $laporan->jenis);
    
    \Log::info('Check editStep4 access:', [
        'laporan_id' => $id,
        'jenis_laporan' => $laporan->jenis,
        'jenis_tindaklanjut_terbaru' => $jenisTindakLanjutTerbaru
    ]);

    // Jika bukan gangguan peralatan, langsung ke step 5
    if ($laporan->jenis != 1) {
        return redirect()->route('logbook.laporan.edit.step5', $id)
            ->with('notif', 'edit_sukses')
            ->with('message', 'Gangguan non-peralatan tidak memerlukan data penggantian.');
    }

    // PERBAIKAN: Jika jenis tindak lanjut terbaru adalah perbaikan (1), langsung ke step 5
    if ($jenisTindakLanjutTerbaru == 1) {
        return redirect()->route('logbook.laporan.edit.step5', $id)
            ->with('notif', 'edit_sukses')
            ->with('message', 'Tindak lanjut terakhir adalah perbaikan, tidak memerlukan data penggantian.');
    }

    $jenisTl = null;
    $peralatanLama = collect();
    $peralatanTersedia = collect();
    $penggantiPeralatan = collect();

    $kodePenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');
    $kodeGangguan = (int) config('constants.kondisi_gangguan_peralatan.gangguan');

    // PERBAIKAN: Hanya proses jika jenis tindak lanjut terbaru adalah penggantian
    if ($jenisTindakLanjutTerbaru == 0) {
        // Cek apakah ada tindak lanjut dengan jenis penggantian
        $tlPenggantian = TlGangguanPeralatan::where('laporan_id', $laporan->id)
            ->where('jenis_tindaklanjut', $kodePenggantian)
            ->exists();

        if (!$tlPenggantian) {
            return redirect()->route('logbook.laporan.edit.step5', $id)
                ->with('notif', 'edit_sukses')
                ->with('message', 'Tidak ada peralatan yang memerlukan penggantian.');
        }

        // Ambil semua peralatan yang memiliki tindak lanjut penggantian
        $peralatanIdsWithPenggantian = TlGangguanPeralatan::where('laporan_id', $laporan->id)
            ->where('jenis_tindaklanjut', $kodePenggantian)
            ->pluck('peralatan_id')
            ->unique();

        // Ambil data gangguan untuk peralatan yang perlu diganti
        $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
            ->where('kondisi', $kodeGangguan)
            ->whereIn('peralatan_id', $peralatanIdsWithPenggantian)
            ->get();

        // Set jenis tindak lanjut
        $jenisTl = $kodePenggantian;

        // Proses data peralatan yang perlu diganti
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

        // Jika tidak ada peralatan yang ditemukan, redirect ke step 5
        if ($peralatanLama->isEmpty()) {
            return redirect()->route('logbook.laporan.edit.step5', $id)
                ->with('notif', 'edit_sukses')
                ->with('message', 'Tidak ada data peralatan yang perlu diganti.');
        }

        // Peralatan yang tersedia untuk penggantian
        $peralatanTersedia = Peralatan::where('status', 1)
            ->where('kondisi', 1)
            ->whereNotIn('id', $peralatanLama->pluck('id'))
            ->get();

        // Ambil peralatan pengganti yang sudah ada
        $pengganti = TlPenggantianPeralatan::where('laporan_id', $laporan->id)
            ->with('peralatanBaru')
            ->get();

        $penggantiPeralatan = $pengganti->keyBy('peralatan_lama_id');
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
        'jenis' => JenisAlat::where('status', 1)->get(),
        'perusahaan' => Perusahaan::where('status', 1)->get(),
    ]);
}
/**
 * Function untuk menyimpan/update data edit step 4 (penggantian).
 * PERBAIKAN: Handle kasus dimana tidak ada data penggantian
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
        // Cek apakah ada tindak lanjut penggantian
        $kodePenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');
        $tlPenggantian = TlGangguanPeralatan::where('laporan_id', $id)
            ->where('jenis_tindaklanjut', $kodePenggantian)
            ->exists();

        if (!$tlPenggantian) {
            // Jika tidak ada tindak lanjut penggantian, langsung redirect ke step 5
            DB::rollback();
            \Log::info('Tidak ada tindak lanjut penggantian untuk diproses', ['laporan_id' => $id]);
            return redirect()->route('logbook.laporan.edit.step5', $id)
                ->with('notif', 'edit_sukses')
                ->with('message', 'Tidak ada data penggantian yang perlu diproses.');
        }

        // Validasi dasar
        $validated = $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
        ]);

        // Jika tidak ada data penggantian yang dikirim, skip step ini
        if (!$request->has('penggantian') || empty($request->penggantian)) {
            DB::rollback();
            \Log::info('Tidak ada data penggantian yang dikirim', ['laporan_id' => $id]);
            return redirect()->route('logbook.laporan.edit.step5', $id)
                ->with('notif', 'edit_sukses')
                ->with('message', 'Tidak ada data penggantian baru yang diproses.');
        }

        // Validasi data penggantian jika ada
        $request->validate([
            'penggantian' => 'required|array|min:1',
            'penggantian.*.peralatan_lama_id' => 'required|exists:peralatan,id',
            'penggantian.*.peralatan_baru_id' => 'required|exists:peralatan,id|different:penggantian.*.peralatan_lama_id',
        ]);

        $dataPenggantian = $request->penggantian;

        // Ambil tindak lanjut gangguan dengan jenis penggantian
        $tl = TlGangguanPeralatan::where('laporan_id', $id)
            ->where('jenis_tindaklanjut', $kodePenggantian)
            ->latest()
            ->first();

        if (!$tl) {
            DB::rollback();
            return redirect()->back()->with('error', 'Tidak ada tindak lanjut penggantian yang ditemukan.');
        }

        // Hapus data penggantian lama
        TlPenggantianPeralatan::where('laporan_id', $id)->delete();

        $saved = 0;

        foreach ($dataPenggantian as $i => $item) {
            if ($item['peralatan_lama_id'] === $item['peralatan_baru_id']) {
                \Log::warning("Step 4: Peralatan lama & baru sama (laporan_id: $id, index: $i)");
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
            \Log::info("Step 4: Tidak ada data valid disimpan untuk laporan ID {$id}");
            return redirect()->back()->with('warning', 'Tidak ada data yang disimpan. Periksa kembali isian Anda.');
        }

        // Redirect ke step 5
        return redirect()->route('logbook.laporan.edit.step5', $id)
            ->with('notif', 'edit_sukses')
            ->with('message', 'Data penggantian berhasil disimpan.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Gagal update Step 4: " . $e->getMessage());
        
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
    $semuaTindakLanjut = collect();
    $tindaklanjut = null;
    $gangguanNonPeralatan = null;

    if ($laporan->jenis == 1) {
        // Ambil semua gangguan peralatan
        $detailGangguanPeralatan = GangguanPeralatan::with('peralatan')
            ->where('laporan_id', $laporan->id)
            ->get();

        // PERBAIKAN: Ambil SEMUA tindak lanjut gangguan peralatan
        $semuaTindakLanjut = TlGangguanPeralatan::with([
            'peralatan' => function($query) {
                $query->select('id', 'kode', 'nama', 'merk', 'tipe', 'model', 'serial_number');
            },
            'getCreatedName' => function($query) {
                $query->select('id', 'name');
            },
            'getUpdatedName' => function($query) {
                $query->select('id', 'name');
            }
        ])
        ->where('laporan_id', $laporan->id)
        ->orderBy('waktu', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        // Ambil tindaklanjut terakhir untuk referensi
        $tindaklanjut = $semuaTindakLanjut->first();

        // PERBAIKAN: Pisahkan berdasarkan jenis tindak lanjut dengan benar
        $kodePenggantian = config('constants.jenis_tindaklanjut.penggantian');
        $kodePerbaikan = config('constants.jenis_tindaklanjut.perbaikan');

        // Ambil semua tindak lanjut perbaikan
        $perbaikan = $semuaTindakLanjut->where('jenis_tindaklanjut', $kodePerbaikan);

        // Ambil tindak lanjut penggantian
        $penggantianTl = $semuaTindakLanjut->where('jenis_tindaklanjut', $kodePenggantian);

        // Ambil data penggantian peralatan jika ada tindak lanjut penggantian
        if ($penggantianTl->isNotEmpty()) {
            $penggantian = TlPenggantianPeralatan::with([
                'peralatanLama' => function($query) {
                    $query->select('id', 'kode', 'nama', 'merk', 'tipe', 'model', 'serial_number');
                },
                'peralatanBaru' => function($query) {
                    $query->select('id', 'kode', 'nama', 'merk', 'tipe', 'model', 'serial_number');
                },
                'tindaklanjut' => function ($query) {
                    $query->select('id', 'jenis_tindaklanjut', 'deskripsi', 'waktu', 'kondisi');
                },
                'getCreatedName' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->where('laporan_id', $laporan->id)
            ->orderBy('created_at', 'desc')
            ->get();
        } else {
            // Jika tidak ada penggantian, set sebagai collection kosong
            $penggantian = collect();
        }

    } else {
        // Ambil gangguan non peralatan
        $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)
            ->latest()
            ->first();

        // PERBAIKAN: Ambil SEMUA tindak lanjut gangguan non-peralatan
        $semuaTindakLanjut = TlGangguanNonPeralatan::with([
            'getCreatedName' => function($query) {
                $query->select('id', 'name');
            },
            'getUpdatedName' => function($query) {
                $query->select('id', 'name');
            }
        ])
        ->where('laporan_id', $laporan->id)
        ->orderBy('waktu', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        // Ambil tindaklanjut terakhir untuk referensi
        $tindaklanjut = $semuaTindakLanjut->first();
        
        // Untuk non-peralatan, semua tindak lanjut dianggap sebagai perbaikan
        $perbaikan = $semuaTindakLanjut;
    }

    // Log untuk debugging
    \Log::info('Step 5 Data:', [
        'laporan_id' => $id,
        'jenis_laporan' => $laporan->jenis,
        'total_tindak_lanjut' => $semuaTindakLanjut->count(),
        'total_penggantian' => $penggantian->count(),
        'total_perbaikan' => $perbaikan->count(),
        'kode_penggantian' => config('constants.jenis_tindaklanjut.penggantian'),
        'kode_perbaikan' => config('constants.jenis_tindaklanjut.perbaikan'),
    ]);

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
        'semuaTindakLanjut' => $semuaTindakLanjut,
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

        // Validasi status laporan - hanya draft dan open yang bisa diedit
        if (!in_array($laporan->status, ['draft', 'open', 1, 2])) {
            return back()->withErrors(['msg' => 'Laporan dengan status ini tidak dapat diedit.']);
        }

        // PERBAIKAN: Tentukan kondisi layanan berdasarkan tindak lanjut terakhir
        $kondisiLayananAkhir = $laporan->kondisi_layanan_temp;

        // Jika kondisi_layanan_temp masih null, cek dari tindak lanjut terakhir
        if ($kondisiLayananAkhir === null) {
            if ($laporan->jenis == 1) {
                $tindakLanjutTerakhir = TlGangguanPeralatan::where('laporan_id', $id)
                    ->latest('waktu')
                    ->latest('created_at')
                    ->first();
                
                if ($tindakLanjutTerakhir) {
                    $kondisiLayananAkhir = $tindakLanjutTerakhir->kondisi;
                }
            } else {
                $tindakLanjutTerakhir = TlGangguanNonPeralatan::where('laporan_id', $id)
                    ->latest('waktu')
                    ->latest('created_at')
                    ->first();
                
                if ($tindakLanjutTerakhir) {
                    $kondisiLayananAkhir = $tindakLanjutTerakhir->kondisi;
                }
            }
        }

        // PERBAIKAN: Update kondisi_layanan_temp dengan nilai yang sudah ditentukan
        if ($kondisiLayananAkhir !== null) {
            $laporan->kondisi_layanan_temp = $kondisiLayananAkhir;
        }

        // PERBAIKAN: Update status berdasarkan kondisi layanan yang sudah ditentukan
        if ($kondisiLayananAkhir == 1) { // SERVICEABLE
            $laporan->status = config('constants.status_laporan.closed');
            $laporan->waktu_close = now(); // Set waktu close jika ditutup
        } else { // UNSERVICEABLE
            $laporan->status = config('constants.status_laporan.open');
            $laporan->waktu_close = null; // Reset waktu close jika masih terbuka
        }
        
        // Update user yang melakukan perubahan terakhir
        $laporan->updated_by = Auth::id();
        
        $laporan->save();

        // Update kondisi layanan di tabel layanan
        if ($laporan->layanan && $kondisiLayananAkhir !== null) {
            $laporan->layanan->kondisi = $kondisiLayananAkhir;
            $laporan->layanan->save();
        }

        DB::commit();

        // Log untuk debugging
        \Log::info('Step 5 Update Success:', [
            'laporan_id' => $id,
            'kondisi_layanan_temp_before' => $laporan->getOriginal('kondisi_layanan_temp'),
            'kondisi_layanan_akhir' => $kondisiLayananAkhir,
            'kondisi_layanan_temp_after' => $laporan->kondisi_layanan_temp,
            'status_laporan' => $laporan->status,
            'waktu_close' => $laporan->waktu_close,
        ]);

        // Redirect berdasarkan status akhir
        if ($laporan->status == config('constants.status_laporan.closed')) {
            return redirect()->route('logbook.riwayat.daftar')
                ->with('notif', 'edit_sukses')
                ->with('message', 'Laporan berhasil diselesaikan dan dipindahkan ke riwayat.');
        } else {
            return redirect()->route('logbook.laporan.daftar')
                ->with('notif', 'edit_sukses')
                ->with('message', 'Laporan berhasil diperbarui dan masih dalam status terbuka.');
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
        