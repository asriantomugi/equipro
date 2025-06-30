@extends('logbook.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- Stepper -->
    <div class="row mb-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body py-2">
            <ul class="step d-flex flex-nowrap">
              <li class="step-item active"><a href="#">Pilih Layanan</a></li>
              <li class="step-item"><a href="#">Input Gangguan</a></li>
              <li class="step-item"><a href="#">Tindaklanjut</a></li>
              <li class="step-item"><a href="#">Review</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Form (langsung tampil di halaman) -->
    <div class="row mb-3">
      <div class="col-lg-12">
        <div class="card">
          <form id="form-filter-layanan">
            @csrf
            <div class="card-body row">
              <div class="form-group col-md-3">
                <label>Fasilitas</label>
                <select name="fasilitas" class="form-control">
                  <option value="">- ALL -</option>
                  @foreach($fasilitas as $item)
                  <option value="{{ $item->id }}">{{ $item->nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 1</label>
                <select name="LokasiTk1" class="form-control">
                  <option value="">- ALL -</option>
                  @foreach($LokasiTk1 as $item)
                  <option value="{{ $item->id }}">{{ $item->nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 2</label>
                <select name="LokasiTk2" class="form-control">
                  <option value="">- ALL -</option>
                  @foreach($LokasiTk2 as $item)
                  <option value="{{ $item->id }}">{{ $item->nama }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Lokasi Tingkat 3</label>
                <select name="LokasiTk3" class="form-control">
                  <option value="">- ALL -</option>
                  @foreach($LokasiTk3 as $item)
                  <option value="{{ $item->id }}">{{ $item->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right">
                <i class="fas fa-filter"></i> Filter
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Tabel Layanan (kosong awalnya) -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">DAFTAR LAYANAN</h3>
          </div>
          <div class="card-body" id="tabel-daftar-layanan">
            <table id="layananTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>NO.</th>
                  <th>KODE</th>
                  <th>NAMA LAYANAN</th>
                  <th>FASILITAS</th>
                  <th>LOKASI T.1</th>
                  <th>LOKASI T.2</th>
                  <th>LOKASI T.3</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                {{-- Kosong dulu --}}
              </tbody>
            </table>
          </div>
          <div class="card-footer">
            <a href="{{ url('/logbook/laporan/daftar') }}" class="btn btn-default">Batal</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection

@section('tail')
<script>
  $(document).ready(function () {
    let table = $('#layananTable').DataTable();

    $('#form-filter-layanan').on('submit', function (e) {
      e.preventDefault();

      $.post('{{ route('logbook.laporan.filter') }}',
        $(this).serialize(),
        function (data) {
          $('#tabel-daftar-layanan').html(data);

          // Re-init datatable setelah replace
          $('#layananTable').DataTable();
        }
      ).fail(function (xhr) {
        alert('Gagal mengambil data: ' + xhr.responseText);
      });
    });
  });
</script>
@endsection
