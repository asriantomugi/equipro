@extends('logbook.main')

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- Step Navigation -->
    <div class="row mb-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body py-2">
            <ul class="step d-flex flex-nowrap">
              <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
              <li class="step-item completed"><a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}">Input Gangguan</a></li>
              <li class="step-item completed"><a href="{{ route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}">Tindaklanjut</a></li>
              <li class="step-item active"><a href="#">Penggantian</a></li>
              <li class="step-item"><a href="#">Review</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">FORM PENGGANTIAN (Kembali)</h3>
      </div>

      <form action="{{ route('tambah.simpanStep4', ['laporan_id' => $laporan->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
        <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
        <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis }}">
        <input type="hidden" name="jenis_tindaklanjut" value="{{ $jenis_tindaklanjut }}">

        <div class="card-body">
          @if($laporan->jenis == 1 && $jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian'))
            @foreach ($peralatanLama as $index => $peralatan)
              @php
                  $statusText = $peralatan->status == 1 ? 'Aktif' : 'Tidak Aktif';
                  $kondisiText = $peralatan->kondisi == 1 ? 'Normal' : 'Rusak';

                  $peralatanBaruData = $peralatanBaru[$index] ?? null;
              @endphp

              <div class="row">
                <!-- Peralatan Lama -->
                <div class="col-md-6 mb-3">
                  <div class="form-group row mb-2">
                    <div class="col-sm-8 offset-sm-4">
                      <h5 class="mb-2">Peralatan Lama {{ $index + 1 }}</h5>
                    </div>
                  </div>
                  @foreach(['kode', 'nama', 'merk', 'tipe', 'model', 'serial_number'] as $field)
                  <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" value="{{ $peralatan->$field }}" readonly>
                    </div>
                  </div>
                  @endforeach
                  <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label">Status</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" value="{{ $statusText }}" readonly>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Kondisi</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" value="{{ $kondisiText }}" readonly>
                    </div>
                  </div>
                </div>

                <!-- Peralatan Baru -->
                <div class="col-md-6 mb-6">
                  <div class="form-group row mb-2">
                    <div class="col-sm-8 offset-sm-2 d-flex justify-content-between align-items-center">
                      <h5 class="mb-1">Peralatan Baru {{ $index + 1 }}</h5>
                      <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalPilihPeralatanGanti" data-index="{{ $index }}">
                        Pilih
                      </button>
                    </div>
                  </div>
                  <input type="hidden" name="peralatan_baru[{{ $index }}][id]" id="pb_id_{{ $index }}" value="{{ $peralatanBaruData['id'] ?? '' }}">
                  @foreach(['kode', 'nama', 'merk', 'tipe', 'model', 'serial_number', 'status', 'kondisi'] as $field)
                  <div class="form-group row mb-2">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control"
                        name="peralatan_baru[{{ $index }}][{{ $field }}]"
                        id="pb_{{ $field }}_{{ $index }}"
                        value="{{ $peralatanBaruData[$field] ?? '' }}"
                        readonly>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              <hr>
            @endforeach
          @endif
        </div>

        <!-- Footer -->
        <div class="card-footer">
          <a href="{{ route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}" class="btn btn-success btn-sm">
            <i class="fas fa-angle-left"></i> Kembali
          </a>
          <button type="submit" class="btn btn-success btn-sm float-right">
            Lanjut <i class="fas fa-angle-right"></i>
          </button>
        </div>
      </form>
    </div>

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

  let selectedIndex = null;
  $('#modalPilihPeralatanGanti').on('show.bs.modal', function (e) {
    selectedIndex = $(e.relatedTarget).data('index');
  });

  $('#tabel-peralatan-ganti').on('click', '.btn-pilih-peralatan', function () {
    const alat = $(this).data('detail');

    if (selectedIndex !== null) {
      $('#pb_id_' + selectedIndex).val(alat.id);
      $('#pb_kode_' + selectedIndex).val(alat.kode);
      $('#pb_nama_' + selectedIndex).val(alat.nama);
      $('#pb_merk_' + selectedIndex).val(alat.merk);
      $('#pb_tipe_' + selectedIndex).val(alat.tipe);
      $('#pb_model_' + selectedIndex).val(alat.model);
      $('#pb_serial_number_' + selectedIndex).val(alat.serial_number);

      const statusText = alat.status == 1 ? 'Aktif' : 'Tidak Aktif';
      const kondisiText = alat.kondisi == 1 ? 'Normal' : 'Rusak';

      $('#pb_status_' + selectedIndex).val(statusText);
      $('#pb_kondisi_' + selectedIndex).val(kondisiText);
    }

    $('#modalPilihPeralatanGanti').modal('hide');
  });
});
</script>
@endpush
