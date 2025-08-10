<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Fasilitas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogbookModuleController extends Controller
{
    public function home(Request $request)
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
        $STATUS_OPEN = config('constants.status_laporan.open', 2);
        $STATUS_CLOSED = config('constants.status_laporan.closed', 3);

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
        $total_laporan = $fasilitas->sum('total_laporan');
        $total_open = $fasilitas->sum('laporan_open');
        $total_close = $fasilitas->sum('laporan_close');

        // ===================== DATA UNTUK CHART PIE LAPORAN PER FASILITAS =======================
        $laporanPerFasilitas = Fasilitas::select('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->selectRaw('COUNT(laporan.id) as total_laporan')
            ->leftJoin('layanan', 'fasilitas.id', '=', 'layanan.fasilitas_id')
            ->leftJoin('laporan', 'layanan.id', '=', 'laporan.layanan_id')
            ->groupBy('fasilitas.id', 'fasilitas.kode', 'fasilitas.nama')
            ->having('total_laporan', '>', 0)
            ->orderBy('total_laporan', 'DESC')
            ->get();

        return view('logbook.home', [
            'judul' => 'Home Logbook',
            'module' => 'Logbook',
            'menu' => 'Home',
            'menu_url' => route('logbook.home'),
            // Data fasilitas dengan laporan (semua data)
            'fasilitas' => $fasilitas,
            // Data summary untuk template (sesuai dengan variable di blade)
            'total_open' => $total_open,
            'total_close' => $total_close,
            // Data untuk chart pie laporan per fasilitas
            'laporanPerFasilitas' => $laporanPerFasilitas,
            // Status constants untuk JavaScript di template
            'STATUS_OPEN' => $STATUS_OPEN,
            'STATUS_CLOSED' => $STATUS_CLOSED,
            // Default values untuk form filter - tidak di-set agar template menampilkan "semua data"
            // 'tanggal_mulai' => null,
            // 'tanggal_selesai' => null,
        ]);
    }

    public function filter(Request $request)
    {
        // ========================= PROSES VERIFIKASI ========================
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect('/login');
        }

        $status = User::find(session()->get('id'))->status;
        if ($status != TRUE) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Account inactive'], 401);
            }
            return redirect('/logout');
        }

        if (session()->get('role_id') != config('constants.role.super_admin') 
            && session()->get('role_id') != config('constants.role.admin')
            && session()->get('role_id') != config('constants.role.teknisi')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Access denied'], 403);
            }
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
        $STATUS_OPEN = config('constants.status_laporan.open', 2);
        $STATUS_CLOSED = config('constants.status_laporan.closed', 3);

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
        $total_laporan = $fasilitas->sum('total_laporan');
        $total_open = $fasilitas->sum('laporan_open');
        $total_close = $fasilitas->sum('laporan_close');

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

        // Jika request AJAX, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,
                    'fasilitas' => $fasilitas,
                    'total_open' => $total_open,
                    'total_close' => $total_close,
                    'laporanPerFasilitas' => $laporanPerFasilitas,
                    'STATUS_OPEN' => $STATUS_OPEN,
                    'STATUS_CLOSED' => $STATUS_CLOSED,
                ]
            ]);
        }

        // Return ke view logbook.home dengan data filtered
        return view('logbook.home', [
            'judul' => 'Home Logbook',
            'module' => 'Logbook', 
            'menu' => 'Home',
            'menu_url' => route('logbook.home'),
            // Data filter - penting untuk template mengetahui ini data filtered
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            // Data fasilitas dengan laporan (filtered)
            'fasilitas' => $fasilitas,
            // Data summary untuk template (sesuai dengan variable di blade)
            'total_open' => $total_open,
            'total_close' => $total_close,
            // Data untuk chart pie laporan per fasilitas (filtered)
            'laporanPerFasilitas' => $laporanPerFasilitas,
            // Status constants untuk JavaScript di template
            'STATUS_OPEN' => $STATUS_OPEN,
            'STATUS_CLOSED' => $STATUS_CLOSED,
        ]);
    }
}