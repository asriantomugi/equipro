<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Fasilitas;
use App\Models\Layanan;
use App\Models\LokasiTk1;
use App\Models\LokasiTk2;
use App\Models\LokasiTk3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DashboardFasilitasController extends Controller
{
    private function calculateMetrics($layanan_collection, $tanggal_mulai = null, $tanggal_selesai = null)
{
    $layanan_with_metrics = collect();
    
    foreach ($layanan_collection as $layanan) {
        // Set default period (30 hari jika tidak ada filter tanggal)
        if ($tanggal_mulai && $tanggal_selesai) {
            $start_date = Carbon::parse($tanggal_mulai);
            $end_date = Carbon::parse($tanggal_selesai)->endOfDay();
            $period_hours = $start_date->diffInHours($end_date);
        } else {
            // Default 30 hari = 720 jam
            $period_hours = 720; // 30 hari × 24 jam
            $end_date = Carbon::now();
            $start_date = Carbon::now()->subDays(30);
        }
        
        // Hitung data real berdasarkan kondisi layanan
        $metrics = $this->calculateRealMetrics($layanan->id, $start_date, $end_date, $period_hours);
        
        // Tambahkan metrics ke object layanan
        $layanan->total_waktu_serviceable = $metrics['total_waktu_serviceable']; // dalam jam
        $layanan->total_waktu_perbaikan = $metrics['total_waktu_perbaikan']; // dalam jam
        $layanan->total_perbaikan = $metrics['total_perbaikan']; // berapa kali
        $layanan->total_unserviceable = $metrics['total_unserviceable']; // berapa kali
        $layanan->period_hours = $period_hours;
        
        // RUMUS 1: Indikator Performa (Availability)
        $base_hours = ($tanggal_mulai && $tanggal_selesai) ? $period_hours : 720;
        $layanan->availability_percentage = $base_hours > 0 
            ? round(($metrics['total_waktu_serviceable'] / $base_hours) * 100, 2) 
            : 0;
        
        // RUMUS 2: Mean Time To Repair (MTTR)
        $layanan->mttr = $metrics['total_perbaikan'] > 0 
            ? round($metrics['total_waktu_perbaikan'] / $metrics['total_perbaikan'], 2) 
            : 0; // dalam jam
        
        // RUMUS 3: Mean Time Between Failure (MTBF)  
        $layanan->mtbf = $metrics['total_unserviceable'] > 0 
            ? round($metrics['total_waktu_serviceable'] / $metrics['total_unserviceable'], 2) 
            : ($metrics['total_waktu_serviceable'] > 0 ? $metrics['total_waktu_serviceable'] : 0); // dalam jam
        
        $layanan_with_metrics->push($layanan);
    }
    
    return $layanan_with_metrics;
}

private function calculateRealMetrics($layanan_id, $start_date, $end_date, $period_hours)
{
    $layanan = Layanan::find($layanan_id);
    
    if (!$layanan) {
        return [
            'total_waktu_serviceable' => 0,
            'total_waktu_perbaikan' => 0,
            'total_perbaikan' => 0,
            'total_unserviceable' => 0
        ];
    }

    // Hitung total waktu perbaikan dan total unserviceable
    $total_waktu_perbaikan = 0;
    $total_perbaikan = 0;
    $total_unserviceable = 0;

    // Ambil data dari histori_gangguan_layanan
    $gangguan = DB::table('histori_gangguan_layanan')
        ->where('layanan_id', $layanan_id)
        ->whereBetween('waktu_unserv', [$start_date, $end_date])
        ->get();

    foreach ($gangguan as $g) {
        if ($g->waktu_serv) {
            // Hitung durasi perbaikan (waktu_unserv -> waktu_serv)
            $total_waktu_perbaikan += Carbon::parse($g->waktu_unserv)
                ->diffInHours(Carbon::parse($g->waktu_serv)); // dalam jam
            $total_perbaikan++;
        }
        $total_unserviceable++;
    }

    // Hitung total waktu serviceable
    $total_waktu_serviceable = max(0, $period_hours - $total_waktu_perbaikan);

    return [
        'total_waktu_serviceable' => $total_waktu_serviceable,
        'total_waktu_perbaikan' => $total_waktu_perbaikan,
        'total_perbaikan' => $total_perbaikan,
        'total_unserviceable' => $total_unserviceable
    ];
}

    public function dashboarddaftarIndex(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // Ambil parameter dari URL
        $fasilitas_id = $request->get('fasilitas');
        $status_filter = $request->get('status');
        
        // ===================== PENGAMBILAN DATA UNTUK FILTER =======================
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        // Ambil data fasilitas yang dipilih (jika ada)
        $fasilitas_selected = null;
        if ($fasilitas_id) {
            $fasilitas_selected = Fasilitas::find($fasilitas_id);
            if (!$fasilitas_selected) {
                return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas tidak ditemukan');
            }
        }
        
        $daftar = collect();
        
        // Cek apakah ini adalah request hasil dari form filter (ada tanggal)
        $has_date_filter = $request->has('tanggal_mulai') && $request->has('tanggal_selesai') && 
                          $request->get('tanggal_mulai') && $request->get('tanggal_selesai');
        
        if ($fasilitas_id && $has_date_filter) {
            // Query dasar untuk layanan
            $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3']);
            
            // Filter berdasarkan fasilitas
            $query->where('fasilitas_id', $fasilitas_id);
            
            $tanggal_mulai = $request->get('tanggal_mulai');
            $tanggal_selesai = $request->get('tanggal_selesai');
            
            // Filter berdasarkan tanggal - gunakan field yang sesuai dengan kebutuhan
            $query->whereBetween('created_at', [
                Carbon::parse($tanggal_mulai)->startOfDay(),
                Carbon::parse($tanggal_selesai)->endOfDay()
            ]);
            
            // Filter berdasarkan status jika ada
            if ($status_filter) {
                if ($status_filter == 'serviceable') {
                    $query->where('kondisi', config('constants.kondisi_layanan.serviceable'));
                } elseif ($status_filter == 'unserviceable') {
                    $query->where('kondisi', config('constants.kondisi_layanan.unserviceable'));
                }
            }
            
            // Eksekusi query
            $layanan_collection = $query->get();
            
            // Hitung metrics dengan data real
            if ($layanan_collection->count() > 0) {
                $daftar = $this->calculateMetrics(
                    $layanan_collection, 
                    $request->get('tanggal_mulai'), 
                    $request->get('tanggal_selesai')
                );
            }
        }
        
        $judul = "Fasilitas";
        $module = "Dashboard";
        $menu = "Layanan";
        $menu_url = route('dashboard.fasilitas');
            
        return view('dashboard.dashboarddaftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)    
            ->with('fasilitas', $fasilitas)
            ->with('fasilitas_selected', $fasilitas_selected)
            ->with('lokasi_tk_1', $lokasi_tk_1)
            ->with('daftar', $daftar)
            ->with('fasilitas_id', $fasilitas_id)
            ->with('lokasi_tk_1_id', null)
            ->with('lokasi_tk_2_id', null)
            ->with('lokasi_tk_3_id', null)
            ->with('kondisi', $status_filter)
            ->with('status_filter', $status_filter)
            ->with('tanggal_mulai', $request->get('tanggal_mulai'))
            ->with('tanggal_selesai', $request->get('tanggal_selesai'));
    }
    /**
     * Function untuk menampilkan dashboard daftar layanan per fasilitas
     */
    public function dashboarddaftar($fasilitas_id)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        if (!$fasilitas_id) {
            return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas harus dipilih');
        }

        $fasilitas_selected = Fasilitas::find($fasilitas_id);
        
        if (!$fasilitas_selected) {
            return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas tidak ditemukan');
        }

        // ===================== PENGAMBILAN DATA UNTUK FILTER =======================
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        $fasilitas_id_filter = $fasilitas_id;
        $lokasi_tk_1_id = null;
        $lokasi_tk_2_id = null;
        $lokasi_tk_3_id = null;
        $kondisi = null;
        
        $daftar = collect();
        
        $judul = "Fasilitas";
        $module = "Dashboard";
        $menu = "Fasilitas";
        $menu_url = route('dashboard.fasilitas');
            
        return view('dashboard.dashboarddaftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)    
            ->with('fasilitas', $fasilitas)
            ->with('fasilitas_selected', $fasilitas_selected)
            ->with('lokasi_tk_1', $lokasi_tk_1)
            ->with('daftar', $daftar)
            ->with('fasilitas_id', $fasilitas_id_filter)
            ->with('lokasi_tk_1_id', $lokasi_tk_1_id)
            ->with('lokasi_tk_2_id', $lokasi_tk_2_id)
            ->with('lokasi_tk_3_id', $lokasi_tk_3_id)
            ->with('kondisi', $kondisi)
            ->with('tanggal_mulai', null)
            ->with('tanggal_selesai', null);
    }
    
    /**
     * Function untuk menampilkan dashboard fasilitas
     */
    public function index(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        // ===================== PROSES PENGAMBILAN DATA FASILITAS =======================
        $fasilitas = Fasilitas::where('status', 1)->get();
        // ===================== AKHIR PROSES PENGAMBILAN DATA FASILITAS =======================

        // buat variabel untuk dikirim ke halaman view
        $judul = "Dashboard Fasilitas";
        $module = "Dashboard";
        $menu = "Fasilitas";
        $menu_url = route('dashboard.fasilitas');
            
        // alihkan ke halaman view dashboard
        return view('dashboard.fasilitas')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)    
            ->with('fasilitas', $fasilitas);
    }

    /**
     * Function untuk filter dashboard daftar layanan
     */
    public function dashboarddaftarFilter(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        if (session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')) {
            return redirect('/');
        }
        // ===================== AKHIR PROSES VERIFIKASI =======================

        $fasilitas_id = $request->input('fasilitas');
        $lokasi_tk_1_id = $request->input('lokasi_tk_1');
        $lokasi_tk_2_id = $request->input('lokasi_tk_2');
        $lokasi_tk_3_id = $request->input('lokasi_tk_3');
        $kondisi = $request->input('kondisi');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $status_filter = $request->input('status');

        // Validasi input yang diperlukan
        if (!$fasilitas_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Fasilitas harus dipilih');
        }

        if (!$tanggal_mulai || !$tanggal_selesai) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal mulai dan selesai harus diisi');
        }

        // Validasi tanggal
        if ($tanggal_mulai && $tanggal_selesai) {
            if (Carbon::parse($tanggal_selesai)->lt(Carbon::parse($tanggal_mulai))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
            }
        }

        // ===================== PENGAMBILAN DATA UNTUK FILTER =======================
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        $fasilitas_selected = null;
        if ($fasilitas_id) {
            $fasilitas_selected = Fasilitas::find($fasilitas_id);
        }
        
        $lokasi_tk_2 = collect();
        if ($lokasi_tk_1_id) {
            $lokasi_tk_2 = LokasiTk2::where('lokasi_tk_1_id', $lokasi_tk_1_id)
                                   ->where('status', 1)
                                   ->get();
        }
        
        $lokasi_tk_3 = collect();
        if ($lokasi_tk_2_id) {
            $lokasi_tk_3 = LokasiTk3::where('lokasi_tk_2_id', $lokasi_tk_2_id)
                                   ->where('status', 1)
                                   ->get();
        }
        
        // ===================== PENGAMBILAN DATA LAYANAN DENGAN FILTER =======================
        $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3']);
        
        if ($fasilitas_id) {
            $query->where('fasilitas_id', $fasilitas_id);
        }
        
        if ($tanggal_mulai && $tanggal_selesai) {
            $query->whereBetween('created_at', [
                Carbon::parse($tanggal_mulai)->startOfDay(),
                Carbon::parse($tanggal_selesai)->endOfDay()
            ]);
        }
        
        if ($lokasi_tk_1_id) {
            $query->where('lokasi_tk_1_id', $lokasi_tk_1_id);
        }
        
        if ($lokasi_tk_2_id) {
            $query->where('lokasi_tk_2_id', $lokasi_tk_2_id);
        }
        
        if ($lokasi_tk_3_id) {
            $query->where('lokasi_tk_3_id', $lokasi_tk_3_id);
        }
        
        if ($kondisi !== null && $kondisi !== '') {
            $query->where('kondisi', $kondisi);
        }
        
        if ($status_filter) {
            if ($status_filter == 'serviceable') {
                $query->where('kondisi', config('constants.kondisi_layanan.serviceable'));
            } elseif ($status_filter == 'unserviceable') {
                $query->where('kondisi', config('constants.kondisi_layanan.unserviceable'));
            }
        }
        
        $layanan_collection = $query->get();

        // Hitung metrics dengan rumus yang benar
        $daftar = $this->calculateMetrics($layanan_collection, $tanggal_mulai, $tanggal_selesai);

        $judul = "Fasilitas";
        $module = "Dashboard";
        $menu = "Fasilitas";
        $menu_url = route('dashboard.fasilitas');
            
        return view('dashboard.dashboarddaftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)    
            ->with('fasilitas', $fasilitas)
            ->with('fasilitas_selected', $fasilitas_selected)
            ->with('lokasi_tk_1', $lokasi_tk_1)
            ->with('lokasi_tk_2', $lokasi_tk_2)
            ->with('lokasi_tk_3', $lokasi_tk_3)
            ->with('daftar', $daftar)
            ->with('fasilitas_id', $fasilitas_id)
            ->with('lokasi_tk_1_id', $lokasi_tk_1_id)
            ->with('lokasi_tk_2_id', $lokasi_tk_2_id)
            ->with('lokasi_tk_3_id', $lokasi_tk_3_id)
            ->with('kondisi', $kondisi)
            ->with('tanggal_mulai', $tanggal_mulai)
            ->with('tanggal_selesai', $tanggal_selesai)
            ->with('status_filter', $status_filter);
    }
}