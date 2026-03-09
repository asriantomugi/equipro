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
                            <li class="step-item completed"><a href="#">Input Gangguan & Tindaklanjut</a></li>
    @if($jenis_tindaklanjut != null)
        @if($jenis_tindaklanjut == 2)
                            <li class="step-item active"><a href="#">Penggantian Alat</a></li>
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

        {{-- Form --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">FORM PENGGANTIAN PERALATAN</h3></div>
                    <div class="card-body">

@foreach ($daftarPeralatan as $satu)
                        <div class="row">
                            <!-- =============== kolom peralatan lama ============= -->
                            <div class="col-sm-6">

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"></label>
                                    <label class="col-sm-8 col-form-label"><center>PERALATAN LAMA</center></label>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Kode</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="kode" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->kode) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Nama</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="nama" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->nama) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Merk</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="merk" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->merk) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Tipe</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="tipe" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->tipe) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Model</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="model" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->model) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Serial Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="serial_number" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->serial_number) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">No. Aset</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="no_aset" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->no_aset) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Tahun Produksi</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="thn_produksi" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->thn_produksi) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Tahun Pengadaan</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="thn_pengadaan" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->thn_pengadaan) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Jenis Alat</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="jenis_alat" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->jenis->nama) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Kepemilikan</label>
                                    <div class="col-sm-8">
                                @if($satu->peralatan->sewa == 1)
                                        <input type="text" 
                                                name="sewa" 
                                                class="form-control" 
                                                value="SEWA" 
                                                readonly>
                                @elseif($satu->peralatan->sewa == 0)
                                        <input type="text" 
                                                name="sewa" 
                                                class="form-control" 
                                                value="ASET" 
                                                readonly>
                                @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Pemilik</label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="perusahaan" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->peralatan->perusahaan->nama) }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Kondisi</label>
                                    <div class="col-sm-8">
                                @if($satu->peralatan->kondisi == 1)
                                        <input type="text" 
                                                name="kondisi" 
                                                class="form-control" 
                                                value="NORMAL" 
                                                readonly>
                                @elseif($satu->peralatan->kondisi == 2)
                                        <input type="text" 
                                                name="kondisi" 
                                                class="form-control" 
                                                value="NORMAL SEBAGIAN" 
                                                readonly>
                                @elseif($satu->peralatan->kondisi == 0)
                                        <input type="text" 
                                                name="kondisi" 
                                                class="form-control" 
                                                value="RUSAK" 
                                                readonly>
                                @endif
                                    </div>
                                </div>

                            </div>
                            <!-- col-sm-6 -->

                            <!-- =============== kolom peralatan baru ============= -->
                            <div class="col-sm-6">

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <label class="col-sm-8 col-form-label"><center>PERALATAN BARU</center></label>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="kode" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->kode ?? '-') }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="nama" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->nama ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="merk" 
                                                class="form-control"
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->merk ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="tipe" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->tipe ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="model" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->model ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="serial_number" 
                                                class="form-control"
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->serial_number ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="no_aset" 
                                                class="form-control"
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->no_aset ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="thn_produksi" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->thn_produksi ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="thn_pengadaan" 
                                                class="form-control"
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->thn_pengadaan ?? '-') }}"
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="jenis_alat" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->jenis->nama ?? '-') }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                            class="form-control"
                                            value="{{ $satu->tlPenggantianPeralatan?->peralatanBaru?->status_sewa ?? '-' }}"
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="perusahaan" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->perusahaan->nama ?? '-') }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                                name="perusahaan" 
                                                class="form-control" 
                                                value="{{ strtoupper($satu->tlPenggantianPeralatan->peralatanBaru->kondisi ?? '-') }}" 
                                                readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-8">
                                        <button class="btn btn-success btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#modalPeralatan"
                                            data-gangguan-id="{{ $satu->gangguan_id }}"
                                            data-tl-gangguan-id="{{ $satu->id }}"
                                            data-peralatan-lama-id="{{ $satu->peralatan_id }}">
                                            <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;Pilih</button>

                                        <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}"
                                            class="btn btn-danger btn-sm float-right">
                                            <i class="fas fa-trash-alt"></i>&nbsp;&nbsp;&nbsp;Hapus
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <!-- col-sm-6 -->
                        </div>
                        <!-- row -->

                        <hr class="my-4" style="border-top: 3px solid #a8a5a5;">
