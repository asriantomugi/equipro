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
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        $jenisLaporan = config('constants.jenis_laporan');
        $rules = [
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_laporan' => ['required', Rule::in(array_keys($jenisLaporan))],
            'waktu_gangguan' => 'required|date',
        ];

        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $rules = array_merge($rules, [
                'peralatan' => 'required|array|min:1',
                'peralatan.*.id' => 'required|exists:peralatan,id',
                'peralatan.*.kondisi' => 'required|in:0,1',
                'peralatan.*.deskripsi' => 'required|string',
            ]);
        } else {
            $rules['deskripsi_gangguan'] = 'required|string';
        }

        // Tambahkan custom error messages
        $messages = [
            'layanan_id.required' => 'Layanan tidak boleh kosong.',
            'layanan_id.exists' => 'Layanan tidak ditemukan.',
            'jenis_laporan.required' => 'Jenis laporan wajib dipilih.',
            'jenis_laporan.in' => 'Jenis laporan tidak valid.',
            'waktu_gangguan.required' => 'Waktu gangguan wajib diisi.',
            'waktu_gangguan.date' => 'Format waktu gangguan tidak valid.',

            // Peralatan
            'peralatan.required' => 'Data peralatan wajib diisi.',
            'peralatan.array' => 'Format data peralatan tidak sesuai.',
            'peralatan.*.id.required' => 'ID peralatan wajib diisi.',
            'peralatan.*.id.exists' => 'Peralatan tidak ditemukan.',
            'peralatan.*.kondisi.required' => 'Kondisi peralatan wajib dipilih.',
            'peralatan.*.kondisi.in' => 'Kondisi peralatan tidak valid.',
            'peralatan.*.deskripsi.string' => 'Deskripsi gangguan harus berupa teks.',

            // Non-peralatan
            'deskripsi_gangguan.string' => 'Deskripsi gangguan harus berupa teks.',
        ];

        // Proses validasi
        $validated = $request->validate($rules, $messages);

        // Simpan ke database
        DB::beginTransaction();
        try {
            $laporan = Laporan::create([
                'no_laporan' => now()->format('YmdHis') . rand(100, 999),
                'layanan_id' => $request->layanan_id,
                'jenis' => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
                'waktu' => $request->waktu_gangguan,
                'status' => 1,
                'kondisi_layanan_temp' => false,
                'created_by' => auth()->id(),
            ]);

            if ($request->jenis_laporan === 'gangguan_peralatan') {
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
                GangguanNonPeralatan::create([
                    'laporan_id' => $laporan->id,
                    'layanan_id' => $request->layanan_id,
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'deskripsi' => $request->deskripsi_gangguan,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()
                ->route('tambah.step3', ['laporan_id' => $laporan->id])
                ->with('notif', 'tambah_sukses');

        } catch (\Exception $e) {
            DB::rollBack();
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

        $rules = [
            'laporan_id' => 'required|exists:laporan,id',
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_laporan' => 'required|in:gangguan_peralatan,gangguan_non_peralatan',
            'waktu_gangguan' => 'required|date',
        ];

        if ($request->jenis_laporan === 'gangguan_peralatan') {
            $rules = array_merge($rules, [
                'peralatan' => 'required|array|min:1',
                'peralatan.*.id' => 'required|exists:peralatan,id',
                'peralatan.*.kondisi' => 'required|in:0,1',
                'peralatan.*.deskripsi' => 'required|string',
            ]);
        } else {
            $rules['deskripsi_gangguan'] = 'required|string';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $laporan = Laporan::findOrFail($request->laporan_id);
            $laporan->update([
                'layanan_id' => $request->layanan_id,
                'jenis' => $request->jenis_laporan === 'gangguan_peralatan' ? 1 : 2,
                'waktu' => $request->waktu_gangguan,
                'status' => 1,
            ]);

            // Bersihkan data lama
            GangguanPeralatan::where('laporan_id', $laporan->id)->delete();
            GangguanNonPeralatan::where('laporan_id', $laporan->id)->delete();

            if ($request->jenis_laporan === 'gangguan_peralatan') {
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
                GangguanNonPeralatan::create([
                    'laporan_id' => $laporan->id,
                    'layanan_id' => $request->layanan_id,
                    'waktu_gangguan' => $request->waktu_gangguan,
                    'deskripsi' => $request->deskripsi_gangguan,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('tambah.step3', ['laporan_id' => $laporan->id])
                ->with('notif', 'perubahan_tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan perubahan: ' . $e->getMessage()])
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
        if (!Auth::check()) {
            return redirect('/login');
        }    

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (
            session()->get('role_id') != config('constants.role.super_admin')
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')
        ) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================//

        // Ambil laporan
        $laporan = Laporan::findOrFail($laporan_id);

        // Ambil layanan + daftar peralatan
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($laporan->layanan_id);

        // Ambil gangguan
        $gangguanPeralatan = null;
        $gangguanNonPeralatan = null;

        if ($laporan->jenis_laporan == 1) {
            $gangguanPeralatan = GangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        } elseif ($laporan->jenis_laporan == 2) {
            $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
        }

        // Constants
        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiTindaklanjut = config('constants.kondisi_tindaklanjut');

        // View
        return view('logbook.laporan.tambah.step3', [
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step3',
            'submenu' => 'Tambah',
            'laporan' => $laporan,
            'layanan' => $layanan,
            'gangguanPeralatan' => $gangguanPeralatan,
            'gangguanNonPeralatan' => $gangguanNonPeralatan,
            'jenisTindakLanjut' => $jenisTindakLanjut,
            'kondisiTindaklanjut' => $kondisiTindaklanjut,
            'kondisiSetelah' => config('constants.kondisi_layanan'),
            'step' => 3,
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

        $laporan = Laporan::findOrFail($laporan_id);
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])->findOrFail($laporan->layanan_id);

        $jenisTindakLanjut = config('constants.jenis_tindaklanjut');
        $kondisiTindaklanjut = config('constants.kondisi_tindaklanjut');

        $tlPeralatan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->get()->groupBy('peralatan_id');
        $tlNon = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->first();

        return view('logbook.laporan.tambah.step3_back')->with([
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step3',
            'submenu' => 'Tambah',
            'laporan' => $laporan,
            'layanan' => $layanan,
            'jenisTindakLanjut' => $jenisTindakLanjut,
            'kondisiTindaklanjut' => $kondisiTindaklanjut,
            'tindaklanjutPeralatan' => $tlPeralatan,
            'tindaklanjutNonPeralatan' => $tlNon,
            'kondisiSetelah' => config('constants.kondisi_layanan'),
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

        $laporan = Laporan::with('layanan')->findOrFail($laporan_id);
        $jenis_tindaklanjut = null;
        $peralatanLama = collect();
        $peralatanTersedia = collect();

        $constPenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');

        // Mapping boolean => label kondisi
        $kondisiMap = [];
        foreach (config('constants.kondisi_peralatan') as $label => $value) {
            $kondisiMap[$value] = ucfirst($label); // ex: 'normal' => 'Normal'
        }

        if ($laporan->jenis == 1) {
            $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();

            if ($tindaklanjut) {
                $jenis_tindaklanjut = (int) $tindaklanjut->jenis_tindaklanjut;

                if ($jenis_tindaklanjut === $constPenggantian) {
                    // Ambil data kondisi dari tabel gangguan_peralatan
                    $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                        ->get()
                        ->keyBy('peralatan_id');

                    // Ambil peralatan dari layanan
                    $peralatanLama = $laporan->layanan
                        ->daftarPeralatanLayanan()
                        ->with('peralatan')
                        ->where('status', true)
                        ->get()
                        ->filter(fn($item) => $item->peralatan)
                        ->map(function ($item) use ($gangguan, $kondisiMap) {
                            $p = $item->peralatan;
                            $kondisiBool = $gangguan[$p->id]->kondisi ?? $item->kondisi;
                            $kondisiLabel = $kondisiMap[$kondisiBool] ?? '-';

                            return (object)[
                                'id' => $p->id,
                                'kode' => $p->kode,
                                'nama' => $p->nama,
                                'merk' => $p->merk,
                                'tipe' => $p->tipe,
                                'model' => $p->model,
                                'serial_number' => $p->serial_number,
                                'status' => $p->status ? 'Aktif' : 'Tidak Aktif',
                                'kondisi' => $kondisiLabel,
                            ];
                        })
                        ->values();

                    // Ambil peralatan baru yang tersedia
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
        $jenis_tindaklanjut = null;
        $peralatanLama = collect();
        $peralatanTersedia = collect();
        $peralatanBaru = [];

        $constPenggantian = (int) config('constants.jenis_tindaklanjut.penggantian');

        $kondisiMap = [];
        foreach (config('constants.kondisi_peralatan') as $label => $value) {
            $kondisiMap[$value] = ucfirst($label);
        }

        if ($laporan->jenis == 1) {
            $tindaklanjut = TlGangguanPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tindaklanjut) {
                $jenis_tindaklanjut = (int) $tindaklanjut->jenis_tindaklanjut;
                if ($jenis_tindaklanjut === $constPenggantian) {
                    $gangguan = GangguanPeralatan::where('laporan_id', $laporan->id)->get()->keyBy('peralatan_id');

                    $peralatanLama = $laporan->layanan
                        ->daftarPeralatanLayanan()
                        ->with('peralatan')
                        ->where('status', true)
                        ->get()
                        ->filter(fn($item) => $item->peralatan)
                        ->map(function ($item) use ($gangguan, $kondisiMap) {
                            $p = $item->peralatan;
                            $kondisiBool = $gangguan[$p->id]->kondisi ?? $item->kondisi;
                            $kondisiLabel = $kondisiMap[$kondisiBool] ?? '-';

                            return (object)[
                                'id' => $p->id,
                                'kode' => $p->kode,
                                'nama' => $p->nama,
                                'merk' => $p->merk,
                                'tipe' => $p->tipe,
                                'model' => $p->model,
                                'serial_number' => $p->serial_number,
                                'status' => $p->status ? 'Aktif' : 'Tidak Aktif',
                                'kondisi' => $kondisiLabel,
                            ];
                        })
                        ->values();

                    // Ambil peralatan baru tersedia
                    $peralatanTersedia = Peralatan::where('status', 1)
                        ->whereNotIn('id', $peralatanLama->pluck('id'))
                        ->get();

                    // Ambil data penggantian sebelumnya untuk prefill
                    $penggantian = TlPenggantianPeralatan::where('laporan_id', $laporan->id)->get();
                    foreach ($penggantian as $index => $pg) {
                        $pb = Peralatan::find($pg->peralatan_baru_id);
                        if ($pb) {
                            $peralatanBaru[$index] = [
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
            $tindaklanjut = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)->latest()->first();
            if ($tindaklanjut) {
                $jenis_tindaklanjut = (int) $tindaklanjut->jenis_tindaklanjut;
            }
        }

        return view('logbook.laporan.tambah.step4_back', [
            'judul' => 'Laporan',
            'module' => 'Logbook',
            'menu' => 'Laporan',
            'menu_url' => '/logbook/laporan/tambah/step4/back',
            'submenu' => 'Tambah',
            'laporan' => $laporan,
            'jenis_tindaklanjut' => $jenis_tindaklanjut,
            'peralatanLama' => $peralatanLama,
            'peralatanTersedia' => $peralatanTersedia,
            'jenis' => JenisAlat::where('status', 1)->get(),
            'perusahaan' => Perusahaan::where('status', 1)->get(),
            'peralatanBaru' => $peralatanBaru,
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
        } else {
            $query->where('kondisi', 1); 
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

        try {
            $laporan = Laporan::findOrFail($request->laporan_id);

            // Ambil data tindak lanjut peralatan terbaru
            $tlGangguan = TlGangguanPeralatan::where('laporan_id', $laporan->id)->get()->keyBy('peralatan_id');

            // Loop peralatan baru yang dikirim berdasarkan index
            foreach ($request->peralatan_baru as $index => $pb) {
                if (!empty($pb['id'])) {
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

            return redirect()->route('tambah.step5', ['laporan_id' => $laporan->id])
                            ->with('notif', 'tambah_sukses');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
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
                        $query->select('id', 'jenis_tindaklanjut', 'deskripsi', 'waktu');
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
            // Ambil tindaklanjut terakhir dari tl_gangguan_non_peralatan
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
        ]);

        DB::beginTransaction();
        try {
            $laporan = Laporan::findOrFail($request->laporan_id);

            if ($laporan->kondisi_layanan_temp) { // SERVICEABLE
                $laporan->status = config('constants.status_laporan.closed');
            } else { // UNSERVICEABLE
                $laporan->status = config('constants.status_laporan.open');
            }

            $laporan->save();

            DB::commit();

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

}