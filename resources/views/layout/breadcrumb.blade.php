  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{$judul}}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
              <li class="breadcrumb-item"><a href="#">{{$module}}</a></li>

@if(isset($menu))
  @if(isset($submenu))
              <li class="breadcrumb-item"><a href="{{url($menu_url)}}">{{$menu}}</a></li>
              <li class="breadcrumb-item active" aria-current="page">{{$submenu}}</li>
  @else
              <li class="breadcrumb-item active" aria-current="page">{{$menu}}</a></li>
  @endif
@endif    
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

