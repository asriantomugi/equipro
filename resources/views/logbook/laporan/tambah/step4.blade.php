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
                  <th style="width: 10px"><center>NO.</center></th>
                  <th><center>KODE</center></th>
                  <th><center>NAMA</center></th>
                  <th><center>MERK</center></th>
                  <th><center>JENIS ALAT</center></th>
                  <th><center>IP ADDRESS</center></th>
                  <th><center>KONDISI SEBELUM</center></th>
                  <th><center>KONDISI GANGGUAN</center></th>
                  <th><center>KONDISI TINDAKLANJUT</center></th>
                  <th style="width: 100px"></th>
                </tr>
              </thead>
              <tbody>
              {{-- Looping Data Peralatan --}}
@foreach ($layanan->daftarPeralatanLayanan as $index => $satu)
                <tr class="table-condensed">
                  <td></td>
                  <td><center>{{ strtoupper($satu->peralatan->kode) }}</center></td>
                  <td><center>{{ strtoupper($satu->peralatan->nama) }}</center></td>
                  <td><center>{{ strtoupper($satu->peralatan->merk) }}</center></td>
                  <td><center>{{ strtoupper($satu->peralatan->jenis->nama) }}</center></td>
                  <td><center>{{ strtoupper($satu->ip_address) }}</center></td>
  
                  {{-- Kondisi Peralatan Sebelum Gangguan --}}
  @if($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
  @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
  @elseif($satu->peralatan->kondisi === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
  @else
                  <td></td>
  @endif

                  {{-- Kondisi Peralatan Saat Gangguan --}}
  @if($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
  @elseif($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
  @elseif($satu->peralatan?->kondisiGangguan($laporan->id) === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
  @else
                  <td></td>
  @endif

                  {{-- Kondisi Peralatan Setelah Tindaklanjut --}}
  @if($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.normal'))
                  <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
  @elseif($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.normal_sebagian'))
                  <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
  @elseif($satu->peralatan?->kondisiTlGangguan($laporan->id) === config('constants.kondisi_peralatan.rusak'))
                  <td class="text-center"><span class="badge bg-danger">RUSAK </span></td>
  @else
                  <td></td>
  @endif
                  <td>
                    <center>
                      <button class="btn btn-secondary btn-sm" 
                              onclick="detail('{{ $satu->peralatan->id }}')"
                              title="Detail Peralatan">
                              <i class="fas fa-angle-double-right"></i>
                      </button>
                    </center>
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



  </div> <!-- container-fluid -->
</section> <!-- content -->

@endsection

