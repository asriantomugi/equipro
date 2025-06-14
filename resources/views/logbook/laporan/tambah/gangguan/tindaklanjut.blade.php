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
                            <a class="btn btn-info mx-2 my-1" href="{{ route('tambah.step2', ['layanan_id' => $laporan->layanan_id]) }}">
                                Step - 2 &nbsp;&nbsp; Input Jenis Laporan
                            </a>
                            <a class="btn btn-info mx-2 my-1" href="{{ route('tambah.gangguan.form', [
                                'laporan_id' => $laporan->id,
                                'layanan_id' => $laporan->layanan_id, 
                                'peralatan_id' => $laporan->peralatan_id
                            ]) }}">
                                Step - 3 &nbsp;&nbsp; Input Gangguan
                            </a>
                            <a class="btn btn-outline-secondary disabled mx-2 my-1" href="{{ route('tambah.gangguan.tindaklanjut', ['laporan_id' => $laporan->id]) }}">
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
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">FORM TINDAK LANJUT</h3>
                    </div>
                    <form action="{{ route('tambah.gangguan.tindaklanjut.simpan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                        <input type="hidden" name="gangguan_peralatan_id" value="{{ $gangguan_peralatan_id }}">

                        <div class="card-body">
                            <div class="form-group">
                                <label for="jenis_tindaklanjut">Jenis Tindaklanjut</label>
                                <select name="jenis_tindaklanjut" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    @foreach (config('constants.jenis_tindaklanjut') as $label => $value)
                                        <option value="{{ $value ? '1' : '0' }}">{{ ucfirst($label) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="waktu">Waktu Tindaklanjut</label>
                                <input type="datetime-local" name="waktu" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                            </div>
                        </div>
                        <!-- Tombol Navigasi -->
                        <div class="card-footer">
                            <a href="{{ route('tambah.gangguan.form', [
                                'laporan_id' => $laporan->id,
                                'layanan_id' => $laporan->layanan_id, 
                                'peralatan_id' => $laporan->peralatan_id
                            ]) }}" class="btn btn-secondary">Kembali</a>

                            <button type="submit" class="btn btn-primary float-right">Lanjut</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
