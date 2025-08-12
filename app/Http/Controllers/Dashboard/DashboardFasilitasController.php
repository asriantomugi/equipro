<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Fasilitas;
use App\Models\Layanan;
use App\Models\Laporan;
use App\Models\LokasiTk1;
use App\Models\LokasiTk2;
use App\Models\LokasiTk3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;

class DashboardFasilitasController extends Controller
{
    /**
     * FIXED: Hitung metrics dengan logika yang benar untuk kondisi saat ini
     */
    private function calculateMetrics($layanan_collection, $tanggal_mulai = null, $tanggal_selesai = null)
    {
        $layanan_with_metrics = collect();
        
        foreach ($layanan_collection as $layanan) {
            // Set periode waktu
            if ($tanggal_mulai && $tanggal_selesai) {
                $start_date = Carbon::parse($tanggal_mulai)->startOfDay();
                $end_date = Carbon::parse($tanggal_selesai)->endOfDay();
                $period_hours = $start_date->diffInHours($end_date);
            } else {
                // Default 30 hari terakhir
                $end_date = Carbon::now();
                $start_date = Carbon::now()->subDays(30)->startOfDay();
                $period_hours = 720; // 30 hari × 24 jam
            }
            
            // Hitung metrics real berdasarkan histori gangguan
            $metrics = $this->calculateRealMetrics($layanan->id, $start_date, $end_date, $period_hours);
            
            // Assign metrics ke layanan
            $layanan->total_waktu_serviceable = $metrics['total_waktu_serviceable'];
            $layanan->total_waktu_perbaikan = $metrics['total_waktu_perbaikan'];
            $layanan->total_perbaikan = $metrics['total_perbaikan'];
            $layanan->total_unserviceable = $metrics['total_unserviceable'];
            $layanan->total_kegagalan_selesai = $metrics['total_kegagalan_selesai'];
            $layanan->period_hours = $period_hours;
            
            // PERBAIKAN 1: Cek kondisi saat ini
            $kondisi_saat_ini = $layanan->kondisi ?? config('constants.kondisi_layanan.Unserviceable');
            $is_currently_unserviceable = ($kondisi_saat_ini == config('constants.kondisi_layanan.Unserviceable'));
            
            // PERBAIKAN 2: Cek apakah sedang dalam gangguan aktif
            $gangguan_aktif = $this->hasActiveDisruption($layanan->id);
            $waktu_mulai_gangguan = $this->getActiveDisruptionStartTime($layanan->id);
            
            // PERBAIKAN 3: Logika Availability yang Diperbaiki
            if ($is_currently_unserviceable || $gangguan_aktif) {
                // Jika saat ini unserviceable ATAU ada gangguan aktif
                if ($metrics['total_waktu_perbaikan'] > 0) {
                    // Ada histori gangguan dalam periode, hitung berdasarkan histori
                    $layanan->availability_percentage = round(($metrics['total_waktu_serviceable'] / $period_hours) * 100, 2);
                } else {
                    // Tidak ada histori gangguan dalam periode, tapi saat ini unserviceable
                    if ($waktu_mulai_gangguan) {
                        // Ada gangguan aktif, hitung downtime dari waktu mulai gangguan
                        $gangguan_start = Carbon::parse($waktu_mulai_gangguan)->max($start_date);
                        $current_downtime = $gangguan_start->diffInHours(Carbon::now());
                        $current_downtime = min($current_downtime, $period_hours);
                        
                        $adjusted_serviceable_time = max(0, $period_hours - $current_downtime);
                        $layanan->availability_percentage = round(($adjusted_serviceable_time / $period_hours) * 100, 2);
                    } else {
                        // Tidak ada info gangguan, set 0% jika unserviceable
                        $layanan->availability_percentage = 0;
                    }
                }
            } else {
                // Jika saat ini serviceable, hitung berdasarkan histori
                $layanan->availability_percentage = $period_hours > 0 
                    ? round(($metrics['total_waktu_serviceable'] / $period_hours) * 100, 2) 
                    : 0;
            }
            
            // RUMUS 2: Mean Time To Repair (MTTR) - KONVERSI KE MENIT
            $mttr_hours = $metrics['total_perbaikan'] > 0 
                ? $metrics['total_waktu_perbaikan'] / $metrics['total_perbaikan']
                : 0;
            $layanan->mttr = round($mttr_hours * 60, 2);
            
            // RUMUS 3: Mean Time Between Failure (MTBF) - KONVERSI KE MENIT
            $mtbf_hours = $metrics['total_unserviceable'] > 0 
                ? $metrics['total_waktu_serviceable'] / $metrics['total_unserviceable']
                : ($metrics['total_waktu_serviceable'] > 0 ? $metrics['total_waktu_serviceable'] : 0);
            $layanan->mtbf = round($mtbf_hours * 60, 2);
            
            $layanan_with_metrics->push($layanan);
        }
        
        return $layanan_with_metrics;
    }

