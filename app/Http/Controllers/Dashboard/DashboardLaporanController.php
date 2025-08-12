<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Fasilitas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardLaporanController extends Controller
{
    public function laporan(Request $request)
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

        // ===================== KONSTANTA STATUS LAPORAN =======================
        $STATUS_OPEN = config('constants.status_laporan.open');     // 2
        $STATUS_CLOSED = config('constants.status_laporan.closed'); // 3

        // ===================== TAMPILKAN SEMUA DATA LAPORAN (DEFAULT) =======================
        $fasilitas = Fasilitas::select('fasilitas.*')
            ->selectRaw("
                COUNT(laporan.id) as total_laporan,
                COUNT(CASE WHEN laporan.status = {$STATUS_OPEN} THEN 1 END) as laporan_open,
                COUNT(CASE WHEN laporan.status = {$STATUS_CLOSED} THEN 1 END) as laporan_close
            ")
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', 'layanan.id', '=', 'laporan.layanan_id')
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama', 'fasilitas.status', 
                     'fasilitas.created_by', 'fasilitas.updated_by', 'fasilitas.created_at', 'fasilitas.updated_at')
            ->having('total_laporan', '>', 0) // Hanya fasilitas yang memiliki laporan
            ->orderBy('fasilitas.nama', 'ASC')
            ->get();

        // ===================== PERHITUNGAN SUMMARY DATA =======================
        $total_laporan_semua = $fasilitas->sum('total_laporan');
        $total_open_semua = $fasilitas->sum('laporan_open');
        $total_close_semua = $fasilitas->sum('laporan_close');

        // ===================== DATA UNTUK CHART PIE LAPORAN PER FASILITAS =======================
        $laporanPerFasilitas = Fasilitas::select('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->selectRaw('COUNT(laporan.id) as total_laporan')
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', 'layanan.id', '=', 'laporan.layanan_id')
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->having('total_laporan', '>', 0)
            ->orderBy('total_laporan', 'DESC')
            ->get();

        return view('dashboard.laporan', [
            'judul' => 'Laporan Dashboard',
            'module' => 'Dashboard',
            'menu' => 'Laporan',
            'menu_url' => route('dashboard.laporan'),
            // Data fasilitas dengan laporan (semua data)
            'fasilitas' => $fasilitas,
            // Data summary (untuk MENU LAPORAN)
            'total_laporan_semua' => $total_laporan_semua,
            'total_open' => $total_open_semua,
            'total_close' => $total_close_semua,
            // Data untuk chart pie laporan per fasilitas
            'laporanPerFasilitas' => $laporanPerFasilitas,
            // Status constants untuk view
            'STATUS_OPEN' => $STATUS_OPEN,
            'STATUS_CLOSED' => $STATUS_CLOSED,
            // Default values untuk form filter
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'show_all_data' => true, // Flag untuk menandakan menampilkan semua data
        ]);
    }

    public function filter(Request $request)
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

        // Validasi input
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
        ]);

        // ===================== PROSES FILTER TANGGAL =======================
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $start = Carbon::parse($tanggal_mulai)->startOfDay();
        $end = Carbon::parse($tanggal_selesai)->endOfDay();
        // ===================== AKHIR PROSES FILTER TANGGAL =======================

        // ===================== KONSTANTA STATUS LAPORAN =======================
        $STATUS_OPEN = config('constants.status_laporan.open');     // 2
        $STATUS_CLOSED = config('constants.status_laporan.closed'); // 3

        // ===================== PROSES PENGAMBILAN DATA LAPORAN PER FASILITAS (FILTERED) =======================
        $fasilitas = Fasilitas::select('fasilitas.*')
            ->selectRaw("
                COUNT(laporan.id) as total_laporan,
                COUNT(CASE WHEN laporan.status = {$STATUS_OPEN} THEN 1 END) as laporan_open,
                COUNT(CASE WHEN laporan.status = {$STATUS_CLOSED} THEN 1 END) as laporan_close
            ")
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', function($join) use ($start, $end) {
                $join->on('layanan.id', '=', 'laporan.layanan_id')
                     ->whereBetween('laporan.waktu_open', [$start, $end]);
            })
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama', 'fasilitas.status', 
                     'fasilitas.created_by', 'fasilitas.updated_by', 'fasilitas.created_at', 'fasilitas.updated_at')
            ->having('total_laporan', '>', 0) // Hanya fasilitas yang memiliki laporan
            ->orderBy('fasilitas.nama', 'ASC')
            ->get();

        // ===================== PERHITUNGAN SUMMARY DATA =======================
        $total_laporan_semua = $fasilitas->sum('total_laporan');
        $total_open_semua = $fasilitas->sum('laporan_open');
        $total_close_semua = $fasilitas->sum('laporan_close');

        // ===================== DATA UNTUK CHART PIE LAPORAN PER FASILITAS (FILTERED) =======================
        $laporanPerFasilitas = Fasilitas::select('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->selectRaw('COUNT(laporan.id) as total_laporan')
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', function($join) use ($start, $end) {
                $join->on('layanan.id', '=', 'laporan.layanan_id')
                     ->whereBetween('laporan.waktu_open', [$start, $end]);
            })
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->having('total_laporan', '>', 0)
            ->orderBy('total_laporan', 'DESC')
            ->get();

        return view('dashboard.laporan', [
            'judul' => 'Laporan Dashboard',
            'module' => 'Dashboard',
            'menu' => 'Laporan',
            'menu_url' => route('dashboard.laporan'),
            // Data filter
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            // Data fasilitas dengan laporan (filtered)
            'fasilitas' => $fasilitas,
            // Data summary (untuk MENU LAPORAN)
            'total_laporan_semua' => $total_laporan_semua,
            'total_open' => $total_open_semua,
            'total_close' => $total_close_semua,
            // Data untuk chart pie laporan per fasilitas (filtered)
            'laporanPerFasilitas' => $laporanPerFasilitas,
            // Status constants untuk view
            'STATUS_OPEN' => $STATUS_OPEN,
            'STATUS_CLOSED' => $STATUS_CLOSED,
            'show_all_data' => false, // Flag untuk menandakan data sudah difilter
        ]);
    }

    public function fasilitas()
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

        // ===================== KONSTANTA STATUS LAPORAN =======================
        $STATUS_OPEN = config('constants.status_laporan.open');     // 2
        $STATUS_CLOSED = config('constants.status_laporan.closed'); // 3

        // Ambil semua fasilitas dengan relasi laporan melalui layanan dan hitung jumlah laporan
        $fasilitas = Fasilitas::select('fasilitas.*')
            ->selectRaw("
                COUNT(laporan.id) as laporan_count,
                COUNT(CASE WHEN laporan.status = {$STATUS_OPEN} THEN 1 END) as laporan_open,
                COUNT(CASE WHEN laporan.status = {$STATUS_CLOSED} THEN 1 END) as laporan_close
            ")
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', 'layanan.id', '=', 'laporan.layanan_id')
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama', 'fasilitas.status', 
                     'fasilitas.created_by', 'fasilitas.updated_by', 'fasilitas.created_at', 'fasilitas.updated_at')
            ->orderBy('fasilitas.nama', 'ASC')
            ->get();

        // Data untuk Chart: Distribusi Laporan per Fasilitas
        $data_fasilitas = Fasilitas::select('fasilitas.id', 'fasilitas.nama')
            ->join('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->join('laporan', 'layanan.id', '=', 'laporan.layanan_id')
            ->selectRaw('fasilitas.nama, COUNT(laporan.id) as jumlah_laporan')
            ->groupBy('fasilitas.id', 'fasilitas.nama')
            ->having('jumlah_laporan', '>', 0) // Hanya fasilitas yang memiliki laporan
            ->orderBy('jumlah_laporan', 'DESC')
            ->get();

        $total_fasilitas = $data_fasilitas->count();
        $total_laporan_semua = $data_fasilitas->sum('jumlah_laporan');

        return view('dashboard.fasilitas', [
            'judul' => 'Fasilitas Dashboard',
            'module' => 'Dashboard',
            'menu' => 'Fasilitas',
            'menu_url' => route('dashboard.fasilitas.laporan'),
            'fasilitas' => $fasilitas,
            'data_fasilitas' => $data_fasilitas,
            'total_fasilitas' => $total_fasilitas,
            'total_laporan_semua' => $total_laporan_semua,
        ]);
    }
}