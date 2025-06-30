@extends('logbook.main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <!-- Step Navigation -->
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap justify-content-between mb-0">
                            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Step 1</a></li>
                            <li class="step-item completed"><a href="{{ route('tambah.step2') }}">Step 2</a></li>
                            <li class="step-item active"><a href="{{ route('tambah.step3', ['laporan_id' => $laporan->id]) }}">Step 3</a></li>
                            <li class="step-item"><a href="#">Step 4</a></li>
                            <li class="step-item"><a href="#">Step 5</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tindak Lanjut -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Tindak Lanjut</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tambah.simpanStep3') }}" method="POST">
                            @csrf

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                            <input type="hidden" name="layanan_id" value="{{ $laporan->layanan_id }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">

                            @if ($laporan->jenis == 1)
                                {{-- Gangguan Peralatan --}}
                                <div class="form-group row">
                                    <label for="jenis_tindaklanjut" class="col-sm-2 col-form-label">Jenis Tindak Lanjut</label>
                                    <div class="col-sm-6">
                                        <select name="jenis_tindaklanjut" id="jenis_tindaklanjut" class="form-control" required>
                                            <option value="">- Pilih -</option>
                                            @foreach ($jenisTindakLanjut as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="waktu" class="col-sm-2 col-form-label">Waktu</label>
                                    <div class="col-sm-6">
                                        <input type="datetime-local" name="waktu" id="waktu" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="deskripsi" class="col-sm-2 col-form-label">Deskripsi Perbaikan</label>
                                    <div class="col-sm-6">
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="kondisi" class="col-sm-2 col-form-label">Kondisi Peralatan Setelah Perbaikan</label>
                                    <div class="col-sm-6">
                                        <select name="kondisi" id="kondisi" class="form-control" required>
                                            <option value="">- Pilih -</option>
                                            @foreach ($kondisiSetelahPerbaikan as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}">{{ ucfirst($label) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                {{-- Gangguan Non-Peralatan --}}
                                <div class="form-group row">
                                    <label for="waktu" class="col-sm-2 col-form-label">Waktu</label>
                                    <div class="col-sm-6">
                                        <input type="datetime-local" name="waktu" id="waktu" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="deskripsi" class="col-sm-2 col-form-label">Deskripsi</label>
                                    <div class="col-sm-6">
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
                                    </div>
                                </div>
                            @endif
                        
                        </div>
                        <!-- Tombol Navigasi -->
                        <div class="card-footer">
                            <a href="{{ route('tambah.step2') }}" class="btn btn-default btn-sm" role="button">
                                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali
                            </a>
                            <button type="submit" class="btn btn-success btn-sm float-right">
                                Lanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                            </button>
                        </div> 
                        </form>    
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
