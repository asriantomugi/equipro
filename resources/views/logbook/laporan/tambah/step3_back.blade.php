
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
                            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
                            <li class="step-item completed"><a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}">Input Gangguan</a></li>
                            <li class="step-item active"><a href="#">Tindak Lanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">FORM TINDAK LANJUT</h3></div>
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

                        <form action="{{ route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}" method="POST" id="formStep3">
                            @csrf
                            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                            <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">
                            
                            {{-- Hidden field untuk kondisi layanan yang akan diset otomatis --}}
                            <input type="hidden" name="kondisi_setelah" id="kondisi_setelah_hidden" value="">

                            {{-- =============== PERALATAN (jika gangguan_peralatan) =============== --}}
                            @if ($laporan->jenis == 1)
                                @php $shown = 0; @endphp
                                @foreach ($layanan->daftarPeralatanLayanan as $index => $dpl)
                                    @continue(!in_array($dpl->peralatan->id, $peralatanGangguanIds)) {{-- skip beroperasi --}}
                                    @php $shown++; @endphp
                                    @if ($shown > 1)<hr>@endif

                                    <div class="mb-3">
                                        <strong>Peralatan {{ $shown }}:
                                            <span class="badge bg-primary">{{ $dpl->peralatan->nama }}</span>
                                        </strong>
                                    </div>

                                    

                                    {{-- Jenis --}}
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Jenis <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select name="tindaklanjut[{{ $dpl->peralatan->id }}][jenis]"
                class="form-control tindak-jenis"
                data-nama="{{ $dpl->peralatan->nama }}">
            <option value="">- Pilih -</option>
            @foreach ($jenisTindakLanjut as $label => $value)
                <option value="{{ $value ? 1 : 0 }}" 
                    {{ isset($tindaklanjutPeralatan[$dpl->peralatan->id]) && $tindaklanjutPeralatan[$dpl->peralatan->id][0]->jenis_tindaklanjut == ($value ? 1 : 0) ? 'selected' : '' }}>
                    {{ ucfirst($label) }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback dynamic"></div>
    </div>
</div>

                                    {{-- Waktu --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="datetime-local"
                                                   name="tindaklanjut[{{ $dpl->peralatan->id }}][waktu]"
                                                   class="form-control tindak-waktu"
                                                   data-nama="{{ $dpl->peralatan->nama }}"
                                                   value="{{ isset($tindaklanjutPeralatan[$dpl->peralatan->id]) ? $tindaklanjutPeralatan[$dpl->peralatan->id][0]->waktu : '' }}">
                                            <div class="invalid-feedback dynamic"></div>
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Deskripsi</label>
                                        <div class="col-sm-9">
                                            <textarea name="tindaklanjut[{{ $dpl->peralatan->id }}][deskripsi]"
                                                      class="form-control"
                                                      rows="3">{{ isset($tindaklanjutPeralatan[$dpl->peralatan->id]) ? $tindaklanjutPeralatan[$dpl->peralatan->id][0]->deskripsi : '' }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Kondisi --}}
                                    {{-- Kondisi --}}
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select name="tindaklanjut[{{ $dpl->peralatan->id }}][kondisi]"
                class="form-control tindak-kondisi"
                data-nama="{{ $dpl->peralatan->nama }}">
            <option value="">- Pilih -</option>
            @foreach ($kondisiTindaklanjut as $label => $value)
                <option value="{{ $value ? 1 : 0 }}" 
                    {{ isset($tindaklanjutPeralatan[$dpl->peralatan->id]) && $tindaklanjutPeralatan[$dpl->peralatan->id][0]->kondisi == ($value ? 1 : 0) ? 'selected' : '' }}>
                    {{ ucfirst($label) }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback dynamic"></div>
    </div>
</div>
                                @endforeach
                            @else
                                {{-- =============== NON‑PERALATAN =============== --}}
                                
                                

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" 
                                               name="waktu" 
                                               class="form-control"
                                               value="{{ $tindaklanjutNonPeralatan ? $tindaklanjutNonPeralatan->waktu : '' }}">
                                        <div class="invalid-feedback dynamic"></div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9">
                                        <textarea name="deskripsi" 
                                                  class="form-control"
                                                  rows="3">{{ $tindaklanjutNonPeralatan ? $tindaklanjutNonPeralatan->deskripsi : '' }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <select name="kondisi" class="form-control non-peralatan-kondisi">
            <option value="">- Pilih -</option>
            @foreach ($kondisiTindaklanjut as $label => $value)
                <option value="{{ $value ? 1 : 0 }}" 
                    {{ $tindaklanjutNonPeralatan && $tindaklanjutNonPeralatan->kondisi == ($value ? 1 : 0) ? 'selected' : '' }}>
                    {{ ucfirst($label) }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback dynamic"></div>
    </div>
</div>
                            @endif

                            <div class="card-footer">
                                <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}"
                                   class="btn btn-success btn-sm">
                                   <i class="fas fa-angle-left"></i>&nbsp;Kembali
                                </a>
                                <button type="submit" class="btn btn-success btn-sm float-right" id="btn-submit">
                                    Lanjut&nbsp;<i class="fas fa-angle-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* hilangkan pseudo‑asterisk bawaan browser / bootstrap */
input[type="datetime-local"]::after,
input.form-control:required::after,
input[type="datetime-local"]:required::after {
    content: none !important;
    display: none !important;
    color: transparent !important;
}

.alert-info {
    font-size: 0.9em;
}
</style>
@endpush

@push('scripts')
<script>
function markInvalid(el, msg) {
    el.classList.remove('is-valid'); 
    el.classList.add('is-invalid');
    
    // Hapus error message yang ada
    const existingError = el.closest('.form-group').querySelector('.invalid-feedback.dynamic');
    if (existingError) existingError.remove();
    
    // Tambah error message baru
    const div = document.createElement('div');
    div.className = 'invalid-feedback dynamic d-block'; 
    div.innerHTML = msg;
    
    el.parentNode.appendChild(div);
}

function markValid(el) {
    el.classList.remove('is-invalid'); 
    el.classList.add('is-valid');
    
    // Hapus error message
    const fb = el.closest('.form-group').querySelector('.invalid-feedback.dynamic');
    if (fb) fb.remove();
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formStep3');
    const submitBtn = document.getElementById('btn-submit');
    const jenisLaporan = {{ $laporan->jenis }};

    console.log('Form loaded, jenis laporan:', jenisLaporan);

    /* ========== VALIDASI SUBMIT ========== */
    form.addEventListener('submit', e => {
        let valid = true;
        
        // Reset semua error state
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

        if (jenisLaporan == 1) {
            // Validasi untuk gangguan peralatan
            document.querySelectorAll('.tindak-jenis').forEach(el => {
                if(el.value === '') { 
                    markInvalid(el, 'Jenis tindak lanjut wajib dipilih'); 
                    valid = false; 
                } else {
                    markValid(el);
                }
            });
            
            document.querySelectorAll('.tindak-waktu').forEach(el => {
                if(el.value === '' || el.value.trim() === '') { 
                    markInvalid(el, 'Waktu wajib diisi'); 
                    valid = false; 
                } else {
                    markValid(el);
                }
            });
            
            document.querySelectorAll('.tindak-kondisi').forEach(el => {
                if(el.value === '') { 
                    markInvalid(el, 'Kondisi wajib dipilih'); 
                    valid = false; 
                } else {
                    markValid(el);
                }
            });
        } else {
            // Validasi untuk gangguan non-peralatan
            const waktu = form.querySelector('[name="waktu"]');
            const kondisi = form.querySelector('[name="kondisi"]');

            if (!waktu.value || waktu.value.trim() === '') {
                markInvalid(waktu, 'Waktu wajib diisi');
                valid = false;
            } else {
                markValid(waktu);
            }

            if (!kondisi.value) {
                markInvalid(kondisi, 'Kondisi wajib dipilih');
                valid = false;
            } else {
                markValid(kondisi);
            }
        }

        if (!valid) {
            e.preventDefault();
            console.log('Form validation failed');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
    });

    // Tambahan logging untuk debug select changes
    document.querySelectorAll('.tindak-jenis, .tindak-kondisi').forEach(select => {
        select.addEventListener('change', function() {
            console.log('Select changed:', this.name, 'value:', this.value);
        });
    });
});
</script>
@endpush

