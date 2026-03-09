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
          <div class="col-lg-12">
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
  @if($satu->peralatan->kondisi === null)
                      <td></td>
  @elseif($satu->peralatan->kondisi == config('constants.kondisi_peralatan.normal'))
                      <td class="text-center"><span class="badge bg-success">NORMAL</span></td>
    @elseif($satu->peralatan->kondisi == config('constants.kondisi_peralatan.normal_sebagian'))
                      <td class="text-center"><span class="badge bg-warning">NORMAL SEBAGIAN</span></td>
  @elseif($satu->peralatan->kondisi == config('constants.kondisi_peralatan.rusak'))
                      <td class="text-center"><span class="badge bg-danger">RUSAK</span></td>
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
                            <div class="invalid-feedback">Wajib dipilih</div>
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
    @if (session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan laporan baru.',
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
        // ambil variable dari controller
        const daftarPeralatan = @json($daftar_peralatan);
        // ambil variable dari constants.php
        const JENIS = {
            PERALATAN: {{ config('constants.jenis_laporan.gangguan_peralatan') }},
            NON: {{ config('constants.jenis_laporan.gangguan_non_peralatan') }}
        };

        const KONDISI_PERALATAN = @json(config('constants.kondisi_peralatan'));
        const KONDISI_LAYANAN = @json(config('constants.kondisi_layanan'));
        const STATUS_LAPORAN = @json(config('constants.status_laporan'));
        const JENIS_TINDAKLANJUT_PERALATAN = @json(config('constants.jenis_tindaklanjut_gangguan_peralatan'));
        const JENIS_TINDAKLANJUT_NON = @json(config('constants.jenis_tindaklanjut_gangguan_non_peralatan'));
       
        // ==============================================================
        //                        FUNCTION UMUM
        // ==============================================================
        function show(el, status) {
            if (!el) return;
            el.style.display = status ? 'block' : 'none';
        }

        function toggleRequired(container, status, className) {
            if (!container) return;

            container.querySelectorAll('.' + className).forEach(el => {
                el.disabled = !status;
                el.required = status;

                if (!status){
                    el.value = '';
                    el.classList.remove('is-invalid');
                }
            });
        }

        function showKondisiLayanan(status) {
            const dropdown = document.getElementById('dropdown_kondisi');
            if (!dropdown) return;

            dropdown.style.display = status ? 'block' : 'none';

            dropdown.querySelectorAll('select').forEach(el => {
                el.required = status;
                el.disabled = !status;

                if (!status) {
                    el.value = '';
                    el.classList.remove('is-invalid');
                }
            });
        }

        function initDateTimePeralatan(id) {
            if (!$('#datetime_gangguan_' + id).data('datetimepicker')) {
                $('#datetime_gangguan_' + id).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_mulai_perbaikan_' + id).data('datetimepicker')) {
                $('#datetime_mulai_perbaikan_' + id).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_selesai_perbaikan_' + id).data('datetimepicker')) {
                $('#datetime_selesai_perbaikan_' + id).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_mulai_penggantian_' + id).data('datetimepicker')) {
                $('#datetime_mulai_penggantian_' + id).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_selesai_penggantian_' + id).data('datetimepicker')) {
                $('#datetime_selesai_penggantian_' + id).datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }
        }

        function initDateTimeNonPeralatan() {
            if (!$('#datetime_nonperalatan').data('datetimepicker')) {
                $('#datetime_nonperalatan').datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_mulai_perbaikan').data('datetimepicker')) {
                $('#datetime_mulai_perbaikan').datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }

            if (!$('#datetime_selesai_perbaikan').data('datetimepicker')) {
                $('#datetime_selesai_perbaikan').datetimepicker({
                    format: 'DD-MM-YYYY HH:mm',
                    icons: { time: 'far fa-clock' }
                });
            }
        }
        // ===============================================================
        //                       END OF FUNCTION UMUM
        // ===============================================================

        // ===============================================================
        // FUNCTION UNTUK MENAMPILKAN FORM INPUT GANGGUAN DAN TINDAKLANJUT
        // ===============================================================
        /** 
         * Javascript untuk menampilkan form input gangguan berdasarkan jenis laporan.
         * Saat jenis laporan dipilih GANGGUAN PERALATAN, maka akan tampil form input gangguan peralatan.
         * Saat jenis laporan dipilih GANGGUAN NON PERALATAN, maka akan tampil form input gangguan non peralatan.
         */

        formJenis.addEventListener('change', function () {
            // ambil value dari dropdown jenis laporan
            const jenis = this.value;
            let html = '';

            // jika jenis laporan = 1 (gangguan peralatan)
            if (jenis == JENIS.PERALATAN) {

                // LOOP daftar peralatan
                daftarPeralatan.forEach(function (alat, index) {

                    const gangguanPickerId  = 'datetime_gangguan_' + index;
                    const mulaiPerbaikanPickerId = 'datetime_mulai_perbaikan_' + index;
                    const selesaiPerbaikanPickerId = 'datetime_selesai_perbaikan_' + index;
                    const mulaiPenggantianPickerId = 'datetime_mulai_penggantian_' + index;
                    const selesaiPenggantianPickerId = 'datetime_selesai_penggantian_' + index;

                    // =========================== FORM DATA PERALATAN ======================================
                    html += '<input type="hidden" name="peralatan['+ index +'][peralatan_id]" value="'+ alat.peralatan.id +'">';
                    html += '<input type="hidden" name="peralatan['+ index +'][kondisi_awal]" value="'+ alat.peralatan.kondisi +'">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Kode</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="" class="form-control" value="'+ alat.peralatan.kode +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Nama</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="" class="form-control" value="'+ alat.peralatan.nama +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Merk</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="" class="form-control" value="'+ alat.peralatan.merk +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Jenis Alat</label>';
                    html += '<div class="col-sm-6">';
                    html += '<input type="text" name="" class="form-control" value="'+ alat.peralatan.jenis.nama +'" readonly>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">IP Address</label>';
                    html += '<div class="col-sm-6">';
                    if(alat.ip_address != null){
                        html += '<input type="text" name="" class="form-control" value="'+ alat.ip_address +'" readonly>';
                    }else{
                        html += '<input type="text" name="" class="form-control" value="" readonly>';
                    }
                    html += '</div></div>';

                    // tampilkan tombol input gangguan hanya saja jika peralatan normal atau normal sebagian
                    if(alat.peralatan.kondisi == KONDISI_PERALATAN.rusak){
                        html += '<div class="form-group row">';
                        html += '<div class="col-sm-3"></div>';
                        html += '<div class="col-sm-6">';
                        html += '<span class="badge bg-danger">RUSAK</span>';
                        html += '</div></div>';
                    }else{
                        html += '<div class="form-group row">';
                        html += '<div class="col-sm-3"></div>';
                        html += '<div class="col-sm-6">';
                        html += '<button type="button" class="btn btn-success btn-sm btn-input-gangguan" data-index="'+index+'">';
                        html += 'Input Gangguan &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-down"></i>';
                        html += '</button>';
                        html += '</div></div>';
                    }

                    // =========================== END OF FORM DATA PERALATAN =================================

                    // ============================= FORM INPUT GANGGUAN ======================================
                    html += '<div class="form-gangguan-peralatan" id="gangguan_'+ index +'" style="display:none">';
                    
                    // kirim penanda bahwa gangguan diisi di peralatan ini, nilai akan berubah jadi 1 saat tombol Input Gangguan diklik
                    html += '<input type="hidden" id="flag_gangguan_'+ index +'" name="peralatan['+ index +'][flag_gangguan]" value="0">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Gangguan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ gangguanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="peralatan['+ index +'][waktu_gangguan]" class="form-control datetimepicker-input gangguan-required" data-target="#'+gangguanPickerId+'" disabled/>';
                    html += '<div class="input-group-append" data-target="#'+ gangguanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Gangguan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control gangguan-required" rows="5" name="peralatan['+ index +'][deskripsi_gangguan]" placeholder="" disabled></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Kondisi Saat Gangguan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="peralatan['+ index +'][kondisi_gangguan]" class="form-control kondisi-peralatan gangguan-required" data-index="'+ index +'" disabled>';
                    html += '<option value="">- Pilih -</option>';
                    // hanya tampilkan pilihan kondisi peralatan sesuai kondisi terakhir
                    if(alat.peralatan.kondisi == KONDISI_PERALATAN.normal){
                        html += `<option value="${ KONDISI_PERALATAN.normal }">NORMAL</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.normal_sebagian }">NORMAL SEBAGIAN</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.rusak }">RUSAK</option>`;
                    }else{
                        html += `<option value="${ KONDISI_PERALATAN.normal_sebagian }">NORMAL SEBAGIAN</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.rusak }">RUSAK</option>`;
                    }
                    
                    html += '</select>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label">Jenis Tindak Lanjut</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="peralatan['+ index +'][jenis_tindaklanjut]" class="form-control jenis-tindaklanjut" data-index="'+ index +'">';
                    html += '<option value="">- Pilih -</option>';
                    html += `<option value="${ JENIS_TINDAKLANJUT_PERALATAN.perbaikan }">PERBAIKAN</option>`;
                    html += `<option value="${ JENIS_TINDAKLANJUT_PERALATAN.penggantian }">PENGGANTIAN</option>`;
                    html += '</select>';
                    html += '</div></div>';

                    // ============================= FORM INPUT PERBAIKAN ======================================
                    // jika dipilih PERBAIKAN maka muncul form input perbaikan 
                    // Codingan berada di dalam div class form-gangguan-peralatan
                    html += '<div class="form-perbaikan-peralatan" id="perbaikan_'+ index +'" style="display:none">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ mulaiPerbaikanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.perbaikan +'][waktu_mulai_tindaklanjut]" class="form-control datetimepicker-input perbaikan-required" data-target="#'+ mulaiPerbaikanPickerId +'" disabled/>';
                    html += '<div class="input-group-append" data-target="#'+ mulaiPerbaikanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control perbaikan-required" rows="5" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.perbaikan +'][deskripsi_tindaklanjut]" placeholder="" disabled></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ selesaiPerbaikanPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.perbaikan +'][waktu_selesai_tindaklanjut]" class="form-control datetimepicker-input perbaikan-required" data-target="#'+ selesaiPerbaikanPickerId +'" disabled/>';
                    html += '<div class="input-group-append" data-target="#'+ selesaiPerbaikanPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Kondisi Setelah Perbaikan</label>';
                    html += '<div class="col-sm-6">';
                    html += '<select name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.perbaikan +'][kondisi_tindaklanjut]" class="form-control perbaikan-required" data-index="'+ index +'" disabled>';
                    html += '<option value="">- Pilih -</option>';
                    // hanya tampilkan pilihan kondisi peralatan sesuai kondisi terakhir
                    if(alat.peralatan.kondisi == KONDISI_PERALATAN.normal){
                        html += `<option value="${ KONDISI_PERALATAN.normal }">NORMAL</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.normal_sebagian }">NORMAL SEBAGIAN</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.rusak }">RUSAK</option>`;
                    }else{
                        html += `<option value="${ KONDISI_PERALATAN.normal_sebagian }">NORMAL SEBAGIAN</option>`;
                        html += `<option value="${ KONDISI_PERALATAN.rusak }">RUSAK</option>`;
                    }
                    html += '</select>';
                    html += '</div></div>';

                    html += '</div>';
                    // ======================= END OF FORM INPUT PERBAIKAN ==================================

                    // ============================= INPUT PENGGANTIAN ======================================
                    // jika dipilih PENGGANTIAN maka muncul form input penggantian 
                    // Codingan berada di dalam div class form-gangguan-peralatan
                    html += '<div class="form-penggantian-peralatan" id="penggantian_'+ index +'" style="display:none">';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ mulaiPenggantianPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.penggantian +'][waktu_mulai_tindaklanjut]" class="form-control datetimepicker-input penggantian-required" data-target="#'+ mulaiPenggantianPickerId +'" disabled/>';
                    html += '<div class="input-group-append" data-target="#'+ mulaiPenggantianPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Deskripsi Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<textarea class="form-control penggantian-required" rows="5" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.penggantian +'][deskripsi_tindaklanjut]" placeholder="" disabled></textarea>';
                    html += '</div></div>';

                    html += '<div class="form-group row">';
                    html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Penggantian</label>';
                    html += '<div class="col-sm-6">';
                    html += '<div class="input-group date" id="'+ selesaiPenggantianPickerId +'" data-target-input="nearest">';
                    html += '<input type="text" name="peralatan['+ index +'][tindaklanjut]['+ JENIS_TINDAKLANJUT_PERALATAN.penggantian +'][waktu_selesai_tindaklanjut]" class="form-control datetimepicker-input penggantian-required" data-target="#'+ selesaiPenggantianPickerId +'" disabled/>';
                    html += '<div class="input-group-append" data-target="#'+ selesaiPenggantianPickerId +'" data-toggle="datetimepicker">';
                    html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                    html += '</div></div></div></div>';

                    html += '</div>';
                    // =========================== END OF FORM INPUT PENGGANTIAN ======================================

                    html += '<div class="form-group row">';
                    html += '<div class="col-sm-3"></div>';
                    html += '<div class="col-sm-6">';
                    html += '<button type="button" class="btn btn-danger btn-sm float-right btn-close-input-gangguan" data-index="'+index+'">';
                    html += '<i class="fas fa-times"></i>&nbsp;&nbsp;&nbsp; Batal';
                    html += '</button>';
                    html += '</div></div>';

                    html += '</div>';
                    // ============================= END OF FORM INPUT GANGGUAN ======================================

                    // html += '<hr class="my-4">';
                    html += '<hr class="my-4" style="border-top: 3px solid #a8a5a5;">';
                });
                // END OF LOOP   
            }

            // jika jenis laporan = 2 (gangguan non peralatan)
            else if (jenis == JENIS.NON) {

                // ============================= FORM INPUT GANGGUAN ======================================
                html += '<div class="form-gangguan-nonperalatan" id="gangguan_non">';

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
                html += '<div class="col-sm-3"></div>';
                html += '<div class="col-sm-6">';
                html += '<button type="button" class="btn btn-success btn-sm btn-input-tindaklanjut">';
                html += 'Input Tindaklanjut &nbsp;&nbsp;&nbsp;<i class="fas fa-angle-down"></i>';
                html += '</button>';
                html += '</div></div>';

                // ============================= FORM INPUT TINDAKLANJUT ======================================
                // form untuk menampilkan waktu mulai perbaikan, deskripsi perbaikan, dan waktu selesai perbaikan
                // muncul setelah dropdown PERBAIKAN dipilih
                html += '<div class="form-tindaklanjut-nonperalatan" id="tindaklanjut_non" style="display:none">';

                // kirim penanda bahwa tindaklanjut diisi, nilai akan berubah jadi 1 saat tombol Input Tindaklanjut diklik
                html += '<input type="hidden" id="flag_tindaklanjut" name="flag_tindaklanjut" value="0">';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Waktu Mulai Tindak Lanjut</label>';
                html += '<div class="col-sm-6">';
                html += '<div class="input-group date" id="datetime_mulai_perbaikan" data-target-input="nearest">';
                html += '<input type="text" name="waktu_mulai_tindaklanjut" class="form-control datetimepicker-input tindaklanjut-required" data-target="#datetime_mulai_perbaikan" disabled/>';
                html += '<div class="input-group-append" data-target="#datetime_mulai_perbaikan" data-toggle="datetimepicker">';
                html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                html += '</div></div></div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Deskripsi Tindak Lanjut</label>';
                html += '<div class="col-sm-6">';
                html += '<textarea class="form-control tindaklanjut-required" rows="5" name="deskripsi_tindaklanjut" placeholder="" disabled></textarea>';
                html += '</div></div>';

                html += '<div class="form-group row">';
                html += '<label class="col-sm-3 col-form-label required">Waktu Selesai Tindak Lanjut</label>';
                html += '<div class="col-sm-6">';
                html += '<div class="input-group date" id="datetime_selesai_perbaikan" data-target-input="nearest">';
                html += '<input type="text" name="waktu_selesai_tindaklanjut" class="form-control datetimepicker-input tindaklanjut-required" data-target="#datetime_selesai_perbaikan" disabled/>';
                html += '<div class="input-group-append" data-target="#datetime_selesai_perbaikan" data-toggle="datetimepicker">';
                html += '<div class="input-group-text"><i class="fa fa-calendar"></i></div>';
                html += '</div></div></div></div>';

                html += '<div class="form-group row">';
                html += '<div class="col-sm-3"></div>';
                html += '<div class="col-sm-6">';
                html += '<button type="button" class="btn btn-danger btn-sm float-right btn-close-input-tindaklanjut">';
                html += '<i class="fas fa-times"></i>&nbsp;&nbsp;&nbsp; Batal';
                html += '</button>';
                html += '</div></div>';

                html += '</div>';
                // end of form untuk menampilkan waktu perbaikan dan deskripsi perbaikan
                // ============================= END OF FORM INPUT TINDAKLANJUT ======================================

                html += '</div>';
                // end of div form-gangguan-nonperalatan

                html += '<hr class="my-4">';
            }
            // ============================= END OF FORM INPUT GANGGUAN ======================================

            html += '<div class="form-group row">';
            html += '<label class="col-sm-3 col-form-label required">Kondisi Layanan Saat Gangguan</label>';
            html += '<div class="col-sm-6">';
            html += '<select name="kondisi_layanan_open" class="form-control" required>';
            html += '<option value="">- Pilih -</option>';
            html += `<option value="${ KONDISI_LAYANAN.serviceable }">SERVICEABLE</option>`;
            html += `<option value="${ KONDISI_LAYANAN.unserviceable }">UNSERVICEABLE</option>`;
            html += '</select>';
            html += '<div class="invalid-feedback">Wajib dipilih</div>';
            html += '</div></div>';

            // div form kondisi layanan setelah dilakukan tindaklanjut
            html += '<div class="form-kondisi-tindaklanjut" id="dropdown_kondisi" style="display:none">';

            html += '<div class="form-group row">';
            html += '<label class="col-sm-3 col-form-label required">Kondisi Layanan Setelah Tindak Lanjut</label>';
            html += '<div class="col-sm-6">';
            html += '<select name="kondisi_layanan_close" class="form-control">';
            html += '<option value="">- Pilih -</option>';
            html += `<option value="${ KONDISI_LAYANAN.serviceable }">SERVICEABLE</option>`;
            html += `<option value="${ KONDISI_LAYANAN.unserviceable }">UNSERVICEABLE</option>`;
            html += '</select>';
            html += '<div class="invalid-feedback">Wajib dipilih.</div>';
            html += '</div></div>';

            html += '</div>';
            // end of div form kondisi layanan setelah dilakukan tindaklanjut

            formGangguan.innerHTML = html; // menampilkan variabel html ke halaman blade

            // aktifkan required pada form input gangguan pada jenis laporan GANGGUAN NON PERALATAN 
            if (jenis == JENIS.NON) {
                const formGangguanNon = document.getElementById('gangguan_non');

                toggleRequired(formGangguanNon, true, 'nonperalatan-required');
                initDateTimeNonPeralatan();
            }
        });
        // ===============================================================
        //                     END OF FUNCTION FORM JENIS
        // ===============================================================

        // =================================================================
        //                            EVEN GLOBAL
        // =================================================================
        document.addEventListener('click', function (e) {
            // ketika tombol Input Gangguan diklik, tampilkan form input gangguan (Jenis Laporan Gangguan Peralatan)
            if (e.target.closest('.btn-input-gangguan')) {

                const btn = e.target.closest('.btn-input-gangguan');
                const index = btn.dataset.index;

                const gangguan = document.getElementById('gangguan_' + index);
            
                // ubah nilai flag_gangguan menjadi 1
                const flag = document.getElementById('flag_gangguan_' + index);
                if (flag) flag.value = 1;

                show(gangguan, true);
                initDateTimePeralatan(index);
                toggleRequired(gangguan, true, 'gangguan-required');
            }

            // ketika tombol Batal diklik, sembunyikan form input gangguan (Jenis Laporan Gangguan Peralatan)
            if (e.target.closest('.btn-close-input-gangguan')) {

                const btn = e.target.closest('.btn-close-input-gangguan');
                const index = btn.dataset.index;

                const gangguan = document.getElementById('gangguan_' + index);

                // ubah nilai flag_gangguan menjadi 0
                const flag = document.getElementById('flag_gangguan_' + index);
                if (flag) flag.value = 0;

                show(gangguan, false);
                toggleRequired(gangguan, false, 'gangguan-required');
            }

            // jika tombol tombol Input Tindaklanjut diklik (Jenis Laporan Gangguan Non Peralatan)
            if (e.target.closest('.btn-input-tindaklanjut')) {

                const btn = e.target.closest('.btn-input-tindaklanjut');
                const isPerbaikan = true;
                const perbaikan = document.getElementById('tindaklanjut_non');

                // ubah nilai flag_tindaklanjut menjadi 1
                const flag = document.getElementById('flag_tindaklanjut');
                if (flag) flag.value = 1;

                show(perbaikan, isPerbaikan);
                initDateTimeNonPeralatan();
                toggleRequired(perbaikan, isPerbaikan, 'tindaklanjut-required');

                showKondisiLayanan(isPerbaikan);
            }

            // ketika tombol Batal diklik, sembunyikan form input tindaklanjut (Jenis Laporan Gangguan Non Peralatan)
            if (e.target.closest('.btn-close-input-tindaklanjut')) {

                const btn = e.target.closest('.btn-close-input-tindaklanjut');
                const isPerbaikan = false;
                const perbaikan = document.getElementById('tindaklanjut_non');

                // ubah nilai flag_tindaklanjut menjadi 0
                const flag = document.getElementById('flag_tindaklanjut');
                if (flag) flag.value = 0;

                show(perbaikan, isPerbaikan);
                toggleRequired(perbaikan, isPerbaikan, 'tindaklanjut-required');
                showKondisiLayanan(isPerbaikan);
            }
        });
        

        document.addEventListener('change', function (e) {

            // jika dropdown jenis tindaklanjut dipilih (Jenis Laporan Gangguan Peralatan)
            if (e.target.classList.contains('jenis-tindaklanjut')) {

                const index = e.target.dataset.index;

                const isPerbaikan = e.target.value == JENIS_TINDAKLANJUT_PERALATAN.perbaikan;

                const isPenggantian = e.target.value == JENIS_TINDAKLANJUT_PERALATAN.penggantian;

                const perbaikan = document.getElementById('perbaikan_' + index);
                const penggantian = document.getElementById('penggantian_' + index);

                show(perbaikan, isPerbaikan);
                show(penggantian, isPenggantian);

                initDateTimePeralatan(index);

                toggleRequired(perbaikan, isPerbaikan, 'perbaikan-required');
                toggleRequired(penggantian, isPenggantian, 'penggantian-required');

                showKondisiLayanan(isPerbaikan || isPenggantian);
            }
        });
        // =================================================================
        //                        END OF EVEN GLOBAL
        // =================================================================
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