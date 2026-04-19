@extends('logbook.main')

@section('content')

<section class="content">
  <div class="container-fluid">

    {{-- Step Navigation --}}
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body py-2">
                    <ul class="step d-flex flex-nowrap">
                        <li class="step-item completed"><a href="{{ route('logbook.laporan.tambah.step1.form') }}">Pilih Layanan</a></li>
                        <li class="step-item completed"><a href="#">Input Gangguan & Tindaklanjut</a></li>
@if($jenis_tindaklanjut != null)
    @if($jenis_tindaklanjut == 2)
                        <li class="step-item completed"><a href="#">Penggantian Alat</a></li>
    @endif
@endif
                        <li class="step-item active"><a href="#">Review</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Layanan --}}
    <div class="row">
      <div class="col-lg-12 col-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">DATA LAYANAN GANGGUAN</h3>
          </div>
          <!-- /.card-header -->

          <div class="card-body">

            <table border='0' cellpadding='5px'>          
            <tr><th>Fasilitas</th><td>:</td><td>{{ $layanan->fasilitas->kode }} - {{ $layanan->fasilitas->nama }}</td></tr>
            <tr><th>Kode</th><td>:</td><td>{{ $layanan->kode }}</td></tr>
            <tr><th>Nama</th><td>:</td><td>{{ $layanan->nama }}</td></tr>
            <tr><th>Lokasi Tingkat I</th><td>:</td><td>{{ $layanan->lokasiTk1->kode }} - {{ $layanan->LokasiTk1->nama }}</td></tr>
            <tr><th>Lokasi Tingkat II</th><td>:</td><td>{{ $layanan->lokasiTk2->kode }} - {{ $layanan->LokasiTk2->nama }}</td></tr>
            <tr><th>Lokasi Tingkat III</th><td>:</td><td>{{ $layanan->lokasiTk3->kode }} - {{ $layanan->LokasiTk3->nama }}</td></tr>
@if($laporan->kondisi_layanan_open == config('constants.kondisi_layanan.serviceable'))
            <tr><th>Kondisi Layanan Saat Gangguan</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
@else
            <tr><th>Kondisi Layanan Saat Gangguan</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
@endif

@if($laporan->kondisi_layanan_temp == config('constants.kondisi_layanan.serviceable'))
            <tr><th>Kondisi Layanan Saat Ini</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
@else
            <tr><th>Kondisi Layanan Saat Ini</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
@endif

@if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan'))
            <tr><th>Jenis Laporan</th><td>:</td><td><span class="badge bg-warning">GANGGUAN PERALATAN</span></td></tr>
@elseif($laporan->jenis == config('constants.jenis_laporan.gangguan_non_peralatan'))
            <tr><th>Jenis Laporan</th><td>:</td><td><span class="badge bg-warning">GANGGUAN NON PERALATAN</span></td></tr>
@endif
            </table>

          </div>
          <!-- /.card-body -->
          
        </div>
        <!-- /.card -->
      </div> <!-- col-lg-12 col-6 -->
    </div> <!-- row -->

    {{-- Daftar Peralatan --}}
    <div class="row">
      <div class="col-lg-12 col-6">
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">DAFTAR PERALATAN</h3>
          </div>
          <!-- /.card-header -->

          <div class="card-body">

            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr class="table-condensed">
                  <th class="text-center" style="width: 10px">NO.</th>
                  <th class="text-center">KODE</th>
                  <th class="text-center">NAMA</th>
                  <th class="text-center">MERK</th>
                  <th class="text-center">JENIS ALAT</th>
                  <th class="text-center">IP ADDRESS</th>
  @if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan'))
                  <th class="text-center">KONDISI SEBELUM</th>
                  <th class="text-center">KONDISI GANGGUAN</th>
                  <th class="text-center">KONDISI TINDAK LANJUT</th>
  @elseif($laporan->jenis == config('constants.jenis_laporan.gangguan_non_peralatan'))
                  <th class="text-center">KONDISI</th>
  @endif
                  <th style="width: 100px"></th>
                </tr>
              </thead>
              <tbody>
              {{-- Looping Data Peralatan --}}
