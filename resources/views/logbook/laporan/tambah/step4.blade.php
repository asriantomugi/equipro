@extends('logbook.main')

@section('content')
<section class="content">
  <div class="container-fluid">

    {{-- STEP NAVIGATION --}}
    <div class="row mb-2">
      <div class="col-lg-12">
        <div class="card"><div class="card-body py-2">
          <ul class="step d-flex flex-nowrap">
            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
            <li class="step-item completed"><a href="{{ route('tambah.step2.back',['laporan_id'=>$laporan->id]) }}">Input Gangguan</a></li>
            <li class="step-item completed"><a href="{{ route('tambah.step3.back',['laporan_id'=>$laporan->id]) }}">Tindaklanjut</a></li>
            <li class="step-item active"><a href="#">Penggantian</a></li>
            <li class="step-item"><a href="#">Review</a></li>
          </ul>
        </div></div>
      </div>
    </div>

    {{-- FORM --}}
    <div class="card">
      <div class="card-header"><h3 class="card-title">FORM PENGGANTIAN</h3></div>

      <form action="{{ route('tambah.simpanStep4',['laporan_id'=>$laporan->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
        <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
        <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis }}">
        <input type="hidden" name="jenis_tindaklanjut" value="{{ $jenis_tindaklanjut }}">

        <div class="card-body">
          @if ($laporan->jenis == 1 && $jenis_tindaklanjut === 0) {{-- 0 = penggantian --}}

            @foreach ($peralatanLama as $idx => $peralatan)
             @if ($loop->iteration > 1) <hr> @endif
  @php
    $statusText  = $peralatan->status == 1 ? 'Aktif' : 'Tidak Aktif';
  @endphp

  

  <div class="row">
    {{-- PERALATAN LAMA --}}
    <div class="col-md-6 mb-3">
      <div class="form-group row mb-2">
        <div class="col-sm-12">
          <div class="mb-3">
            <strong>Peralatan {{ $loop->iteration }}:
                <span class="badge bg-primary">{{ $peralatan->nama }}</span>
             </strong>
          </div>
        </div>
      </div>

      {{-- Data peralatan lama --}}
      @foreach (['kode','nama','merk','tipe','model','serial_number'] as $f)
        <div class="form-group row mb-2">
          <label class="col-sm-4 col-form-label">{{ ucwords(str_replace('_',' ',$f)) }}</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" value="{{ $peralatan->$f }}" readonly>
          </div>
        </div>
      @endforeach

      <div class="form-group row mb-2">
        <label class="col-sm-4 col-form-label">Status</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" value="{{ $statusText }}" readonly>
        </div>
      </div>
      
    </div>

    {{-- PERALATAN BARU --}}
    <div class="col-md-6 mb-3">
      <div class="form-group row mb-2">
        <div class="col-sm-8 offset-sm-2 d-flex justify-content-end align-items-center">
          <button type="button" class="btn btn-success btn-sm btn-ganti-peralatan"
                  data-toggle="modal"
                  data-target="#modalPilihPeralatanGanti"
                  data-index="{{ $idx }}"
                  data-nama="{{ $peralatan->nama }}">
            Pilih
          </button>
        </div>
      </div>

      {{-- Input pengiriman ke controller --}}
      <input type="hidden" name="penggantian[{{ $idx }}][peralatan_lama_id]" value="{{ $peralatan->id }}">
      <input type="hidden" name="penggantian[{{ $idx }}][peralatan_baru_id]" id="pg_peralatan_baru_id_{{ $idx }}">

      {{-- Tampilan detail peralatan baru (readonly display) --}}
      @foreach (['kode','nama','merk','tipe','model','serial_number','status'] as $f)
        <div class="form-group row mb-2">
          <label class="col-sm-2"></label>
          <div class="col-sm-8">
            <input type="text" class="form-control"
                   name="peralatan_baru[{{ $idx }}][{{ $f }}]"
                   id="pb_{{ $f }}_{{ $idx }}" readonly>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endforeach

          @endif
        </div>

        {{-- FOOTER --}}
        <div class="card-footer">
          <a href="{{ route('tambah.step3.back',['laporan_id'=>$laporan->id]) }}" class="btn btn-success btn-sm">
            <i class="fas fa-angle-left"></i>&nbsp;Kembali
          </a>
          <button type="submit" class="btn btn-success btn-sm float-right">
            Lanjut&nbsp;<i class="fas fa-angle-right"></i>
          </button>
        </div>
      </form>
    </div>

    {{-- MODAL PILIH PERALATAN --}}
    @include('logbook.laporan.modal_pilih_peralatan')
  </div>
</section>
@endsection

@push('scripts')
<script>
$(function(){
  let selectedIndex = null;

  $('#modalPilihPeralatanGanti').on('show.bs.modal', function(e) {
    const btn = $(e.relatedTarget);
    selectedIndex = btn.data('index');
    const namaPeralatan = btn.data('nama');
    $('#info-nama-peralatan').text(namaPeralatan || '(data nama peralatan)');
  });

  $('#filter-ganti-peralatan-form').on('submit', function(e){
    e.preventDefault();
    $.post('{{ route("laporan.filterPeralatan") }}', $(this).serialize(), function(data){
      $('#tabel-peralatan-ganti').html(data);
    }).fail(xhr => {
      alert('Gagal memuat data: ' + xhr.responseText);
    });
  });

  $('#tabel-peralatan-ganti').on('click', '.btn-pilih-peralatan', function(){
    const alat = $(this).data('detail');
    if(selectedIndex !== null){
      $('#pg_peralatan_baru_id_' + selectedIndex).val(alat.id);
      ['kode','nama','merk','tipe','model','serial_number'].forEach(f => {
        $('#pb_' + f + '_' + selectedIndex).val(alat[f]);
      });
      $('#pb_status_' + selectedIndex).val(alat.status == 1 ? 'Aktif' : 'Tidak Aktif');
      $('#pb_kondisi_' + selectedIndex).val(alat.kondisi == 1 ? 'Normal' : 'Rusak');
    }
    $('#modalPilihPeralatanGanti').modal('hide');
  });
});
</script>
@endpush