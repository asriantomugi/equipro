@extends('logbook.main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')

<section class="content">
  <div class="container-fluid">

    {{------------------------------------------- Step Navigation ---------------------------------------------}}
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

    {{--------------------------------------------- Data Layanan ---------------------------------------------------}}
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
            
            <tr><th>Waktu Mulai Gangguan</th><td>:</td><td>{{ strtoupper($laporan->waktu_layanan_open_formatted ?? '-') }}</td></tr>
        
@if($laporan->kondisi_layanan_open == config('constants.kondisi_layanan.serviceable'))
            <tr><th>Kondisi Saat Gangguan</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
@else
            <tr><th>Kondisi Saat Gangguan</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
@endif

            <tr><th>Waktu Selesai Gangguan</th><td>:</td><td>{{ strtoupper($laporan->waktu_layanan_close_formatted ?? '-') }}</td></tr>
@if($laporan->kondisi_layanan_temp == config('constants.kondisi_layanan.serviceable'))
            <tr><th>Kondisi Saat Ini</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
@else
            <tr><th>Kondisi Saat Ini</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
@endif

@if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan'))
            <tr><th>Jenis Laporan</th><td>:</td><td><span class="badge bg-success">GANGGUAN PERALATAN</span></td></tr>
@elseif($laporan->jenis == config('constants.jenis_laporan.gangguan_non_peralatan'))
            <tr><th>Jenis Laporan</th><td>:</td><td><span class="badge bg-success">GANGGUAN NON PERALATAN</span></td></tr>
@endif

@if($laporan->status == config('constants.status_laporan.open'))
            <tr><th>Status Laporan</th><td>:</td><td><span class="badge bg-danger">OPEN</span></td></tr>
@elseif($laporan->jenis == config('constants.status_laporan.close'))
            <tr><th>Status Laporan</th><td>:</td><td><span class="badge bg-success">CLOSE</span></td></tr>
@elseif($laporan->jenis == config('constants.status_laporan.draft'))
            <tr><th>Status Laporan</th><td>:</td><td><span class="badge bg-warning">DRAFT</span></td></tr>
@endif
            </table>

          </div>
          <!-- /.card-body -->
          
        </div>
        <!-- /.card -->
      </div> <!-- col-lg-12 col-6 -->
    </div> <!-- row -->

    {{-------------------------------------------------- Daftar Peralatan --------------------------------------------------}}
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

    {{------------------------------------------- Data Gangguan -------------------------------------------}}
    <div class="row">
      <div class="col-lg-12 col-6">
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">DATA GANGGUAN - TINDAKLANJUT GANGGUAN</h3>
          </div>
          <!-- /.card-header -->

          <div class="card-body">
          
            {{-- Looping Data Gangguan Peralatan untuk Jenis Gangguan Peralatan --}}

@if($laporan->jenis == config('constants.jenis_laporan.gangguan_peralatan'))
    @foreach ($laporan->gangguanPeralatan as $index => $satu)
            <table border='0' cellpadding='5px'>        
              <tr><th>Kode</th><td>:</td><td>{{ strtoupper($satu->peralatan?->kode) }}</td></tr>
              <tr><th>Nama</th><td>:</td><td>{{ strtoupper($satu->peralatan?->nama) }}</td></tr>
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
                  <th class="text-center" style="width: 500px">DESKRIPSI</th>
                  <th class="text-center">KONDISI AKHIR</th>
                  <th class="text-center">DETAIL</th>
                </tr>
              </thead>
              <tbody>
              {{-- Looping Data Peralatan --}}
        @foreach ($laporan->tlGangguanPeralatan as $index => $satu)
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

                    <td>{{ strtoupper($satu->deskripsi) }}</td>

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

                @if($satu->jenis === config('constants.jenis_tindaklanjut_gangguan_peralatan.penggantian'))
                    <td class="text-center">
                        <button class="btn btn-secondary btn-sm" 
                                onclick="detail('{{ $satu->tlPenggantianPeralatan->peralatan_baru_id }}')"
                                title="Detail Peralatan Pengganti">
                                <i class="fas fa-exchange-alt"></i>
                        </button>
                  </td>
                @else
                    <td>-</td>
                @endif

                </tr>
        @endforeach                   
              </tbody>
            </table>
            <hr class="my-4" style="border-top: 3px solid #a8a5a5;">
    @endforeach


          {{-- Data Gangguan Peralatan untuk Jenis Gangguan Non Peralatan --}}

