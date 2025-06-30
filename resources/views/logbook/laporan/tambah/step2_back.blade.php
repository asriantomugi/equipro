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
                            <li class="step-item active"><a href="#">Input Gangguan</a></li>
                            <li class="step-item"><a href="#">Tindaklanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('tambah.step2.back.simpan') }}" id="step2-form">
            @csrf
            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">
            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

            {{-- DATA LAYANAN --}}
            <div class="card mb-2">
                <div class="card-header"><h3 class="card-title">DATA LAYANAN</h3></div>
                <div class="card-body">
                    @php
                        $fasilitasNama = $layanan->fasilitas->nama ?? '-';
                        $lokasiTkt1Nama = $layanan->LokasiTk1->nama ?? '-';
                        $lokasiTkt2Nama = $layanan->LokasiTk2->nama ?? '-';
                        $lokasiTkt3Nama = $layanan->LokasiTk3->nama ?? '-';
                    @endphp

                    @foreach([
                        'KODE' => $layanan->kode,
                        'NAMA' => $layanan->nama,
                        'FASILITAS' => $fasilitasNama,
                        'LOKASI TINGKAT 1' => $lokasiTkt1Nama,
                        'LOKASI TINGKAT 2' => $lokasiTkt2Nama,
                        'LOKASI TINGKAT 3' => $lokasiTkt3Nama,
                        'STATUS' => $layanan->status ? 'Aktif' : 'Tidak Aktif',
                        'KONDISI' => $layanan->kondisi == config('constants.kondisi_layanan.Serviceable') ? 'Serviceable' : 'Unserviceable',
                    ] as $label => $value)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ $label }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $value }}" readonly>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">JENIS LAPORAN <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="jenis_laporan" id="jenis_laporan" class="form-control" required>
                                <option value="">- Pilih -</option>
                                @foreach ($jenisLaporan as $key => $value)
                                    <option value="{{ $key }}" {{ (old('jenis_laporan', $selectedJenisLaporan) == $key) ? 'selected' : '' }}>
                                        {{ Str::title(str_replace('_', ' ', $key)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_laporan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            {{-- INPUT GANGGUAN --}}
            <div class="card d-none" id="card-input-gangguan">
                <div class="card-header"><h3 class="card-title">INPUT GANGGUAN</h3></div>
                <div class="card-body" id="form-gangguan-container">
                    {{-- Diisi dinamis oleh JavaScript --}}
                </div>
            </div>

            {{-- BUTTON --}}
            <div class="card mt-2">
                <div class="card-footer">
                    <a href="{{ route('tambah.step1', ['laporan_id' => $laporan->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-angle-left"></i>&nbsp;Kembali 
                    </a>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        Lanjut <i class="fas fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
const kondisiPeralatan = @json(config('constants.kondisi_peralatan'));
const peralatan = @json($layanan->daftarPeralatanLayanan);
const jenisLaporanAwal = @json($selectedJenisLaporan ?? old('jenis_laporan'));
const waktuGangguanAwal = "{{ $waktuGangguan ? \Carbon\Carbon::parse($waktuGangguan)->format('Y-m-d\\TH:i') : old('waktu_gangguan') }}";
const gangguanPeralatan = @json($gangguanPeralatan);
const gangguanNonPeralatan = @json($gangguanNonPeralatan);
 console.log('selectedJenisLaporan dari Blade:', @json($selectedJenisLaporan));
  console.log('old(jenis_laporan):', "{{ old('jenis_laporan') }}");
  console.log('laporan->jenis_laporan:', "{{ $laporan->jenis_laporan }}");


function buildGangguanForm(jenis) {
    const container = document.getElementById('form-gangguan-container');
    const card = document.getElementById('card-input-gangguan');
    let html = '';

    card.classList.remove('d-none');

    html += `
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Waktu Gangguan <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="datetime-local" name="waktu_gangguan" class="form-control" value="${waktuGangguanAwal}">
            </div>
        </div>
    `;

    if (jenis === 'gangguan_peralatan') {
        peralatan.forEach((item, index) => {
            const gItem = gangguanPeralatan.find(g => g.peralatan_id == item.peralatan.id) || {};
            html += `
                <hr>
                <div><strong>Peralatan ${index + 1}: <span class="badge bg-primary">${item.peralatan?.nama ?? '-'}</span></strong></div>
                <input type="hidden" name="peralatan[${index}][id]" value="${item.peralatan?.id}">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="peralatan[${index}][kondisi]" class="form-control">
                            <option value="">- Pilih -</option>
                            ${Object.entries(kondisiPeralatan).map(([label, val]) => {
                                const valOption = val === true ? 1 : 0;
                                const selected = gItem.kondisi == valOption ? 'selected' : '';
                                return `<option value="${valOption}" ${selected}>${label}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
                    <div class="col-sm-9">
                        <textarea name="peralatan[${index}][deskripsi]" class="form-control" rows="3">${gItem.deskripsi ?? ''}</textarea>
                    </div>
                </div>
            `;
        });
    } else {
        html += `
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
                <div class="col-sm-9">
                    <textarea name="deskripsi_gangguan" class="form-control" rows="3">${gangguanNonPeralatan?.deskripsi ?? ''}</textarea>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function () {
    if (jenisLaporanAwal) {
        buildGangguanForm(jenisLaporanAwal);
        document.getElementById('card-input-gangguan').classList.remove('d-none');
    }

    document.getElementById('jenis_laporan').addEventListener('change', function () {
        if (this.value) buildGangguanForm(this.value);
        else {
            document.getElementById('card-input-gangguan').classList.add('d-none');
            document.getElementById('form-gangguan-container').innerHTML = '';
        }
    });
});
</script>
@endpush
