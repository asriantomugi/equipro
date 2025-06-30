@extends('logbook.main')

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- Stepper -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <br>
            <div class="stepper-wrapper">
              <div class="stepper-item completed">
                <div class="step-counter">1</div>
                <div class="step-name">Pilih Layanan</div>
              </div>
              <div class="stepper-item completed">
                <div class="step-counter">2</div>
                <div class="step-name">Jenis Gangguan</div>
              </div>
              <div class="stepper-item completed">
                <div class="step-counter">3</div>
                <div class="step-name">Tindak Lanjut</div>
              </div>
              <div class="stepper-item completed">
                <div class="step-counter">4</div>
                <div class="step-name">Perbaikan</div>
              </div>
              <div class="stepper-item active">
                <div class="step-counter">5</div>
                <div class="step-name">Review</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <form action="{{ route('tambah.simpanStep5') }}" method="POST">
      @csrf
      <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

      <!-- Informasi Layanan -->
      <div class="row">
        <div class="col-lg-12 col-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">INFORMASI LAYANAN</h3>
            </div>
            <div class="card-body">
              <table border='0' cellpadding='5px'>
                <tr><th>Fasilitas</th><td>:</td><td>{{ $laporan->layanan->fasilitas->kode }} - {{ $laporan->layanan->fasilitas->nama }}</td></tr>
                <tr><th>Kode Layanan</th><td>:</td><td>{{ $laporan->layanan->kode }}</td></tr>
                <tr><th>Nama Layanan</th><td>:</td><td>{{ $laporan->layanan->nama }}</td></tr>
                <tr><th>Lokasi Tingkat I</th><td>:</td><td>{{ $laporan->layanan->lokasiTk1->kode }} - {{ $laporan->layanan->lokasiTk1->nama }}</td></tr>
                <tr><th>Lokasi Tingkat II</th><td>:</td><td>{{ $laporan->layanan->lokasiTk2->kode }} - {{ $laporan->layanan->lokasiTk2->nama }}</td></tr>
                <tr><th>Lokasi Tingkat III</th><td>:</td><td>{{ $laporan->layanan->lokasiTk3->kode }} - {{ $laporan->layanan->lokasiTk3->nama }}</td></tr>
                <tr><th>Kondisi Awal</th><td>:</td>
                  <td>
                    @if($laporan->layanan->kondisi == config('constants.kondisi_layanan.Serviceable'))
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

      <!-- Informasi Gangguan -->
      <div class="row">
        <div class="col-lg-12 col-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">INFORMASI GANGGUAN</h3>
            </div>
            <div class="card-body">
              <table border="0" cellpadding="5px">
                <tr><th>Jenis Gangguan</th><td>:</td><td>{{ $laporan->jenis == 1 ? 'Gangguan Peralatan' : 'Gangguan Non-Peralatan' }}</td></tr>
                <tr><th>Waktu Gangguan</th><td>:</td><td>{{ \Carbon\Carbon::parse($laporan->waktu)->format('d-m-Y H:i') }}</td></tr>

                @if($laporan->jenis == 1)
                  @foreach ($detailGangguanPeralatan as $i => $gangguan)
                    <tr><th colspan="3"><strong>Peralatan : {{ $i + 1 }}</strong></th></tr>
                    <tr><th>Nama Peralatan</th><td>:</td><td>{{ $gangguan->peralatan->nama }}</td></tr>
                    <tr><th>Kondisi</th><td>:</td>
                      <td>
                        @if($gangguan->kondisi == config('constants.kondisi_peralatan.Unserviceable'))
                          <span class="badge bg-danger">TIDAK BERFUNGSI</span>
                        @else
                          <span class="badge bg-success">BERFUNGSI</span>
                        @endif
                      </td>
                    </tr>
                    <tr><th>Deskripsi</th><td>:</td><td>{{ $gangguan->deskripsi }}</td></tr>
                  @endforeach
                @else
                  <tr><th>Deskripsi Gangguan</th><td>:</td><td>{{ $laporan->gangguanNonPeralatan->deskripsi ?? '-' }}</td></tr>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Tindak Lanjut -->
      <!-- Tindak Lanjut -->
<div class="row">
  <div class="col-lg-12 col-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">TINDAK LANJUT</h3>
      </div>
      <div class="card-body">
        <table border="0" cellpadding="5px">

          @if($laporan->jenis == 1)
            <tr>
              <th>Jenis Tindak Lanjut</th><td>:</td>
              <td>
                @if($tindaklanjut)
                  @php
                    $jenis = config('constants.jenis_tindaklanjut');
                    $namaTindakLanjut = array_search((int)$tindaklanjut->jenis_tindaklanjut, $jenis);
                  @endphp
                  {{ strtoupper($namaTindakLanjut) }}
                @else
                  -
                @endif
              </td>
            </tr>

            @if($tindaklanjut && $tindaklanjut->jenis_tindaklanjut == config('constants.jenis_tindaklanjut.penggantian'))
              <tr>
                <th>Peralatan Lama</th><td>:</td>
                <td>{{ $penggantian->peralatanLama->nama ?? '-' }}</td>
              </tr>
              <tr>
                <th>Peralatan Baru</th><td>:</td>
                <td>{{ $penggantian->peralatanBaru->nama ?? '-' }}</td>
              </tr>
            @endif

            <tr>
              <th>Deskripsi Tindak Lanjut</th><td>:</td>
              <td>{{ $tindaklanjut->deskripsi ?? '-' }}</td>
            </tr>
          @else
            <tr>
              <th>Tanggal</th><td>:</td>
              <td>{{ \Carbon\Carbon::parse($tindaklanjut->tanggal ?? now())->format('d-m-Y') }}</td>
            </tr>
            <tr>
              <th>Waktu</th><td>:</td>
              <td>{{ \Carbon\Carbon::parse($tindaklanjut->waktu ?? now())->format('H:i') }}</td>
            </tr>
            <tr>
              <th>Deskripsi</th><td>:</td>
              <td>{{ $tindaklanjut->deskripsi ?? '-' }}</td>
            </tr>
          @endif

        </table>
      </div>
    </div>
  </div>
</div>

      <!-- Kondisi Setelah -->
      <div class="row">
        <div class="col-lg-12 col-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">KONDISI SETELAH TINDAK LANJUT</h3>
            </div>
            <div class="card-body">
              <table border="0" cellpadding="5px">
                <tr><th>Kondisi Setelah</th><td>:</td>
                  <td>
                    @if($laporan->kondisi_layanan_temp)
                      <span class="badge bg-success">SUDAH NORMAL</span>
                    @else
                      <span class="badge bg-danger">BELUM NORMAL</span>
                    @endif
                  </td>
                </tr>
              </table>
            </div>
            <!-- Tombol Simpan -->
            <div class="card-footer">
              <a href="{{ route('tambah.step4', ['laporan_id' => $laporan->id]) }}" class="btn btn-default btn-sm">
                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
              </a>
              <button type="submit" class="btn btn-success btn-sm float-right">
                <i class="fas fa-check-circle"></i>&nbsp;&nbsp;Simpan Laporan
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection
