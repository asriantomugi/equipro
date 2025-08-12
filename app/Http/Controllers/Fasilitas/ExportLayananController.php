<?php

namespace App\Http\Controllers\Fasilitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fasilitas;
use App\Models\Layanan;
use App\Models\DaftarPeralatanLayanan;
use App\Models\Peralatan;
use App\Models\LokasiTk1;
use App\Models\LokasiTk2;
use App\Models\LokasiTk3;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportLayananController extends Controller
{
    public function daftar()
    {
        $fasilitas = Fasilitas::orderBy('nama', 'asc')->get();
        $lokasi_tk_1 = LokasiTk1::orderBy('nama', 'asc')->get();

         // Variabel untuk dikirim ke view
        $judul = "Export";
        $module = "Fasilitas";
        $menu = "Export";
        $menu_url = "/layanan/export/daftar";

        return view('fasilitas.layanan.export.daftar')
            ->with('judul', $judul)
            ->with('module', $module)
            ->with('menu', $menu)
            ->with('menu_url', $menu_url)
            ->with('fasilitas', $fasilitas)
            ->with('lokasi_tk_1', $lokasi_tk_1);
    }

public function getData(Request $request)
{
    // Query dasar
    $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3']);

    // Filter berdasarkan fasilitas
    if ($request->fasilitas_id) {
        $query->where('fasilitas_id', $request->fasilitas_id);
    }

    // Filter berdasarkan lokasi tingkat 1
    if ($request->lokasi_tk_1_id) {
        $query->where('lokasi_tk_1_id', $request->lokasi_tk_1_id);
    }

    // Filter berdasarkan lokasi tingkat 2
    if ($request->lokasi_tk_2_id) {
        $query->where('lokasi_tk_2_id', $request->lokasi_tk_2_id);
    }

    // Filter berdasarkan lokasi tingkat 3
    if ($request->lokasi_tk_3_id) {
        $query->where('lokasi_tk_3_id', $request->lokasi_tk_3_id);
    }

    // Filter berdasarkan kondisi
    if ($request->kondisi !== null && $request->kondisi !== '') {
        $query->where('kondisi', $request->kondisi);
    }

    // Filter berdasarkan status
    if ($request->status !== null && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Filter pencarian
    if ($request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('kode', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%")
              ->orWhereHas('fasilitas', function($subq) use ($search) {
                  $subq->where('nama', 'like', "%{$search}%")
                       ->orWhere('kode', 'like', "%{$search}%");
              });
        });
    }

    // Urutkan data
    $query->orderBy('created_at', 'desc');

    // Pagination
    $data = $query->paginate(25);
    
    // Jika request AJAX
    if ($request->ajax()) {
        $html = '';
        $no = ($data->currentPage() - 1) * $data->perPage() + 1;
        
        foreach ($data as $layanan) {
            $kondisiBadge = $layanan->kondisi == config('constants.kondisi_layanan.serviceable')
                ? '<span class="badge bg-success">SERVICEABLE</span>'
                : '<span class="badge bg-danger">UNSERVICEABLE</span>';
            
            $statusBadge = '';
            if ($layanan->status == 0) {
                $statusBadge = '<span class="badge bg-danger">TIDAK AKTIF</span>';
            } elseif ($layanan->status == 1) {
                $statusBadge = '<span class="badge bg-success">AKTIF</span>';
            } elseif ($layanan->status == 2) {
                $statusBadge = '<span class="badge bg-warning">DRAFT</span>';
            }

            $html .= '<tr>';
            $html .= '<td><center>' . $no++ . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->kode) . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->nama) . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->fasilitas->nama ?? 'N/A') . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->LokasiTk1->nama ?? 'N/A') . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->LokasiTk2->nama ?? 'N/A') . '</center></td>';
            $html .= '<td><center>' . strtoupper($layanan->LokasiTk3->nama ?? 'N/A') . '</center></td>';
            $html .= '<td><center>' . $kondisiBadge . '</center></td>';
            $html .= '<td><center>' . $statusBadge . '</center></td>';
            $html .= '</tr>';
        }

        return response()->json([
            'html' => $html,
            'total' => $data->total(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'pagination' => $data->appends(request()->query())->links()->render()
        ]);
    }

    // Jika bukan AJAX, pastikan untuk mengirimkan data ke view
    $fasilitas = Fasilitas::orderBy('nama', 'asc')->get();
    $lokasi_tk_1 = LokasiTk1::orderBy('nama', 'asc')->get();

    return view('fasilitas.layanan.export.daftar', compact('data', 'fasilitas', 'lokasi_tk_1'));
}


    public function export(Request $request)
    {
        try {
            // Validasi kolom yang dipilih
            if (!$request->columns) {
                return redirect()->back()->with('notif', 'export_gagal');
            }

            $columns = explode(',', $request->columns);
            
            // Mapping kolom database ke label yang user-friendly
            $columnMapping = [
                'kode' => 'Kode Layanan',
                'nama' => 'Nama Layanan',
                'fasilitas_kode' => 'Kode Fasilitas',
                'fasilitas_nama' => 'Nama Fasilitas',
                'lokasi_tk_1_kode' => 'Kode Lokasi Tk I',
                'lokasi_tk_1_nama' => 'Nama Lokasi Tk I',
                'lokasi_tk_2_kode' => 'Kode Lokasi Tk II',
                'lokasi_tk_2_nama' => 'Nama Lokasi Tk II',
                'lokasi_tk_3_kode' => 'Kode Lokasi Tk III',
                'lokasi_tk_3_nama' => 'Nama Lokasi Tk III',
                'kondisi' => 'Kondisi',
                'status' => 'Status'
            ];

            // Query data dengan filter yang sama seperti getData
            $query = Layanan::with(['fasilitas', 'LokasiTk1', 'LokasiTk2', 'LokasiTk3']);

            // Apply filters
            if ($request->fasilitas_id) {
                $query->where('fasilitas_id', $request->fasilitas_id);
            }
            if ($request->lokasi_tk_1_id) {
                $query->where('lokasi_tk_1_id', $request->lokasi_tk_1_id);
            }
            if ($request->lokasi_tk_2_id) {
                $query->where('lokasi_tk_2_id', $request->lokasi_tk_2_id);
            }
            if ($request->lokasi_tk_3_id) {
                $query->where('lokasi_tk_3_id', $request->lokasi_tk_3_id);
            }
            if ($request->kondisi !== null && $request->kondisi !== '') {
                $query->where('kondisi', $request->kondisi);
            }
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }
            if ($request->tanggal_mulai) {
                $query->whereDate('created_at', '>=', $request->tanggal_mulai);
            }
            if ($request->tanggal_selesai) {
                $query->whereDate('created_at', '<=', $request->tanggal_selesai);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $col = 1;
            foreach ($columns as $column) {
                $headerLabel = $columnMapping[$column] ?? strtoupper(str_replace('_', ' ', $column));
                $sheet->setCellValueByColumnAndRow($col, 1, $headerLabel);
                $col++;
            }

            // Set header styles
            $headerRange = 'A1:' . chr(64 + count($columns)) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Fill data
            $row = 2;
            foreach ($data as $layanan) {
                $col = 1;
                foreach ($columns as $column) {
                    $value = $this->getColumnValue($layanan, $column);
                    $sheet->setCellValueByColumnAndRow($col, $row, $value);
                    $col++;
                }
                $row++;
            }

            // Set data styles
            if ($data->count() > 0) {
                $dataRange = 'A2:' . chr(64 + count($columns)) . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Set column widths
            foreach ($columns as $index => $column) {
                $columnLetter = chr(65 + $index);
                $width = $this->getColumnWidth($column);
                $sheet->getColumnDimension($columnLetter)->setWidth($width);
            }

            // Create Excel writer and save
            $writer = new Xlsx($spreadsheet);
            $filename = 'data_layanan_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            \Log::error('Export Layanan Error: ' . $e->getMessage());
            return redirect()->back()->with('notif', 'export_gagal');
        }
    }

    private function getColumnValue($layanan, $column)
    {
        switch ($column) {
            case 'kode':
                return strtoupper($layanan->kode);
            case 'nama':
                return strtoupper($layanan->nama);
            case 'fasilitas_kode':
                return strtoupper($layanan->fasilitas->kode ?? '');
            case 'fasilitas_nama':
                return strtoupper($layanan->fasilitas->nama ?? '');
            case 'lokasi_tk_1_kode':
                return strtoupper($layanan->LokasiTk1->kode ?? '');
            case 'lokasi_tk_1_nama':
                return strtoupper($layanan->LokasiTk1->nama ?? '');
            case 'lokasi_tk_2_kode':
                return strtoupper($layanan->LokasiTk2->kode ?? '');
            case 'lokasi_tk_2_nama':
                return strtoupper($layanan->LokasiTk2->nama ?? '');
            case 'lokasi_tk_3_kode':
                return strtoupper($layanan->LokasiTk3->kode ?? '');
            case 'lokasi_tk_3_nama':
                return strtoupper($layanan->LokasiTk3->nama ?? '');
            case 'kondisi':
                return $layanan->kondisi == config('constants.kondisi_layanan.serviceable') 
                    ? 'SERVICEABLE' : 'UNSERVICEABLE';
            case 'status':
                if ($layanan->status == 0) {
                    return 'TIDAK AKTIF';
                } elseif ($layanan->status == 1) {
                    return 'AKTIF';
                } elseif ($layanan->status == 2) {
                    return 'DRAFT';
                } else {
                    return '';
                }
            default:
                return $layanan->{$column} ?? '';
        }
    }

    private function getColumnWidth($column)
    {
        switch ($column) {
            case 'kode':
            case 'fasilitas_kode':
            case 'lokasi_tk_1_kode':
            case 'lokasi_tk_2_kode':
            case 'lokasi_tk_3_kode':
            case 'kondisi':
            case 'status':
                return 15;
            case 'nama':
            case 'fasilitas_nama':
                return 25;
            case 'lokasi_tk_1_nama':
            case 'lokasi_tk_2_nama':
            case 'lokasi_tk_3_nama':
                return 20;
            default:
                return 15;
        }
    }

    // Method untuk mendapatkan lokasi tingkat 2 berdasarkan tingkat 1
    public function getLokasiTk2ByTk1(Request $request)
    {
        $lokasi_tk_2 = LokasiTk2::where('lokasi_tk_1_id', $request->lokasi_tk_1_id)
                              ->select('id', 'kode', 'nama')
                              ->orderBy('nama')
                              ->get();
        
        return response()->json($lokasi_tk_2);
    }

    // Method untuk mendapatkan lokasi tingkat 3 berdasarkan tingkat 2
    public function getLokasiTk3ByTk2(Request $request)
    {
        $lokasi_tk_3 = LokasiTk3::where('lokasi_tk_2_id', $request->lokasi_tk_2_id)
                              ->select('id', 'kode', 'nama')
                              ->orderBy('nama')
                              ->get();
        
        return response()->json($lokasi_tk_3);
    }

    // Method untuk mendapatkan layanan berdasarkan fasilitas (jika diperlukan)
    public function getLayananByFasilitas(Request $request)
    {
        $layanan = Layanan::where('fasilitas_id', $request->fasilitas_id)
                        ->where('status', '!=', 2) // Exclude draft
                        ->select('id', 'nama')
                        ->orderBy('nama')
                        ->get();
        
        return response()->json($layanan);
    }
}