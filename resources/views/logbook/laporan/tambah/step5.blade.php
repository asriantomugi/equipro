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
              <li class="step-item completed"><a href="{{ route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}">Tindaklanjut</a></li>
              <li class="step-item completed"><a href="{{ route('tambah.step4.back', ['laporan_id' => $laporan->id]) }}">Penggantian</a></li>
              <li class="step-item active"><a href="#">Review</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <form action="{{ route('tambah.simpanStep5') }}" method="POST">
      @csrf
      <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

      {{-- Informasi Layanan --}}
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><h3 class="card-title">INFORMASI LAYANAN</h3></div>
            <div class="card-body">
              <table border="0" cellpadding="5px">
                <tr><th>Fasilitas</th><td>:</td><td>{{ $laporan->layanan->fasilitas->kode }} - {{ $laporan->layanan->fasilitas->nama }}</td></tr>
                <tr><th>Kode Layanan</th><td>:</td><td>{{ $laporan->layanan->kode }}</td></tr>
                <tr><th>Nama Layanan</th><td>:</td><td>{{ $laporan->layanan->nama }}</td></tr>
                <tr><th>Lokasi Tingkat I</th><td>:</td><td>{{ $laporan->layanan->lokasiTk1->kode }} - {{ $laporan->layanan->lokasiTk1->nama }}</td></tr>
                <tr><th>Lokasi Tingkat II</th><td>:</td><td>{{ $laporan->layanan->lokasiTk2->kode }} - {{ $laporan->layanan->lokasiTk2->nama }}</td></tr>
                <tr><th>Lokasi Tingkat III</th><td>:</td><td>{{ $laporan->layanan->lokasiTk3->kode }} - {{ $laporan->layanan->lokasiTk3->nama }}</td></tr>
                <tr>
                  <th>Kondisi Saat Ini</th><td>:</td>
                  <td>
                    @if($laporan->kondisi_layanan_temp)
                      <span class="badge bg-success">SERVICEABLE</span>
                    @else
                      <span class="badge bg-danger">UNSERVICEABLE</span>
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- Informasi Gangguan --}}
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><h3 class="card-title">INFORMASI GANGGUAN</h3></div>
            <div class="card-body">
              @if($laporan->jenis == 1)
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th><center>No</center></th>
                    <th><center>Kode</center></th>
                    <th><center>Nama Peralatan</center></th>
                    <th><center>Waktu</center></th>
                    <th><center>Deskripsi</center></th>
                    <th><center>Kondisi</center></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($detailGangguanPeralatan as $i => $gangguan)
                  <tr>
                    <td><center>{{ $i + 1 }}</center></td>
                    <td><center>{{ strtoupper($gangguan->peralatan->kode ?? '-') }}</center></td>
                    <td><center>{{ strtoupper($gangguan->peralatan->nama ?? '-') }}</center></td>
                    <td><center>{{ \Carbon\Carbon::parse($gangguan->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                    <td><center>{{ strtoupper($gangguan->deskripsi ?? '-') }}</center></td>
                    <td>
                      <center>
                        @if(isset($gangguan->kondisi) && $gangguan->kondisi == config('constants.kondisi_peralatan.Normal'))
                          <span class="badge bg-danger">RUSAK</span>
                        @else
                          <span class="badge bg-success">NORMAL</span>
                        @endif
                      </center>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="6" class="text-center">Tidak ada data gangguan peralatan</td></tr>
                  @endforelse
                </tbody>
              </table>
              @else
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th><center>No</center></th>
                    <th><center>Waktu</center></th>
                    <th><center>Deskripsi</center></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><center>1</center></td>
                    <td><center>{{ \Carbon\Carbon::parse($laporan->gangguanNonPeralatan->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                    <td><center>{{ strtoupper($laporan->gangguanNonPeralatan->deskripsi ?? '-') }}</center></td>
                  </tr>
                </tbody>
              </table>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Tindak Lanjut --}}
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">TINDAK LANJUT</h3>
            </div>
            <div class="card-body">
              @if($laporan->jenis == 1 && $penggantian->count() > 0)
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th><center>No</center></th>
                    <th><center>Jenis</center></th>
                    <th><center>Peralatan Lama</center></th>
                    <th><center>Peralatan Baru</center></th>
                    <th><center>Waktu</center></th>
                    <th><center>Deskripsi</center></th>
                    <th><center>Kondisi</center></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($penggantian as $i => $item)
                  @php
                    $jenis = $item->tindaklanjut && $item->tindaklanjut->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.perbaikan') ? 'PERBAIKAN' : 'PENGGANTIAN';
                    $kondisiTindaklanjut = $item->tindaklanjut->kondisi == config('constants.kondisi_tindaklanjut.beroperasi')
                      ? '<span class="badge bg-success">BEROPERASI</span>'
                      : '<span class="badge bg-danger">GANGGUAN</span>';
                  @endphp
                  <tr>
                    <td><center>{{ $i + 1 }}</center></td>
                    <td><center>{{ $jenis }}</center></td>
                    <td><center>{{ strtoupper($item->peralatanLama->nama ?? '-') }}</center></td>
                    <td><center>{{ strtoupper($item->peralatanBaru->nama ?? '-') }}</center></td>
                    <td><center>{{ \Carbon\Carbon::parse($item->tindaklanjut->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                    <td><center>{{ strtoupper($item->tindaklanjut->deskripsi ?? '-') }}</center></td>
                    <td><center>{!! $kondisiTindaklanjut !!}</center></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @elseif($tindaklanjut)
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th><center>No</center></th>
                    <th><center>Waktu</center></th>
                    <th><center>Deskripsi</center></th>
                    <th><center>Kondisi</center></th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $kondisiTindaklanjut = $tindaklanjut->kondisi == config('constants.kondisi_tindaklanjut.beroperasi')
                      ? '<span class="badge bg-success">BEROPERASI</span>'
                      : '<span class="badge bg-danger">GANGGUAN</span>';
                  @endphp
                  <tr>
                    <td><center>1</center></td>
                    <td><center>{{ \Carbon\Carbon::parse($tindaklanjut->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                    <td><center>{{ strtoupper($tindaklanjut->deskripsi ?? '-') }}</center></td>
                    <td><center>{!! $kondisiTindaklanjut !!}</center></td>
                  </tr>
                </tbody>
              </table>
              @else
                <p class="text-center">Tidak ada data tindak lanjut yang tersedia.</p>
              @endif
            </div>
            <div class="card-footer">
              <a href="{{ $penggantian->count() > 0 ? route('tambah.step4.back', ['laporan_id' => $laporan->id]) : route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}" class="btn btn-success btn-sm">
                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
              </a>
              <button type="submit" class="btn btn-success btn-sm float-right">
                <i class="fas fa-check-circle"></i>&nbsp;&nbsp;Submit
              </button>
            </div>
          </div>
        </div>
      </div>


    </form>
  </div>
</section>
@endsection