@elseif($laporan->jenis == config('constants.jenis_laporan.gangguan_non_peralatan'))

            <table border='0' cellpadding='5px'>        
              <tr><th>Waktu Gangguan</th><td>:</td><td>{{ strtoupper($laporan->gangguanNonPeralatan->waktu_formatted ?? '-') }}</td></tr>
              <tr><th>Deskripsi Gangguan</th><td>:</td><td>{{ strtoupper($laporan->gangguanNonPeralatan->deskripsi) }}</td></tr>
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
                  <th class="text-center" style="width: 600px">DESKRIPSI</th>
                  <th class="text-center">KONDISI AKHIR</th>
                </tr>
              </thead>
              <tbody>
                {{-- Looping Data Peralatan --}}
          @foreach ($laporan->TlgangguanNonPeralatan as $index => $satu)
                <tr class="table-condensed">
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td class="text-center">{{ strtoupper($satu->waktu_mulai_formatted) }}</td>
                  <td class="text-center">{{ strtoupper($satu->waktu_selesai_formatted) }}</td>
                  <td>{{ strtoupper($satu->deskripsi) }}</td>
  
                  {{-- Kondisi Layanan Setelah Tindaklanjut --}}
              @if($satu->kondisi === config('constants.kondisi_layanan.serviceable'))
                  <td class="text-center"><span class="badge bg-success">SERVICEABLE</span></td>
              @elseif($satu->kondisi === config('constants.kondisi_layanan.unserviceable'))
                  <td class="text-center"><span class="badge bg-danger">UNSERVICEABLE</span></td>
              @else
                  <td class="text-center">-</td>
              @endif
                </tr>
          @endforeach
              </tbody>
            </table>
@endif

          </div>
          <!-- /.card-body -->

          <div class="card-footer">
      @if($jenis_tindaklanjut != null)
          @if($jenis_tindaklanjut == 2)
            <a href="{{ route('logbook.laporan.tambah.step3.form', ['laporan_id' => $laporan->id]) }}"
                class="btn btn-success btn-sm">
                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali
            </a> 
          @endif
      @else
          <a href="{{ route('logbook.laporan.tambah.step2.back.form', ['laporan_id' => $laporan->id]) }}"
                class="btn btn-success btn-sm">
                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali
            </a>
      @endif
            
            <button class="btn btn-primary btn-sm float-right" 
                    data-toggle="modal" 
                    data-target="#modalSubmit">
                    Submit&nbsp;&nbsp;&nbsp;<i class="fas fa-check"></i>
            </button>
        </div>
        </div>
        <!-- /.card -->

      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->

  </div> <!-- container-fluid -->
</section> <!-- content -->

<!-- Modal tombol detail -->
<div class="modal fade" id="modal_detail">
    <div class="modal-dialog modal-lg">
    <div class="modal-content" id="detail">
        
    </div>
    <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection


@section('tail')

<!-- modal untuk submit laporan -->
<div class="modal fade" 
     id="modalSubmit" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="modalSubmitLabel" 
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

<form action="{{ route('logbook.laporan.tambah.step4') }}"
      method="post" 
      novalidate>
    @csrf
            <div class="modal-body">

                <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                <p><center>Ingin melakukan submit Laporan?</center></p>
                <em><center>Laporan yang telah di-submit akan berubah status dari DRAFT menjadi OPEN atau CLOSE</center></em>
                
            </div>
            <!-- modal-body -->

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tidak</button>
                <button type="submit" class="btn btn-primary btn-sm float-right">Ya, submit</button>
            </div> <!-- modal footer --> 
</form>
<!-- form -->

        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- modal fade -->
<!-- Akhir dari Modal untuk submit laporan -->

<!-- javascript untuk menampilkan modal detail -->
<script type="text/javascript">
  function detail(id){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Ajax Load data from ajax
    
    $.ajax({
        url : "{{ route('fasilitas.peralatan.detail') }}",
        type: "POST",
        data : {id: id},
        success: function(data){

          //alert(data.jenis.nama);

          $('#detail').empty();

          var row = '<div class="modal-header">';
              row += '<h4 class="modal-title">Detail Peralatan</h4>';
              row += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              row += '<span aria-hidden="true">&times;</span></button></div>';// modal header

              row += '<div class="modal-body">';
              row += "<table border='0' cellpadding='5px'>";            
              row += "<tr><th>Kode</th><td>:</td><td>"+ data.peralatan.kode.toUpperCase(); +"</td></tr>";  
              row += "<tr><th>Nama</th><td>:</td><td>"+ data.peralatan.nama.toUpperCase(); +"</td></tr>";

          if(data.peralatan.merk != null){
              row += "<tr><th>Merk</th><td>:</td><td>"+ data.peralatan.merk.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Merk</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.tipe != null){
              row += "<tr><th>Tipe</th><td>:</td><td>"+ data.peralatan.tipe.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Tipe</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.model != null){
              row += "<tr><th>Model</th><td>:</td><td>"+ data.peralatan.model.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Model</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.serial_number != null){
              row += "<tr><th>Serial Number</th><td>:</td><td>"+ data.peralatan.serial_number.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Serial Number</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.no_aset != null){
              row += "<tr><th>No. Aset</th><td>:</td><td>"+ data.peralatan.no_aset.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>No. Aset</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.thn_produksi != null){
              row += "<tr><th>Tahun Produksi</th><td>:</td><td>"+ data.peralatan.thn_produksi; +"</td></tr>";
          }else{
              row += "<tr><th>Tahun Produksi</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.thn_pengadaan != null){
              row += "<tr><th>Tahun Pengadaan</th><td>:</td><td>"+ data.peralatan.thn_pengadaan; +"</td></tr>";
          }else{
              row += "<tr><th>Tahun Pengadaan</th><td>:</td><td></td></tr>";
          }
                   
              row += "<tr><th>Jenis Alat</th><td>:</td><td>"+ data.jenis.nama.toUpperCase(); +"</td></tr>";
          
          if(data.peralatan.sewa == 1){
              row += "<tr><th>Status Kepemilikan</th><td>:</td><td>SEWA</td></tr>";
          }else{
              row += "<tr><th>Status Kepemilikan</th><td>:</td><td>ASET</td></tr>";
          }

              row += "<tr><th>Perusahaan Pemilik</th><td>:</td><td>"+ data.perusahaan.nama.toUpperCase(); +"</td></tr>";

          if(data.peralatan.kondisi == 1){
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-success'>NORMAL</span></td></tr>";
          }
          else if(data.peralatan.kondisi == 2){
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-warning'>NORMAL SEBAGIAN</span></td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-danger'>TIDAK AKTIF</span></td></tr>";
          }

          if(data.peralatan.no_sertifikasi != null){
              row += "<tr><th>No. Sertifikasi</th><td>:</td><td>"+ data.peralatan.no_sertifikasi; +"</td></tr>";
          }else{
              row += "<tr><th>No. Sertifikasi</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.terbit_sertifikasi != null){
              row += "<tr><th>Tgl Terbit Sertifikasi</th><td>:</td><td>"+ data.peralatan.terbit_sertifikasi_formatted +"</td></tr>";
          }else{
              row += "<tr><th>Tgl Terbit Sertifikasi</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.exp_sertifikasi != null){
              row += "<tr><th>Tgl Kadaluarsa Sertifikasi</th><td>:</td><td>"+ data.peralatan.exp_sertifikasi_formatted +"</td></tr>";
          }else{
              row += "<tr><th>Tgl Kadaluarsa Sertifikasi</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.lembaga_sertifikasi != null){
              row += "<tr><th>Lembaga Sertifikasi</th><td>:</td><td>"+ data.peralatan.lembaga_sertifikasi; +"</td></tr>";
          }else{
              row += "<tr><th>Lembaga Sertifikasi</th><td>:</td><td></td></tr>";
          }
          
          if(data.peralatan.keterangan != null){
              row += "<tr><th>Keterangan</th><td>:</td><td>"+ data.peralatan.keterangan.toUpperCase(); +"</td></tr>";
          }else{
              row += "<tr><th>Keterangan</th><td>:</td><td></td></tr>";
          }

          if(data.peralatan.status == 1){
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-success'>AKTIF</span></td></tr>";
          }else{
              row += "<tr><th>Status</th><td>:</td><td><span class='badge bg-danger'>TIDAK AKTIF</span></td></tr>";
          }

          if(data.created_by != null){
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td>"+ data.created_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.peralatan.created_at_formatted +"</td></tr>";
          }else{
              row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
          } 

          if(data.updated_by != null){
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.updated_by.name.toUpperCase(); +"</td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.peralatan.updated_at_formatted +"</td></tr>";
          }else{
              row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td></td></tr>";
              row += "<tr><th>Update Terakhir Pada</th><td>:</td><td></td></tr>";
          }      
              
              row += '</table>';
              row += '</div>'; // modal body
              row += '<div class="modal-footer justify-content-between">';
              row += '<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>';
              //row += '<button type="button" class="btn btn-danger">Hapus</button>';
              row += '</div>';
        
            $("#detail").append(row);
            $("#modal_detail").modal('show'); // show bootstrap modal when complete loaded
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Gagal menampilkan detail peralatan');
        }
    });
}
</script>

@endsection