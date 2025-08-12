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
        $module = "Logbook";
        $menu = "Export";
        $menu_url = "/logbook/export/daftar";

        return view('logbook.export.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
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
                            <td colspan="10" class="text-center">No data available in table</td>
                        </tr>';
            } else {
                foreach ($daftar as $index => $satu) {
                    $jenisLabel = $satu->jenis == 1 
                        ? '<span class="badge badge-warning">Gangguan Peralatan</span>'
                        : '<span class="badge badge-info">Gangguan Non Peralatan</span>';
                    
                    // Gunakan waktu_open untuk tampilan
                    $waktu_open = $satu->waktu_open ? Carbon::parse($satu->waktu_open)->format('d/m/Y H:i') : '-';
                     $waktu_close = $satu->waktu_close ? Carbon::parse($satu->waktu_close)->format('d/m/Y H:i') : '-';
                    
                    $html .= '<tr>
                                <td><center>' . (($daftar->currentPage() - 1) * $daftar->perPage() + $index + 1) . '</center></td>
                                <td>' . strtoupper($satu->no_laporan) . '</td>
                                <td>' . strtoupper($satu->layanan->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->fasilitas->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk1->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk2->nama ?? '-') . '</td>
                                <td>' . strtoupper($satu->layanan->LokasiTk3->nama ?? '-') . '</td>
                                <td>' . $jenisLabel . '</td>
                                <td>' . $waktu_open . '</td>
                                <td>' . $waktu_close . '</td>
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
     *  Function untuk mengekspor data laporan ke file Excel.
     * Format nomor laporan sebagai text/varchar agar tidak menjadi notasi ilmiah
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
            // PERBAIKAN: Log semua parameter untuk debugging
            \Log::info('Export request received:', $request->all());
            
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
            
            // Validasi dan filter kolom yang valid
            $validColumns = array_keys($this->getColumnDefinitions());
            $selectedColumns = array_intersect($selectedColumns, $validColumns);
            
            if (empty($selectedColumns)) {
                return redirect()->back()->with('notif', 'export_gagal')->with('message', 'Tidak ada kolom valid yang dipilih');
            }
            
            // PERBAIKAN: Ambil data dengan query yang sama persis dengan getData
            $query = $this->buildLaporanQuery($request);
            $data = $query->get();

            // Log jumlah data yang akan di-export
            \Log::info('Export data count:', ['count' => $data->count()]);

            // Buat spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul worksheet
            $sheet->setTitle('Laporan Gangguan');

            // Header kolom dengan urutan yang konsisten
            $headers = $this->getHeaders($selectedColumns);
            $sheet->fromArray($headers, null, 'A1');

            // Debug logging untuk memastikan header dan data sejajar
            \Log::info('Export Headers:', [
                'headers' => $headers,
                'header_count' => count($headers),
                'selected_columns' => $selectedColumns
            ]);

            // Style header
            $headerRange = 'A1:' . $this->getColumnLetter(count($headers)) . '1';
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

            // Identifikasi kolom yang perlu diformat sebagai text
            $textColumns = $this->getTextColumns($selectedColumns, $headers);

            // Data rows dengan urutan yang konsisten
            $row = 2;
            $reportNumber = 1;
            
            foreach ($data as $laporan) {
                // Jika gangguan peralatan dan memiliki lebih dari 1 peralatan
                if ($laporan->jenis == 1 && $laporan->gangguanPeralatan->count() > 1) {
                    // Tampilkan setiap peralatan dalam row terpisah
                    foreach ($laporan->gangguanPeralatan as $indexPeralatan => $gangguanPeralatan) {
                        $rowData = $this->mapRowDataPerPeralatan($laporan, $selectedColumns, $reportNumber, $gangguanPeralatan, $indexPeralatan);
                        
                        // Validasi jumlah kolom
                        if (count($rowData) !== count($headers)) {
                            \Log::error('Column count mismatch (Multiple Equipment):', [
                                'laporan_id' => $laporan->id,
                                'peralatan_index' => $indexPeralatan,
                                'header_count' => count($headers),
                                'row_data_count' => count($rowData),
                                'headers' => $headers,
                                'row_data' => $rowData
                            ]);
                            
                            // Sesuaikan jumlah kolom jika tidak sama
                            $rowData = $this->adjustColumnCount($rowData, count($headers));
                        }
                        
                        // Set data dengan format yang tepat
                        $this->setRowDataWithFormat($sheet, $rowData, $row, $textColumns);
                        
                        // Style data row
                        $dataRange = 'A' . $row . ':' . $this->getColumnLetter(count($headers)) . $row;
                        $sheet->getStyle($dataRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN
                                ]
                            ]
                        ]);
                        
                        $row++;
                    }
                } else {
                    // Tampilkan normal untuk gangguan non-peralatan atau peralatan tunggal
                    $rowData = $this->mapRowData($laporan, $selectedColumns, $reportNumber);
                    
                    // Validasi jumlah kolom
                    if (count($rowData) !== count($headers)) {
                        \Log::error('Column count mismatch (Single Equipment/Non-Equipment):', [
                            'laporan_id' => $laporan->id,
                            'header_count' => count($headers),
                            'row_data_count' => count($rowData),
                            'headers' => $headers,
                            'row_data' => $rowData
                        ]);
                        
                        // Sesuaikan jumlah kolom jika tidak sama
                        $rowData = $this->adjustColumnCount($rowData, count($headers));
                    }
                    
                    //Set data dengan format yang tepat
                    $this->setRowDataWithFormat($sheet, $rowData, $row, $textColumns);
                    
                    // Style data row
                    $dataRange = 'A' . $row . ':' . $this->getColumnLetter(count($headers)) . $row;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ]
                        ]
                    ]);
                    
                    $row++;
                }
                
                // Tambahkan row kosong setelah setiap laporan (jarak antar laporan)
                $row++;
                $reportNumber++;
            }

            // Auto size columns
            foreach (range('A', $this->getColumnLetter(count($headers))) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
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
     * Function untuk menyesuaikan jumlah kolom jika tidak sejajar
     */
    private function adjustColumnCount($rowData, $expectedCount)
    {
        $currentCount = count($rowData);
        
        if ($currentCount < $expectedCount) {
            // Jika kurang, tambahkan kolom kosong
            for ($i = $currentCount; $i < $expectedCount; $i++) {
                $rowData[] = '-';
            }
        } elseif ($currentCount > $expectedCount) {
            // Jika lebih, potong array
            $rowData = array_slice($rowData, 0, $expectedCount);
        }
        
        return $rowData;
    }

    /**
     * Get column letter dari index (A, B, C, ..., AA, AB, dst)
     */
    private function getColumnLetter($index)
    {
        if ($index <= 26) {
            return chr(64 + $index);
        } else {
            // Untuk kolom > 26 (AA, AB, AC, dst)
            $first = chr(64 + intval(($index - 1) / 26));
            $second = chr(65 + (($index - 1) % 26));
            return $first . $second;
        }
    }

    /**
     *  Get headers berdasarkan kolom yang dipilih
     * Menggunakan urutan yang konsisten dengan definisi kolom
     */
    private function getHeaders($selectedColumns)
    {
        $columnDefinitions = $this->getColumnDefinitions();
        
        // Mulai dengan nomor urut
        $headers = ['NO'];
        
        // Tambahkan header sesuai urutan yang dipilih - mengikuti urutan definisi kolom
        foreach ($selectedColumns as $column) {
            if (isset($columnDefinitions[$column])) {
                $headers[] = $columnDefinitions[$column];
            }
        }

        return $headers;
    }

    /**
     * Identifikasi kolom yang perlu diformat sebagai text
     */
    private function getTextColumns($selectedColumns, $headers)
    {
        $textColumnKeys = [
            'no_laporan',
        ];
        
        $textColumns = [];
        $headerIndex = 1; // Mulai dari 1 karena kolom 0 adalah NO (nomor urut)
        
        foreach ($selectedColumns as $column) {
            if (in_array($column, $textColumnKeys)) {
                $textColumns[] = $headerIndex;
            }
            $headerIndex++;
        }
        
        return $textColumns;
    }

    /**
     * Set data row dengan format yang tepat
     */
    private function setRowDataWithFormat($sheet, $rowData, $rowNumber, $textColumns)
    {
        $columnIndex = 0;
        
        foreach ($rowData as $index => $value) {
            $cellCoordinate = $this->getColumnLetter($columnIndex + 1) . $rowNumber;
            
            // Jika kolom ini harus diformat sebagai text
            if (in_array($columnIndex, $textColumns)) {
                // Format sebagai text dan set value
                $sheet->getStyle($cellCoordinate)->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                
                // apostrof di awal untuk memaksa Excel menganggapnya sebagai text
                $sheet->setCellValueExplicit($cellCoordinate, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            } else {
                // Set nilai normal
                $sheet->setCellValue($cellCoordinate, $value);
            }
            
            $columnIndex++;
        }
    }

    /**
     * Map data row berdasarkan kolom yang dipilih (untuk laporan dengan peralatan tunggal atau non-peralatan)
     * Menggunakan urutan yang sama persis dengan headers dan logika data dari kode kedua
     */
    private function mapRowData($laporan, $selectedColumns, $no)
    {
        // Mulai dengan nomor urut - SAMA dengan header
        $rowData = [$no];

        // Ambil data dengan relasi yang tepat
        $detailGangguanPeralatan = $laporan->gangguanPeralatan;
        $penggantian = $laporan->tlPenggantianPeralatan;
        $tindaklanjut = null;

        if ($laporan->jenis == 1) {
            //Untuk gangguan peralatan, ambil tindaklanjut yang tepat
            // Jika ada penggantian, prioritaskan data penggantian
            if ($penggantian->count() > 0) {
                // Data penggantian sudah tersedia di $penggantian
            } else {
                // Jika tidak ada penggantian, ambil tindaklanjut perbaikan
                $tindaklanjut = $laporan->tlGangguanPeralatan->sortByDesc('waktu')->first();
            }
        } else {
            // Untuk gangguan non-peralatan
            $tindaklanjut = $laporan->tlGangguanNonPeralatan->sortByDesc('waktu')->first();
        }

        // PERBAIKAN: Proses setiap kolom sesuai urutan yang SAMA PERSIS dengan header
        foreach ($selectedColumns as $column) {
            $rowData[] = $this->getColumnValue($laporan, $column, $detailGangguanPeralatan, $penggantian, $tindaklanjut);
        }

        return $rowData;
    }

    /**
     * Map data row per peralatan (untuk laporan dengan multiple peralatan)
     * Menggunakan urutan yang sama persis dengan headers dan logika data dari kode kedua
     */
    private function mapRowDataPerPeralatan($laporan, $selectedColumns, $no, $gangguanPeralatan, $indexPeralatan)
    {
        // Mulai dengan nomor urut - SAMA dengan header
        $rowData = [$no];

        // Ambil data tindaklanjut untuk peralatan spesifik
        $penggantianPeralatan = $laporan->tlPenggantianPeralatan->where('peralatan_lama_id', $gangguanPeralatan->peralatan_id);
        $tindaklanjutPeralatan = $laporan->tlGangguanPeralatan->where('peralatan_id', $gangguanPeralatan->peralatan_id)->sortByDesc('waktu')->first();

        // PERBAIKAN: Proses setiap kolom sesuai urutan yang SAMA PERSIS dengan header
        foreach ($selectedColumns as $column) {
            $rowData[] = $this->getColumnValuePerPeralatan($laporan, $column, $gangguanPeralatan, $penggantianPeralatan, $tindaklanjutPeralatan);
        }

        return $rowData;
    }

    /**
     *Definisi urutan kolom yang konsisten
     * Urutan ini akan digunakan untuk header dan data agar selalu sejajar
     */
    private function getColumnDefinitions()
    {
        return [
            'no_laporan' => 'NO. LAPORAN',
            'kode_layanan' => 'KODE LAYANAN',
            'nama_layanan' => 'NAMA LAYANAN',
            'jenis_gangguan' => 'JENIS GANGGUAN',
            'waktu_open' => 'WAKTU OPEN',
            'waktu_close' => 'WAKTU CLOSE',
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
            // Kolom lama (legacy) - tetap dipertahankan untuk backward compatibility
            'layanan' => 'LAYANAN',
            'fasilitas' => 'FASILITAS',
            'lokasi_t1' => 'LOKASI T.1',
            'lokasi_t2' => 'LOKASI T.2',
            'lokasi_t3' => 'LOKASI T.3',
            'jenis_laporan' => 'JENIS LAPORAN'
        ];
    }

    /**Function untuk mendapatkan nilai kolom (untuk laporan normal)
     * Mengambil logika data dari kode kedua yang lebih lengkap
     */
    private function getColumnValue($laporan, $column, $detailGangguanPeralatan, $penggantian, $tindaklanjut)
    {
        switch ($column) {
            case 'no_laporan':
                return (string) ($laporan->no_laporan ?? '-');
                
            case 'kode_layanan':
                return strtoupper($laporan->layanan->kode ?? '-');
                
            case 'nama_layanan':
                return strtoupper($laporan->layanan->nama ?? '-');
                
            case 'jenis_gangguan':
                return $laporan->jenis == 1 ? 'Gangguan Peralatan' : 'Gangguan Non Peralatan';
                
            case 'waktu_open':
                return $laporan->waktu_open ? Carbon::parse($laporan->waktu_open)->format('d/m/Y H:i') : '-';
                
            case 'waktu_close':
                return $laporan->waktu_close ? Carbon::parse($laporan->waktu_close)->format('d/m/Y H:i') : '-';
                
            case 'status':
                return $this->getStatusLabel($laporan->status);
                
            case 'kondisi_layanan_terakhir':
                // Ambil kondisi layanan dari tabel laporan kolom kondisi_layanan_temp
                $kondisiLayanan = $laporan->kondisi_layanan_temp ?? null;
                return $this->getKondisiLayananLabel($kondisiLayanan);
                
            case 'kode_peralatan':
                if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                    $kodes = $detailGangguanPeralatan->pluck('peralatan.kode')->filter()->toArray();
                    return strtoupper(implode(', ', $kodes)) ?: '-';
                }
                return '-';
                
            case 'nama_peralatan':
                if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                    $names = $detailGangguanPeralatan->pluck('peralatan.nama')->filter()->toArray();
                    return strtoupper(implode(', ', $names)) ?: '-';
                }
                return '-';
                
            case 'deskripsi_gangguan':
                if ($laporan->jenis == 1 && $detailGangguanPeralatan->count() > 0) {
                    // Gunakan field yang tepat untuk deskripsi gangguan
                    $descriptions = $detailGangguanPeralatan->pluck('deskripsi')->filter()->toArray();
                    return implode('; ', $descriptions) ?: '-';
                } elseif ($laporan->jenis == 0 && $laporan->gangguanNonPeralatan) {
                    // Gunakan field yang tepat untuk gangguan non peralatan
                    return $laporan->gangguanNonPeralatan->deskripsi ?? '-';
                }
                return '-';
                
            case 'jenis_tindaklanjut':
                if ($penggantian->count() > 0) {
                    return 'Penggantian';
                } elseif ($tindaklanjut) {
                    if ($laporan->jenis == 1) {
                        $jenis = $tindaklanjut->jenis_tindaklanjut ?? null;
                        // Sesuaikan dengan konstanta yang benar
                        if ($jenis == 1) { // 1 = perbaikan
                            return 'Perbaikan';
                        } else {
                            return 'Penggantian'; // 0 = penggantian
                        }
                    } else {
                        return 'Tindak Lanjut Non Peralatan';
                    }
                }
                return '-';
                
            case 'waktu_tindaklanjut':
                $waktuTL = null;
                if ($penggantian->count() > 0) {
                    //Ambil waktu dari tindaklanjut penggantian yang pertama
                    $firstPenggantian = $penggantian->first();
                    $waktuTL = $firstPenggantian->waktu_pasang ?? $firstPenggantian->tindaklanjut->waktu ?? null;
                } elseif ($tindaklanjut) {
                    $waktuTL = $tindaklanjut->waktu ?? null;
                }
                return $waktuTL ? Carbon::parse($waktuTL)->format('d/m/Y H:i') : '-';
                
            case 'deskripsi_tindaklanjut':
                if ($penggantian->count() > 0) {
                    // Ambil deskripsi dari relasi tindaklanjut
                    $descriptions = [];
                    foreach ($penggantian as $p) {
                        if ($p->tindaklanjut && $p->tindaklanjut->deskripsi) {
                            $descriptions[] = $p->tindaklanjut->deskripsi;
                        }
                    }
                    return implode('; ', $descriptions) ?: '-';
                } elseif ($tindaklanjut) {
                    return $tindaklanjut->deskripsi ?? '-';
                }
                return '-';
                
            case 'kondisi_peralatan_terakhir':
                // Ambil kondisi peralatan dari tindaklanjut
                $kondisiPeralatan = null;
                if ($penggantian->count() > 0) {
                    $lastPenggantian = $penggantian->last();
                    $kondisiPeralatan = $lastPenggantian->tindaklanjut->kondisi ?? null;
                } elseif ($tindaklanjut && $laporan->jenis == 1) {
                    $kondisiPeralatan = $tindaklanjut->kondisi ?? null;
                }
                return $this->getKondisiGangguanPeralatanLabel($kondisiPeralatan);
                            
            case 'kode_peralatan_pengganti':
                if ($penggantian->count() > 0) {
                    $kodes = $penggantian->pluck('peralatanBaru.kode')->filter()->toArray();
                    return strtoupper(implode(', ', $kodes)) ?: '-';
                }
                return '-';
                
            case 'nama_peralatan_pengganti':
                if ($penggantian->count() > 0) {
                    $names = $penggantian->pluck('peralatanBaru.nama')->filter()->toArray();
                    return strtoupper(implode(', ', $names)) ?: '-';
                }
                return '-';

            // Kolom lama tetap ada
            case 'layanan':
                return strtoupper($laporan->layanan->nama ?? '-');
                
            case 'fasilitas':
                return strtoupper($laporan->layanan->fasilitas->nama ?? '-');
                
            case 'lokasi_t1':
                return strtoupper($laporan->layanan->LokasiTk1->nama ?? '-');
                
            case 'lokasi_t2':
                return strtoupper($laporan->layanan->LokasiTk2->nama ?? '-');
                
            case 'lokasi_t3':
                return strtoupper($laporan->layanan->LokasiTk3->nama ?? '-');
                
            case 'jenis_laporan':
                return $laporan->jenis == 1 ? 'Gangguan Peralatan' : 'Gangguan Non Peralatan';
                
            default:
                return '-';
        }
    }

    /**
     * Function untuk mendapatkan nilai kolom per peralatan
     * Mengambil logika data dari kode kedua yang lebih lengkap
     */
    private function getColumnValuePerPeralatan($laporan, $column, $gangguanPeralatan, $penggantianPeralatan, $tindaklanjutPeralatan)
    {
        switch ($column) {
            case 'no_laporan':
                // Tampilkan nomor laporan untuk semua row peralatan
                return (string) ($laporan->no_laporan ?? '-');
                
            case 'kode_layanan':
                // Tampilkan kode layanan untuk semua row peralatan
                return strtoupper($laporan->layanan->kode ?? '-');
                
            case 'nama_layanan':
                // Tampilkan nama layanan untuk semua row peralatan
                return strtoupper($laporan->layanan->nama ?? '-');
                
            case 'jenis_gangguan':
                // Tampilkan jenis gangguan untuk semua row peralatan
                return 'Gangguan Peralatan';
                
            case 'waktu_open':
                // Tampilkan waktu open untuk semua row peralatan
                return $laporan->waktu_open ? Carbon::parse($laporan->waktu_open)->format('d/m/Y H:i') : '-';
                
            case 'waktu_close':
                // Tampilkan waktu close untuk semua row peralatan
                return $laporan->waktu_close ? Carbon::parse($laporan->waktu_close)->format('d/m/Y H:i') : '-';
                
            case 'status':
                // Tampilkan status untuk semua row peralatan
                return $this->getStatusLabel($laporan->status);
                
            case 'kondisi_layanan_terakhir':
                // Tampilkan kondisi layanan untuk semua row peralatan
                $kondisiLayanan = $laporan->kondisi_layanan_temp ?? null;
                return $this->getKondisiLayananLabel($kondisiLayanan);
                
            case 'kode_peralatan':
                return strtoupper($gangguanPeralatan->peralatan->kode ?? '-');
                
            case 'nama_peralatan':
                return strtoupper($gangguanPeralatan->peralatan->nama ?? '-');
                
            case 'deskripsi_gangguan':
                // PERBAIKAN: Gunakan field yang tepat untuk deskripsi gangguan
                return $gangguanPeralatan->deskripsi ?? $gangguanPeralatan->keterangan ?? '-';
                
            case 'jenis_tindaklanjut':
                if ($penggantianPeralatan->count() > 0) {
                    return 'Penggantian';
                } elseif ($tindaklanjutPeralatan) {
                    $jenis = $tindaklanjutPeralatan->jenis_tindaklanjut ?? null;
                    // PERBAIKAN: Sesuaikan dengan konstanta yang benar
                    if ($jenis == 1) { // 1 = perbaikan
                        return 'Perbaikan';
                    } else {
                        return 'Penggantian'; // 0 = penggantian
                    }
                }
                return '-';
                
            case 'waktu_tindaklanjut':
                $waktuTL = null;
                if ($penggantianPeralatan->count() > 0) {
                    // PERBAIKAN: Ambil waktu dari tindaklanjut penggantian yang pertama
                    $firstPenggantian = $penggantianPeralatan->first();
                    $waktuTL = $firstPenggantian->waktu_pasang ?? $firstPenggantian->tindaklanjut->waktu ?? null;
                } elseif ($tindaklanjutPeralatan) {
                    $waktuTL = $tindaklanjutPeralatan->waktu ?? null;
                }
                return $waktuTL ? Carbon::parse($waktuTL)->format('d/m/Y H:i') : '-';
                
            case 'deskripsi_tindaklanjut':
                if ($penggantianPeralatan->count() > 0) {
                    // PERBAIKAN: Ambil deskripsi dari relasi tindaklanjut
                    $descriptions = [];
                    foreach ($penggantianPeralatan as $p) {
                        if ($p->tindaklanjut && $p->tindaklanjut->deskripsi) {
                            $descriptions[] = $p->tindaklanjut->deskripsi;
                        }
                    }
                    return implode('; ', $descriptions) ?: '-';
                } elseif ($tindaklanjutPeralatan) {
                    return $tindaklanjutPeralatan->deskripsi ?? '-';
                }
                return '-';
                
            case 'kondisi_peralatan_terakhir':
                $kondisiPeralatan = null;
                if ($penggantianPeralatan->count() > 0) {
                    $lastPenggantian = $penggantianPeralatan->last();
                    $kondisiPeralatan = $lastPenggantian->tindaklanjut->kondisi ?? null;
                } elseif ($tindaklanjutPeralatan) {
                    $kondisiPeralatan = $tindaklanjutPeralatan->kondisi ?? null;
                }
                return $this->getKondisiGangguanPeralatanLabel($kondisiPeralatan);
                            
            case 'kode_peralatan_pengganti':
                if ($penggantianPeralatan->count() > 0) {
                    $kodes = $penggantianPeralatan->pluck('peralatanBaru.kode')->filter()->toArray();
                    return strtoupper(implode(', ', $kodes)) ?: '-';
                }
                return '-';
                
            case 'nama_peralatan_pengganti':
                if ($penggantianPeralatan->count() > 0) {
                    $names = $penggantianPeralatan->pluck('peralatanBaru.nama')->filter()->toArray();
                    return strtoupper(implode(', ', $names)) ?: '-';
                }
                return '-';

            // Kolom lama - tampil untuk semua row peralatan
            case 'layanan':
                // Tampilkan nama layanan untuk semua row peralatan
                return strtoupper($laporan->layanan->nama ?? '-');
                
            case 'fasilitas':
                // Tampilkan fasilitas untuk semua row peralatan
                return strtoupper($laporan->layanan->fasilitas->nama ?? '-');
                
            case 'lokasi_t1':
                // Tampilkan lokasi T1 untuk semua row peralatan
                return strtoupper($laporan->layanan->LokasiTk1->nama ?? '-');
                
            case 'lokasi_t2':
                // Tampilkan lokasi T2 untuk semua row peralatan
                return strtoupper($laporan->layanan->LokasiTk2->nama ?? '-');
                
            case 'lokasi_t3':
                // Tampilkan lokasi T3 untuk semua row peralatan
                return strtoupper($laporan->layanan->LokasiTk3->nama ?? '-');
                
            case 'jenis_laporan':
                // Tampilkan jenis laporan untuk semua row peralatan
                return 'Gangguan Peralatan';
                
            default:
                return '-';
        }
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

    /**
     * Method untuk mendapatkan layanan berdasarkan fasilitas (untuk AJAX)
     */
    public function getLayananByFasilitas(Request $request)
    {
        $layanan = Layanan::where('fasilitas_id', $request->fasilitas_id)->get();
        return response()->json($layanan);
    }

    /**
     * Method untuk build query laporan dengan filter
     */
    private function buildLaporanQuery(Request $request)
    {
        // Debug: Log parameter yang diterima
        \Log::info('Filter parameters:', $request->all());
        
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

        // Filter berdasarkan fasilitas - memastikan kondisi yang konsisten
        if ($request->filled('fasilitas_id') && $request->fasilitas_id != '' && $request->fasilitas_id != null) {
            $query->whereHas('layanan', function($q) use ($request) {
                $q->where('fasilitas_id', $request->fasilitas_id);
            });
        }

        // Filter berdasarkan layanan - memastikan kondisi yang konsisten
        if ($request->filled('layanan_id') && $request->layanan_id != '' && $request->layanan_id != null) {
            $query->where('layanan_id', $request->layanan_id);
        }

        // Filter berdasarkan status - memastikan kondisi yang konsisten
        if ($request->filled('status') && $request->status != '' && $request->status != null) {
            // memastikann status yang dipilih adalah open (2) atau closed (3)
            $allowedStatuses = [
                config('constants.status_laporan.open'),    // 2
                config('constants.status_laporan.closed')   // 3
            ];
            
            if (in_array((int)$request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
        }

        //  Filter berdasarkan jenis laporan -memastikan kondisi yang konsisten
        if ($request->filled('jenis') && $request->jenis != '' && $request->jenis != null) {
            $query->where('jenis', $request->jenis);
        }

        //  Filter berdasarkan rentang waktu - GUNAKAN waktu_open
        if ($request->filled('tanggal_mulai') && $request->tanggal_mulai != '' && $request->tanggal_mulai != null) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->where('waktu_open', '>=', $tanggalMulai);
        }

        if ($request->filled('tanggal_selesai') && $request->tanggal_selesai != '' && $request->tanggal_selesai != null) {
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
            $query->where('waktu_close', '<=', $tanggalSelesai); 
        }

        // Debug: Log query SQL
        \Log::info('SQL Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
        return $query->orderBy('waktu_open', 'desc');
    }
}