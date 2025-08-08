@extends('dashboard.main')

@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- =====================  Dashboard ====================== -->
            @php $index = 1; @endphp

            @if(isset($fasilitas) && count($fasilitas) > 0)
                @foreach($fasilitas as $satu)
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title clickable-title" data-fasilitas-id="{{ $satu->id }}">
                                {{ strtoupper($satu->kode) }} - {{ strtoupper($satu->nama) }}
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="row">
                                <!-- chart -->
                                <div class="col-md-6">
                                    <div style="position: relative; height: 250px; cursor: pointer;" class="chart-container" data-fasilitas-id="{{ $satu->id }}">
                                        <canvas id="chart{{ $index }}" style="height: 100%; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div><!-- /.col -->
                                
                                <div class="col-md-6">
                                    <!-- small box -->
                                    <div class="small-box bg-success clickable-box" data-fasilitas-id="{{ $satu->id }}" data-status="serviceable">
                                        <div class="inner">
                                            <h3>{{ $satu->getJlhLayananServ() }}</h3>
                                            <p>SERVICEABLE</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <a href="{{ url('/dashboard/dashboarddaftar?fasilitas=' . $satu->id . '&status=serviceable') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                    </div><!-- /.small-box -->

                                    <!-- small box -->
                                    <div class="small-box bg-danger clickable-box" data-fasilitas-id="{{ $satu->id }}" data-status="unserviceable">
                                        <div class="inner">
                                            <h3>{{ $satu->getJlhLayananUnserv() }}</h3>
                                            <p>UNSERVICEABLE</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <a href="{{ url('/dashboard/dashboarddaftar?fasilitas=' . $satu->id . '&status=unserviceable') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                    </div><!-- /.small-box -->
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div><!-- /.col -->
                @php $index++; @endphp
                @endforeach
            @else
                <!-- No Data Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Tidak Ada Data</h4>
                            <p class="text-muted">Tidak ada data fasilitas yang tersedia.</p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- =====================  End of Dashboard ====================== -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>

<!-- CSS untuk hover effect -->
<style>
.clickable-title {
    cursor: pointer;
    transition: all 0.3s ease;
}

.clickable-title:hover {
    color: #ffffff !important;
    text-shadow: 0 0 5px rgba(255,255,255,0.5);
}

.chart-container:hover {
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.clickable-box:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease;
}
</style>

<!-- Script untuk Chart dan Click Events -->
<script>
console.log('Dashboard script loaded');

// Fungsi untuk redirect ke daftar fasilitas
function redirectToFasilitas(fasilitasId, status = null) {
    let url = '/dashboard/dashboarddaftar?fasilitas=' + fasilitasId;
    if (status) {
        url += '&status=' + status;
    }
    window.location.href = url;
}

// Tunggu sampai semua elemen dimuat
window.addEventListener('load', function() {
    console.log('Window loaded, creating charts...');
    
    // Event listener untuk click pada judul
    document.querySelectorAll('.clickable-title').forEach(function(title) {
        title.addEventListener('click', function(e) {
            e.stopPropagation();
            const fasilitasId = this.getAttribute('data-fasilitas-id');
            redirectToFasilitas(fasilitasId);
        });
    });

    // Event listener untuk click pada chart
    document.querySelectorAll('.chart-container').forEach(function(container) {
        container.addEventListener('click', function(e) {
            e.stopPropagation();
            const fasilitasId = this.getAttribute('data-fasilitas-id');
            redirectToFasilitas(fasilitasId);
        });
    });

    // Event listener untuk click pada small boxes
    document.querySelectorAll('.clickable-box').forEach(function(box) {
        box.addEventListener('click', function(e) {
            if (e.target.closest('.small-box-footer')) {
                return;
            }
            
            e.stopPropagation();
            const fasilitasId = this.getAttribute('data-fasilitas-id');
            const status = this.getAttribute('data-status');
            redirectToFasilitas(fasilitasId, status);
        });
    });
    
    @if(isset($fasilitas) && count($fasilitas) > 0)
        @foreach($fasilitas as $index => $satu)
            @php 
                $chartNum = $index + 1;
                $serviceable = $satu->getJlhLayananServ();
                $unserviceable = $satu->getJlhLayananUnserv();
            @endphp
            
            // Chart untuk {{ $satu->nama }}
            (function() {
                var canvasId = 'chart{{ $chartNum }}';
                var canvas = document.getElementById(canvasId);
                
                console.log('Looking for canvas:', canvasId);
                console.log('Canvas found:', canvas);
                
                if (canvas) {
                    var ctx = canvas.getContext('2d');
                    
                    // Data untuk chart
                    var chartData = {
                        labels: ['Serviceable', 'Unserviceable'],
                        datasets: [{
                            data: [{{ $serviceable }}, {{ $unserviceable }}],
                            backgroundColor: [
                                '#28a745', // Green untuk serviceable
                                '#dc3545'  // Red untuk unserviceable
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverBackgroundColor: [
                                '#34ce57',
                                '#e4606d'
                            ]
                        }]
                    };
                    
                    console.log('Chart data for {{ $satu->nama }}:', chartData);
                    
                    try {
                        var chart = new Chart(ctx, {
                            type: 'pie',
                            data: chartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 15,
                                            usePointStyle: true,
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var label = context.label || '';
                                                var value = context.parsed;
                                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                return label + ': ' + value + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                },
                                layout: {
                                    padding: {
                                        top: 10,
                                        bottom: 10
                                    }
                                },
                                // Event handler untuk click pada chart
                                onClick: function(event, elements) {
                                    const fasilitasId = '{{ $satu->id }}';
                                    if (elements.length > 0) {
                                        const elementIndex = elements[0].index;
                                        const status = elementIndex === 0 ? 'serviceable' : 'unserviceable';
                                        redirectToFasilitas(fasilitasId, status);
                                    } else {
                                        redirectToFasilitas(fasilitasId);
                                    }
                                }
                            }
                        });
                        
                        console.log('Chart {{ $chartNum }} created successfully for {{ $satu->nama }}');
                        
                    } catch (error) {
                        console.error('Error creating chart {{ $chartNum }}:', error);
                    }
                } else {
                    console.error('Canvas element ' + canvasId + ' not found');
                }
            })();
            
        @endforeach
    @else
        console.log('No fasilitas data available');
    @endif
});

// Backup: Jika window load tidak bekerja, coba dengan DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Tunggu sebentar untuk memastikan semua elemen sudah render
    setTimeout(function() {
        console.log('Checking if charts exist...');
        
        @if(isset($fasilitas) && count($fasilitas) > 0)
            @foreach($fasilitas as $index => $satu)
                @php $chartNum = $index + 1; @endphp
                
                var canvas{{ $chartNum }} = document.getElementById('chart{{ $chartNum }}');
                if (!canvas{{ $chartNum }}) {
                    console.error('Canvas chart{{ $chartNum }} still not found after DOM loaded');
                } else {
                    console.log('Canvas chart{{ $chartNum }} found in DOM');
                }
            @endforeach
        @endif
    }, 100);
});
</script>
@endsection

