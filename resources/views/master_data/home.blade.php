@extends('master_data.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- =====================  Dashboard User ====================== -->
        <div class="row">
          <div class="col-md-12">
            
            <div class="card card-info">

              <div class="card-header">
                <h3 class="card-title">USER</h3>

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
                  <div class="col-md-6">
                    <canvas id="chartRoleUser" style="min-height: 250px; height: 250px; max-height: 90%; max-width: 100%;"></canvas>
                  </div><!-- /.col -->
                  
                  <!-- info box jumlah user berdasarkan role -->
                  <div class="col-md-6">
                   
                    <div class="info-box mb-3 bg-warning">
                      <span class="info-box-icon"><i class="fas fa-user-secret"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text">Super Admin</span>
                        <span class="info-box-number">{{ $dataChartRoleUser['data'][0] }}</span>
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                    <div class="info-box mb-3 bg-success">
                      <span class="info-box-icon"><i class="far fa-user-circle"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text">Admin</span>
                        <span class="info-box-number">{{ $dataChartRoleUser['data'][1] }}</span>
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                    <div class="info-box mb-3 bg-danger">
                      <span class="info-box-icon"><i class="fas fa-users"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text">Teknisi</span>
                        <span class="info-box-number">{{ $dataChartRoleUser['data'][2] }}</span>
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

          </div><!-- /.col -->
        </div><!-- /.row -->
        <!-- =====================  End of Dashboard User ====================== -->


      </div><!-- /.container-fluid -->
  
    <!-- JavaScript untuk memproses tampilan chart jumlah user berdasarkan role -->
    <script>
      // Ambil data dari PHP (yang dipassing melalui Blade)
      var data = @json($dataChartRoleUser); // data berisi labels dan data

      // Konfigurasi untuk pie chart
      var ctx = document.getElementById('chartRoleUser').getContext('2d');
      var chartRoleUser = new Chart(ctx, {
          type: 'pie', // Jenis chart
          data: {
              labels: data.labels, // Label untuk pie chart
              datasets: [{
                  data: data.data, // Data untuk pie chart
                  backgroundColor: ['#f39c12', '#00a65a', '#f56954'], // Warna untuk masing-masing bagian pie
              }]
          },
          options: {
              responsive: true,
          }
      });
    </script>
    
@endsection   