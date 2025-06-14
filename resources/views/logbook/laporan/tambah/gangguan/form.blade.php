@extends('logbook.main')

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Step Navigation -->
        <div class="row mb-2"> 
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2"> 
                        <div class="d-flex justify-content-center flex-wrap">
                            <a class="btn btn-info mx-2 my-1" href="{{ route('tambah.step1') }}">
                                Step - 1 &nbsp;&nbsp; Pilih Layanan
                            </a>
                            <a class="btn btn-info mx-2 my-1" href="{{ route('tambah.step2', ['layanan_id' => $layanan->id]) }}">
                                Step - 2 &nbsp;&nbsp; Input Jenis Laporan
                            </a>
                            <a class="btn btn-info mx-2 my-1" href="{{ route('tambah.gangguan.form') }}">
                                Step - 3 &nbsp;&nbsp; Input Gangguan
                            </a>
                            <a class="btn btn-outline-secondary disabled mx-2 my-1" href="#">
                                Step - 4 &nbsp;&nbsp; Tindaklanjut
                            </a>
                            <a class="btn btn-outline-secondary disabled mx-2 my-1" href="#">
                                Step - 5 &nbsp;&nbsp; Jenis TindakLanjut
                            </a>
                            <a class="btn btn-outline-secondary disabled mx-2 my-1" href="#">
                                Step - 6 &nbsp;&nbsp; Review
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Input Gangguan -->
        <form method="POST" action="{{ route('tambah.gangguan.submit') }}">
            @csrf
            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

            <!-- Header Form -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">FORM INPUT GANGGUAN - {{ $layanan->nama }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="waktu_gangguan">Waktu Gangguan</label>
                                <input type="datetime-local" class="form-control" name="waktu_gangguan" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Peralatan -->
            @if($layanan->daftarPeralatanLayanan && $layanan->daftarPeralatanLayanan->count())
                @foreach($layanan->daftarPeralatanLayanan as $index => $item)
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h6 class="mb-3 font-weight-bold">PERALATAN {{ $index + 1 }}: {{ $item->peralatan->nama ?? '-' }}</h6>
                                    <input type="hidden" name="peralatan[{{ $index }}][id]" value="{{ $item->peralatan->id }}">

                                        <!-- Kolom Kondisi -->
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Kondisi Peralatan</label>
                                                <select name="peralatan[{{ $index }}][kondisi]" class="form-control" required>
                                                    <option value="">- Pilih -</option>
                                                    @foreach (config('constants.kondisi_gangguan_peralatan') as $label => $value)
                                                        <option value="{{ $value }}" {{ old("peralatan.$index.kondisi") == $value ? 'selected' : '' }}>
                                                            {{ ucfirst($label) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Kolom Deskripsi -->
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Deskripsi Gangguan</label>
                                                <textarea class="form-control" name="peralatan[{{ $index }}][deskripsi]" rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-danger">Tidak ada peralatan pada layanan ini.</div>
            @endif

            <!-- Tombol Navigasi -->
            <div class="card-footer">
                <a href="{{ route('tambah.step2', ['layanan_id' => $layanan->id]) }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary float-right">Lanjut</button>
            </div>
        </form>
        </div>
        </div>
        </div>
    </div>
</section>
@endsection
