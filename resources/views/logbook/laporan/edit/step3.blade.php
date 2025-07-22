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
                            <li class="step-item active"><a href="#">Tindak Lanjut</a></li>
                            
                                <li class="step-item"><a href="#">Review</a></li>
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Tindak Lanjut yang Sudah Ada --}}
        @if($existingTindakLanjut->count() > 0)
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DATA TINDAK LANJUT SEBELUMNYA</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Waktu</th>
                                            @if($laporan->jenis == 1)
                                                <th>Peralatan</th>
                                                <th>Jenis</th>
                                            @endif
                                            <th>Deskripsi</th>
                                            <th>Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existingTindakLanjut as $index => $tindak)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ \Carbon\Carbon::parse($tindak->waktu)->format('d/m/Y H:i') }}</td>
    @if($laporan->jenis == 1)
        <td>{{ $tindak->peralatan->nama ?? '-' }}</td>
        <td>
            {{ $tindak->jenis_tindaklanjut ? 'Perbaikan' : 'Penggantian' }}
        </td>
    @endif
    <td>{{ $tindak->deskripsi ?? '-' }}</td>
    <td>
        {{ $tindak->kondisi ? 'Beroperasi' : 'Gangguan' }}
    </td>
</tr>
@endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Tindak Lanjut --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">FORM TINDAK LANJUT</h3>
                    </div>
                    
                    <form action="{{ route('logbook.laporan.edit.step3.update', $laporan->id) }}" method="POST" id="formStep3">
                        @csrf
                        <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                        <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                        <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">

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

                            {{-- =============== PERALATAN (jika gangguan_peralatan) =============== --}}
                            @if ($laporan->jenis == 1)
                                @php $shown = 0; @endphp
                                @foreach ($layanan->daftarPeralatanLayanan as $index => $dpl)
                                    @continue(!in_array($dpl->peralatan->id, $peralatanGangguanIds))
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
                                                    <option value="{{ $value ? 1 : 0 }}">
                                                        {{ ucfirst($label) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Waktu --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="datetime-local"
                                                   name="tindaklanjut[{{ $dpl->peralatan->id }}][waktu]"
                                                   class="form-control tindak-waktu"
                                                   data-nama="{{ $dpl->peralatan->nama }}">
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Deskripsi</label>
                                        <div class="col-sm-9">
                                            <textarea name="tindaklanjut[{{ $dpl->peralatan->id }}][deskripsi]"
                                                      class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>

                                    {{-- Kondisi --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="tindaklanjut[{{ $dpl->peralatan->id }}][kondisi]"
                                                    class="form-control tindak-kondisi"
                                                    data-nama="{{ $dpl->peralatan->nama }}">
                                                <option value="">- Pilih -</option>
                                                @foreach ($kondisiTindaklanjut as $label => $value)
                                                    <option value="{{ $value ? 1 : 0 }}">
                                                        {{ ucfirst($label) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- =============== NON‑PERALATAN =============== --}}
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="waktu" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9">
                                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="kondisi" class="form-control" required>
                                            <option value="">- Pilih -</option>
                                            @foreach ($kondisiTindaklanjut as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}">
                                                    {{ ucfirst($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- Update Kondisi Layanan --}}
                            <hr>
                            <div class="form-group row mt-4">
                                <label class="col-sm-3 col-form-label">
                                    Update Kondisi Layanan <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="kondisi_setelah" class="form-control" required>
                                        <option value="">- Pilih Kondisi Baru -</option>
                                        @foreach ($kondisiSetelah as $label => $value)
                                            <option value="{{ $value ? 1 : 0 }}"
                                                    {{ old('kondisi_setelah', $laporan->kondisi_layanan_temp) == ($value ? 1 : 0) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Pilih kondisi layanan setelah melakukan tindak lanjut ini
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('laporan.edit.step2', $laporan->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-angle-left"></i>&nbsp;Kembali
                            </a>
                            <button type="submit" class="btn btn-success btn-sm float-right" >
                                Lanjut&nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function markInvalid(el, msg) {
    el.classList.remove('is-valid'); 
    el.classList.add('is-invalid');
    if (!el.parentNode.querySelector('.invalid-feedback.dynamic')) {
        const div = document.createElement('div');
        div.className = 'invalid-feedback dynamic'; 
        div.innerHTML = msg;
        el.parentNode.appendChild(div);
    }
}

function markValid(el) {
    el.classList.remove('is-invalid'); 
    el.classList.add('is-valid');
    const fb = el.parentNode.querySelector('.invalid-feedback.dynamic');
    if (fb) fb.remove();
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formStep3');
    const submitBtn = document.getElementById('btn-submit');

    if (form && submitBtn) {
        form.addEventListener('submit', e => {
            let valid = true;
            
            // Clear previous validations
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

            @if ($laporan->jenis == 1)
                // Validasi untuk peralatan
                let hasAnyInput = false;
                
                document.querySelectorAll('.tindak-jenis').forEach(el => {
                    if (el.value !== '') hasAnyInput = true;
                });
                
                if (!hasAnyInput) {
                    alert('Minimal satu peralatan harus diisi tindak lanjutnya');
                    valid = false;
                    e.preventDefault();
                    return false;
                }
                
                document.querySelectorAll('.tindak-jenis').forEach(el => {
                    if (el.value !== '') {
                        const nama = el.getAttribute('data-nama');
                        const waktuEl = document.querySelector(`.tindak-waktu[data-nama="${nama}"]`);
                        const kondisiEl = document.querySelector(`.tindak-kondisi[data-nama="${nama}"]`);
                        
                        if (!waktuEl || !waktuEl.value) {
                            if (waktuEl) markInvalid(waktuEl, 'Waktu wajib diisi');
                            valid = false;
                        } else if (waktuEl) markValid(waktuEl);
                        
                        if (!kondisiEl || !kondisiEl.value) {
                            if (kondisiEl) markInvalid(kondisiEl, 'Kondisi wajib dipilih');
                            valid = false;
                        } else if (kondisiEl) markValid(kondisiEl);
                    }
                });
            @else
                // Validasi untuk non-peralatan
                const waktuEl = form.querySelector('[name="waktu"]');
                const kondisiEl = form.querySelector('[name="kondisi"]');

                if (!waktuEl || !waktuEl.value) {
                    if (waktuEl) markInvalid(waktuEl, 'Waktu wajib diisi');
                    valid = false;
                } else if (waktuEl) markValid(waktuEl);

                if (!kondisiEl || !kondisiEl.value) {
                    if (kondisiEl) markInvalid(kondisiEl, 'Kondisi wajib dipilih');
                    valid = false;
                } else if (kondisiEl) markValid(kondisiEl);
            @endif

            // Validasi kondisi layanan
            const kondisiSetelahEl = form.querySelector('select[name="kondisi_setelah"]');
            if (!kondisiSetelahEl || !kondisiSetelahEl.value) {
                if (kondisiSetelahEl) markInvalid(kondisiSetelahEl, 'Kondisi layanan wajib dipilih');
                valid = false;
            } else if (kondisiSetelahEl) markValid(kondisiSetelahEl);

            if (!valid) {
                e.preventDefault();
                return false;
            }

            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>&nbsp;Menyimpan...';
        });
    }
});
</script>
@endpush