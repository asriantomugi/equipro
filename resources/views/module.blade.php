@extends('layout.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

            <div class="row">

@if(session()->get('role_id') == config('constants.role.super_admin') || 
    session()->get('role_id') == config('constants.role.super_admin'))

              <div class="col-lg-2 col-6">            
                <div class="small-box bg-info">
                  <div class="inner">
                    <center>
                      <h4>MASTER DATA</h4>
                    </center>
                  </div>
                 
                  <a href="{{url('/master-data/home')}}" class="small-box-footer">Masuk <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
@endif

              <div class="col-lg-2 col-6">            
                <div class="small-box bg-success">
                  <div class="inner">
                    <center>
                      <h4>FASILITAS</h4>
                    </center>
                  </div>
                 
                  <a href="{{url('/fasilitas/home')}}" class="small-box-footer">Masuk <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->

              <div class="col-lg-2 col-6">            
                <div class="small-box bg-danger">
                  <div class="inner">
                    <center>
                      <h4>LOGBOOK</h4>
                    </center>
                  </div>
                 
                  <a href="{{url('/logbook/home')}}" class="small-box-footer">Masuk <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->

              <div class="col-lg-2 col-6">            
                <div class="small-box bg-primary">
                  <div class="inner">
                    <center>
                      <h4>DASHBOARD</h4>
                    </center>
                  </div>
                 
                  <a href="{{url('/dashboard/home')}}" class="small-box-footer">Masuk <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->

              <div class="col-lg-2 col-6">            
                <div class="small-box bg-warning">
                  <div class="inner">
                    <center>
                      <h4>LOG AKTIVITAS</h4>
                    </center>
                  </div>
                 
                  <a href="{{url('/log_aktivitas/daftar')}}" class="small-box-footer">Masuk <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
			  
            </div>
            <!-- /.row -->        

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    
@endsection   