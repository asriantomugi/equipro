@extends('fasilitas.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <div class="row">

          <!-- =====================  Dashboard ====================== -->
          
@php $index = 1; @endphp

@foreach($fasilitas as $satu)
          <div class="col-md-6">
            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">{{ strtoupper($satu->kode) }} - {{ strtoupper($satu->nama) }}</h3>

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
  @php    
    // buat array untuk data chart
    $dataChart = [
        'labels' => ['Serviceable', 'Unserviceable'],
        'data' => [$satu->getJlhLayananServ(), $satu->getJlhLayananUnserv()]
    ];
  @endphp
                  <div class="col-md-6">
                    <canvas id="chart{{ $index }}" style="main-height: 250px; height: 250px; max-height: 90%; max-width: 100%;"></canvas>
                  </div><!-- /.col -->
                  
                  <div class="col-md-6">
                   
                    <!-- small box -->
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3>{{ $satu->getJlhLayananServ() }}</h3>

                        <p>SERVICEABLE</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-check"></i>
                      </div>
                      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div><!-- /.small-box -->

                    <!-- small box -->
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <h3>{{ $satu->getJlhLayananUnserv() }}</h3>

                        <p>UNSERVICEABLE</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-times"></i>
                      </div>
                      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div><!-- /.small-box -->
                    
                  </div><!-- /.col -->

                </div><!-- /.row -->

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- JavaScript untuk memproses tampilan chart -->
            <script>
              // Ambil data dari PHP (yang dipassing melalui Blade)
              var data = @json($dataChart); // data berisi labels dan data

              // Konfigurasi untuk pie chart
              var ctx = document.getElementById('chart{{ $index }}').getContext('2d');
              var chartRoleUser = new Chart(ctx, {
                  type: 'pie', // Jenis chart
                  data: {
                      labels: data.labels, // Label untuk pie chart
                      datasets: [{
                          data: data.data, // Data untuk pie chart
                          backgroundColor: ['#00a65a', '#f56954'], // Warna untuk masing-masing bagian pie
                      }]
                  },
                  options: {
                      responsive: true,
                  }
              });
            </script>
          </div><!-- /.col -->
  @php $index++; @endphp
@endforeach

          <!-- =====================  End of Dashboard ====================== -->
           

        </div><!-- /.row -->
        



      </div><!-- /.container-fluid -->
  
    
    
@endsection   