    /**
     * PERBAIKAN: Method untuk cek gangguan aktif
     */
    private function hasActiveDisruption($layanan_id)
    {
        return DB::table('histori_gangguan_layanan')
            ->where('layanan_id', $layanan_id)
            ->whereNull('waktu_serv') // Gangguan yang belum selesai
            ->exists();
    }

    /**
     * PERBAIKAN: Method untuk mendapatkan waktu mulai gangguan aktif
     */
    private function getActiveDisruptionStartTime($layanan_id)
    {
        $gangguan = DB::table('histori_gangguan_layanan')
            ->where('layanan_id', $layanan_id)
            ->whereNull('waktu_serv')
            ->orderBy('waktu_unserv', 'desc')
            ->first();
        
        return $gangguan ? $gangguan->waktu_unserv : null;
    }

    /**
     * FIXED: Method pie chart dengan filter tanggal yang benar
     */
    private function calculatePieChartData($fasilitas_id, $tanggal_mulai = null, $tanggal_selesai = null)
    {
        // PERBAIKAN UTAMA: Query layanan dengan filter tanggal yang tepat
        $query = Layanan::where('fasilitas_id', $fasilitas_id);
        
        // Jika ada filter tanggal, hanya ambil layanan yang dibuat DALAM periode tersebut
        if ($tanggal_mulai && $tanggal_selesai) {
            $start_date = Carbon::parse($tanggal_mulai)->startOfDay();
            $end_date = Carbon::parse($tanggal_selesai)->endOfDay();
            
            // PERBAIKAN: Filter berdasarkan created_at dalam periode
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }
        
        $layanan_collection = $query->get();
        
        if ($layanan_collection->count() == 0) {
            return [
                'serviceable' => 0,
                'unserviceable' => 0
            ];
        }

        // Jika tidak ada filter tanggal, gunakan kondisi saat ini
        if (!$tanggal_mulai || !$tanggal_selesai) {
            $serviceable = 0;
            $unserviceable = 0;
            
            foreach ($layanan_collection as $layanan) {
                $kondisi_saat_ini = $layanan->kondisi ?? config('constants.kondisi_layanan.Unserviceable');
                $gangguan_aktif = $this->hasActiveDisruption($layanan->id);
                
                if ($kondisi_saat_ini == config('constants.kondisi_layanan.Serviceable') && !$gangguan_aktif) {
                    $serviceable++;
                } else {
                    $unserviceable++;
                }
            }
            
            return [
                'serviceable' => $serviceable,
                'unserviceable' => $unserviceable
            ];
        }

        // Jika ada filter tanggal, hitung berdasarkan metrics
        $layanan_with_metrics = $this->calculateMetrics($layanan_collection, $tanggal_mulai, $tanggal_selesai);
        
        $serviceable = 0;
        $unserviceable = 0;
        
        foreach ($layanan_with_metrics as $layanan) {
            // Layanan dianggap serviceable jika availability >= 95% DAN kondisi saat ini serviceable
            $kondisi_saat_ini = $layanan->kondisi ?? config('constants.kondisi_layanan.Unserviceable');
            $gangguan_aktif = $this->hasActiveDisruption($layanan->id);
            
            if ($layanan->availability_percentage >= 95 && 
                $kondisi_saat_ini == config('constants.kondisi_layanan.Serviceable') && 
                !$gangguan_aktif) {
                $serviceable++;
            } else {
                $unserviceable++;
            }
        }
        
        return [
            'serviceable' => $serviceable,
            'unserviceable' => $unserviceable
        ];
    }

