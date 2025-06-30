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
                            <li class="step-item active"><a href="#">Tindaklanjut</a></li>
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
                    <div class="card-header">
                        <h3 class="card-title">FORM TINDAK LANJUT</h3>
                    </div>
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
                        <form action="{{ route('tambah.simpanStep3') }}" method="POST" id="formStep3">
                            @csrf
                            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                            <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">

                            @if ($laporan->jenis == 1)
                                @foreach ($layanan->daftarPeralatanLayanan as $index => $dpl)
                                    @if ($index > 0)
                                        <hr>
                                    @endif
                                    <div class="mb-3">
                                        <strong>Peralatan {{ $index + 1 }}: <span class="badge bg-primary">{{ $dpl->peralatan->nama }}</span></strong>
                                    </div>

                                    {{-- Jenis --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Jenis <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="tindaklanjut[{{ $dpl->peralatan->id }}][jenis]" class="form-control tindak-jenis" data-nama="{{ $dpl->peralatan->nama }}">
                                                <option value="">- Pilih -</option>
                                                @foreach ($jenisTindakLanjut as $label => $value)
                                                    <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Waktu --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="datetime-local" name="tindaklanjut[{{ $dpl->peralatan->id }}][waktu]" class="form-control tindak-waktu" data-nama="{{ $dpl->peralatan->nama }}">
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Deskripsi</label>
                                        <div class="col-sm-9">
                                            <textarea name="tindaklanjut[{{ $dpl->peralatan->id }}][deskripsi]" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    {{-- Kondisi --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="tindaklanjut[{{ $dpl->peralatan->id }}][kondisi]" class="form-control tindak-kondisi" data-nama="{{ $dpl->peralatan->nama }}">
                                                <option value="">- Pilih -</option>
                                                @foreach ($kondisiTindaklanjut as $label => $value)
                                                    <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- Non-peralatan --}}
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="waktu" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9">
                                        <textarea name="deskripsi" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="kondisi" class="form-control">
                                            <option value="">- Pilih -</option>
                                            @foreach ($kondisiTindaklanjut as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            {{-- Kondisi Layanan --}}
                            <div class="form-group row mt-4">
                                <label class="col-sm-3 col-form-label">Update Kondisi Layanan <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="kondisi_setelah" class="form-control">
                                        <option value="">- Pilih -</option>
                                        @foreach ($kondisiSetelah as $label => $value)
                                            <option value="{{ $value ? '1' : '0' }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-angle-left"></i> &nbsp; Kembali
                                </a>
                                <button type="submit" class="btn btn-success btn-sm float-right">
                                    Lanjut &nbsp; <i class="fas fa-angle-right"></i>
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

@push('scripts')
<script>
function markInvalid(el, message) {
    el.classList.remove('is-valid');
    el.classList.add('is-invalid');
    const msg = document.createElement('div');
    msg.className = 'invalid-feedback dynamic';
    msg.innerHTML = message;
    if (!el.parentNode.querySelector('.invalid-feedback.dynamic')) {
        el.parentNode.appendChild(msg);
    }
}

function markValid(el) {
    el.classList.remove('is-invalid');
    el.classList.add('is-valid');
    const feedback = el.parentNode.querySelector('.invalid-feedback.dynamic');
    if (feedback) feedback.remove();
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formStep3');

    form.addEventListener('submit', function (e) {
        let valid = true;

        // Bersihkan sebelumnya
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

        @if ($laporan->jenis == 1)
            document.querySelectorAll('.tindak-jenis').forEach(el => {
                if (el.value === "") {
                    markInvalid(el, 'Jenis tindak lanjut wajib dipilih');
                    valid = false;
                } else markValid(el);
            });

            document.querySelectorAll('.tindak-waktu').forEach(el => {
                if (el.value === "") {
                    markInvalid(el, 'Waktu wajib diisi');
                    valid = false;
                } else markValid(el);
            });

            document.querySelectorAll('.tindak-kondisi').forEach(el => {
                if (el.value === "") {
                    markInvalid(el, 'Kondisi wajib dipilih');
                    valid = false;
                } else markValid(el);
            });
        @else
            const waktu = form.querySelector('[name="waktu"]');
            const kondisi = form.querySelector('[name="kondisi"]');

            if (!waktu.value) {
                markInvalid(waktu, 'Waktu wajib diisi');
                valid = false;
            } else markValid(waktu);

            if (!kondisi.value) {
                markInvalid(kondisi, 'Kondisi wajib dipilih');
                valid = false;
            } else markValid(kondisi);
        @endif

        const kondisiSetelah = form.querySelector('[name="kondisi_setelah"]');
            if (kondisiSetelah.value === "") {
                markInvalid(kondisiSetelah, 'Kondisi layanan wajib dipilih');
                valid = false;
            } else {
                markValid(kondisiSetelah);
            }

        if (!valid) e.preventDefault();
    });
});
</script>
@endpush
