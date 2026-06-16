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

        // generate nomor laporan
        // $no_laporan = now()->format('YmdHis') . rand(100, 999);
        // ambil data dari form
        $jenis = $request->jenis;
        $peralatan = $request->peralatan;
        $kondisi_layanan_open = $request->kondisi_layanan_open;
        $kondisi_layanan_temp = $request->kondisi_layanan_tindaklanjut;

        //dd($request->all());

        // mulai transaksi ke database
        DB::beginTransaction();

        try{

            // insert data laporan baru ke tabel Laporan
            $laporan = Laporan::create([
                //'no_laporan' => $no_laporan,
                'layanan_id' => $layanan->id,
                'jenis' =>  $jenis,
                'status' => config('constants.status_laporan.draft'), // draft
                'kondisi_layanan_open' => $kondisi_layanan_open,
                'kondisi_layanan_temp' => $kondisi_layanan_temp, // ini kondisi terupdate, nilai ini nantinya akan diupdate ke field kondisi_layanan_close
                'created_by' => session()->get('id')
            ]);

            // jika jenis laporan = gangguan peralatan, maka insert data ke tabel Gangguan Peralatan
            if($jenis == config('constants.jenis_laporan.gangguan_peralatan')){

                // validasi input
                $request->validate([
                    'peralatan.*.peralatan_id' => 'exists:peralatan,id',
                ],[
                    // pesan error
                    'peralatan_id.*.peralatan_id.exists' => 'Peralatan tidak valid.',   
                ]);

                // cek dulu apakah minimal ada 1 peralatan yang mengalami gangguan
                if (!collect($peralatan)->pluck('flag_gangguan')->contains(1)) {
                    // jika tidak ada, kembalikan ke halaman tambah step 2 dan kirim pesan error
                    return back()
                        ->with(['notif', 'gangguan_null'])
                        ->withInput();
                }

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

                        // jika tindaklanjut ada diisi dan jenis tindaklanjut adalah PERBAIKAN
                        if(!empty($satu['jenis_tindaklanjut']) && 
                            ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan'))){
                                
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
                        // jika tindaklanjut ada diisi dan jenis tindaklanjut adalah PENGGANTIAN
                        else if(!empty($satu['jenis_tindaklanjut']) && 
                            ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))){
                            
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
                                'kondisi' => null,  // kondisi diisi saat proses penggantian peralatan dilakukan
                                'jenis' => $satu['jenis_tindaklanjut'],
                                'created_by' => session()->get('id')
                            ]);
                        }
                    }
                }
                // -------------------------------------------------------------------------
                //                             END OF LOOPING
                // -------------------------------------------------------------------------

                // lakukan pengisian data waktu layanan itu mulai terhitung DOWN di data laporan
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

            // dd($ex->getMessage());
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
                ->contains(fn ($item) => (int) ($item['jenis_tindaklanjut'] ?? 0) === config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'));
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
            'tlGangguanPeralatan' => function ($query) {
                    $query->orderBy('waktu_mulai', 'asc'); // menampilkan urutan waktu dari paling awal 
                },
            'tlPenggantianPeralatan',
            'gangguanNonPeralatan',
            'tlGangguanNonPeralatan' => function ($query) {
                    $query->orderBy('waktu_mulai', 'asc'); // menampilkan urutan waktu dari paling awal 
                },
            ])
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
     * Function untuk menampilkan form tambah laporan step 2 melalui tombol Back.
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
     * Function untuk memproses form tambah laporan step 2 melalui tombol Back.
     * Status laporan masih berstatus Draft.
     *
     * Akses:
     * - Super Admin
     * - Admin
     * - Teknisi
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step2/back{laporan_id}
     *
     * @return \Illuminate\Http\Response
     */
    public function tambahStep2Back(Request $request)
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
        
        // ambil data laporan berdasarkan ID laporan dari form
        $laporan = Laporan::where('id', $request->laporan_id)
            ->where('status', config('constants.status_laporan.draft'))
            ->first();
        
        // jika laporan dengan ID dan status tersebut tidak ada
        if(! $laporan){
            // kembali ke halaman daftar laporan dan tampilkan pesan error
            return redirect()
                    ->route('logbook.laporan.daftar')
                    ->with('notif', 'laporan_null');
        }

        // ambil data layanan aktif berdasarkan ID layanan dari form
        $layanan = Layanan::where('id', $request->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->first();

        // jika layanan dengan ID dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar laporan dan tampilkan pesan error
            return redirect()
                    ->route('logbook.laporan.daftar')
                    ->with('notif', 'layanan_null');
        }

        // ambil data dari form
        $jenis = $request->jenis;
        $peralatan = $request->peralatan;
        $kondisi_layanan_open = $request->kondisi_layanan_open;
        $kondisi_layanan_temp = $request->kondisi_layanan_tindaklanjut; // kondisi layanan terakhir
        // ambil data jenis laporan lama
        $jenisLama = $laporan->jenis;

        //dd($request->all());

        // mulai transaksi ke database
        DB::beginTransaction();

        try{

            // update data laporan ke DB
            $laporan->update([
                'jenis' =>  $jenis,
                'kondisi_layanan_open' => $kondisi_layanan_open,
                'kondisi_layanan_temp' => $kondisi_layanan_temp, // ini kondisi terupdate, nilai ini nantinya akan diupdate ke field kondisi_layanan_close
                'updated_by' => session()->get('id')
            ]);

            // -------------------------------------------------------------------------
            //                    JENIS LAPORAN = GANGGUAN PERALATAN
            // -------------------------------------------------------------------------
            // jika jenis laporan = gangguan peralatan, maka update data ke tabel Gangguan Peralatan
            if($jenis == config('constants.jenis_laporan.gangguan_peralatan')){

                // validasi input
                $request->validate([
                    'peralatan.*.peralatan_id' => 'exists:peralatan,id',
                ],[
                    // pesan error
                    //'peralatan_id.*.peralatan_id.exists' => 'Peralatan tidak valid.',   
                    'peralatan.*.peralatan_id.exists' => 'Peralatan tidak valid.',
                ]);

                //dd(collect($peralatan)->pluck('flag_gangguan'));

                // cek dulu apakah minimal ada 1 peralatan yang mengalami gangguan
                if (!collect($peralatan)->pluck('flag_gangguan')->contains('1')) {
                    // jika tidak ada, kembalikan ke halaman tambah step 2 back dan tampilkan pesan error
                    return redirect()->to(
                        url()->previous().'?notif=gangguan_null'
                    );
                }

                // ambil daftar gangguan peralatan dari laporan ini
                // jika TIDAK ADA maka menghasilkan NULL
                $daftarGangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                    ->where('layanan_id', $layanan->id)
                    ->get();

                // Looping untuk mengambil data dan menyimpannya ke tabel Gangguan Peralatan
                // -------------------------------------------------------------------------
                //              LOOPING INSERT/UPDATE DATA KE TABEL GANGGUAN PERALATAN
                // -------------------------------------------------------------------------
                foreach ($peralatan as $satu) {

                    // cek apakah peralatan itu sudah pernah di-input gangguan yang lama
                    // ambil data gangguan peralatan yang lama, jika tidak ada maka bernilai NULL
                    $dataGangguan = $daftarGangguan->firstWhere('peralatan_id', $satu['peralatan_id']);

                    // ambil data tindaklanjut yang lama, jika tidak ada maka bernilai NULL
                    $dataTlGangguan = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                        ->where('layanan_id', $layanan->id)
                        ->where('peralatan_id', $satu['peralatan_id'])
                        ->first();               

                    // jika ADA data gangguan yang lama
                    if($dataGangguan){

                        // dan jika peralatan ini diinputkan sebagai gangguan peralatan
                        if ($satu['flag_gangguan'] == 1) {
                            // lakukan update data gangguan yang lama
                            $dataGangguan->update([
                                'waktu' => Carbon::createFromFormat('d-m-Y H:i', $satu['waktu_gangguan'])->format('Y-m-d H:i'),
                                'deskripsi' => $satu['deskripsi_gangguan'],
                                'kondisi' => $satu['kondisi_gangguan'], // kondisi saat gangguan
                                'kondisi_awal' => $satu['kondisi_awal'], // kondisi sebelum gangguan, diambil dari kondisi di tabel Peralatan.
                                'updated_by' => session()->get('id')
                            ]);

                            // jika ADA data tindaklanjut yang lama, update data tindaklanjut
                            if($dataTlGangguan){

                                // ambil data jenis tindaklanjut yang lama
                                $jenisTindaklanjutLama = $dataTlGangguan->jenis;

                                // dan jika form tindaklanjut ada diisi dan jenis tindaklanjut = PERBAIKAN
                                // update data tindaklanjut yang lama
                                if (!empty($satu['jenis_tindaklanjut']) && 
                                    ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan'))){
                                    
                                    // update data tindaklanjut gangguan
                                    $dataTlGangguan->update([
                                        'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                        'kondisi' => $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['kondisi_tindaklanjut'],
                                        'jenis' => $satu['jenis_tindaklanjut'],
                                        'updated_by' => session()->get('id')
                                    ]);
                                    
                                    // jika ada perubahan jenis tindaklanjut dari PENGGANTIAN ke PERBAIKAN
                                    // hapus data penggantian peralatan sebelumnya
                                    if(
                                        $jenisTindaklanjutLama == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian') && 
                                        $satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan')
                                        ){

                                        // ambil data penggantian peralatan di table tl_penggantian_peralatan yang sebelumnya dibuat
                                        $dataPenggantian = TlPenggantianPeralatan::where('tl_gangguan_id', $dataTlGangguan->id)
                                            ->first();
                                        
                                        // jika ada, hapus data panggantian peralatan
                                        if($dataPenggantian){
                                            // update flag_layanan menjadi 0, sebagai penanda bahwa peralatan sudah dihapus sebagai peralatan pengganti
                                            Peralatan::where('id', $dataPenggantian->peralatan_baru_id)
                                            ->update([
                                                'flag_layanan' => 0, // peralatan diberi tanda bahwa sudah tidak terpasang di layanan
                                                'updated_by' => session()->get('id')
                                            ]);

                                            // hapus data penggantian alat
                                            $dataPenggantian->delete();
                                        }
                                        
                                        
                                    }
                                }
                                // dan jika form tindaklanjut ADA diisi dan jenis tindaklanjut = PENGGANTIAN
                                // update data tindaklanjut yang lama
                                else if(!empty($satu['jenis_tindaklanjut']) && 
                                    ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))){
                                    
                                    // update data tindaklanjut gangguan
                                    $dataTlGangguan->update([
                                        'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                        'kondisi' => null, // kondisi diisi pada saat proses penggantian alat di step 3
                                        'jenis' => $satu['jenis_tindaklanjut'],
                                        'updated_by' => session()->get('id')
                                    ]);
                                }
                                // jika form tindaklanjut tidak diisi, hapus data tindaklanjut yang lama
                                else{
                                    // jika jenis tindaklanjut yang lama adalah PENGGANTIAN
                                    if($dataTlGangguan->jenis == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian')){
                                        
                                        // ambil data penggantian peralatan di table tl_penggantian_peralatan yang lama
                                        $dataPenggantian = TlPenggantianPeralatan::where('tl_gangguan_id', $dataTlGangguan->id)
                                            ->first();
                                        
                                        // jika ada, hapus data penggantian peralatannya
                                        if($dataPenggantian){
                                            // update flag_layanan menjadi 0, sebagai penanda bahwa peralatan sudah dihapus sebagai peralatan pengganti
                                            Peralatan::where('id', $dataPenggantian->peralatan_baru_id)
                                            ->update([
                                                'flag_layanan' => 0, // peralatan diberi tanda bahwa sudah tidak terpasang di layanan
                                                'updated_by' => session()->get('id')
                                            ]);

                                            // hapus data penggnatian yang lama
                                            $dataPenggantian->delete();
                                        }
                                    }
                                    // kemudian lakukan penghapusan data tindaklanjut yang lama
                                    $dataTlGangguan->delete();
                                }    
                            }

                            // jika TIDAK ADA data tindaklanjut yang lama
                            else{
                                // dan jika form tindaklanjut ada diisi, create data tindaklanjut baru
                                if (
                                    !empty($satu['jenis_tindaklanjut']) && 
                                    ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan')
                                    || $satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))
                                    ){

                                    // create data ke table Tindaklanjut Gangguan Peralatan
                                    $tl_gangguan = TlGangguanPeralatan::create([
                                        'gangguan_id' => $dataGangguan->id,
                                        'laporan_id' => $laporan->id,
                                        'layanan_id' => $layanan->id,
                                        'peralatan_id' => $satu['peralatan_id'],
                                        'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                            ->format('Y-m-d H:i'),
                                        'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                        'kondisi' => $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['kondisi_tindaklanjut'],
                                        'jenis' => $satu['jenis_tindaklanjut'],
                                        'created_by' => session()->get('id')
                                    ]);    
                                }
                            }
                        }

                        // jika peralatan ini tidak diinputkan sebagai gangguan peralatan, namun ada data gangguannya yang lama,
                        // maka hapus data gangguannya yang lama tsb
                        else if($satu['flag_gangguan'] == 0){
                            // jika jenis tindaklanjut yang lama adalah PENGGANTIAN
                            if($dataTlGangguan->jenis == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian')){
                                
                                // ambil data penggantian peralatan di table tl_penggantian_peralatan yang lama
                                $dataPenggantian = TlPenggantianPeralatan::where('tl_gangguan_id', $dataTlGangguan->id)
                                    ->first();
                                
                                // jika ada, hapus data panggantian peralatan
                                if($dataPenggantian){
                                    // update flag_layanan menjadi 0, sebagai penanda bahwa peralatan sudah dihapus sebagai peralatan pengganti
                                    Peralatan::where('id', $dataPenggantian->peralatan_baru_id)
                                    ->update([
                                        'flag_layanan' => 0, // peralatan diberi tanda bahwa sudah tidak terpasang di layanan
                                        'updated_by' => session()->get('id')
                                    ]);

                                    // hapus data penggantian alat
                                    $dataPenggantian->delete();
                                }
                            }

                            // kemudian lakukan penghapusan data tindaklanjut yang lama
                            $dataTlGangguan->delete();
                            // dan lakukan penghapusan data gangguan yang lama
                            $dataGangguan->delete();
                        }
                    }

                    // jika TIDAK ADA data gangguan yang lama
                    else{
                        // dan jika peralatan ini diinputkan sebagai gangguan peralatan
                        if($satu['flag_gangguan'] == 1){
                            // create data gangguan baru ke DB
                            $gangguanBaru = GangguanPeralatan::create([
                                'laporan_id' => $laporan->id,
                                'layanan_id' => $layanan->id,
                                'peralatan_id' => $satu['peralatan_id'],
                                'waktu' => Carbon::createFromFormat('d-m-Y H:i', $satu['waktu_gangguan'])->format('Y-m-d H:i'),
                                'deskripsi' => $satu['deskripsi_gangguan'],
                                'kondisi' => $satu['kondisi_gangguan'], // kondisi saat gangguan
                                'kondisi_awal' => $satu['kondisi_awal'], // kondisi sebelum gangguan, diambil dari kondisi di tabel Peralatan.
                                'created_by' => session()->get('id')
                            ]);

                            // jika form tindaklanjutnya ada diisi dan jenis tindaklanjut = PERBAIKAN, create data tindaklanjut baru ke DB
                            if(!empty($satu['jenis_tindaklanjut']) && 
                                ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan'))){

                                // create data ke table Tindaklanjut Gangguan Peralatan
                                $tl_gangguan = TlGangguanPeralatan::create([
                                    'gangguan_id' => $gangguanBaru->id,
                                    'laporan_id' => $laporan->id,
                                    'layanan_id' => $layanan->id,
                                    'peralatan_id' => $satu['peralatan_id'],
                                    'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                        ->format('Y-m-d H:i'),
                                    'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                        ->format('Y-m-d H:i'),
                                    'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                    'kondisi' => $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['kondisi_tindaklanjut'],
                                    'jenis' => $satu['jenis_tindaklanjut'],
                                    'created_by' => session()->get('id')
                                ]);
                            }
                            // jika form tindalanjut ada diisi dan jenis tindaklanjut = PENGGANTIAN
                            else if(!empty($satu['jenis_tindaklanjut']) && 
                                ($satu['jenis_tindaklanjut'] == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))){
                                
                                // create data ke table Tindaklanjut Gangguan Peralatan
                                $tl_gangguan = TlGangguanPeralatan::create([
                                    'gangguan_id' => $gangguanBaru->id,
                                    'laporan_id' => $laporan->id,
                                    'layanan_id' => $layanan->id,
                                    'peralatan_id' => $satu['peralatan_id'],
                                    'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_mulai_tindaklanjut'])
                                        ->format('Y-m-d H:i'),
                                    'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['waktu_selesai_tindaklanjut'])
                                        ->format('Y-m-d H:i'),
                                    'deskripsi'=> $satu['tindaklanjut'][$satu['jenis_tindaklanjut']]['deskripsi_tindaklanjut'],
                                    'kondisi' => null, // kondisi diisi pada saat proses penggantian alat di step 3
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

                // jika jenis laporan yang lama adalah GANGGUAN NON PERALATAN,
                // maka hapus data gangguan non peralatan yang lama
                if(($jenis == config('constants.jenis_laporan.gangguan_peralatan')) && 
                    ($jenisLama == config('constants.jenis_laporan.gangguan_non_peralatan'))){
                    // hapus data tindaklanjut gangguan non peralatannya
                    $laporan->tlGangguanNonPeralatan()->delete();
                    // hapus data gangguan non peralatannya
                    $laporan->gangguanNonPeralatan()->delete();
                }

                // lakukan pengisian data waktu layanan itu mulai terhitung DOWN di data laporan
                // ambil waktu terawal dari daftar gangguan peralatan
                $waktuAwalGangguan = GangguanPeralatan::where('laporan_id', $laporan->id)
                    ->min('waktu');
                // masukkan ke variable waktu_layanan_open di tabel Laporan, sebagai waktu awal layanan gangguan
                $laporan->update([
                        'waktu_layanan_open' => $waktuAwalGangguan,
                        'updated_by' => session()->get('id')
                    ]);
            }
            // akhir proses simpan ke DB jika jenis gangguan peralatan

            // -------------------------------------------------------------------------
            //                   JENIS LAPORAN = GANGGUAN NON PERALATAN
            // -------------------------------------------------------------------------
            // jika jenis laporan = gangguan non peralatan, maka insert data ke tabel Gangguan Non Peralatan
            else if($jenis == config('constants.jenis_laporan.gangguan_non_peralatan')){

                // ambil data dari form gangguan
                // ubah format waktu ke format yang bisa disimpan oleh DB
                $waktu_gangguan = Carbon::createFromFormat('d-m-Y H:i', $request->waktu_gangguan)->format('Y-m-d H:i');

                // ambil data flag tindaklanjut dari form gangguan
                $flag_tindaklanjut = $request->flag_tindaklanjut;

                // ambil data gangguan non peralatan yang lama
                $dataGangguan = GangguanNonPeralatan::where('laporan_id', $laporan->id)
                    ->where('layanan_id', $layanan->id)
                    ->first();

                // jika ADA data gangguan non peralatan yang lama, update data gangguan yang lama
                if($dataGangguan){
                    // ambil data tindaklanjut gangguan peralatan yang lama
                    $dataTlGangguan = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                        ->where('layanan_id', $layanan->id)
                        ->where('gangguan_id', $dataGangguan->id)
                        ->first();

                    // lakukan update data gangguan non peralatan
                    $dataGangguan->update([
                        'waktu' => $waktu_gangguan,
                        'deskripsi'=> $request->deskripsi_gangguan,
                        'updated_by' => session()->get('id')
                    ]);

                    // jika ADA data tindaklanjut yang lama
                    if($dataTlGangguan){
                        // jika form tindaklanjut ada diisi
                        if(!empty($flag_tindaklanjut) && $flag_tindaklanjut == 1){
                            // update data tindaklanjut yang lama
                            $dataTlGangguan->update([
                                'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_mulai_tindaklanjut)->format('Y-m-d H:i'),
                                'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_selesai_tindaklanjut)->format('Y-m-d H:i'),
                                'deskripsi'=> $request->deskripsi_tindaklanjut,
                                'kondisi' => $request->kondisi_layanan_tindaklanjut,
                                'updated_by' => session()->get('id')
                            ]);
                        }
                        // jika form tindaklanjut tidak diisi
                        else{
                            // hapus data tindaklanjut yang lama
                            $dataTlGangguan->delete();
                        }
                    }

                    // jika TIDAK ADA data tindaklanjut yang lama
                    else{
                        // jika form tindaklanjut ada diisi
                        if(!empty($flag_tindaklanjut) && $flag_tindaklanjut == 1){
                            // create data tindaklanjut ke table tl_gangguan_peralatan
                            $tlGangguanBaru = TlGangguanNonPeralatan::create([
                                'gangguan_id' => $dataGangguan->id,
                                'laporan_id' => $laporan->id,
                                'layanan_id' => $layanan->id,
                                'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_mulai_tindaklanjut)->format('Y-m-d H:i'),
                                'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_selesai_tindaklanjut)->format('Y-m-d H:i'),
                                'deskripsi'=> $request->deskripsi_tindaklanjut,
                                'kondisi' => $request->kondisi_layanan_tindaklanjut,
                                'created_by' => session()->get('id')
                            ]);
                        }
                    }
                }
                // jika TIDAK ADA data gangguan non peralatan yang lama, create data gangguan baru
                else{
                    // create data baru ke tabel gangguan_non_peralatan
                    $gangguanBaru = GangguanNonPeralatan::create([
                        'laporan_id' => $laporan->id,
                        'layanan_id' => $layanan->id,
                        'waktu' => $waktu_gangguan,
                        'deskripsi'=> $request->deskripsi_gangguan,
                        'created_by' => session()->get('id')
                    ]);

                    // jika form tindaklanjut ada diisi
                    if(!empty($flag_tindaklanjut) && $flag_tindaklanjut == 1){
                        // create data baru ke table tl_gangguan_non_peralatan
                        $tlGangguanBaru = TlGangguanNonPeralatan::create([
                            'gangguan_id' => $gangguan->id,
                            'laporan_id' => $laporan->id,
                            'layanan_id' => $layanan->id,
                            'waktu_mulai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_mulai_tindaklanjut)->format('Y-m-d H:i'),
                            'waktu_selesai' => Carbon::createFromFormat('d-m-Y H:i', $request->waktu_selesai_tindaklanjut)->format('Y-m-d H:i'),
                            'deskripsi'=> $request->deskripsi_tindaklanjut,
                            'kondisi' => $request->kondisi_layanan_close,
                        ]);
                    }
                }

                // jika jenis laporan yang lama adalah GANGGUAN PERALATAN,
                // maka hapus data gangguan peralatan yang lama
                if(($jenis == config('constants.jenis_laporan.gangguan_non_peralatan')) && 
                    ($jenisLama == config('constants.jenis_laporan.gangguan_peralatan'))){
                    // ambil data tindaklanjut penggantian yang lama
                    // ambil semua data peralatan pengganti beserta relasi peralatan baru
                    $dataPenggantian = $laporan->tlPenggantianPeralatan()
                        ->with('peralatanBaru')
                        ->get();

                    // looping data penggantian, update status flag_layanan menjadi 0 jika ada data peralatan baru
                    foreach ($dataPenggantian as $satu) {
                        // jika ada peralatan baru
                        if ($satu->peralatanBaru) {
                            // update flag_layanan menjadi 0, sebagai penanda bahwa peralatan sudah dihapus sebagai peralatan pengganti
                            $satu->peralatanBaru->update([
                                'flag_layanan' => 0, // peralatan diberi tanda bahwa sudah tidak terpasang di layanan
                                'updated_by' => session()->get('id')
                            ]);
                        }
                    }

                    // hapus data penggantian peralatannya
                    $laporan->tlPenggantianPeralatan()->delete();
                    // hapus data tindaklanjut gangguan peralatannya
                    $laporan->tlGangguanPeralatan()->delete();
                    // hapus data gangguan non peralatannya
                    $laporan->gangguanPeralatan()->delete();
                }

                // lakukan pengisian data waktu layanan itu mulai terhitung DOWN di data laporan
                // ambil waktu gangguan dari form gangguan peralatan
                $laporan->update([
                        'waktu_layanan_open' => $waktu_gangguan,
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
            // dd($ex->getMessage());
            // kembali ke halaman tambah step 2 back dan tampilkan pesan error
            return redirect()->to(
                url()->previous().'?notif=tambah_gagal'
            );
        }

        // jika ada tindaklanjut penggantian alat, lanjutkan ke form tambah step 3 (khusus jenis gangguan peralatan)
        if($jenis == config('constants.jenis_laporan.gangguan_peralatan')){
            // cek apakah jenis tindaklanjut = PENGGANTIAN
            $adaPenggantian = collect($request->peralatan)
                ->where('flag_gangguan', 1)
                ->contains(fn ($item) => (int) ($item['jenis_tindaklanjut'] ?? 0) === config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'));
            // jika ada, lanjut ke halaman form tambah step 3 (input penggantian peralatan)
            if($adaPenggantian){
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
    public function filterPeralatan(Request $request)
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
    public function tambahPeralatan(Request $request)
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

            // update data kondisi di tabel tl_ganggauan_peralatan
            TlGangguanPeralatan::where('id', $request->tl_gangguan_id)
            ->update([
                'kondisi' => $peralatan->kondisi,
                'updated_by' => session()->get('id')
            ]);

            // simpan transaksi ke database
            DB::commit();
        }
        // jika proses tambah gagal
        catch(QueryException $ex){
             // batalkan semua transaksi ke database
            DB::rollBack();
            //dd($ex->getMessage());
            // kirim pesan gagal melalui JSON, kode 400 agar masuk ke function .fail di AJAX
            return response()->json(['success' => false, 'reason' => 'Gagal menyimpan peralatan'], 400);
        }
        // kirim pesan berhasil dan masuk ke function .done di AJAX
        return response()->json(['success' => true]);
    }


    /**
     * Function untuk menghapus peralatan pengganti di form penggantian.
     * 
     * Akses:
     * - Super Admin
     * - Admin
     * 
     * Method: POST
     * URL: /logbook/laporan/tambah/step3/peralatan/hapus
     *
     * @param  peralatan_id
     * @param  layanan_id
     * @return \Illuminate\Http\Response
     */
    public function hapusPeralatan(Request $request)
    {
        // ambil data gangguan peralatan berdasarkan ID
        $gangguan = GangguanPeralatan::where('id', $request->gangguan_id)
            ->where('peralatan_id', $request->peralatan_lama_id)
            ->first();
        
        // jika gangguan tidak ada
        if(! $gangguan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'gangguan_null');
        }

        // pastikan ID laporan sama
        if(! ($gangguan->laporan_id == $request->laporan_id)){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil laporan dengan status OPEN atau DRAFT berdasarkan ID
        $laporan = Laporan::where('id', $request->laporan_id)
            ->whereIn('status', [
                config('constants.status_laporan.draft'),
                config('constants.status_laporan.open')
            ])
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $laporan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data layanan dengan status aktif
        $layanan = Layanan::where('id', $laporan->layanan_id)
            ->where('status', config('constants.status_layanan.aktif'))
            ->first();

        // jika layanan dengan id dan status tersebut tidak ada
        if(! $layanan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'layanan_null');
        }

        // ambil data tindaklanjut gangguan
        $tlGangguan = TlGangguanPeralatan::where('id', $request->tl_gangguan_id)
            ->where('gangguan_id', $gangguan->id)
            ->where('laporan_id', $laporan->id)
            ->where('layanan_id', $layanan->id)
            ->where('peralatan_id', $request->peralatan_lama_id)
            ->first();
        
        // jika tindaklanjut tidak ada
        if(! $tlGangguan){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // pastikan apakah jenis tindaklanjut gangguan tsb merupakan PENGGANTIAN
        if(! ($tlGangguan->jenis == config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data penggantian peralatan
        $tlPenggantian = TlPenggantianPeralatan::where('id', $request->tl_penggantian_id)
            ->where('laporan_id', $laporan->id)
            ->where('layanan_id', $layanan->id)
            //->where('tl_gangguan_id', $tlGangguan->id)
            //->where('peralatan_lama_id', $request->peralatan_lama_id)
            //->where('peralatan_baru_id', $request->peralatan_baru_id)
            ->first();

        // jka tidak ada data
        if(! $tlPenggantian){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'laporan_null');
        }

        // ambil data peralatan yang akan dihapus
        $peralatanHapus = Peralatan::where('id', $tlPenggantian->peralatan_baru_id)
            ->first();

        // jika peralatan tsb tidak ada
        if(! $peralatanHapus){
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()
                ->route('logbook.laporan.daftar')
                ->with('notif', 'peralatan_null');
        }

        // mulai transaksi ke database
        DB::beginTransaction();

        try{
            // hapus data penggantian peralatan
            $tlPenggantian->delete();

            // update flag_layanan menjadi 0, sebagai penanda bahwa peralatan sudah dihapus dari layanan
            $peralatanHapus->update([
                'flag_layanan' => 0, // peralatan diberi tanda bahwa sedang tidak terpasang di layanan
                'updated_by' => session()->get('id')
            ]);

            // update data kondisi di tabel tl_gangguan_peralatan
            $tlGangguan->update([
                'kondisi' => null, // status 
                'updated_by' => session()->get('id')
            ]);

            // simpan transaksi ke database
            DB::commit();
        }
        // jika proses update gagal
        catch(QueryException $ex){
            // batalkan semua transaksi ke database
            DB::rollBack();
            //dd($ex->getMessage());
            // kembali ke halaman daftar dan tampilkan pesan error
            return redirect()->back()->with('notif', 'hapus_gagal');
        }

        // jika proses update berhasil
        // kembali ke halaman daftar dan tampilkan pesan sukses
        return redirect()->back()->with('notif', 'hapus_sukses');
    }


}
        