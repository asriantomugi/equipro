@extends('dashboard.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Chart.js with a stable and compatible version -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Script loaded');
        
        // Test Chart.js
        console.log('Chart.js available:', typeof Chart !== 'undefined');
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js version:', Chart.version);
        }
        
        // Status constants from PHP
        const STATUS_OPEN = {{ isset($STATUS_OPEN) ? $STATUS_OPEN : 2 }};
        const STATUS_CLOSED = {{ isset($STATUS_CLOSED) ? $STATUS_CLOSED : 3 }};
        
        console.log('Status constants:', {
            open: STATUS_OPEN,
            closed: STATUS_CLOSED
        });
        
        // Redirect functions
        function redirectToLaporan(status = null) {
            let url = '/dashboard/laporandaftar?';
            if (status && status !== 'all') {
                url += 'status=' + status + '&';
            }
            @if(isset($tanggal_mulai) && isset($tanggal_selesai))
                url += 'tanggal_mulai={{ $tanggal_mulai }}&tanggal_selesai={{ $tanggal_selesai }}';
            @endif
            window.location.href = url;
        }

        function redirectToLaporanFasilitas(fasilitasId = null) {
            let url = '/dashboard/laporandaftar?';
            if (fasilitasId) {
                url += 'fasilitas_id=' + fasilitasId + '&';
            }
            @if(isset($tanggal_mulai) && isset($tanggal_selesai))
                url += 'tanggal_mulai={{ $tanggal_mulai }}&tanggal_selesai={{ $tanggal_selesai }}';
            @endif
            window.location.href = url;
        }
        
        // Create main chart
        @if(isset($fasilitas) && count($fasilitas) > 0)
            setTimeout(function() {
                console.log('📊 Creating main chart...');
                
                const canvas = document.getElementById('chartLaporan');
                console.log('Canvas found:', !!canvas);
                
                if (canvas && typeof Chart !== 'undefined') {
                    const ctx = canvas.getContext('2d');
                    
                    const totalOpen = {{ isset($total_open) ? $total_open : 0 }};
                    const totalClose = {{ isset($total_close) ? $total_close : 0 }};
                    
                    console.log('Chart totals:', {
                        open: totalOpen,
                        close: totalClose
                    });
                    
                    const chartData = {
                        labels: ['Open', 'Close'],
                        datasets: [{
                            data: [totalOpen, totalClose],
                            backgroundColor: ['#dc3545', '#28a745'], // Danger, Success
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    };
                    
                    console.log('Chart data:', chartData);
                    
                    const chart = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                    
                    console.log('✅ Main chart created:', chart);
                }
            }, 500);
        @endif
        
        // Create facility chart
        @if(isset($laporanPerFasilitas) && count($laporanPerFasilitas) > 0)
            setTimeout(function() {
                console.log('📊 Creating facility chart...');
                
                const canvas = document.getElementById('chartLaporanFasilitas');
                console.log('Facility canvas found:', !!canvas);
                
                if (canvas && typeof Chart !== 'undefined') {
                    const ctx = canvas.getContext('2d');
                    
                    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'];
                    
                    const chartData = {
                        labels: [
                            @foreach($laporanPerFasilitas as $laporan)
                                '{{ $laporan->kode }}'{{ $loop->last ? '' : ',' }}
                            @endforeach
                        ],
                        datasets: [{
                            data: [
                                @foreach($laporanPerFasilitas as $laporan)
                                    {{ $laporan->total_laporan }}{{ $loop->last ? '' : ',' }}
                                @endforeach
                            ],
                            backgroundColor: colors,
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    };
                    
                    console.log('Facility chart data:', chartData);
                    
                    const chart = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });
                    
                    console.log('✅ Facility chart created:', chart);
                    
                    // Store chart colors for boxes
                    window.facilityColors = colors;
                    
                    // Apply colors to facility boxes
                    const facilityBoxes = document.querySelectorAll('[data-status^="fasilitas_"]');
                    facilityBoxes.forEach(function(box, index) {
                        if (index < colors.length) {
                            // Remove existing background classes
                            box.classList.remove('bg-light');
                            // Apply custom background color
                            box.style.backgroundColor = colors[index];
                            box.style.color = '#fff';
                            // Ensure text is readable
                            const inner = box.querySelector('.inner');
                            if (inner) {
                                inner.style.color = '#fff';
                            }
                        }
                    });
                }
            }, 700);
        @endif
        
        // Event listeners for charts only
        setTimeout(function() {
            console.log('Charts are clickable, boxes are display only');
        }, 1000);
    });
  </script>

  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    .wrapper {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .content-wrapper {
      flex: 1;
    }

    .main-footer {
      position: relative;
      clear: both;
      margin-top: 20px;
      background: #f4f6f9;
      padding: 15px;
      text-align: left;
      font-size: 14px;
      color: #444;
    }

    .clickable-title {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .clickable-title:hover {
      color: #ffffff !important;
      text-shadow: 0 0 5px rgba(255,255,255,0.5);
    }

    .chart-container,
    .chart-container-fasilitas {
      position: relative;
      height: 450px;
      width: 100%;
    }

    .chart-container canvas,
    .chart-container-fasilitas canvas {
      width: 100% !important;
      height: 100% !important;
    }

    .clickable-box {
      cursor: pointer;
    }

    .clickable-box:hover {
      transform: scale(1.02);
      transition: transform 0.2s ease;
    }

    /* Additional styles for alignment */
    .row {
      margin-bottom: 20px;
    }

    /* Style untuk small-box agar tingginya konsisten */
    .small-box {
      margin-bottom: 15px;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Style untuk text fasilitas agar lebih mudah dibaca */
    .facility-text {
      font-size: 12px;
      line-height: 1.2;
      word-wrap: break-word;
    }

    /* Style for facility boxes with custom colors */
    .facility-box-custom {
      border-radius: 5px;
    }

    /* Style untuk informasi periode filter */
    .alert {
      border-radius: 8px;
      border: none;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .alert-info {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      color: #0c5460;
    }

    .alert-primary {
      background: linear-gradient(135deg, #cce7ff 0%, #b3d9ff 100%);
      color: #004085;
    }
  </style>
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">
    <!-- Filter Form -->
    <div class="row mb-4">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form class="form-horizontal needs-validation"
                  action="{{ route('dashboard.laporan.filter') }}"
                  method="post"
                  enctype="multipart/form-data"
                  novalidate>
              @csrf
              <div class="row">
                <div class="col-lg-3">
                  <label>Dari Tanggal <span class="text-danger">*</span></label>
                  <input type="date"
                         class="form-control"
                         name="tanggal_mulai"
                         id="tanggal_mulai"
                         value="{{ $tanggal_mulai ?? '' }}"
                         required>
                  <div class="invalid-feedback">Tanggal mulai harus diisi</div>
                </div>

                <div class="col-lg-3">
                  <label>Sampai Tanggal <span class="text-danger">*</span></label>
                  <input type="date"
                         class="form-control"
                         name="tanggal_selesai"
                         id="tanggal_selesai"
                         value="{{ $tanggal_selesai ?? '' }}"
                         required>
                  <div class="invalid-feedback">Tanggal selesai harus diisi</div>
                </div>
              </div>
              <br>
              <div class="card-footer">
                <button type="submit"
                        class="btn btn-primary btn-sm float-right">
                  <i class="fas fa-filter"></i> Filter
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Informasi Status Filter -->
    @if(isset($show_all_data) && $show_all_data)
      <div class="row">
        <div class="col-12">
          <div class="alert alert-info alert-dismissible mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Semua Data Laporan</strong> - Gunakan filter tanggal untuk melihat periode tertentu
          </div>
        </div>
      </div>
    @elseif(isset($tanggal_mulai) && isset($tanggal_selesai))
      <div class="row">
        <div class="col-12">
          <div class="alert alert-primary mb-3">
            <i class="fas fa-calendar-alt mr-2"></i>
            <strong>Periode Laporan:</strong> {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d F Y') }} sampai {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d F Y') }}
          </div>
        </div>
      </div>
    @endif

    <!-- Dashboard Content -->
    @if(isset($fasilitas) && count($fasilitas) > 0)
      @php
        $total_open = $fasilitas->sum('laporan_open');
        $total_close = $fasilitas->sum('laporan_close');
        $total_laporan = $total_open + $total_close;
      @endphp

      <!-- MENU LAPORAN -->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary mb-4">
            <div class="card-header">
              <h3 class="card-title">MENU LAPORAN</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="chart-container">
                    <canvas id="chartLaporan"></canvas>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="small-box bg-info" data-status="all">
                    <div class="inner">
                      <h3>{{ $total_laporan }}</h3>
                      <p>TOTAL LAPORAN</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-clipboard-list"></i>
                    </div>
                  </div>

                  <div class="small-box bg-danger" data-status="open">
                    <div class="inner">
                      <h3>{{ $total_open }}</h3>
                      <p>OPEN</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-exclamation-triangle"></i>
                    </div>
                  </div>

                  <div class="small-box bg-success" data-status="close">
                    <div class="inner">
                      <h3>{{ $total_close }}</h3>
                      <p>CLOSE</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-check-circle"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Chart Laporan Per Fasilitas --}}
      @if(isset($laporanPerFasilitas) && count($laporanPerFasilitas) > 0)
      <div class="row">
        <div class="col-md-12">
          <div class="card card-info mb-4">
            <div class="card-header">
              <h3 class="card-title">LAPORAN PER FASILITAS</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="chart-container-fasilitas">
                    <canvas id="chartLaporanFasilitas"></canvas>
                  </div>
                </div>
                <div class="col-md-4">
                  @foreach($laporanPerFasilitas as $laporan)
                  <div class="small-box bg-light facility-box-custom" data-status="fasilitas_{{ $laporan->id }}">
                    <div class="inner">
                      <h3>{{ $laporan->total_laporan }}</h3>
                      <p class="facility-text">{{ $laporan->kode }} - {{ $laporan->judul ?? $laporan->nama ?? 'Fasilitas' }}</p>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

    @else
      <div class="row">
        <div class="col-12">
          <div class="card text-center p-4">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h4>Tidak Ada Data Laporan</h4>
            @if(isset($tanggal_mulai) && isset($tanggal_selesai))
              <p class="text-muted">
                Tidak ada data laporan untuk periode {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d F Y') }} sampai {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d F Y') }}.
              </p>
              <p class="text-muted">Silakan coba dengan rentang tanggal yang berbeda.</p>
            @else
              <p class="text-muted">Belum ada data laporan di sistem.</p>
            @endif
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection

@section('scripts')
<!-- Scripts moved to head section -->
@endsection