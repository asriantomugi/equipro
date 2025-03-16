  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{$judul}}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
              <li class="breadcrumb-item"><a href="#">{{$menu}}</a></li>

@if(isset($page))
  @if(isset($subpage))
              <li class="breadcrumb-item"><a href="{{url($page_url)}}">{{$page}}</a></li>
              <li class="breadcrumb-item active" aria-current="page">{{$subpage}}</li>
  @else
              <li class="breadcrumb-item active" aria-current="page">{{$page}}</a></li>
  @endif
@endif    
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