@foreach ($layanan->daftarPeralatanLayanan as $index => $satu)
                <tr class="table-condensed">
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td class="text-center">{{ strtoupper($satu->peralatan->kode) }}</td>
                  <td class="text-center">{{ strtoupper($satu->peralatan->nama) }}</td>
                  <td class="text-center">{{ strtoupper($satu->peralatan->merk) }}</td>
                  <td class="text-center">{{ strtoupper($satu->peralatan->jenis->nama) }}</td>
                  <td class="text-center">{{ strtoupper($satu->ip_address) }}</td>

  @if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan'))
  
                  {{-- Kondisi Peralatan Sebelum Gangguan --}}
              @if($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
              @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
              @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
              @else
                  <td class="text-center">-</td>
              @endif

                  {{-- Kondisi Peralatan Saat Gangguan --}}
              @if($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
              @elseif($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
              @elseif($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
              @else
                  <td class="text-center">-</td>
              @endif

                  {{-- Kondisi Peralatan Setelah Tindaklanjut --}}
              @if($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
              @elseif($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
              @elseif($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK </span></td>
              @else
                  <td class="text-center">-</td>
              @endif

  @elseif($laporan->jenis == config('constants.jenis_laporan.gangguan_non_peralatan'))

              {{-- Kondisi Peralatan --}}
              @if($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
              @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
              @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
              @else
                  <td class="text-center">-</td>
              @endif
  @endif

                  <td class="text-center">
                      <button class="btn btn-secondary btn-sm" 
                              onclick="detail('{{ $satu->peralatan->id }}')"
                              title="Detail Peralatan">
                              <i class="fas fa-angle-double-right"></i>
                      </button>
                  </td>
                </tr>
@endforeach                   
              </tbody>
            </table>

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->

    {{-- Data Gangguan --}}
    <div class="row">
      <div class="col-lg-12 col-6">
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">DATA GANGGUAN - TINDAKLANJUT PERALATAN</h3>
          </div>
          <!-- /.card-header -->

          <div class="card-body">
          
            {{-- Looping Data Gangguan Peralatan --}}
@foreach ($laporan->gangguanPeralatan as $index => $satu)
            <table border='0' cellpadding='5px'>        
              <tr><th>Kode</th><td>:</td><td>{{ strtoupper($satu->peralatan?->kode) }}</td></tr>
              <tr><th>Nama</th><td>:</td><td>{{ strtoupper($satu->peralatan?->nama) }}</td></tr>
              <tr></tr>
              <tr><th>Waktu Gangguan</th><td>:</td><td>{{ strtoupper($satu->waktu_formatted ?? '-') }}</td></tr>
              
  @if($satu->kondisi === config('constants.kondisi_peralatan.normal'))
              <tr><th>Kondisi Peralatan Saat Gangguan</th><td>:</td><td><span class="badge bg-success">NORMAL</span></td></tr>
  @elseif($satu->kondisi === config('constants.kondisi_peralatan.normal_sebagian'))
              <tr><th>Kondisi Peralatan Saat Gangguan</th><td>:</td><td><span class="badge bg-warning">NORMAL SEBAGIAN</span></td></tr>      
  @elseif($satu->kondisi === config('constants.kondisi_peralatan.rusak'))
              <tr><th>Kondisi Peralatan Saat Gangguan</th><td>:</td><td><span class="badge bg-danger">RUSAK</span></td></tr>
  @else
              <tr><th>Kondisi Peralatan Saat Gangguan</th><td>:</td><td>-</td></tr>
  @endif
              <tr><th>Deskripsi Gangguan</th><td>:</td><td>{{ strtoupper($satu->deskripsi) }}</td></tr>
              
              <tr><th>Tindak Lanjut</th><td>:</td><td></td></tr>
            </table>

            <div class="my-3">

            {{-- Daftar Tindak Lanjut Gangguan --}}
            <table id="example" class="table table-bordered table-striped">
              <thead>
                <tr class="table-condensed">
                  <th class="text-center" style="width: 10px">NO.</th>
                  <th class="text-center">WAKTU MULAI</th>
                  <th class="text-center">WAKTU SELESAI</th>
                  <th class="text-center">JENIS</th>
                  <th class="text-center" style="width: 600px">DESKRIPSI</th>
                  <th class="text-center">KONDISI AKHIR</th>
                </tr>
              </thead>
              <tbody>
              {{-- Looping Data Peralatan --}}
  @foreach ($laporan->TlgangguanPeralatan as $index => $satu)
                <tr class="table-condensed">
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td class="text-center">{{ strtoupper($satu->waktu_mulai_formatted) }}</td>
                  <td class="text-center">{{ strtoupper($satu->waktu_selesai_formatted) }}</td>

    @if($satu->jenis === config('constants.jenis_tindaklanjut_gangguan_peralatan.perbaikan'))
                  <td class="text-center"><span class="badge bg-warning">PERBAIKAN</span></td>
    @elseif($satu->jenis === config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))
                  <td class="text-center"><span class="badge bg-warning">PENGGANTIAN</span></td>
    @else
                  <td class="text-center">-</td>
    @endif

                  <td class="text-center">{{ strtoupper($satu->deskripsi) }}</td>
  
                  {{-- Kondisi Peralatan Setelah Tindaklanjut --}}
    @if($satu->kondisi === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
    @elseif($satu->kondisi === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
    @elseif($satu->kondisi === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
    @else
                  <td class="text-center">-</td>
    @endif
                </tr>
  @endforeach                   
              </tbody>
            </table>
            <hr class="my-4" style="border-top: 3px solid #a8a5a5;">
@endforeach

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->



  </div> <!-- container-fluid -->
</section> <!-- content -->

@endsection

