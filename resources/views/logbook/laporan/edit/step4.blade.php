@extends('logbook.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">

        {{-- Step Navigation --}}
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap">
                            <li class="step-item completed"><a href="#">Pilih Layanan</a></li>
                            <li class="step-item completed"><a href="#">Input Gangguan</a></li>
                            <li class="step-item completed"><a href="#">Tindak Lanjut</a></li>
                            <li class="step-item active"><a href="#">Penggantian</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Penggantian --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">EDIT PENGGANTIAN PERALATAN</h3>
            </div>

            <form action="{{ route('logbook.laporan.edit.step4.update', $laporan->id) }}" method="POST" id="formStep4">
                @csrf
               
                <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis }}">
                <input type="hidden" name="jenis_tindaklanjut" value="{{ $jenis_tindaklanjut }}">

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($laporan->jenis == 1 && $jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian'))
                        @php $shown = 0; @endphp

                        @foreach ($peralatanLama as $idx => $peralatan)
                            @php
                                $statusText = $peralatan->status == 1 ? 'Aktif' : 'Tidak Aktif';
                                $kondisiText = $peralatan->kondisi == 1 ? 'Normal' : 'Rusak';
                                
                                // Ambil data penggantian yang sudah ada
                                $existingPenggantian = $penggantiPeralatan->get($peralatan->id);
                            @endphp

                            @if ($idx > 0) <hr> @endif

                            <div class="row">
                                {{-- PERALATAN LAMA --}}
                                <div class="col-md-6 mb-3">
                                    <div class="form-group row mb-2">
                                        <div class="col-sm-12">
                                            <h5 class="mb-2">
                                                <span class="badge badge-primary">Peralatan : {{ $peralatan->nama }}</span>
                                            </h5>
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
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Kondisi</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="{{ $kondisiText }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                {{-- PERALATAN BARU --}}
                                <div class="col-md-6 mb-3">
                                    <div class="form-group row mb-2">
                                        <div class="col-sm-8 offset-sm-2 d-flex justify-content-end align-items-center">
                                            
                                                <button type="button" class="btn btn-success btn-sm float-right btn-ganti-peralatan"
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
                                    <input type="hidden" name="penggantian[{{ $idx }}][peralatan_baru_id]" 
                                           id="pg_peralatan_baru_id_{{ $idx }}" 
                                           value="{{ old("penggantian.{$idx}.peralatan_baru_id", $existingPenggantian ? $existingPenggantian->peralatan_baru_id : '') }}">

                                    {{-- Tampilan detail peralatan baru (readonly display) --}}
                                    @foreach (['kode','nama','merk','tipe','model','serial_number'] as $f)
                                        <div class="form-group row mb-2">
                                        <label class="col-sm-2"></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"
                                                       name="peralatan_baru[{{ $idx }}][{{ $f }}]"
                                                       id="pb_{{ $f }}_{{ $idx }}" 
                                                       value="{{ old("peralatan_baru.{$idx}.{$f}", $existingPenggantian && $existingPenggantian->peralatanBaru ? $existingPenggantian->peralatanBaru->$f : '') }}"
                                                       placeholder="{{ ucwords(str_replace('_',' ',$f)) }}"
                                                       readonly>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="form-group row mb-2">
                                    <label class="col-sm-2"></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control"
                                                   name="peralatan_baru[{{ $idx }}][status]"
                                                   id="pb_status_{{ $idx }}" 
                                                   value="{{ old("peralatan_baru.{$idx}.status", $existingPenggantian && $existingPenggantian->peralatanBaru ? ($existingPenggantian->peralatanBaru->status == 1 ? 'Aktif' : 'Tidak Aktif') : '') }}"
                                                   placeholder="Status"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label class="col-sm-2"></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control"
                                                   name="peralatan_baru[{{ $idx }}][kondisi]"
                                                   id="pb_kondisi_{{ $idx }}" 
                                                   value="{{ old("peralatan_baru.{$idx}.kondisi", $existingPenggantian && $existingPenggantian->peralatanBaru ? ($existingPenggantian->peralatanBaru->kondisi == 1 ? 'Normal' : 'Rusak') : '') }}"
                                                   placeholder="Kondisi"
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Tidak ada data penggantian peralatan untuk ditampilkan.
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="card-footer">
                    <a href="{{ route('logbook.laporan.edit.step3', $laporan->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-angle-left"></i>&nbsp;Kembali
                    </a>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        Lanjut&nbsp;<i class="fas fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Modal Pilih Peralatan --}}
        @include('logbook.laporan.modal_pilih_peralatan')
    </div>
</section>
@endsection

@push('scripts')
<script>
$(function(){
    let selectedIndex = null;

    // Event saat modal dibuka
    $('#modalPilihPeralatanGanti').on('show.bs.modal', function(e) {
        const btn = $(e.relatedTarget);
        selectedIndex = btn.data('index');
        const namaPeralatan = btn.data('nama');
        $('#info-nama-peralatan').text(namaPeralatan || '(data nama peralatan)');
    });

    // Event filter peralatan
    $('#filter-ganti-peralatan-form').on('submit', function(e){
        e.preventDefault();
        $.post('{{ route("laporan.filterPeralatan") }}', $(this).serialize(), function(data){
            $('#tabel-peralatan-ganti').html(data);
        }).fail(xhr => {
            alert('Gagal memuat data: ' + xhr.responseText);
        });
    });

    // Event pilih peralatan
    $('#tabel-peralatan-ganti').on('click', '.btn-pilih-peralatan', function(){
        const alat = $(this).data('detail');
        if(selectedIndex !== null){
            // Set ID peralatan baru
            $('#pg_peralatan_baru_id_' + selectedIndex).val(alat.id);
            
            // Set detail peralatan baru
            ['kode','nama','merk','tipe','model','serial_number'].forEach(f => {
                $('#pb_' + f + '_' + selectedIndex).val(alat[f]);
            });
            
            // Set status dan kondisi
            $('#pb_status_' + selectedIndex).val(alat.status == 1 ? 'Aktif' : 'Tidak Aktif');
            $('#pb_kondisi_' + selectedIndex).val(alat.kondisi == 1 ? 'Normal' : 'Rusak');
        }
        $('#modalPilihPeralatanGanti').modal('hide');
    });

    // Validasi form sebelum submit
    $('#formStep4').on('submit', function(e) {
        let valid = true;
        let emptyFields = [];

        // Cek apakah semua peralatan pengganti sudah dipilih
        $('input[name*="[peralatan_baru_id]"]').each(function(index) {
            if (!$(this).val()) {
                valid = false;
                const peralatanNama = $(this).closest('.row').find('.badge-primary').text().replace('Peralatan : ', '');
                emptyFields.push(peralatanNama);
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Harap pilih peralatan pengganti untuk:\n- ' + emptyFields.join('\n- '));
        }
    });

    // Load data existing saat halaman dimuat
    $(document).ready(function() {
        // Trigger load data untuk setiap peralatan yang sudah ada penggantinya
        $('input[name*="[peralatan_baru_id]"]').each(function() {
            const peralatanId = $(this).val();
            if (peralatanId) {
                // Data sudah di-load dari server via blade template
                // Tidak perlu AJAX tambahan
            }
        });
    });
});
</script>
@endpush