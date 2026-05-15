<?php

namespace App\Http\Controllers\Logbook;

/**
 * LaporanController.php
 * Controller ini digunakan untuk menangani proses CRUD Laporan
 *
 * @author Yanti Melani, Mugi Asrianto
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
     * Function untuk menampilkan daftar laporan.
     *
     * Akses:
     * - Super Admin
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
        // Ambil daftar laporan hanya yang draft & open
        $daftar = Laporan::with([
            'layanan.fasilitas',
            'layanan.lokasiTk1',
            'layanan.lokasiTk2',
            'layanan.lokasiTk3',
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
        $menu_url = route('logbook.laporan.daftar');

        return view('logbook.laporan.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('daftar', $daftar);
    }

    /**
     * Function untuk menampilkan form tambah step 1.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step1
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep1(Request $request)
    {
        // ambil daftar Fasilitas yang aktif
        $fasilitas = Fasilitas::where('status', 1)->get();

        // ambil daftar Lokasi yang aktif
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        $lokasi_tk_2 = LokasiTk2::where('status', 1)->get();
        $lokasi_tk_3= LokasiTk3::where('status', 1)->get();

        // KOSONGKAN daftar layanan di load awal - user harus klik filter terlebih dahulu
        $layanan = collect(); // collection kosong

        // ambil daftar Layanan yang aktif
        $query = Layanan::with([
            'fasilitas', 
            'lokasiTk1', 
            'lokasiTk2', 
            'lokasiTk3'
            ])
            ->where('status', 1); // status layanan aktif
                
        // Filter berdasarkan input
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas_id', $request->fasilitas);
        }

        if ($request->filled('lokasiTk1')) {
            $query->where('lokasi_tk_1_id', $request->lokasiTk1);
        }

        if ($request->filled('lokasiTk2')) {
            $query->where('lokasi_tk_2_id', $request->lokasiTk2);
        }

        if ($request->filled('lokasiTk3')) {
            $query->where('lokasi_tk_3_id', $request->lokasiTk3);
        }

        $layanan = $query->get();

        // buat variable jenis_tindaklanjut untuk di breadcrumb
        $jenis_tindaklanjut = null;

        // variabel untuk dikirim ke halaman view
        $judul = "Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = route('logbook.laporan.daftar');
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
        ->with('lokasiTk1', $lokasi_tk_1)
        ->with('lokasiTk2', $lokasi_tk_2)
        ->with('lokasiTk3', $lokasi_tk_3)
        ->with('jenis_tindaklanjut', $jenis_tindaklanjut)
        ;
    }

    /**
     * Function untuk memproses filter daftar layanan pada form tambah step 1.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step1/filter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep1Filter(Request $request)
    {
        // ambil ID layanan yang sudah memiliki laporan dengan status "open"
        /*$layanan_open_ids = Laporan::where('status', config('constants.status_laporan.open'))
                            ->pluck('layanan_id')
                            ->toArray();*/

        // ambil daftar Layanan yang aktif dan dengan kondisi serviceable
        $query = Layanan::with(['fasilitas', 'lokasiTk1', 'lokasiTk2', 'lokasiTk3'])
                ->whereIn('kondisi', [config('constants.kondisi_layanan.serviceable')])
                ->where('status', 1);
        
        // Keluarkan layanan yang sudah ada laporan open
        /*
        if (!empty($layanan_open_ids)) {
            $query->whereNotIn('id', $layanan_open_ids);
        }*/

        // Filter berdasarkan input
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas_id', $request->fasilitas);
        }

        if ($request->filled('lokasiTk1')) {
            $query->where('lokasi_tk_1_id', $request->lokasiTk1);
        }

        if ($request->filled('lokasiTk2')) {
            $query->where('lokasi_tk_2_id', $request->lokasiTk2);
        }

        if ($request->filled('lokasiTk3')) {
            $query->where('lokasi_tk_3_id', $request->lokasiTk3);
        }

        $layanan = $query->get();

        return response()->view('logbook.laporan.modal_daftar_layanan', compact('layanan'));
    }


    /**
     * Function untuk memproses tambah laporan step 1.
     * Proses ini dimulai saat mengklik tombol "Pilih" pada daftar layanan yang tersedia
     * berdasarkan filter yang telah dipilih pada form tambah laporan step 1.
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
    public function tambahStep1(Request $request)
    {
        // ambil data layanan dengan status aktif dengan kondisi serviceable
        $layanan = Layanan::where('id', $request->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->whereIn('kondisi', [config('constants.kondisi_layanan.serviceable')])
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman form tambah laporan step 1 dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.tambah.step1.form')
                ->with('notif', 'layanan_null');
        }

        // cek apakah layanan tersebut sedang dalam laporan yang berstatus OPEN
        /*$statusOpen = Laporan::where('layanan_id', $layanan->id)
            ->where('status', config('constants.status_laporan.open'))
            ->first();

        // jika benar
        if(statusOpen){
            // kembali ke halaman form tambah laporan step 1 dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.tambah.step1.form')
                ->with('notif', 'layanan_open');
        }*/

        // jika tidak, lanjut ke halaman tambah laporan step 2
        return redirect()
            ->route('logbook.laporan.tambah.step2', ['layanan_id' => $layanan->id])
            ->with('notif', 'tambah_sukses');
    }

    /**
     * Function untuk menampilkan form tambah layanan step 2 (form Input Gangguan).
     * Menampilkan data layanan yang telah dipilih berdasarkan ID Layanan yang dikirim.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step2/{layanan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep2($layanan_id)
    {
        // ambil data layanan dengan status aktif dengan kondisi serviceable
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->where('id', $layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->whereIn('kondisi', [config('constants.kondisi_layanan.serviceable')])
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman form tambah laporan step 1 dan tampilkan pesan error
            return redirect()->back()->with('notif', 'layanan_null');
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // buat variable jenis_tindaklanjut untuk di breadcrumb
        $jenis_tindaklanjut = null;

        // Data tambahan untuk view
        $judul = "Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = route('logbook.laporan.daftar');
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step2')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('daftar_peralatan', $daftar_peralatan)
            ->with('jenis_tindaklanjut', $jenis_tindaklanjut)
            ;
    }

    /**
     * Function untuk memproses form tambah step 2 (form Input Gangguan & Tindaklanjut).
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step2/{layanan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function tambahStep2(Request $request)
    {
        // validasi umum
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'jenis' => 'required|in:1,2', // 1 = peralatan, 2 = non peralatan
            'kondisi_layanan_open' => 'required|in:0,1',
            'kondisi_layanan_close' => 'nullable|in:0,1',
        ],[
            // pesan error
            'layanan_id.required' => 'Layanan kosong.',
            'layanan_id.exists' => 'Layanan tidak valid.',
            'jenis.required' => 'Jenis Laporan kosong.',
            'jenis.in' => 'Jenis Laporan tidak valid.',
        ]);

        // ambil data layanan aktif berdasarkan ID layanan dari form tambah laporan step 2
        $layanan = Layanan::where('id', $request->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar laporan dan tampilkan pesan error
            return redirect()
                    ->route('logbook.laporan.daftar')
                    ->with('notif', 'layanan_null');
        }

        // buat variable
        $no_laporan = now()->format('YmdHis') . rand(100, 999);
        $jenis = $request->jenis;
        $peralatan = $request->peralatan;
        $kondisi_layanan_open = $request->kondisi_layanan_open;
        $kondisi_layanan_temp = $request->kondisi_layanan_close;

        //dd($request->all());

        // jika jenis laporan = gangguan peralatan
        if ($jenis == config('constants.jenis_laporan.gangguan_peralatan')) {
            // validasi input
            $request->validate([
                'peralatan.*.peralatan_id' => 'exists:peralatan,id',
            ],[
                // pesan error
                'peralatan_id.*.peralatan_id.exists' => 'Peralatan tidak valid.',   
            ]);

            // cek apakah minimal ada 1 peralatan yang mengalami gangguan
            if (!collect($peralatan)->pluck('flag_gangguan')->contains(1)) {
                // jika tidak ada, kembalikan ke halaman tambah step 2 dan kirim pesan error
                return back()
                    ->with(['notif', 'gangguan_null'])
                    ->withInput();
            }
        } 

        // mulai transaksi ke database
        DB::beginTransaction();

        try{

            // insert data laporan baru ke tabel Laporan
            $laporan = Laporan::create([
                'no_laporan' => $no_laporan,
                'layanan_id' => $layanan->id,
                'jenis' =>  $jenis,
                'status' => config('constants.status_laporan.draft'), // draft
                'kondisi_layanan_open' => $kondisi_layanan_open,
                'kondisi_layanan_temp' => $kondisi_layanan_temp, // ini kondisi terupdate, nilai ini nantinya akan diupdate ke field kondisi_layanan_close
                'created_by' => session()->get('id')
            ]);

            // jika jenis laporan = gangguan peralatan, maka insert data ke tabel Gangguan Peralatan
            if($jenis == config('constants.jenis_laporan.gangguan_peralatan')){

                // Looping untuk mengambil data dan menyimpannya ke tabel Gangguan Peralatan
                // -------------------------------------------------------------------------
                //              LOOPING INSERT DATA KE TABEL GANGGUAN PERALATAN
                // -------------------------------------------------------------------------
                foreach ($peralatan as $satu) {

                    // jika peralatan tsb ada di-input gangguan
                    if($satu['flag_gangguan'] == 1){

                        $gangguan = GangguanPeralatan::create([
                            'laporan_id' => $laporan->id,
                            'layanan_id' => $layanan->id,
                            'peralatan_id' => $satu['peralatan_id'],
                            'waktu' => Carbon::createFromFormat('d-m-Y H:i', $satu['waktu_gangguan'])->format('Y-m-d H:i'),
                            'deskripsi' => $satu['deskripsi_gangguan'],
                            'kondisi' => $satu['kondisi_gangguan'], // kondisi saat gangguan
                            'kondisi_awal' => $satu['kondisi_awal'], // kondisi sebelum gangguan, diambil dari kondisi di tabel Peralatan.
                            'created_by' => session()->get('id')
                        ]);

                        // jika tindaklanjut ada diisi
                        if($satu['jenis_tindaklanjut'] != null){
                            // jika tindaklanjut perbaikan
                            if($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan')){

                                // ubah format waktu ke format yang bisa disimpan oleh DB
                                $waktu_mulai = Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                    ->format('Y-m-d H:i');
                                $waktu_selesai = Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                    ->format('Y-m-d H:i');

                                // insert data ke table Tindaklanjut Gangguan Peralatan
                                $tl_gangguan = TlGangguanPeralatan::create([
                                    'gangguan_id' => $gangguan->id,
                                    'laporan_id' => $laporan->id,
                                    'layanan_id' => $layanan->id,
                                    'peralatan_id' => $satu['peralatan_id'],
                                    'waktu_mulai' => $waktu_mulai,
                                    'waktu_selesai' => $waktu_selesai,
                                    'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                    'kondisi' => $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['kondisi_tindaklanjut'],
                                    'jenis' => $satu['jenis_tindaklanjut'],
                                    'created_by' => session()->get('id')
                                ]);
                            }
                            // jika tindaklanjut penggantian
                            else if($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian')){
                                // ubah format waktu ke format yang bisa disimpan oleh DB
                                $waktu_mulai = Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                    ->format('Y-m-d H:i');
                                $waktu_selesai = Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                    ->format('Y-m-d H:i');

                                // insert data ke table Tindaklanjut Gangguan Peralatan
                                $tl_gangguan = TlGangguanPeralatan::create([
                                    'gangguan_id' => $gangguan->id,
                                    'laporan_id' => $laporan->id,
                                    'layanan_id' => $layanan->id,
                                    'peralatan_id' => $satu['peralatan_id'],
                                    'waktu_mulai' => $waktu_mulai,
                                    'waktu_selesai' => $waktu_selesai,
                                    'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                    'jenis' => $satu['jenis_tindaklanjut'],
                                    'created_by' => session()->get('id')
                                ]);
                            }
                        } 
                    }
                }
                // -------------------------------------------------------------------------
                //                             END OF LOOPING
                // -------------------------------------------------------------------------

                // ambil waktu terawal dari daftar gangguan peralatan
                $waktuAwalGangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                    ->min('waktu_mulai');
                // masukkan ke variable waktu_layanan_open di tabel Laporan, sebagai waktu awal layanan gangguan
                $laporan->update([
                        'waktu_layanan_open' => $waktuAwalGangguan,
                        'updated_by' => session()->get('id')
                    ]);
            }
            // akhir proses simpan ke DB jika jenis gangguan peralatan

            // jika jenis laporan = gangguan non peralatan, maka insert data ke tabel Gangguan Non Peralatan
            else if($jenis == config('constants.jenis_laporan.gangguan_non_peralatan')){

                // insert data ke tabel Gangguan Peralatan
                $gangguan = GangguanNonPeralatan::create([
                    'laporan_id' => $laporan->id,
                    'layanan_id' => $layanan->id,
                    'waktu' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_gangguan)->format('Y-m-d H:i'),
                    'deskripsi'=> $request->deskripsi_gangguan,
                    'created_by' => session()->get('id')
                ]);
                
                // ambil flag tindaklanjut dari form
                $flag_tindaklanjut = $request->flag_tindaklanjut;
                // jika ada tindaklanjut
                if($flag_tindaklanjut != null && $flag_tindaklanjut == 1){
                    // insert data ke table Tindaklanjut Gangguan Peralatan
                    $tl_gangguan = TlGangguanNonPeralatan::create([
                        'gangguan_id' => $gangguan->id,
                        'laporan_id' => $laporan->id,
                        'layanan_id' => $layanan->id,
                        'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_mulai_tindaklanjut)->format('Y-m-d H:i'),
                        'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_selesai_tindaklanjut)->format('Y-m-d H:i'),
                        'deskripsi'=> $request->deskripsi_tindaklanjut,
                        'kondisi' => $request->kondisi_layanan_close,
                    ]);
                }

                // masukkan waktu mulai gangguan sebagai waktu mulai layanan dalam status
                // sesuai dengan status kondisi_layanan_open
                $laporan->update([
                        'waktu_layanan_open' => $gangguan->waktu,
                        'updated_by' => session()->get('id')
                    ]);
            }
            // akhir proses simpan ke DB jika jenis gangguan non peralatan

            // simpan transaksi ke database
            DB::commit();
        }
        
        // jika proses insert gagal
        catch(QueryException $ex){
            // batalkan semua transaksi ke database
            DB::rollBack();

            dd($ex->getMessage());
            // kembali ke halaman tambah step 2 dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.tambah.step2.form', $layanan->id)
                ->with('notif', 'tambah_gagal');
        }

        // jika ada tindaklanjut penggantian alat, lanjutkan ke form tambah step 3 (khusus jenis gangguan peralatan)
        if($jenis == config('constants.jenis_laporan.gangguan_peralatan')){
            // cek apakah ada penggantian
            $adaPenggantian = collect($request->peralatan)
                ->where('flag_gangguan', 1)
                ->contains(fn ($item) => (int) ($item['jenis_tindaklanjut'] ?? 0) === 2);
            // jika ada
            if($adaPenggantian){
                // lanjut ke halaman form tambah step 3 (input penggantian peralatan)
                return redirect()
                    ->route('logbook.laporan.tambah.step3.form', $laporan->id)
                    ->with('notif', 'draft_sukses');
            }
        }
        
        // selain dari itu, lanjut ke halaman form tambah step 4 (halaman review)
        return redirect()
            ->route('logbook.laporan.tambah.step4.form', $laporan->id)
            ->with('notif', 'draft_sukses');
    }


    /**
     * Function untuk menampilkan form tambah step 3 (form Input Penggantian Alat).
     * Form ini hanya akan muncul apabila memilih tindak lanjut penggantian.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step3/{laporan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep3($laporan_id)
    {
        // ambil laporan dengan status DRAFT berdasarkan id
        $laporan = Laporan::with(['gangguanPeralatan','tlGangguanPeralatan', 'tlPenggantianPeralatan'])
            ->where('id', $laporan_id)
            ->where('status', config('constants.status_laporan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $laporan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data layanan dengan status aktif dengan kondisi serviceable
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->where('id', $laporan->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->whereIn('kondisi', [config('constants.kondisi_layanan.serviceable')])
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // cek apakah ada penggantian peralatan
        $penggantian = $laporan->tlGangguanPeralatan
            ->contains('jenis', 2);

        // jika tidak ada
        if(! $penggantian){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil daftar peralatan yang dilakukan penggantian saja
        $daftarPeralatan = TlGangguanPeralatan::with(['tlPenggantianPeralatan'])
            ->where('laporan_id', $laporan->id)
            ->where('jenis', config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))
            ->get();

        // buat variabel
        $jenis_tindaklanjut = config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian');
        // ambil daftar perusahaan yang aktif
        $perusahaan = Perusahaan::where('status', 1)->get();
        // ambil daftar jenis alat yang aktif
        $jenis = JenisAlat::where('status', 1)->get();

        // Data tambahan untuk view
        $judul = "Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = route('logbook.laporan.daftar');
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step3')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('laporan', $laporan)
            ->with('jenis_tindaklanjut', $jenis_tindaklanjut)
            ->with('perusahaan', $perusahaan)
            ->with('jenis', $jenis)
            ->with('daftarPeralatan', $daftarPeralatan)
            ;
    }


    /**
     * Function untuk menampilkan form tambah step 4 (form Review data laporan yang sudah di-input).
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step4/{laporan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep4($laporan_id)
    {
        // ambil laporan dengan status DRAFT berdasarkan id
        $laporan = Laporan::with([
            'gangguanPeralatan',
            'tlGangguanPeralatan', 
            'tlPenggantianPeralatan',
            'gangguanNonPeralatan',
            'tlGangguanNonPeralatan'])
            ->where('id', $laporan_id)
            ->where('status', config('constants.status_laporan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $laporan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data layanan dengan status aktif dengan kondisi serviceable
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->where('id', $laporan->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->whereIn('kondisi', [config('constants.kondisi_layanan.serviceable')])
            ->first();

        //dd($layanan->daftarPeralatanLayanan());

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // buat variable awal
        $jenis_tindaklanjut = null;

        // cek jenis gangguan
        // apabila jenis gangguan = Gangguan Peralatan
        if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan')){

            // cek apakah ada tindaklanjut penggantian alat
            $penggantian = $laporan->tlGangguanPeralatan
                ->contains('jenis', 2);

            // jika ada penggantian alat
            if($penggantian){
                // cek apakah peralatan pengganti sudah diisi
                $sudahGantiAlat = $laporan->tlPenggantianPeralatan->isNotEmpty();
                // jika belum
                if(! $sudahGantiAlat){
                    // kembali ke halaman form tambah step 3 dan tampilkan pesan error
                    return redirect()
                        ->route('logbook.laporan.tambah.step3', ['laporan_id' => $laporan->id])
                        ->with('notif', 'ganti_null');
                }
                // isi variabel jenis tindaklanjut
                $jenis_tindaklanjut = config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian');
            }
        }

        // Data tambahan untuk view
        $judul = "Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = route('logbook.laporan.daftar');
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step4')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('laporan', $laporan)
            ->with('jenis_tindaklanjut', $jenis_tindaklanjut)
            ;
    }


    /**
     * Function untuk menampilkan form tambah layanan step 2 melalui tombol Back.
     * Status laporan masih berstatus Draft.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/laporan/tambah/step2/back{laporan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function formTambahStep2Back(Request $request, $laporan_id)
    {
        // ambil laporan dengan status DRAFT berdasarkan id
        $laporan = Laporan::with([
            'gangguanPeralatan',
            'tlGangguanPeralatan', 
            'tlPenggantianPeralatan',
            'gangguanNonPeralatan',
            'tlGangguanNonPeralatan'])
            ->where('id', $laporan_id)
            ->where('status', config('constants.status_laporan.draft'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $laporan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data layanan dengan status aktif dengan kondisi serviceable
        $layanan = Layanan::with(['daftarPeralatanLayanan.peralatan'])
            ->where('id', $laporan->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->first();

        //dd($layanan->daftarPeralatanLayanan());

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // jika layanan dalam kondisi unserviceable
        if($layanan->kondisi == config('constants.kondisi_layanan.unserviceable')){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'layanan_unserviceable');
        }

        // buat variable awal
        $jenis_tindaklanjut = null;

        // cek jenis gangguan
        // apabila jenis gangguan = Gangguan Peralatan
        if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan')){

            // cek apakah ada penggantian peralatan
            $penggantian = $laporan->tlGangguanPeralatan
                ->contains('jenis', 2);

            // jika ada penggantian alat
            if($penggantian){
                // isi variabel jenis tindaklanjut
                $jenis_tindaklanjut = config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian');
            }
        }

        // ambil daftar peralatan dari layanan tersebut
        $daftar_peralatan = DaftarPeralatanLayanan::where('layanan_id', $layanan->id)
            ->get();

        // Data tambahan untuk view
        $judul = "Laporan";
        $module = "Logbook";
        $menu = "Laporan";
        $menu_url = route('logbook.laporan.daftar');
        $submenu = "Tambah";

        // menampilkan halaman view
        return view('logbook.laporan.tambah.step2_back')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('layanan', $layanan)
            ->with('laporan', $laporan)
            ->with('daftar_peralatan', $daftar_peralatan)
            ->with('jenis_tindaklanjut', $jenis_tindaklanjut)
            ;
    }

    /**
     * Function untuk memproses filter daftar peralatan tersedia.
     * Function ini digunakan pada saat proses penggantian peralatan 
     * pada saat tambah laporan step 3 (form penggantian peralatan).
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /fasilitas/laporan/peralatan/filter
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
                ->whereIn('kondisi', [config('constants.kondisi_peralatan.normal'), // kondisi peralatan normal
                                    config('constants.kondisi_peralatan.normal_sebagian')]) // kondisi peralatan normal sebagian
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
        return view('logbook.laporan.modal_daftar_peralatan', compact('daftar_tersedia'));
    }


    /**
     * Function untuk memproses tambah peralatan ke halaman form penggantian.
     * Function ini diproses setelah mengklik tombol PILIH pada modal Daftar Peralatan Tersedia
     * di form penggantian peralatan.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step3/peralatan/tambah
     *
     * @param  layanan_id
     * @param  peralatan_id
     * @return \Illuminate\Http\Response
     */
    public function tambahStep3Peralatan(Request $request)
    {
        // cek apakah peralatan ada
        $peralatan = Peralatan::find($request->peralatan_baru_id);
        if (! $peralatan) {
           // kirim pesan gagal melalui JSON
           return response()->json(['success' => false, 'reason' => 'Peralatan tidak terdaftar'], 400);
        }

        // cek apakah status peralatan aktif
        $status = $peralatan->status;
        if (!$status || $status == 0) {
           // kirim pesan gagal melalui JSON
           return response()->json(['success' => false, 'reason' => 'Peralatan tidak aktif'], 400);
        }

        // mulai transaksi ke database
        DB::beginTransaction();
        
        try{
            // tambah row di tabel daftar peralatan layanan
            TlPenggantianPeralatan::create([
                'tl_gangguan_id' => $request->tl_gangguan_id,
                'laporan_id' => $request->laporan_id,
                'layanan_id' => $request->layanan_id,
                'peralatan_lama_id' => $request->peralatan_lama_id,
                'peralatan_baru_id' => $peralatan->id,
                'created_by' => session()->get('id')
            ]);

            // update flag_layanan menjadi 1, sebagai penanda bahwa peralatan sudah ditambahkan ke layanan
            Peralatan::where('id', $peralatan->id)
            ->update([
                'flag_layanan' => 1, // peralatan diberi tanda bahwa sedang terpasang di layanan
                'updated_by' => session()->get('id')
            ]);

            // simpan transaksi ke database
            DB::commit();
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
             // batalkan semua transaksi ke database
            DB::rollBack();
            // kirim pesan error ke file storage/logs/laravel.log
            // \Log::error('Gagal tambah peralatan ke layanan: '.$ex->getMessage());
            // kirim pesan gagal melalui JSON
            return response()->json(['success' => false, 'reason' => 'Gagal menyimpan peralatan'], 400);
        }

        return response()->json(['success' => true]);
    }

}
        