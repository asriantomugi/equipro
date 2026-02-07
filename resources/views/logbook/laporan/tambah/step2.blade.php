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
                            <li class="step-item completed"><a href="{{ route('logbook.laporan.tambah.step1.form') }}">Pilih Layanan</a></li>
                            <li class="step-item active"><a href="#">Input Gangguan & Tindaklanjut</a></li>
    @if($jenis_tindaklanjut != null)
        @if($jenis_tindaklanjut == 2)
                            <li class="step-item"><a href="#">Penggantian Alat</a></li>
        @endif
    @endif
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- pesan error validasi -->
@if($errors->any())
        <div class="row">
          <div class="col-lg-6">
            <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><b></b>Kesalahan !</b></h5>
                  <ul>
  @foreach ($errors->all() as $error)
                  <li>{{$error}}</li>
  @endforeach
                  </ul>
                </div>
          </div>
        </div>
@endif

        <div class="row">
          <div class="col-lg-12 col-6">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DATA LAYANAN</h3>
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
  @if($layanan->kondisi == config('constants.kondisi_layanan.serviceable'))
                <tr><th>Kondisi Layanan</th><td>:</td><td><span class="badge bg-success">SERVICEABLE</span></td></tr>
  @else
                <tr><th>Kondisi Layanan</th><td>:</td><td><span class="badge bg-danger">UNSERVICEABLE</span></td></tr>
  @endif
                </table>

              </div>
              <!-- /.card-body -->
              
            </div>
            <!-- /.card -->
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

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
                      <th><center>KONDISI</center></th>
                      <th style="width: 100px"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($daftar_peralatan as $satu)
                    <tr class="table-condensed">
                      <td></td>
                      <td><center>{{ strtoupper($satu->peralatan->kode) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->merk) }}</center></td>
                      <td><center>{{ strtoupper($satu->peralatan->jenis->nama) }}</center></td>
                      <td><center>{{ strtoupper($satu->ip_address) }}</center></td>
  @if($satu->kondisi === null)
                      <td></td>
  @elseif($satu->kondisi == config('constants.kondisi_peralatan_layanan.beroperasi'))
                      <td class="text-center"><span class="badge bg-success">BEROPERASI</span></td>
  @elseif($satu->kondisi == config('constants.kondisi_peralatan_layanan.gangguan'))
                      <td class="text-center"><span class="badge bg-danger">GANGGUAN</span></td>
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

        <div class="row">
          <div class="col-lg-12 col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">FORM INPUT GANGGUAN</h3>
                </div>
                <div class="card-body">

    <form class="form-horizontal needs-validation" 
          action="{{ route('logbook.laporan.tambah.step2') }}" 
          method="post" 
          novalidate>
    @csrf
                    <input type="hidden" name="layanan_id"  value="{{ $layanan->id }}">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label required">Jenis Laporan</label>
                        <div class="col-sm-6">
                            <select name="jenis" 
                                    class="form-control"
                                    id="formJenis"
                                    required>
                                <option value="">- Pilih -</option>
                                <option value="1">GANGGUAN PERALATAN</option>
                                <option value="2">GANGGUAN NON PERALATAN</option>
                            </select>
                            <div class="invalid-feedback">Jenis Laporan wajib dipilih.</div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div id="formGangguan">
                        {{-- diisi form input gangguan --}} 
                    </div>
 
                </div>
                <!-- card-body -->

                <div class="card-footer">
                    <a class="btn btn-success btn-sm" 
                        href="{{ route('logbook.laporan.tambah.step1.form') }}"
                        role="button"><i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali</a>

                    <button type="submit" 
                            class="btn btn-success btn-sm float-right">
                            Lanjut &nbsp;&nbsp;&nbsp;
                            <i class="fas fa-angle-right"></i>
                    </button>
                    
                  <!--
                  <a class="btn btn-success btn-sm float-right" 
                    href="{{ route('fasilitas.layanan.tambah.step3.form', ['id' => $layanan->id]) }}" 
                    role="button">Lanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i></a>
                    -->
                </div>
    </form>

            </div>
            <!-- /.card -->
          </div> <!-- ./col -->
        </div><!-- /.row -->

    </div>
    <!-- container-fluid -->
