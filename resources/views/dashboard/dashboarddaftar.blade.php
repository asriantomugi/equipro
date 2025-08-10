@extends('dashboard.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->

      <div class="row">
        <div class="col-lg-12 col-6">
          <div class="card">
           
            <div class="card-body">

              <!-- form filter --> 
              <form class="form-horizontal needs-validation" 
                    action="{{ route('dashboard.dashboarddaftar.filter') }}"
                    method="post" 
                    enctype="multipart/form-data"
                    novalidate>
              @csrf
                
                <!-- Hidden inputs untuk mempertahankan parameter fasilitas dan status -->
                @if(isset($fasilitas_id) && $fasilitas_id)
                  <input type="hidden" name="fasilitas" value="{{ $fasilitas_id }}">
                @endif
                
                @if(isset($status_filter) && $status_filter)
                  <input type="hidden" name="status" value="{{ $status_filter }}">
                @endif

                <div class="row">
                  <!-- field tanggal mulai -->
                  <div class="col-lg-6">
                    <label>Tanggal Mulai <span class="text-danger">*</span></label> 
                    <input type="date" 
                           class="form-control" 
                           name="tanggal_mulai"
                           id="tanggal_mulai"
                           value="{{ isset($tanggal_mulai) ? $tanggal_mulai : '' }}"
                           required>
                    <div class="invalid-feedback">
                      Tanggal mulai harus diisi
                    </div>
                  </div>

                  <!-- field tanggal selesai -->
                  <div class="col-lg-6">
                    <label>Tanggal Selesai <span class="text-danger">*</span></label> 
                    <input type="date" 
                           class="form-control" 
                           name="tanggal_selesai"
                           id="tanggal_selesai"
                           value="{{ isset($tanggal_selesai) ? $tanggal_selesai : '' }}"
                           required>
                    <div class="invalid-feedback">
                      Tanggal selesai harus diisi
                    </div>
                  </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" 
                        class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter</button>
            </div>
            </form>
            <!-- form --> 
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col-lg-12 -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-lg-12 col-6">
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                FASILITAS
              </h3>
              
              <div class="card-tools">
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @if(isset($daftar) && count($daftar) > 0)
                <div class="table-responsive">
                  <!-- Menggunakan ID yang berbeda untuk menghindari konflik dengan DataTable di tail.blade.php -->
                  <table id="layanan-table" class="table table-bordered table-striped">
                    <thead>
                      <tr class="table-condensed">
                        <th style="width: 10px"><center>NO.</center></th>
                        <th><center>KODE</center></th>
                        <th><center>NAMA</center></th>
                        @if(!isset($fasilitas_selected))
                          <th><center>FASILITAS</center></th>
                        @endif
                        <th><center>LOK. TK I</center></th>
                        <th><center>LOK. TK II</center></th>
                        <th><center>LOK. TK III</center></th>
                        <th><center>KONDISI</center></th>
                        <th><center>INDIKATOR PERFORMA (%)</center></th>
                        <th><center>MTTR (menit)</center></th>
                        <th><center>MTBF (menit)</center></th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($daftar as $index => $satu)
                      @php
                        // Menggunakan data yang sudah dihitung dengan rumus yang benar
                        $availability = $satu->availability_percentage ?? 0;
                        $mttr = $satu->mttr ?? 0;
                        $mtbf = $satu->mtbf ?? 0;
                        $total_perbaikan = $satu->total_perbaikan ?? 0;
                        $total_unserviceable = $satu->total_unserviceable ?? 0;
                        $total_waktu_serviceable = $satu->total_waktu_serviceable ?? 0;
                      @endphp
                      <tr class="table-condensed">
                        <td><center>{{ $index + 1 }}</center></td>
                        <td><center>{{ strtoupper($satu->kode ?? '-') }}</center></td>
                        <td><center>{{ strtoupper($satu->nama ?? '-') }}</center></td>
                        
                        @if(!isset($fasilitas_selected))
                          <td><center>{{ strtoupper($satu->fasilitas->kode ?? '-') }}</center></td>
                        @endif
                        
                        <td><center>{{ strtoupper($satu->LokasiTk1->nama ?? '-') }}</center></td>
                        <td><center>{{ strtoupper($satu->LokasiTk2->nama ?? '-') }}</center></td>
                        <td><center>{{ strtoupper($satu->LokasiTk3->nama ?? '-') }}</center></td>

                        @if(isset($satu->kondisi) && $satu->kondisi == config('constants.kondisi_layanan.Serviceable'))
                        <td><center><span class="badge bg-success">SERVICEABLE</span></center></td>
                        @else
                        <td><center><span class="badge bg-danger">UNSERVICEABLE</span></center></td>
                        @endif

                        <!-- INDIKATOR PERFORMA (AVAILABILITY) -->
                        <td><center>
                          @if($availability >= 99)
                            <span class="badge bg-success">{{ number_format($availability, 2, '.', '') }}%</span>
                          @elseif($availability >= 95)
                            <span class="badge bg-warning">{{ number_format($availability, 2, '.', '') }}%</span>
                          @else
                            <span class="badge bg-danger">{{ number_format($availability, 2, '.', '') }}%</span>
                          @endif
                          <small class="d-block text-muted">
                            {{ number_format($total_waktu_serviceable / 60, 1, '.', '') }}h serviceable
                          </small>
                        </center></td>

                      <td><center>
                          @if($total_perbaikan > 0)
                          @if($mttr <= 240) {{-- 4 jam --}}
                          <span class="badge bg-success">{{ number_format($mttr, 0, '.', '') }}</span>
                          @elseif($mttr <= 480) {{-- 8 jam --}}
                          <span class="badge bg-warning">{{ number_format($mttr, 0, '.', '') }}</span>
                          @else
                          <span class="badge bg-danger">{{ number_format($mttr, 0, '.', '') }}</span>
                          @endif
                          <small class="d-block text-muted">{{ $total_perbaikan }} perbaikan</small>
                          @else
                          <span class="badge bg-info">0</span>
                          <small class="d-block text-muted">Tidak ada perbaikan</small>
                          @endif
                          </center></td>

                          <td><center>
                          @if($total_unserviceable > 0)
                          @if($mtbf >= 43200) {{-- 30 hari = 43200 menit --}}
                          <span class="badge bg-success">{{ number_format($mtbf, 0, '.', '') }}</span>
                          @elseif($mtbf >= 10080) {{-- 7 hari = 10080 menit --}}
                          <span class="badge bg-warning">{{ number_format($mtbf, 0, '.', '') }}</span>
                          @else
                          <span class="badge bg-danger">{{ number_format($mtbf, 0, '.', '') }}</span>
                          @endif
                          <small class="d-block text-muted">{{ $total_unserviceable }} unserviceable</small>
                          @elseif($total_waktu_serviceable > 0)
                          <span class="badge bg-success">{{ number_format($mtbf, 0, '.', '') }}</span>
                          <small class="d-block text-muted">Tidak ada kegagalan</small>
                          @else
                          <span class="badge bg-secondary">N/A</span>
                          @endif
                          </center></td>

                      </tr>
                      @endforeach                   
                    </tbody>
                  </table>
                </div>

              @else
                <div class="text-center py-5">
                  <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                  <h5>Tidak Ada Data</h5>
                  @if(isset($fasilitas_selected))
                    <p class="text-muted">Tidak ada layanan yang ditemukan untuk fasilitas <strong>{{ $fasilitas_selected->nama }}</strong></p>
                    @if(isset($status_filter))
                      <p class="text-muted">dengan status <strong>{{ strtoupper($status_filter) }}</strong></p>
                    @endif
                  @else
                    <p class="text-muted">Tidak ada layanan yang ditemukan untuk filter yang dipilih</p>
                  @endif
                  
                  @if(!isset($tanggal_mulai) || !isset($tanggal_selesai))
                    <p class="text-info"><i class="fas fa-info-circle"></i> Silakan pilih rentang tanggal untuk melihat data</p>
                  @endif
                </div>
              @endif
            </div>
            
            <div class="card-footer">
              <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
              
         
          <!-- /.card -->

        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
    
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->

@endsection

@section('tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'tambah_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Layanan baru telah berhasil ditambahkan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'draft_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft layanan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'hapus_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Draft layanan telah berhasil dihapus',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'simpan_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Sukses!',
          body: 'Data layanan telah berhasil disimpan',
          autohide: true,
          delay: 3000
        })
    @elseif(session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Gagal!',
          body: 'Terjadi kesalahan saat menambahkan layanan',
          autohide: true,
          delay: 3000
        })
    @endif
  @endif

  // Validasi tanggal
  document.getElementById('tanggal_selesai').addEventListener('change', function() {
    const startDate = document.getElementById('tanggal_mulai').value;
    const endDate = this.value;
    
    if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
      alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
      this.value = '';
    }
  });

  // Inisialisasi DataTable khusus untuk halaman ini
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable khusus untuk layanan-table (menghindari konflik dengan tail.blade.php)
    @if(isset($daftar) && count($daftar) > 0)
        setTimeout(function() {
          // Cek apakah table layanan-table ada
          if (document.getElementById('layanan-table')) {
            // Destroy jika sudah ada
            if ($.fn.DataTable.isDataTable('#layanan-table')) {
              $('#layanan-table').DataTable().destroy();
            }
            
            // Inisialisasi DataTable untuk layanan-table
            $('#layanan-table').DataTable({
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "paging": true,
              "pageLength": 25,
              "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
              },
              "columnDefs": [
                {
                  "targets": [0],
                  "orderable": false
                },
                {
                  "targets": '_all',
                  "className": 'text-center'
                }
              ],
              "order": [[1, 'asc']]
            });
          }
        }, 500);
    @endif
  });

  // Function untuk refresh halaman
  function refreshPage() {
    window.location.reload();
  }

  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'r') {
      e.preventDefault();
      refreshPage();
    }
    
    if (e.key === 'Escape') {
      history.back();
    }
  });