    /**
 * PERBAIKAN: Hitung metrics real dari tabel LAPORAN langsung
 * Lebih sederhana dan akurat
 */
private function calculateRealMetrics($layanan_id, $start_date, $end_date, $period_hours)
{
    $layanan = Layanan::find($layanan_id);
    
    if (!$layanan) {
        return [
            'total_waktu_serviceable' => 0,
            'total_waktu_perbaikan' => 0,
            'total_perbaikan' => 0,
            'total_unserviceable' => 0,
            'total_kegagalan_selesai' => 0
        ];
    }

    $total_waktu_perbaikan = 0;
    $total_perbaikan = 0;
    $total_unserviceable = 0;
    $total_kegagalan_selesai = 0;

    // DEBUG: Log parameter input
    \Log::info("calculateRealMetrics - Layanan ID: {$layanan_id}", [
        'layanan_nama' => $layanan->nama ?? 'N/A',
        'start_date' => $start_date->format('Y-m-d H:i:s'),
        'end_date' => $end_date->format('Y-m-d H:i:s'),
        'period_hours' => $period_hours
    ]);

    // PERBAIKAN UTAMA: Ambil dari tabel LAPORAN langsung
    $laporan_gangguan = DB::table('laporan')
        ->where('layanan_id', $layanan_id)
        ->where(function($query) use ($start_date, $end_date) {
            // Laporan yang dibuka dalam periode
            $query->whereBetween('waktu_open', [$start_date, $end_date])
                  // Atau laporan yang dibuka sebelum periode tapi ditutup dalam periode
                  ->orWhere(function($q) use ($start_date, $end_date) {
                      $q->where('waktu_open', '<', $start_date)
                        ->whereBetween('waktu_close', [$start_date, $end_date]);
                  })
                  // Atau laporan yang dibuka sebelum periode dan belum ditutup
                  ->orWhere(function($q) use ($start_date, $end_date) {
                      $q->where('waktu_open', '<', $start_date)
                        ->whereNull('waktu_close');
                  })
                  // Atau laporan yang dibuka dalam periode tapi ditutup setelah periode
                  ->orWhere(function($q) use ($start_date, $end_date) {
                      $q->whereBetween('waktu_open', [$start_date, $end_date])
                        ->where('waktu_close', '>', $end_date);
                  });
        })
        ->orderBy('waktu_open', 'asc')
        ->get();

    // DEBUG: Log jumlah laporan yang ditemukan
    \Log::info("calculateRealMetrics - Total laporan ditemukan: " . $laporan_gangguan->count());
    
    if ($laporan_gangguan->count() == 0) {
        // Cek apakah ada laporan sama sekali untuk layanan ini
        $total_laporan_all = DB::table('laporan')
            ->where('layanan_id', $layanan_id)
            ->count();
        
        \Log::info("calculateRealMetrics - No reports found in period", [
            'total_laporan_semua_waktu' => $total_laporan_all,
            'kemungkinan_penyebab' => $total_laporan_all == 0 ? 'Tidak ada laporan sama sekali' : 'Laporan ada tapi di luar periode'
        ]);
    }

    foreach ($laporan_gangguan as $index => $laporan) {
        // Setiap laporan gangguan dihitung sebagai 1 kegagalan untuk MTBF
        $total_unserviceable++;
        
        $waktu_open = Carbon::parse($laporan->waktu_open);
        $waktu_close = $laporan->waktu_close ? Carbon::parse($laporan->waktu_close) : null;
        
        // DEBUG: Log detail setiap laporan
        \Log::info("calculateRealMetrics - Laporan #{$index}", [
            'id' => $laporan->id,
            'no_laporan' => $laporan->no_laporan ?? 'N/A',
            'waktu_open' => $waktu_open->format('Y-m-d H:i:s'),
            'waktu_close' => $waktu_close ? $waktu_close->format('Y-m-d H:i:s') : 'NULL',
            'status' => $laporan->status ?? 'N/A',
            'is_closed' => $waktu_close ? 'YES' : 'NO'
        ]);
        
        // Tentukan waktu efektif gangguan dalam periode
        $effective_start = $waktu_open->max($start_date);
        $effective_end = $waktu_close ? $waktu_close->min($end_date) : $end_date;
        
        // DEBUG: Log waktu efektif
        \Log::info("calculateRealMetrics - Effective time", [
            'effective_start' => $effective_start->format('Y-m-d H:i:s'),
            'effective_end' => $effective_end->format('Y-m-d H:i:s'),
            'is_valid_duration' => $effective_end->gt($effective_start) ? 'YES' : 'NO'
        ]);
        
        // Pastikan ada durasi gangguan dalam periode
        if ($effective_end->gt($effective_start)) {
            $duration_hours = $effective_start->diffInHours($effective_end);
            $total_waktu_perbaikan += $duration_hours;
            
            // DEBUG: Log durasi
            \Log::info("calculateRealMetrics - Duration calculated", [
                'duration_hours' => $duration_hours,
                'total_waktu_perbaikan' => $total_waktu_perbaikan
            ]);
            
            // KUNCI MTTR: Hitung sebagai perbaikan HANYA jika laporan sudah DITUTUP
            if ($waktu_close) {
                $total_perbaikan++;
                $total_kegagalan_selesai++;
                \Log::info("calculateRealMetrics - Counted as completed repair", [
                    'laporan_id' => $laporan->id,
                    'total_perbaikan' => $total_perbaikan
                ]);
            } else {
                \Log::info("calculateRealMetrics - NOT counted as repair (still open)", [
                    'laporan_id' => $laporan->id
                ]);
            }
        }
    }

    // Total waktu serviceable = Total periode - Total waktu perbaikan
    $total_waktu_serviceable = max(0, $period_hours - $total_waktu_perbaikan);

    // DEBUG: Log hasil akhir dengan perhitungan MTTR
    $mttr_hours = $total_perbaikan > 0 ? $total_waktu_perbaikan / $total_perbaikan : 0;
    $mttr_minutes = round($mttr_hours * 60, 2);
    
    \Log::info("calculateRealMetrics - Final results", [
        'total_waktu_serviceable' => $total_waktu_serviceable,
        'total_waktu_perbaikan' => $total_waktu_perbaikan,
        'total_perbaikan' => $total_perbaikan,
        'total_unserviceable' => $total_unserviceable,
        'total_kegagalan_selesai' => $total_kegagalan_selesai,
        'mttr_hours' => $mttr_hours,
        'mttr_minutes' => $mttr_minutes,
        'mttr_akan_jadi_0' => $total_perbaikan == 0 ? 'YA - Tidak ada laporan selesai' : 'TIDAK'
    ]);

    return [
        'total_waktu_serviceable' => $total_waktu_serviceable,
        'total_waktu_perbaikan' => $total_waktu_perbaikan,
        'total_perbaikan' => $total_perbaikan,
        'total_unserviceable' => $total_unserviceable,
        'total_kegagalan_selesai' => $total_kegagalan_selesai
    ];
}

/**
 * PERBAIKAN: Update waktu_close otomatis saat laporan selesai
 * Tambahkan di akhir handleDraftFormTindakLanjut dan handleAjaxTindakLanjut
 */
private function updateLaporanWaktuClose($laporan, $jenisTindakLanjutTerbaru)
{
    // Jika kondisi layanan sudah serviceable dan jenis tindak lanjut adalah perbaikan
    if ($laporan->kondisi_layanan_temp == 1 && $jenisTindakLanjutTerbaru == 1) {
        
        // Ambil waktu tindak lanjut terbaru sebagai waktu close
        $waktu_close = null;
        
        if ($laporan->jenis == 1) {
            // Untuk gangguan peralatan
            $tindakLanjutTerbaru = TlGangguanPeralatan::where('laporan_id', $laporan->id)
                ->where('jenis_tindaklanjut', 1) // Perbaikan
                ->where('kondisi', 1) // Beroperasi
                ->latest('waktu')
                ->first();
            
            if ($tindakLanjutTerbaru) {
                $waktu_close = $tindakLanjutTerbaru->waktu;
            }
        } else {
            // Untuk non-peralatan
            $tindakLanjutNonPeralatan = TlGangguanNonPeralatan::where('laporan_id', $laporan->id)
                ->where('kondisi', 1)
                ->latest('waktu')
                ->first();
            
            if ($tindakLanjutNonPeralatan) {
                $waktu_close = $tindakLanjutNonPeralatan->waktu;
            }
        }
        
        // Update waktu_close jika belum ada
        if ($waktu_close && !$laporan->waktu_close) {
            $laporan->waktu_close = $waktu_close;
            $laporan->status = 3; // Asumsi status 3 = selesai
            $laporan->save();
            
            \Log::info('Updated laporan waktu_close:', [
                'laporan_id' => $laporan->id,
                'waktu_close' => $waktu_close,
                'status_baru' => 3
            ]);
        }
    }
}

/**
 * IMPLEMENTASI: Tambahkan di method existing
 */
// Di akhir handleDraftFormTindakLanjut:
// $this->updateLaporanWaktuClose($laporan, $jenisTindakLanjutTerbaru);

// Di akhir handleAjaxTindakLanjut:
// $this->updateLaporanWaktuClose($laporan, $jenisTindakLanjutTerbaru);