@endforeacH
                        

                        <div class="card-footer">
                            <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-angle-left"></i>&nbsp;&nbsp;&nbsp;Kembali
                            </a>
                            <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}"
                                class="btn btn-success btn-sm float-right">
                                Lanjut&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                            </a>
                            <!--
                            <button type="submit" class="btn btn-success btn-sm float-right" id="btn-submit">
                                Lanjut&nbsp;<i class="fas fa-angle-right"></i>
                            </button>
                            -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk pilih peralatan -->
<div class="modal fade" 
     id="modalPeralatan" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="modalPeralatanLabel" 
     aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Daftar Peralatan Tersedia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div><!-- modal-header -->

            <div class="modal-body">

<form id="filter-form">
    @csrf

                <input type="text" name="laporan_id" value="{{ $laporan->id }}">
                <input type="text" id="tl_gangguan_id" name="tl_gangguan_id">
                <input type="text" id="peralatan_lama_id" name="peralatan_lama_id">
                <input type="text" name="layanan_id" value="{{ $laporan->layanan->id }}">

                <div class="row">

                    <div class="col-lg-4">
                        <label for="jenis" class="form-label">Jenis Alat</label>
                        <select name="jenis" class="form-control">
                            <option value="">- ALL -</option>
                            @foreach($jenis as $satu)
                            <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label for="sewa" class="form-label">Status Kepemilikan</label>
                        <select name="sewa" class="form-control">
                            <option value="">- ALL -</option>
                            <option value="1">SEWA</option>
                            <option value="0">ASET</option>
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label for="perusahaan" class="form-label">Perusahaan Pemilik</label>
                        <select name="perusahaan" class="form-control">
                            <option value="">- ALL -</option>
                            @foreach($perusahaan as $satu)
                            <option value="{{ $satu->id }}">{{ $satu->kode }} - {{ $satu->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <!-- row -->
                <br>
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <button type="submit" 
                                class="btn btn-primary btn-sm float-right">
                                <i class="fas fa-filter"></i>&nbsp;&nbsp;&nbsp;Filter Data</button>
                    </div>
                </div>
                <!-- row -->
</form>
<!-- form -->

                <br>
                <div class="row">
                    <div class="col-lg-12">
                    <div id="daftar-peralatan-tabel">
                        <!-- AJAX content will be inserted here -->

                    </div>
                </div><!-- row -->
                
            </div>
            <!-- modal-body -->

        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- modal fade -->

<!-- Akhir dari Modal untuk tambah peralatan -->
@endsection

@section('tail')

<!-- javascript untuk pop up notifikasi -->
<script type="text/javascript">
  @if (session()->has('notif'))
    @if (session()->get('notif') == 'tambah_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menambahkan peralatan baru.',
          autohide: true,
          delay: 3000
        })
    @elseif (session()->get('notif') == 'hapus_gagal')
      $(document).Toasts('create', {
          class: 'bg-danger',
          title: 'Error!',
          body: 'Gagal menghapus peralatan baru.',
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
    @elseif (session()->get('notif') == 'tambah_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Error!',
          body: 'Peralatan baru telah berhasil ditambahkan.',
          autohide: true,
          delay: 3000
        })
    @elseif (session()->get('notif') == 'hapus_sukses')
      $(document).Toasts('create', {
          class: 'bg-success',
          title: 'Error!',
          body: 'Peralatan baru telah berhasil dihapus.',
          autohide: true,
          delay: 3000
        })
    @endif
  @endif
</script>

<!-- javascript untuk pop up khusus notifikasi tambah berhasil -->
<script>
    $(document).ready(function () {
        if (localStorage.getItem('tambah_sukses') === 'true') {
            // Tampilkan toast
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Berhasil!',
                body: 'Peralatan baru telah berhasil disimpan',
                autohide: true,
                delay: 5000
            });
        }
            // Hapus flag agar tidak muncul lagi saat reload berikutnya
            // localStorage.removeItem('tambah_peralatan_sukses');
            localStorage.clear();
    });
