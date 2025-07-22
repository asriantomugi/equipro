<?php
namespace App\Http\Controllers\Logbook;

/**
 * ExportController.php
 * Controller ini digunakan untuk menangani proses export data laporan ke excel
 *
 * @author Yanti Melani
 */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Layanan;
 use App\Models\Peralatan;
 use App\Models\JenisAlat;
 use App\Models\Perusahaan;
 use App\Models\Fasilitas;
 use App\Models\LokasiTk1;
 use App\Models\LokasiTk2;
 use App\Models\LokasiTk3;
 use App\Models\DaftarPeralatanLayanan;
 use App\Models\Laporan;
 use App\Models\GangguanPeralatan;
 use App\Models\GangguanNonPeralatan;
 use App\Models\TlGangguanPeralatan;
 use App\Models\TlPenggantianPeralatan;
 use App\Models\TlGangguanNonPeralatan;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
     /**
     * Function untuk menampilkan daftar export data laporan.
     *
     * Akses:
     * - Admin
     * - Teknisi
     * 
     * Method: GET
     * URL: /logbook/export/daftar
     *
     * @return \Illuminate\Http\Response
     */
    public function daftar(Request $request)
    {
        // Ambil data untuk dropdown filter
        $fasilitas = Fasilitas::all();
        $layanan = Layanan::all();
        
        // Build query dengan filter (sudah termasuk filter status open & closed)
        $query = $this->buildLaporanQuery($request);
        
        // Ambil data laporan dengan pagination
        $daftar = $query->paginate(15);
        
        // Variabel untuk dikirim ke view
        $judul = "Export";
        $module = "Export";
        $menu = "Export";
        $menu_url = "/logbook/export/daftar";
        $submenu = "Daftar";

        return view('logbook.export.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('submenu', $submenu)
            ->with('daftar', $daftar)
            ->with('fasilitas', $fasilitas)
            ->with('layanan', $layanan);
    }

    /**
     * Function untuk mendapatkan data laporan dengan filter yang diterapkan.
     *
     * Method: GET
     * URL: /logbook/export/get-data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $query = $this->buildLaporanQuery($request);
        
        $daftar = $query->paginate(15);
        
        // Jika request AJAX
        if ($request->ajax() || $request->has('ajax')) {
            // Generate HTML untuk table body
            $html = '';
            
            if ($daftar->isEmpty()) {
                $html = '<tr>
                            <td colspan="10" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data laporan</h5>
                                    <p class="text-muted">Tidak ada riwayat laporan yang tersedia dengan filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>';
            } else {
                foreach ($daftar as $index => $satu) {
                    $jenisLabel = $satu->jenis == 1 
                        ? '<span class="badge badge-warning">Gangguan Peralatan</span>'
                        : '<span class="badge badge-info">Gangguan Non Peralatan</span>';
                    
                    $waktu = $satu->waktu ? \Carbon\Carbon::parse($satu->waktu)->format('d/m/Y H:i') : '-';
                    
                    $html .= '<tr>
                                <td><center>' . (($daftar->currentPage() - 1) * $daftar->perPage() + $index + 1) . '</center></td>
                                <td>' . strtoupper($satu->no_laporan) . '</td>
                                <td>' . strtoupper($satu->layanan->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->fasilitas->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk1->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk2->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk3->nama ?? '-') . '</td>
                                <td>' . $jenisLabel . '</td>
                                <td>' . $waktu . '</td>
                                <td><center>' . $satu->status_label . '</center></td>
                            </tr>';
                }
            }
            
            // Generate pagination HTML
            $pagination = $daftar->appends($request->query())->links()->render();
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'total' => $daftar->total(),
                'from' => $daftar->firstItem() ?? 0,
                'to' => $daftar->lastItem() ?? 0,
                'current_page' => $daftar->currentPage(),
                'last_page' => $daftar->lastPage()
            ]);
        }
        
        // Jika bukan AJAX, return data biasa (untuk fallback)
        return response()->json($daftar);
    }

    /**
     * Function untuk mengekspor data laporan ke file Excel.
     *
     * Method: GET
     * URL: /logbook/export/export
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try {
            // Validasi
            $request->validate([
                'tanggal_mulai' => 'nullable|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'fasilitas_id' => 'nullable|exists:fasilitas,id',
                'layanan_id' => 'nullable|exists:layanan,id',
                'status' => 'nullable|integer',
                'jenis' => 'nullable|integer',
                'columns' => 'required|string'
            ]);

            // Parse kolom yang dipilih
            $selectedColumns = explode(',', $request->columns);
            
            // Ambil data
            $query = $this->buildLaporanQuery($request);
            $data = $query->get();

            // Buat spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul worksheet
            $sheet->setTitle('Laporan Gangguan');

            // Header kolom
            $headers = $this->getHeaders($selectedColumns);
            $sheet->fromArray($headers, null, 'A1');

            // Style header
            $headerRange = 'A1:' . chr(64 + count($headers)) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            // Data rows
            $row = 2;
            foreach ($data as $index => $laporan) {
                $rowData = $this->mapRowData($laporan, $selectedColumns, $index + 1);
                $sheet->fromArray($rowData, null, 'A' . $row);
                $row++;
            }

            // Auto size columns
            foreach (range('A', chr(64 + count($headers))) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Style data area
            if ($row > 2) {
                $dataRange = 'A1:' . chr(64 + count($headers)) . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);
            }

            // Generate filename
            $filename = 'laporan_gangguan_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Output file
            $writer = new Xlsx($spreadsheet);
            
            // Set headers untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            \Log::error('Export failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()->with('notif', 'export_gagal');
        }
    }

    /**
     * Get headers berdasarkan kolom yang dipilih
     */
    private function getHeaders($selectedColumns)
    {
        $allHeaders = [
            'no_laporan' => 'NO. LAPORAN',
            'kode_layanan' => 'KODE LAYANAN',
            'nama_layanan' => 'NAMA LAYANAN',
            'jenis_gangguan' => 'JENIS GANGGUAN',
            'waktu_gangguan' => 'WAKTU GANGGUAN',
            'status' => 'STATUS',
            'kondisi_layanan_terakhir' => 'KONDISI LAYANAN TERAKHIR',
            'kode_peralatan' => 'KODE PERALATAN',
            'nama_peralatan' => 'NAMA PERALATAN',
            'deskripsi_gangguan' => 'DESKRIPSI GANGGUAN',
            'jenis_tindaklanjut' => 'JENIS TINDAKLANJUT',
            'waktu_tindaklanjut' => 'WAKTU TINDAKLANJUT',
            'deskripsi_tindaklanjut' => 'DESKRIPSI TINDAKLANJUT',
            'kondisi_peralatan_terakhir' => 'KONDISI PERALATAN TERAKHIR',
            'kode_peralatan_pengganti' => 'KODE PERALATAN PENGGANTI',
            'nama_peralatan_pengganti' => 'NAMA PERALATAN PENGGANTI',
            // Kolom lama
            'layanan' => 'Layanan',
            'fasilitas' => 'Fasilitas',
            'lokasi_t1' => 'Lokasi T.1',
            'lokasi_t2' => 'Lokasi T.2',
            'lokasi_t3' => 'Lokasi T.3',
            'jenis_laporan' => 'Jenis Laporan',
            'waktu_laporan' => 'Waktu Laporan'
        ];

        $headers = ['No']; // Selalu ada nomor urut
        
        foreach ($selectedColumns as $column) {
            if (isset($allHeaders[$column])) {
                $headers[] = $allHeaders[$column];
            }
        }

        return $headers;
    }


    /**
     * Map data row berdasarkan kolom yang dipilih
     */
    private function mapRowData($laporan, $selectedColumns, $no)
    {
        $rowData = [$no]; // Selalu mulai dengan nomor urut

        // Ambil data sesuai logic step5 - MENGGUNAKAN relasi yang sudah ada
        $detailGangguanPeralatan = $laporan->gangguanPeralatan;
        $penggantian = $laporan->tlPenggantianPeralatan;
        $tindaklanjut = null;

        if ($laporan->jenis == 1) {
            // Cek apakah ada penggantian
            if ($penggantian->count() == 0) {
                // Jika tidak ada penggantian, ambil tindaklanjut langsung
                $tindaklanjut = $laporan->tlGangguanPeralatan->sortByDesc('waktu')->first();
            }
        } else {
            // Untuk gangguan non-peralatan
            $tindaklanjut = $laporan->tlGangguanNonPeralatan->sortByDesc('waktu')->first();
        }

        foreach ($selectedColumns as $column) {
            switch ($column) {
                case 'no_laporan':
                    $rowData[] = strtoupper($laporan->no_laporan);
                    break;
                    
                case 'kode_layanan':
                    $rowData[] = strtoupper($laporan->layanan->kode ?? '-');
                    break;
                    
                case 'nama_layanan':
                    $rowData[] = strtoupper($laporan->layanan->nama ?? '-');
                    break;
                    
                case 'jenis_gangguan':
                    $rowData[] = $laporan->jenis == 1 ? 'Gangguan Peralatan' : 'Gangguan Non Peralatan';
                    break;
                    
                case 'waktu_gangguan':
                    $rowData[] = $laporan->waktu ? Carbon::parse($laporan->waktu)->format('d/m/Y H:i') : '-';
                    break;
                    
                case 'status':
                    $rowData[] = $this->getStatusLabel($laporan->status);
                    break;
                    
                case 'kondisi_layanan_terakhir':
                    // Ambil kondisi layanan dari tabel laporan kolom kondisi_layanan_temp
                    $kondisiLayanan = $laporan->kondisi_layanan_temp ?? null;
                    $rowData[] = $this->getKondisiLayananLabel($kondisiLayanan);
                    break;
                    
                case 'kode_peralatan':
                    if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                        $kodes = $detailGangguanPeralatan->pluck('peralatan.kode')->filter()->toArray();
                        $rowData[] = implode(', ', $kodes) ?: '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'nama_peralatan':
                    if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                        $names = $detailGangguanPeralatan->pluck('peralatan.nama')->filter()->toArray();
                        $rowData[] = implode(', ', $names) ?: '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'deskripsi_gangguan':
                    if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                        $descriptions = $detailGangguanPeralatan->pluck('keterangan')->filter()->toArray();
                        $rowData[] = implode('; ', $descriptions) ?: '-';
                    } elseif ($laporan->jenis == 0 && $laporan->gangguanNonPeralatan) {
                        $rowData[] = $laporan->gangguanNonPeralatan->keterangan ?? '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'jenis_tindaklanjut':
                    if ($penggantian->count() > 0) {
                        $rowData[] = 'Penggantian';
                    } elseif ($tindaklanjut) {
                        if ($laporan->jenis == 1) {
                            $jenis = $tindaklanjut->jenis_tindaklanjut ?? null;
                            if ($jenis == config('constants.jenis_tindaklanjut.perbaikan')) {
                                $rowData[] = 'Perbaikan';
                            } else {
                                $rowData[] = 'Tindak Lanjut Peralatan';
                            }
                        } else {
                            $rowData[] = 'Tindak Lanjut Non Peralatan';
                        }
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'waktu_tindaklanjut':
                    $waktuTL = null;
                    if ($penggantian->count() > 0) {
                        $waktuTL = $penggantian->first()->waktu_pasang ?? null;
                    } elseif ($tindaklanjut) {
                        $waktuTL = $tindaklanjut->waktu ?? null;
                    }
                    $rowData[] = $waktuTL ? Carbon::parse($waktuTL)->format('d/m/Y H:i') : '-';
                    break;
                    
                case 'deskripsi_tindaklanjut':
                    if ($penggantian->count() > 0) {
                        $descriptions = $penggantian->pluck('tindaklanjut.deskripsi')->filter()->toArray();
                        $rowData[] = implode('; ', $descriptions) ?: '-';
                    } elseif ($tindaklanjut) {
                        $rowData[] = $tindaklanjut->deskripsi ?? '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'kondisi_peralatan_terakhir':
                    // Ambil kondisi peralatan dari tindaklanjut
                    $kondisiPeralatan = null;
                    if ($penggantian->count() > 0) {
                        $kondisiPeralatan = $penggantian->last()->tindaklanjut->kondisi ?? null;
                    } elseif ($tindaklanjut && $laporan->jenis == 1) {
                        $kondisiPeralatan = $tindaklanjut->kondisi ?? null;
                    }
                    $rowData[] = $this->getKondisiGangguanPeralatanLabel($kondisiPeralatan);
                    break;
                                
                case 'kode_peralatan_pengganti':
                    if ($penggantian->count() > 0) {
                        $kodes = $penggantian->pluck('peralatanBaru.kode')->filter()->toArray();
                        $rowData[] = implode(', ', $kodes) ?: '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;
                    
                case 'nama_peralatan_pengganti':
                    if ($penggantian->count() > 0) {
                        $names = $penggantian->pluck('peralatanBaru.nama')->filter()->toArray();
                        $rowData[] = implode(', ', $names) ?: '-';
                    } else {
                        $rowData[] = '-';
                    }
                    break;

                // Kolom lama tetap ada
                case 'layanan':
                    $rowData[] = strtoupper($laporan->layanan->nama ?? '-');
                    break;
                    
                case 'fasilitas':
                    $rowData[] = strtoupper($laporan->layanan->fasilitas->nama ?? '-');
                    break;
                    
                case 'lokasi_t1':
                    $rowData[] = strtoupper($laporan->layanan->LokasiTk1->nama ?? '-');
                    break;
                    
                case 'lokasi_t2':
                    $rowData[] = strtoupper($laporan->layanan->LokasiTk2->nama ?? '-');
                    break;
                    
                case 'lokasi_t3':
                    $rowData[] = strtoupper($laporan->layanan->LokasiTk3->nama ?? '-');
                    break;
                    
                case 'jenis_laporan':
                    $rowData[] = $laporan->jenis == 1 ? 'Gangguan Peralatan' : 'Gangguan Non Peralatan';
                    break;
                    
                case 'waktu_laporan':
                    $rowData[] = $laporan->waktu ? Carbon::parse($laporan->waktu)->format('d/m/Y H:i') : '-';
                    break;
                    
                default:
                    $rowData[] = '-';
                    break;
            }
        }

        return $rowData;
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        switch ($status) {
            case config('constants.status_laporan.draft'):
                return 'Draft';
            case config('constants.status_laporan.open'):
                return 'Open';
            case config('constants.status_laporan.closed'):
                return 'Closed';
            default:
                return 'Tidak Diketahui';
        }
    }


    // Method untuk mendapatkan layanan berdasarkan fasilitas (untuk AJAX)
    public function getLayananByFasilitas(Request $request)
    {
        $layanan = Layanan::where('fasilitas_id', $request->fasilitas_id)->get();
        return response()->json($layanan);
    }

    // Method untuk build query laporan dengan filter
    private function buildLaporanQuery(Request $request)
    {
        $query = Laporan::with([
        'layanan.fasilitas',
        'layanan.LokasiTk1',
        'layanan.LokasiTk2', 
        'layanan.LokasiTk3',
        'gangguanNonPeralatan',
        'gangguanPeralatan.peralatan',
        'tlGangguanPeralatan.peralatan',
        'tlGangguanNonPeralatan',
        'tlPenggantianPeralatan.peralatanLama',
        'tlPenggantianPeralatan.peralatanBaru',
        'tlPenggantianPeralatan.tindaklanjut'
    ]);

        // FILTER WAJIB: Hanya tampilkan laporan dengan status OPEN (2) dan CLOSED (3)
        $query->whereIn('status', [
            config('constants.status_laporan.open'),    // 2
            config('constants.status_laporan.closed')   // 3
        ]);

        // Filter berdasarkan fasilitas
        if ($request->filled('fasilitas_id')) {
            $query->whereHas('layanan', function($q) use ($request) {
                $q->where('fasilitas_id', $request->fasilitas_id);
            });
        }

        // Filter berdasarkan layanan
        if ($request->filled('layanan_id')) {
            $query->where('layanan_id', $request->layanan_id);
        }

        // Filter berdasarkan status (hanya dari status yang diizinkan: open & closed)
        if ($request->filled('status')) {
            // Pastikan status yang dipilih adalah open (2) atau closed (3)
            $allowedStatuses = [
                config('constants.status_laporan.open'),    // 2
                config('constants.status_laporan.closed')   // 3
            ];
            
            if (in_array((int)$request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
        }

        // Filter berdasarkan jenis laporan
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter berdasarkan rentang waktu
        if ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->where('waktu', '>=', $tanggalMulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
            $query->where('waktu', '<=', $tanggalSelesai);
        }

        return $query->orderBy('waktu', 'desc');
    }

    /**
     * Get kondisi layanan label berdasarkan konstanta
     */
    private function getKondisiLayananLabel($kondisi)
    {
        if ($kondisi === true || $kondisi === 1 || $kondisi === '1') {
            return 'Serviceable';
        } elseif ($kondisi === false || $kondisi === 0 || $kondisi === '0') {
            return 'Unserviceable';
        } else {
            return '-';
        }
    }

    /**
     * Get kondisi gangguan peralatan label berdasarkan konstanta
     */
    private function getKondisiGangguanPeralatanLabel($kondisi)
    {
        if ($kondisi === '1' || $kondisi === 1) {
            return 'Beroperasi';
        } elseif ($kondisi === '0' || $kondisi === 0) {
            return 'Gangguan';
        } else {
            return '-';
        }
    }

}