    /**
     * FIXED: Tampilkan dashboard daftar dengan filter dari request (dengan filter tanggal yang benar)
     */
    public function dashboarddaftarIndex(Request $request)
    {
        // Verifikasi auth dan role
        if (!$this->checkAuth()) {
            return $this->redirectToAuth();
        }

        // Ambil parameter filter
        $fasilitas_id = $request->get('fasilitas');
        $status_filter = $request->get('status');
        $tanggal_mulai = $request->get('tanggal_mulai');
        $tanggal_selesai = $request->get('tanggal_selesai');
        
        // Data untuk dropdown filter
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        $fasilitas_selected = null;
        if ($fasilitas_id) {
            $fasilitas_selected = Fasilitas::find($fasilitas_id);
            if (!$fasilitas_selected) {
                return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas tidak ditemukan');
            }
        }
        
        $daftar = collect();
        
        // Proses data jika ada filter fasilitas dan tanggal
        if ($fasilitas_id && $tanggal_mulai && $tanggal_selesai) {
            // Validasi tanggal
            try {
                $start_date = Carbon::parse($tanggal_mulai);
                $end_date = Carbon::parse($tanggal_selesai);
                
                if ($end_date->lt($start_date)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
                }
            } catch (Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Format tanggal tidak valid');
            }
            
            // PERBAIKAN UTAMA: Query layanan yang dibuat DALAM periode tanggal
            $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3'])
                           ->where('fasilitas_id', $fasilitas_id)
                           // PERBAIKAN: Filter berdasarkan created_at dalam periode
                           ->whereBetween('created_at', [
                               $start_date->startOfDay(), 
                               $end_date->endOfDay()
                           ]);
            
            // Filter berdasarkan status jika ada
            if ($status_filter) {
                if ($status_filter == 'serviceable') {
                    $query->where('kondisi', config('constants.kondisi_layanan.Serviceable'));
                } elseif ($status_filter == 'unserviceable') {
                    $query->where('kondisi', config('constants.kondisi_layanan.Unserviceable'));
                }
            }
            
            $layanan_collection = $query->get();
            
            // Hitung metrics dengan periode yang dipilih
            if ($layanan_collection->count() > 0) {
                $daftar = $this->calculateMetrics($layanan_collection, $tanggal_mulai, $tanggal_selesai);
            }
        }
        
        return view('dashboard.dashboarddaftar')
            ->with('judul', 'Fasilitas')
            ->with('module', 'Dashboard')
            ->with('menu', 'Layanan')
            ->with('menu_url', route('dashboard.fasilitas'))
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
            ->with('tanggal_mulai', $tanggal_mulai)
            ->with('tanggal_selesai', $tanggal_selesai);
    }