</section>
<!-- content -->

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

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'simpan_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menyimpan laporan.',
          autohide: true,
          delay: 3000
        })
    @elseif (session()->get('notif') == 'gangguan_null')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Tidak ada gangguan yang di-input.',
          autohide: true,
          delay: 3000
        })
    @endif
  @endif
</script>

<!-- Javascript untuk menampilkan pilihan form berdasarkan jenis laporan -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const formJenis = document.getElementById('formJenis');
        const formGangguan = document.getElementById('formGangguan');
        // ambil variable daftar peralatan
        const daftarPeralatan = @json($daftar_peralatan);
        // ambil variable dari constants.php
        const JENIS_GANGGUAN_PERALATAN = {{ config('constants.jenis_laporan.gangguan_peralatan') }};
        const JENIS_GANGGUAN_NON_PERALATAN = {{ config('constants.jenis_laporan.gangguan_non_peralatan') }};
        const KONDISI_GANGGUAN = @json(config('constants.kondisi_gangguan'));
        const KONDISI_TINDAKLANJUT = @json(config('constants.kondisi_tindaklanjut'));
        const JENIS_TINDAKLANJUT_PERALATAN = @json(config('constants.jenis_tindaklanjut_gangguan_peralatan'));
        const JENIS_TINDAKLANJUT_NONPERALATAN = @json(config('constants.jenis_tindaklanjut_gangguan_non_peralatan'));
        const KONDISI_LAYANAN = @json(config('constants.kondisi_layanan'));

        // proses ketika terjadi perubahan value di dropdown jenis laporan
        formJenis.addEventListener('change', function () {
            // ambil value dari dropdown jenis laporan
            const jenis = formJenis.value;
            let html = '';

            // jika jenis laporan = 1 (gangguan peralatan)
            if (jenis == JENIS_GANGGUAN_PERALATAN) {

                // input jenis laporan gangguan peralatan
                html += '<input type="hidden" name="jenis"  value="'+ jenis +'">';

                // LOOP daftar peralatan
                daftarPeralatan.forEach(function (alat, index) {

                    const gangguanPickerId  = 'datetime_gangguan_' + index;
                    const mulaiPerbaikanPickerId = 'datetime_mulai_perbaikan_' + index;
                    const selesaiPerbaikanPickerId = 'datetime_selesai_perbaikan_' + index;
                    const mulaiPenggantianPickerId = 'datetime_mulai_penggantian_' + index;
                    const selesaiPenggantianPickerId = 'datetime_selesai_penggantian_' + index;

                    // form input gangguan non peralatan
                    html += '<input type="hidden" name="peralatan_id[]"  value="'+ alat.id +'">';
                    
                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Kode</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="kode" class="form-control" value="'+ alat.peralatan.kode +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Nama</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="nama" class="form-control" value="'+ alat.peralatan.nama +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Merk</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="merk" class="form-control" value="'+ alat.peralatan.merk +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Jenis Alat</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="jenis_alat" class="form-control" value="'+ alat.peralatan.jenis.nama +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">IP Address</label>';
                    html += '<div class="col-sm-6">';
                    if(alat.ip_address != null){
                        html += '<input type="text" name="ip_address" class="form-control" value="'+ alat.ip_address +'" readonly>';
                    }else{
                        html += '<input type="text" name="ip_address" class="form-control" value="" readonly>';
                    }
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Kondisi Peralatan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="kondisi[]" class="form-control kondisi-peralatan" data-index="'+ index +'">';
                    html += `<option value="${ KONDISI_GANGGUAN.beroperasi }">BEROPERASI</option>`;
                    html += `<option value="${ KONDISI_GANGGUAN.gangguan }">GANGGUAN</option>`;
                    html += '</select>';
                    html += '</div></div>';

                    // form untuk menampilkan waktu gangguan dan deskripsi gangguan
                    html += '<div class="form-gangguan-peralatan" id="gangguan_'+ index +'" style="display:none">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Gangguan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ gangguanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="waktu_gangguan[]" class="form-control datetimepicker-input gangguan-required" data-target="#'+gangguanPickerId+'"/>';
                    html += '<div class="input-group-append" data-target="#'+ gangguanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Gangguan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control gangguan-required" rows="5" name="deskripsi_gangguan[]" placeholder=""></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Jenis Tindaklanjut</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="jenis_tindaklanjut[]" class="form-control jenis-tindaklanjut" data-index="'+ index +'">';
                    html += '<option value="">- Pilih -</option>';
                    html += `<option value="${ JENIS_TINDAKLANJUT_PERALATAN.perbaikan }">PERBAIKAN</option>`;
                    html += `<option value="${ JENIS_TINDAKLANJUT_PERALATAN.penggantian }">PENGGANTIAN</option>`;
                    html += '</select>';
                    html += '</div></div>';

                    // jika dipilih PERBAIKAN maka muncul form input perbaikan 
                    // Codingan berada di dalam div class form-gangguan-peralatan
                    html += '<div class="form-perbaikan" id="perbaikan_'+ index +'" style="display:none">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ mulaiPerbaikanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="waktu_mulai_tindaklanjut[]" class="form-control datetimepicker-input perbaikan-required" data-target="#'+ mulaiPerbaikanPickerId +'"/>';
                    html += '<div class="input-group-append" data-target="#'+ mulaiPerbaikanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Tindaklanjut</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control perbaikan-required" rows="5" name="deskripsi_tindaklanjut[]" placeholder=""></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ selesaiPerbaikanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="waktu_selesai_tindaklanjut[]" class="form-control datetimepicker-input perbaikan-required" data-target="#'+ selesaiPerbaikanPickerId +'"/>';
                    html += '<div class="input-group-append" data-target="#'+ selesaiPerbaikanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Kondisi Setelah Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="kondisi_tindaklanjut[]" class="form-control perbaikan-required" data-index="'+ index +'">';
                    html += '<option value="">- Pilih -</option>';
                    html += `<option value="${ KONDISI_TINDAKLANJUT.beroperasi }">BEROPERASI</option>`;
                    html += `<option value="${ KONDISI_TINDAKLANJUT.gangguan }">GANGGUAN</option>`;
                    html += '</select>';
                    html += '</div></div>';

                    html += '</div>';
                    // end of form input perbaikan


                    // jika dipilih PENGGANTIAN maka muncul form input penggantian 
                    // Codingan berada di dalam div class form-gangguan-peralatan
                    html += '<div class="form-penggantian" id="penggantian_'+ index +'" style="display:none">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ mulaiPenggantianPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="waktu_mulai_tindaklanjut[]" class="form-control datetimepicker-input penggantian-required" data-target="#'+ mulaiPenggantianPickerId +'"/>';
                    html += '<div class="input-group-append" data-target="#'+ mulaiPenggantianPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Tindaklanjut</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control penggantian-required" rows="5" name="deskripsi_tindaklanjut[]" placeholder=""></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ selesaiPenggantianPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="waktu_selesai_tindaklanjut[]" class="form-control datetimepicker-input penggantian-required" data-target="#'+ selesaiPenggantianPickerId +'"/>';
                    html += '<div class="input-group-append" data-target="#'+ selesaiPenggantianPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Kondisi Setelah Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="kondisi_tindaklanjut[]" class="form-control penggantian-required" data-index="'+ index +'">';
                    html += '<option value="">- Pilih -</option>';
                    html += `<option value="${ KONDISI_TINDAKLANJUT.beroperasi }">BEROPERASI</option>`;
                    html += `<option value="${ KONDISI_TINDAKLANJUT.gangguan }">GANGGUAN</option>`;
                    html += '</select>';
                    html += '</div></div>';

                    html += '</div>';
                    // end of form input penggantian

                    html += '</div>';
                    // end of div form-gangguan-peralatan

                    html += '<hr class="my-4">';
                });
                // END OF LOOP   
            }

            // jika jenis laporan = 2 (gangguan non peralatan)
            else if (jenis == JENIS_GANGGUAN_NON_PERALATAN) {

                // input jenis laporan gangguan non peralatan
                html += '<input type="hidden" name="jenis"  value="'+ jenis +'">';

                // form input gangguan non peralatan

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Waktu Gangguan</label>';
                html += '<div class="col-sm-6">';
                html += '<div class="input-group date" id="datetime_nonperalatan" data-target-input="nearest">';
                html += '<input type="text" name="waktu_gangguan" class="form-control datetimepicker-input nonperalatan-required" data-target="#datetime_nonperalatan"/>';
                html += '<div class="input-group-append" data-target="#datetime_nonperalatan" data-toggle="datetimepicker">';
                html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Deskripsi Gangguan</label>';
                html += '<div class="col-sm-6">';
                html += '<textarea class="form-control nonperalatan-required" rows="5" name="deskripsi_gangguan" placeholder=""></textarea>';
                html += '</div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label">Jenis Tindaklanjut</label>';
                html += '<div class="col-sm-6">';
                html += '<select name="jenis_tindaklanjut[]" class="form-control jenis-tindaklanjut-nonperalatan">';
                html += '<option value="">- Pilih -</option>';
                html += `<option value="${ JENIS_TINDAKLANJUT_NONPERALATAN.perbaikan }">PERBAIKAN</option>`;
                html += '</select>';
                html += '</div></div>';

                // form untuk menampilkan waktu mulai perbaikan, deskripsi perbaikan, dan waktu selesai perbaikan
                // muncul setelah dropdown PERBAIKAN dipilih
                html += '<div class="form-perbaikan" id="perbaikan" style="display:none">';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Perbaikan</label>';
                html += '<div class="col-sm-6">';
                html += '<div class="input-group date" id="datetime_mulai_perbaikan" data-target-input="nearest">';
                html += '<input type="text" name="waktu_mulai_tindaklanjut" class="form-control datetimepicker-input perbaikan-required" data-target="#datetime_mulai_perbaikan"/>';
                html += '<div class="input-group-append" data-target="#datetime_mulai_perbaikan" data-toggle="datetimepicker">';
                html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                html += '</div></div></div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Deskripsi Tindaklanjut</label>';
                html += '<div class="col-sm-6">';
                html += '<textarea class="form-control perbaikan-required" rows="5" name="deskripsi_tindaklanjut" placeholder=""></textarea>';
                html += '</div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Perbaikan</label>';
                html += '<div class="col-sm-6">';
                html += '<div class="input-group date" id="datetime_selesai_perbaikan" data-target-input="nearest">';
                html += '<input type="text" name="waktu_selesai_tindaklanjut" class="form-control datetimepicker-input perbaikan-required" data-target="#datetime_selesai_perbaikan"/>';
                html += '<div class="input-group-append" data-target="#datetime_selesai_perbaikan" data-toggle="datetimepicker">';
                html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                html += '</div></div></div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Kondisi Setelah Perbaikan</label>';
                html += '<div class="col-sm-6">';
                html += '<select name="kondisi_tindaklanjut" class="form-control perbaikan-required">';
                html += '<option value="">- Pilih -</option>';
                html += `<option value="${ KONDISI_TINDAKLANJUT.beroperasi }">BEROPERASI</option>`;
                html += `<option value="${ KONDISI_TINDAKLANJUT.gangguan }">GANGGUAN</option>`;
                html += '</select>';
                html += '</div></div>';

                html += '</div>';
                // end of form untuk menampilkan waktu perbaikan dan deskripsi perbaikan

                html += '<hr class="my-4">';
            }

            html += '<div class="form-group row">';
            html += '<label class="col-sm-3 col-form-label required">Kondisi Layanan Saat Ini</label>';
            html += '<div class="col-sm-6">';
            html += '<select name="kondisi_layanan" class="form-control" required>';
            html += '<option value="">- Pilih -</option>';
            html += `<option value="${ KONDISI_LAYANAN.serviceable }">SERVICEABLE</option>`;
            html += `<option value="${ KONDISI_LAYANAN.conditional_serviceable }">CONDITIONAL SERVICEABLE</option>`;
            html += `<option value="${ KONDISI_LAYANAN.unserviceable }">UNSERVICEABLE</option>`;
            html += '</select>';
            html += '<div class="invalid-feedback">Kondisi Layanan Saat Ini wajib dipilih.</div>';
            html += '</div></div>';

            formGangguan.innerHTML = html; // menampilkan variabel html ke halaman blade

            // javascript untuk mengaktifkan form validation jika dropdown jenis laporan dipilih GANGGUAN NON PERALATAN
            if (jenis == JENIS_GANGGUAN_NON_PERALATAN) {
                // set required & enable field
                document.querySelectorAll('.nonperalatan-required').forEach(el => {
                    el.disabled = false;
                    el.required = true;
                });

                // init datetimepicker khusus non peralatan
                if (!$('#datetime_nonperalatan').data('datetimepicker')) {
                    $('#datetime_nonperalatan').datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }
            } 
            else {
                // reset jika pindah jenis laporan
                document.querySelectorAll('.nonperalatan-required').forEach(el => {
                    el.disabled = true;
                    el.required = false;
                    el.value = '';
                });
            }
            // akhir javascript proses aktivasi form validasi GANGGUAN NON PERALATAN

        });


        /** 
         * javascript untuk menampilkan field waktu gangguan dan deskripsi gangguan
         * saat field kondisi peralatan dipilih GANGGUAN pada form input gangguan peralatan
         */
        document.addEventListener('change', function (e) {
            // cek bahwa hanya dropdown kondisi peralatan saja yang berubah
            if (!e.target.classList.contains('kondisi-peralatan')) return;

            // ambil nilai index dari dropdown kondisi peralatan
            const index = e.target.dataset.index;
            const gangguanForm = document.getElementById('gangguan_' + index);
            // jika form tidak ada, hentikan javascript, jaga-jaga kalau form terhapus di blade
            if (!gangguanForm) return;
            // ambil value dari dropdown kondisi peralatan, jika nilainya 0 maka TRUE
            const isGangguan = e.target.value === '0';
            // jika nilai isGangguan adalah TRUE, maka tampilkan field waktu gangguan dan deskripsi gangguan
            gangguanForm.style.display = isGangguan ? 'block' : 'none';
            // ambil semua field dengan class gangguan-required (waktu gangguan dan deskripsi gangguan)
            gangguanForm.querySelectorAll('.gangguan-required').forEach(el => {
                // jika pilihan kondisi peralatan = beroperasi, field waktu gangguan dan deskripsi gangguan ter-disable
                // sehingga field tidak aktif
                el.disabled = !isGangguan;
                // jika pilihan kondisi peralatan = gangguan, maka field waktu gangguan dan deskripsi gangguan ter-required
                // sehingga menjadi field mandatory
                el.required = isGangguan;
                // jika kondisi peralatan = beroperasi, maka paksa agar nilai di field waktu gangguan dan deskripsi gangguan menjadi kosong
                if (!isGangguan) el.value = '';
            });

            // init datetimepicker saat dibutuhkan
            if (isGangguan && !$('#datetime_gangguan_' + index).data('datetimepicker')) {
                $('#datetime_gangguan_' + index).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }
        });


        /** 
         * javascript untuk menampilkan form input perbaikan atau penggantian pada form GANGGUAN PERALATAN
         */
        document.addEventListener('change', function (e) {
            // cek bahwa hanya dropdown jenis tindaklanjut saja yang berubah
            if (!e.target.classList.contains('jenis-tindaklanjut')) return;

            // ambil nilai index dari dropdown jenis tindaklanjut
            const index = e.target.dataset.index;
            const perbaikanForm = document.getElementById('perbaikan_' + index);
            const penggantianForm = document.getElementById('penggantian_' + index);

            // mengambil dan mengecek nilai dropdown jenis tindaklanjut
            const isPerbaikan = e.target.value === '1';
            const isPenggantian = e.target.value === '2';

            // jika form input perbaikan ada, cek apakah pilihan dropdown adalah perbaikan 
            if (perbaikanForm) perbaikanForm.style.display = isPerbaikan ? 'block' : 'none';
            // jika form input penggantian ada, cek apakah pilihan dropdown adalah penggantian 
            if (penggantianForm) penggantianForm.style.display = isPenggantian ? 'block' : 'none';

            // jika PERBAIKAN dipilih, aktifkan field mandatory pada form input perbaikan
            if (perbaikanForm) {
                perbaikanForm.querySelectorAll('.perbaikan-required').forEach(el => {
                    el.disabled = !isPerbaikan;
                    el.required = isPerbaikan;
                    if (!isPerbaikan) el.value = '';
                });

                // init datetime picker di form input waktu mulai perbaikan
                if (isPerbaikan && !$('#datetime_mulai_perbaikan_' + index).data('datetimepicker')) {
                    $('#datetime_mulai_perbaikan_' + index).datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }

                // init datetime picker di form input waktu selesai perbaikan
                if (isPerbaikan && !$('#datetime_selesai_perbaikan_' + index).data('datetimepicker')) {
                    $('#datetime_selesai_perbaikan_' + index).datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }
            }

            // jika PENGGANTIAN dipilih, aktifkan field mandatory pada form input penggantian
            if (penggantianForm) {
                penggantianForm.querySelectorAll('.penggantian-required').forEach(el => {
                    el.disabled = !isPenggantian;
                    el.required = isPenggantian;
                    if (!isPenggantian) el.value = '';
                });

                // init datetime picker di form input waktu mulai penggantian
                if (isPenggantian && !$('#datetime_mulai_penggantian_' + index).data('datetimepicker')) {
                    $('#datetime_mulai_penggantian_' + index).datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }

                // init datetime picker di form input waktu selesai penggantian
                if (isPenggantian && !$('#datetime_selesai_penggantian_' + index).data('datetimepicker')) {
                    $('#datetime_selesai_penggantian_' + index).datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }
            }
        });

        /** 
         * javascript untuk menampilkan form input perbaikan pada form GANGGUAN NON PERALATAN
         */
        document.addEventListener('change', function (e) {
            // cek bahwa hanya dropdown jenis tindaklanjut saja yang berubah
            if (!e.target.classList.contains('jenis-tindaklanjut-nonperalatan')) return;

            // ambil nilai dari dropdown jenis tindaklanjut
            const perbaikanForm = document.getElementById('perbaikan');

            // mengambil dan mengecek nilai dropdown jenis tindaklanjut
            const isPerbaikan = e.target.value === '1';

            // jika form input perbaikan ada, cek apakah pilihan dropdown adalah perbaikan 
            if (perbaikanForm) perbaikanForm.style.display = isPerbaikan ? 'block' : 'none';

            // jika PERBAIKAN dipilih, aktifkan field mandatory pada form input perbaikan
            if (perbaikanForm) {
                perbaikanForm.querySelectorAll('.perbaikan-required').forEach(el => {
                    el.disabled = !isPerbaikan;
                    el.required = isPerbaikan;
                    if (!isPerbaikan) el.value = '';
                });

                // init datetime picker di form input waktu mulai perbaikan
                if (isPerbaikan && !$('#datetime_mulai_perbaikan').data('datetimepicker')) {
                    $('#datetime_mulai_perbaikan').datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }

                // init datetime picker di form input waktu selesai perbaikan
                if (isPerbaikan && !$('#datetime_selesai_perbaikan').data('datetimepicker')) {
                    $('#datetime_selesai_perbaikan').datetimepicker({
                        format: 'DD-MM-YYYY HH:mm',
                        icons: { time: 'far fa-clock' }
                    });
                }
            }
        });
    });
</script>

<!-- javascript untuk menampilkan modal detail -->
<script type="text/javascript">
function detail(id){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Ajax Load data from ajax
    $.ajax({
        url : "{{url('/fasilitas/peralatan/detail')}}",
        type: "POST",
        data : {id: id},
        success: function(data){

        //alert(data.nama);

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
            row += "<tr><th>Dibuat Pada</th><td>:</td><td>"+ data.peralatan.created_at +"</td></tr>";
        }else{
            row += "<tr><th>Dibuat Oleh</th><td>:</td><td></td></tr>";
            row += "<tr><th>Dibuat Pada</th><td>:</td><td></td></tr>";
        } 

        if(data.updated_by != null){
            row += "<tr><th>Update Terakhir Oleh</th><td>:</td><td>"+ data.updated_by.name.toUpperCase(); +"</td></tr>";
            row += "<tr><th>Update Terakhir Pada</th><td>:</td><td>"+ data.peralatan.updated_at +"</td></tr>";
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
<!-- /. section tail -->