</script>

<style>
/* Custom styling */
.alert {
  border-left: 5px solid;
}

.alert-info {
  border-left-color: #17a2b8;
}

.card-title {
  font-weight: 600;
}

.badge {
  font-size: 85%;
  padding: 0.375rem 0.5rem;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  border-top: 2px solid #dee2e6;
}

.table-condensed td,
.table-condensed th {
  padding: 0.5rem 0.25rem;
  font-size: 0.9rem;
}

/* Styling khusus untuk metrics */
.metrics-info {
  font-size: 0.8rem;
  margin-top: 2px;
}

.badge-metrics {
  min-width: 60px;
  display: inline-block;
}

/* Tooltip untuk penjelasan rumus */
.tooltip-metrics {
  position: relative;
  cursor: help;
}

.tooltip-metrics:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  background: #333;
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 1000;
}

@media (max-width: 768px) {
  .card-title {
    font-size: 1rem;
  }
  
  .btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
  
  .table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
  }
  
  .metrics-info {
    font-size: 0.7rem;
  }
}

.loading {
  opacity: 0.6;
  pointer-events: none;
}

.table-hover tbody tr:hover {
  background-color: rgba(0,123,255,.075);
}

/* Color coding untuk metrics */
.metrics-excellent {
  background-color: #28a745 !important;
}

.metrics-good {
  background-color: #ffc107 !important;
}

.metrics-poor {
  background-color: #dc3545 !important;
}

.metrics-info-text {
  font-size: 0.75rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

@media print {
  .card-header .card-tools,
  .card-footer,
  .btn,
  .alert {
    display: none !important;
  }
  
  .card {
    border: none !important;
    box-shadow: none !important;
  }
  
  .metrics-info-text {
    display: none !important;
  }
}
</style>

@endsection