    /**
     * Tampilkan dashboard daftar layanan per fasilitas
     */
    public function dashboarddaftar($fasilitas_id)
    {
        if (!$this->checkAuth()) {
            return $this->redirectToAuth();
        }

        if (!$fasilitas_id) {
            return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas harus dipilih');
        }

        $fasilitas_selected = Fasilitas::find($fasilitas_id);
        
        if (!$fasilitas_selected) {
            return redirect()->route('dashboard.fasilitas')->with('error', 'Fasilitas tidak ditemukan');
        }

        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        return view('dashboard.dashboarddaftar')
            ->with('judul', 'Fasilitas')
            ->with('module', 'Dashboard')
            ->with('menu', 'Fasilitas')
            ->with('menu_url', route('dashboard.fasilitas'))
            ->with('fasilitas', $fasilitas)
            ->with('fasilitas_selected', $fasilitas_selected)
            ->with('lokasi_tk_1', $lokasi_tk_1)
            ->with('daftar', collect())
            ->with('fasilitas_id', $fasilitas_id)
            ->with('lokasi_tk_1_id', null)
            ->with('lokasi_tk_2_id', null)
            ->with('lokasi_tk_3_id', null)
            ->with('kondisi', null)
            ->with('tanggal_mulai', null)
            ->with('tanggal_selesai', null);
    }
    