</script>

<!-- javascript untuk mengirimkan data ID gangguan dan ID peralatan lama ke modal pilih peralatan -->
<script>
$(document).ready(function(){
    $('#modalPeralatan').on('show.bs.modal', function (e) {

        var btn = $(e.relatedTarget);

        var gangguanId = btn.data('gangguanId');
        var tlGangguanId = btn.data('tlGangguanId');
        var peralatanLamaId = btn.data('peralatanLamaId');

        // isi hidden input
        $('#gangguan_id').val(gangguanId);
         $('#tl_gangguan_id').val(tlGangguanId);
        $('#peralatan_lama_id').val(peralatanLamaId);

    });
});
</script>


<!-- javascript untuk proses submit filter data peralatan tersedia dan tambah peralatan -->
<script type="text/javascript">
    $(function () {
    // Filter form submit
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();

        // proses POST filter data daftar peralatan tersedia
        $.post('{{ route('logbook.laporan.peralatan.filter') }}', // url('/logbook/laporan/peralatan/filter') 
            $(this).serialize(), 
            function (data) {
            $('#daftar-peralatan-tabel').html(data);

            // Inisialisasi ulang DataTable setelah isi diganti
            if ($.fn.DataTable.isDataTable('#tabelPeralatanTersedia')) {
                $('#tabelPeralatanTersedia').DataTable().destroy();
            }

            peralatanTable = $('#tabelPeralatanTersedia').DataTable({
                paging: true,
                pageLength: 5, // jumlah row default data yang ditampilkan
                lengthChange: true,
                searching: true,
                ordering: true,
                autoWidth: false,
                responsive: true,
                language: {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari:",
                    "paginate": {
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                    }
                },
                columnDefs: [{
                    targets: 0,
                    searchable: false,
                    orderable: false
                }],
                order: [[1, 'asc']]
            });

            // Penomoran ulang kolom pertama
            peralatanTable.on('order.dt search.dt draw.dt', function () {
                let i = 1;
                peralatanTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell) {
                    cell.innerHTML = i++;
                });
            }).draw();
        })
        /*.done(function () {
            alert('Berhasil');
        })*/
        // jika proses POST gagal
        .fail(function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Error: ' + xhr.responseText);
        });
    });
    // akhir dari proses POST filter data daftar peralatan tersedia
    
    // proses tambah peralatan di modal daftar peralatan tersedia
    $('#daftar-peralatan-tabel').on('click', '.btn-tambah-peralatan', function () {
        const peralatanBaruId = $(this).data('id');
        const laporanId = $('input[name="laporan_id"]').val();
        const tlGangguanId = $('input[name="tl_gangguan_id"]').val();
        const peralatanLamaId = $('input[name="peralatan_lama_id"]').val();
        const layananId = $('input[name="layanan_id"]').val();

        $.post('{{ route('logbook.laporan.tambah.step3.peralatan.tambah') }}', { // url('/logbook/laporan/tambah/step3/peralatan/tambah')
            _token: '{{ csrf_token() }}',
            peralatan_baru_id: peralatanBaruId,
            laporan_id: laporanId,
            tl_gangguan_id: tlGangguanId,
            peralatan_lama_id: peralatanLamaId,
            layanan_id: layananId,
        })
        // apabila proses tambah berhasil
        .done(function () {
            // Tutup modal
            $('#modalPeralatan').modal('hide');
            // Simpan flag sukses ke localStorage agar tetap ada setelah reload
            localStorage.setItem('tambah_sukses', 'true');
            // Reload halaman
            location.reload();
        })
        // apabila proses tambah gagal, tampilkan pesan error
        .fail(function (xhr) {
            let errorMsg = 'Gagal menyimpan peralatan baru.';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.reason) {
                    errorMsg = xhr.responseJSON.reason;
                } else if (xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
            }

            // Tutup modal
            $('#modalPeralatan').modal('hide');

            // Tampilkan toast gagal
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error!',
                body: errorMsg,
                autohide: true,
                delay: 3000
            });
        });
    });
    // akhir dari proses tambah peralatan di modal daftar peralatan tersedia
});
</script>

@endsection
<!-- /. section tail -->