@extends('fasilitas.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <div class="row">

          <!-- =====================  Dashboard User ====================== -->
          <div class="col-md-6">

@foreach($fasilitas as $satu)
            
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

                  <!-- chart jumlah user berdasarkan role -->
  @php
    // Buat array untuk data chart
    $dataChart = [
        'labels' => ['Serviceable', 'Unserviceable'],
        'data' => [$satu->getJlhLayananServ(), $satu->getJlhLayananUnserv()]
    ];

    //dd($dataChart);
  @endphp
                  <div class="col-md-6">
                    <canvas id="chartRoleUser" style="min-height: 250px; height: 250px; max-height: 90%; max-width: 100%;"></canvas>
                  </div><!-- /.col -->
                  
                  <!-- info box jumlah user berdasarkan role -->
                  <div class="col-md-6">
                   
                    <div class="info-box mb-3 bg-success">
                      <span class="info-box-icon"><i class="fas fa-check"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text">Serviceable</span>
                        <span class="info-box-number">{{ $satu->getJlhLayananServ() }}</span>
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                    <div class="info-box mb-3 bg-danger">
                      <span class="info-box-icon"><i class="fas fa-times"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text">Unserviceable</span>
                        <span class="info-box-number">{{ $satu->getJlhLayananUnserv() }}</span>
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                    
                  </div><!-- /.col -->
                </div><!-- /.row -->

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
@endforeach

          </div><!-- /.col -->
          <!-- =====================  End of Dashboard User ====================== -->


        </div><!-- /.row -->
        



      </div><!-- /.container-fluid -->
  
    <!-- JavaScript untuk memproses tampilan chart jumlah user berdasarkan role -->
    <script>
      // Ambil data dari PHP (yang dipassing melalui Blade)
      var data = @json($dataChart); // data berisi labels dan data

      // Konfigurasi untuk pie chart
      var ctx = document.getElementById('chartRoleUser').getContext('2d');
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
    
@endsection   