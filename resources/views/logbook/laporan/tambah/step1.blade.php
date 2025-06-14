@extends('logbook.main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
    <!-- Step Navigation -->
        <div class="row mb-2"> 
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2"> 
                        <div class="container">
                            <ul class="step d-flex flex-nowrap">
                                <li class="step-item active"><a href="{{ route('tambah.step1') }}">Step 1</a></li>
                                <li class="step-item"><a href="#">Step 2</a></li>
                                <li class="step-item"><a href="#">Step 3</a></li>
                                <li class="step-item"><a href="#">Step 4</a></li>
                                <li class="step-item"><a href="#">Step 5</a></li>
                            </ul> 
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <form method="GET" action="{{ url('/logbook/laporan/tambah/step1') }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="fasilitas">Fasilitas</label>
                                    <select name="fasilitas" id="fasilitas" class="form-control">
                                        <option value="">Semua Fasilitas</option>
                                        @foreach($fasilitas as $fasilitas)
                                            <option value="{{ $fasilitas->id }}" {{ request('fasilitas') == $fasilitas->id ? 'selected' : '' }}>
                                                {{ $fasilitas->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="lokasi_tk_1_id">Lokasi Tingkat 1</label>
                                    <select name="LokasiTk1" id="lokasi_tk_1_id" class="form-control">
                                        <option value="">Semua Lokasi Tingkat 1</option>
                                        @foreach($LokasiTk1 as $lokasi)
                                            <option value="{{ $lokasi->id }}" {{ request('LokasiTk1') == $lokasi->id ? 'selected' : '' }}>
                                                {{ $lokasi->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="lokasi_tk_2_id">Lokasi Tingkat 2</label>
                                    <select name="LokasiTk2" id="lokasi_tk_2_id" class="form-control">
                                        <option value="">Semua Lokasi Tingkat 2</option>
                                        @foreach($LokasiTk2 as $lokasi)
                                            <option value="{{ $lokasi->id }}" {{ request('LokasiTk2') == $lokasi->id ? 'selected' : '' }}>
                                                {{ $lokasi->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="lokasi_tk_3_id">Lokasi Tingkat 3</label>
                                    <select name="LokasiTk3" id="lokasi_tk_3_id" class="form-control">
                                        <option value="">Semua Lokasi Tingkat 3</option>
                                        @foreach($LokasiTk3 as $lokasi)
                                            <option value="{{ $lokasi->id }}" {{ request('LokasiTk3') == $lokasi->id ? 'selected' : '' }}>
                                                {{ $lokasi->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-right">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Layanan -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">DAFTAR LAYANAN</h3>
                    </div>
                    <div class="card-body">
                        <table id="layanan" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><center>NO.</center></th>
                                    <th><center>KODE</center></th>
                                    <th><center>NAMA LAYANAN</center></th>
                                    <th><center>FASILITAS</center></th>
                                    <th><center>LOKASI T.1</center></th>
                                    <th><center>LOKASI T.2</center></th>
                                    <th><center>LOKASI T.3</center></th>
                                    <th><center>ACTION</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($layanan as $item)
                                    <tr>
                                        <td><center>{{ $loop->iteration }}</center></td>
                                        <td>{{ strtoupper($item->kode ?? '-') }}</td>
                                        <td>{{ strtoupper($item->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($item->fasilitas->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($item->getLokasiTk1->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($item->getLokasiTk2->nama ?? '-') }}</td>
                                        <td>{{ strtoupper($item->getLokasiTk3->nama ?? '-') }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-secondary btn-sm" 
                                                        onclick="detail('{{ $item->id }}')"
                                                        title="Detail">
                                                        <i class="fas fa-angle-double-right"></i>
                                            </button>
                                            <a href="{{ route('tambah.step2', ['layanan_id' => $item->id]) }}" 
                                                class="btn btn-primary btn-sm">Pilih
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data layanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-default" 
                        href="{{url('/logbook/laporan/daftar')}}" 
                        role="button">Batal</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lokasiTingkat1 = document.querySelector('#lokasi_tk_1_id');
        const lokasiTingkat2 = document.querySelector('#lokasi_tk_2_id');
        const lokasiTingkat3 = document.querySelector('#lokasi_tk_3_id');

        lokasiTingkat1.addEventListener('change', function () {
            if (this.value) {
                fetch(`/get-lokasi-tingkat-2/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        lokasiTingkat2.innerHTML = '<option value="">Semua Lokasi Tingkat 2</option>';
                        data.forEach(lokasi => {
                            lokasiTingkat2.innerHTML += `<option value="${lokasi.id}">${lokasi.nama}</option>`;
                        });
                    });
            }
        });

        lokasiTingkat2.addEventListener('change', function () {
            if (this.value) {
                fetch(`/get-lokasi-tingkat-3/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        lokasiTingkat3.innerHTML = '<option value="">Semua Lokasi Tingkat 3</option>';
                        data.forEach(lokasi => {
                            lokasiTingkat3.innerHTML += `<option value="${lokasi.id}">${lokasi.nama}</option>`;
                        });
                    });
            }
        });
    });
</script>
@endsection
