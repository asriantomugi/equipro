@extends('logbook.main')

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- Step Navigation -->
    <div class="row mb-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body py-2">
            <ul class="step d-flex flex-nowrap justify-content-between mb-0">
              <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Step 1</a></li>
              <li class="step-item completed"><a href="{{ route('tambah.step2') }}">Step 2</a></li>
              <li class="step-item completed"><a href="{{ route('tambah.step3', ['laporan_id' => $laporan->id]) }}">Step 3</a></li>
              <li class="step-item active"><a href="#">Step 4</a></li>
              <li class="step-item"><a href="#">Step 5</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Form Tindak Lanjut</h3>
      </div>

      <form action="{{ route('tambah.simpanStep4', ['laporan_id' => $laporan->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
        <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
        <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis }}">
        <input type="hidden" name="jenis_tindaklanjut" value="{{ $jenis_tindaklanjut }}">
        <input type="hidden" name="peralatan_baru_id" id="peralatan_baru_id">

        <div class="card-body">
          {{-- Tampilkan hanya jika penggantian --}}
          @if($laporan->jenis == 1 && $jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian'))
          <div class="row">
            <!-- Peralatan Lama -->
            <div class="col-md-6">
              <h5 class="text">Peralatan Lama</h5>
              @if(count($peralatanLama) > 0)
                @foreach ($peralatanLama as $peralatan)
                  @php
                    $kondisiLama = $peralatan->kondisi ? 'Serviceable' : 'Unserviceable';
                  @endphp
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Kode</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->kode }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Nama</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->nama }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Merk</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->merk }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Tipe</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->tipe }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Model</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->model }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Serial Number</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->serial_number }}" readonly></div></div>
                  <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Status</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $peralatan->status }}" readonly></div></div>
                  <div class="form-group row"><label class="col-sm-4 col-form-label">Kondisi</label><div class="col-sm-8"><input type="text" class="form-control" value="{{ $kondisiLama }}" readonly></div></div>
                @endforeach
              @else
                <p class="text-muted">Belum ada peralatan lama dipilih.</p>
              @endif
            </div>

            <!-- Peralatan Baru -->
            <div class="col-md-6">
              <h5 class="d-flex justify-content-between align-items-center">
                Peralatan Baru
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modalPilihPeralatanGanti">
                  Ganti Peralatan
                </button>
              </h5>
              <div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Kode</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_kode" id="pb_kode" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Nama</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_nama" id="pb_nama" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Merk</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_merk" id="pb_merk" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Tipe</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_tipe" id="pb_tipe" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Model</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_model" id="pb_model" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Serial Number</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_serial_number" id="pb_serial" readonly></div></div>
                <div class="form-group row mb-2"><label class="col-sm-4 col-form-label">Status</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_status" id="pb_status" readonly></div></div>
                <div class="form-group row"><label class="col-sm-4 col-form-label">Kondisi</label><div class="col-sm-8"><input type="text" class="form-control" name="peralatan_baru_kondisi" id="pb_kondisi" readonly></div></div>
              </div>
            </div>
          </div>
          @endif

          <!-- Kondisi Layanan Setelah -->
          <div class="form-group row mt-4">
            <label for="kondisi_setelah" class="col-sm-2 col-form-label">Update Kondisi Layanan</label>
            <div class="col-sm-4">
              <select name="kondisi_setelah" class="form-control" required>
                <option value="">- Pilih -</option>
                @foreach ($kondisiSetelah as $label => $value)
                  <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer">
          <a href="{{ route('tambah.step3', ['laporan_id' => $laporan->id]) }}" class="btn btn-default btn-sm" role="button">
            <i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali
          </a>
          <button type="submit" class="btn btn-success btn-sm float-right">
            Lanjut&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i>
          </button>
        </div>
      </form>
    </div>

    <!-- Modal Pilih Peralatan -->
    @include('logbook.laporan.modal_pilih_peralatan') 
  </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
  $('#filter-ganti-peralatan-form').on('submit', function (e) {
    e.preventDefault();
    $.post('{{ route("laporan.filterPeralatan") }}', $(this).serialize(), function (data) {
      $('#tabel-peralatan-ganti').html(data);
    }).fail(function (xhr) {
      alert('Gagal memuat data: ' + xhr.responseText);
    });
  });

  $('#tabel-peralatan-ganti').on('click', '.btn-pilih-peralatan', function () {
    const alat = $(this).data('detail');
    $('#peralatan_baru_id').val(alat.id);
    $('#pb_kode').val(alat.kode);
    $('#pb_nama').val(alat.nama);
    $('#pb_merk').val(alat.merk);
    $('#pb_tipe').val(alat.tipe);
    $('#pb_model').val(alat.model);
    $('#pb_serial').val(alat.serial_number);
    $('#pb_status').val(alat.status);
    $('#pb_kondisi').val(alat.kondisi == 1 ? 'Serviceable' : 'Unserviceable');
    $('#modalPilihPeralatanGanti').modal('hide');
  });
});
</script>
@endpush
