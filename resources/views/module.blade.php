@extends('layout.main')


@section('content')
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

            <div class="row">

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
			  
            </div>
            <!-- /.row -->        

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    
@endsection   