    /**
     * UPDATED: Tampilkan dashboard fasilitas utama dengan data pie chart yang konsisten
     */
    public function index(Request $request)
    {
        if (!$this->checkAuth()) {
            return $this->redirectToAuth();
        }

        $fasilitas = Fasilitas::where('status', 1)->get();
        
        // Hitung data pie chart untuk setiap fasilitas
        $fasilitas_with_chart_data = $fasilitas->map(function($satu) {
            $chart_data = $this->calculatePieChartData($satu->id);
            $satu->chart_serviceable = $chart_data['serviceable'];
            $satu->chart_unserviceable = $chart_data['unserviceable'];
            return $satu;
        });
        
        return view('dashboard.fasilitas')
            ->with('judul', 'Dashboard Fasilitas')
            ->with('module', 'Dashboard')
            ->with('menu', 'Fasilitas')
            ->with('menu_url', route('dashboard.fasilitas'))
            ->with('fasilitas', $fasilitas_with_chart_data);
    }

    /**
     * FIXED: Filter dashboard daftar layanan (dengan filter tanggal yang benar)
     */
    public function dashboarddaftarFilter(Request $request)
    {
        if (!$this->checkAuth()) {
            return $this->redirectToAuth();
        }

        // Ambil parameter filter
        $fasilitas_id = $request->input('fasilitas');
        $lokasi_tk_1_id = $request->input('lokasi_tk_1');
        $lokasi_tk_2_id = $request->input('lokasi_tk_2');
        $lokasi_tk_3_id = $request->input('lokasi_tk_3');
        $kondisi = $request->input('kondisi');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $status_filter = $request->input('status');

        // Validasi input wajib
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

        // Validasi dan parsing tanggal
        try {
            $start_date = Carbon::parse($tanggal_mulai);
            $end_date = Carbon::parse($tanggal_selesai);
            
            if ($end_date->lt($start_date)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
            }

            // Validasi rentang maksimal (1 tahun)
            if ($start_date->diffInDays($end_date) > 365) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Rentang tanggal maksimal 1 tahun');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Format tanggal tidak valid');
        }

        // Ambil data untuk dropdown
        $fasilitas = Fasilitas::where('status', 1)->get();
        $lokasi_tk_1 = LokasiTk1::where('status', 1)->get();
        
        $fasilitas_selected = Fasilitas::find($fasilitas_id);
        
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
        
        // PERBAIKAN UTAMA: Query layanan yang dibuat DALAM periode tanggal
        $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3'])
                        ->where('fasilitas_id', $fasilitas_id)
                        // PERBAIKAN: Filter berdasarkan created_at dalam periode
                        ->whereBetween('created_at', [
                            $start_date->startOfDay(), 
                            $end_date->endOfDay()
                        ]);
        
        // Filter lokasi
        if ($lokasi_tk_1_id) {
            $query->where('lokasi_tk_1_id', $lokasi_tk_1_id);
        }
        
        if ($lokasi_tk_2_id) {
            $query->where('lokasi_tk_2_id', $lokasi_tk_2_id);
        }
        
        if ($lokasi_tk_3_id) {
            $query->where('lokasi_tk_3_id', $lokasi_tk_3_id);
        }
        
        // Filter kondisi
        if ($kondisi !== null && $kondisi !== '') {
            $query->where('kondisi', $kondisi);
        }
        
        if ($status_filter) {
            if ($status_filter == 'serviceable') {
                $query->where('kondisi', config('constants.kondisi_layanan.Serviceable'));
            } elseif ($status_filter == 'unserviceable') {
                $query->where('kondisi', config('constants.kondisi_layanan.Unserviceable'));
            }
        }
        
        $layanan_collection = $query->get();

        // Hitung metrics dengan periode yang dipilih
        $daftar = $this->calculateMetrics($layanan_collection, $tanggal_mulai, $tanggal_selesai);

        return view('dashboard.dashboarddaftar')
            ->with('judul', 'Fasilitas')
            ->with('module', 'Dashboard')
            ->with('menu', 'Fasilitas')
            ->with('menu_url', route('dashboard.fasilitas'))
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

    /**
     * Helper method untuk cek otentikasi
     */
    private function checkAuth()
    {
        if (!Auth::check()) {
            return false;
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return false;
        }

        $role_id = session()->get('role_id');
        return in_array($role_id, [
            config('constants.role.super_admin'),
            config('constants.role.admin'),
            config('constants.role.teknisi')
        ]);
    }

    /**
     * Helper method untuk redirect auth
     */
    private function redirectToAuth()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            return redirect('/logout');
        }

        return redirect('/');
    }
}