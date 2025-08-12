<?php

namespace App\Http\Controllers\Logbook;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\User;
use App\Models\GangguanPeralatan;
use App\Models\TindakLanjutPeralatan;
use App\Models\PenggantiPeralatan;
use App\Models\GangguanNonPeralatan;
use App\Models\TindakLanjutNonPeralatan;

class RiwayatController extends Controller
{
    /**
     * Function untuk menampilkan daftar riwayat.
     */
    public function daftar()
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

        // Ambil hanya laporan dengan status CLOSED
        $daftar = Laporan::where('status', config('constants.status_laporan.closed'))
            ->with(['layanan.fasilitas', 'layanan.LokasiTk1', 'layanan.LokasiTk2', 'layanan.LokasiTk3'])
            ->orderBy('created_at', 'desc')
            ->get();

        $judul = "Riwayat";
        $module = "Logbook";
        $menu = "Riwayat";
        $menu_url = "/logbook/riwayat/daftar";

        return view('logbook.riwayat.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('daftar', $daftar);
    }

    /**
     * Function untuk menampilkan detail laporan (untuk modal)
     */
    public function detail(Request $request)
    {
        try {
            // ========================= PROSES VERIFIKASI ========================
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }    
            
            $status = User::find(session()->get('id'))->status;
            if($status != TRUE){
                return response()->json(['error' => 'User inactive'], 403);
            }
            
            if(session()->get('role_id') != config('constants.role.super_admin')
             && session()->get('role_id') != config('constants.role.admin')
             && session()->get('role_id') != config('constants.role.teknisi')){
                return response()->json(['error' => 'Access denied'], 403);
            }
            // ===================== AKHIR PROSES VERIFIKASI =======================

            $id = $request->input('id');
            
            if (!$id) {
                return response()->json(['error' => 'ID laporan tidak ditemukan'], 400);
            }

            // PERBAIKAN: Gunakan query yang tidak memicu update timestamps
            // Ambil data laporan dengan relasi tanpa memicu model events
            $laporan = Laporan::with([
                'layanan.fasilitas',
                'layanan.LokasiTk1', 
                'layanan.LokasiTk2', 
                'layanan.LokasiTk3',
                'getCreatedName',
                'getUpdatedName'
            ])
            ->where('id', $id)
            ->first();

            if (!$laporan) {
                return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
            }

            // Log untuk debugging
            \Log::info('Laporan detail request', [
                'id' => $id,
                'created_by' => $laporan->created_by,
                'updated_by' => $laporan->updated_by,
                'created_at' => $laporan->created_at,
                'updated_at' => $laporan->updated_at
            ]);

            // Prepare response data
            $responseData = [
                'laporan' => $laporan,
                'detailGangguanPeralatan' => [],
                'gangguanNonPeralatan' => null,
                'perbaikan' => [],
                'penggantian' => [],
                'semuaTindakLanjutNonPeralatan' => []
            ];

            if ($laporan->jenis == 1) {
                // Gangguan Peralatan - gunakan query builder untuk menghindari model events
                $detailGangguan = GangguanPeralatan::where('laporan_id', $id)
                    ->with('peralatan')
                    ->get();
                $responseData['detailGangguanPeralatan'] = $detailGangguan;

                // Data Perbaikan
                $perbaikan = TindakLanjutPeralatan::where('laporan_id', $id)
                    ->where('jenis', config('constants.jenis_tindaklanjut.perbaikan'))
                    ->with('peralatan')
                    ->get();
                $responseData['perbaikan'] = $perbaikan;

                // Data Penggantian dengan relasi
                $penggantian = PenggantiPeralatan::where('laporan_id', $id)
                    ->with([
                        'peralatanLama',
                        'peralatanBaru',
                        'tindaklanjut'
                    ])
                    ->get();
                $responseData['penggantian'] = $penggantian;

            } else {
                // Gangguan Non-Peralatan
                $gangguanNonPeralatan = GangguanNonPeralatan::where('laporan_id', $id)->first();
                $responseData['gangguanNonPeralatan'] = $gangguanNonPeralatan;

                // Tindak Lanjut Non-Peralatan
                $tindakLanjutNonPeralatan = TindakLanjutNonPeralatan::where('laporan_id', $id)->get();
                $responseData['semuaTindakLanjutNonPeralatan'] = $tindakLanjutNonPeralatan;
            }

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error('Error in RiwayatController@detail: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Terjadi kesalahan sistem'], 500);
        }
    }
}