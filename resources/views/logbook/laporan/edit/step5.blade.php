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
          <!-- Step 1: Pilih Layanan -->
          <li class="step-item completed">
            <a href="{{ route('tambah.step1') }}">Pilih Layanan</a>
          </li>
          
          <!-- Step 2: Input Gangguan -->
          <li class="step-item completed">
            <a href="{{ route('tambah.step2.back', ['laporan_id' => $laporan->id]) }}">Input Gangguan</a>
          </li>
          
          <!-- Step 3: Tindaklanjut -->
          <li class="step-item completed">
            <a href="{{ route('tambah.step3.back', ['laporan_id' => $laporan->id]) }}">Tindaklanjut</a>
          </li>
          
          @php
            // Cek apakah ada penggantian untuk gangguan peralatan
            $adaPenggantian = false;
            if ($laporan->jenis == 1) { // Gangguan peralatan
              $tindakLanjutPeralatan = \App\Models\TlGangguanPeralatan::where('laporan_id', $laporan->id)->get();
              foreach ($tindakLanjutPeralatan as $tl) {
                if ($tl->jenis_tindaklanjut == 0) { // 0 = penggantian
                  $adaPenggantian = true;
                  break;
                }
              }
            }
          @endphp
          
          <!-- Step 4: Penggantian (hanya untuk gangguan peralatan dengan penggantian) -->
          @if ($adaPenggantian)
            <li class="step-item completed">
              <a href="{{ route('tambah.step4.back', ['laporan_id' => $laporan->id]) }}">Penggantian</a>
            </li>
          @endif
          
          <!-- Step Final: Review -->
          <li class="step-item active">
            <a href="#">Review</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

    <form action="{{ route('logbook.laporan.edit.step5.update', $laporan->id) }}" method="POST">
      @csrf
      @method('POST')
      <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

      {{-- Informasi Layanan --}}
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><h3 class="card-title">INFORMASI LAYANAN</h3></div>
            <div class="card-body">
              <table border="0" cellpadding="5px">
                <tr><th>Fasilitas</th><td>:</td><td>{{ $laporan->layanan->fasilitas->kode ?? '-' }} - {{ $laporan->layanan->fasilitas->nama ?? '-' }}</td></tr>
                <tr><th>Kode Layanan</th><td>:</td><td>{{ $laporan->layanan->kode ?? '-' }}</td></tr>
                <tr><th>Nama Layanan</th><td>:</td><td>{{ $laporan->layanan->nama ?? '-' }}</td></tr>
                <tr><th>Lokasi Tingkat I</th><td>:</td><td>{{ $laporan->layanan->lokasiTk1->kode ?? '-' }} - {{ $laporan->layanan->lokasiTk1->nama ?? '-' }}</td></tr>
                <tr><th>Lokasi Tingkat II</th><td>:</td><td>{{ $laporan->layanan->lokasiTk2->kode ?? '-' }} - {{ $laporan->layanan->lokasiTk2->nama ?? '-' }}</td></tr>
                <tr><th>Lokasi Tingkat III</th><td>:</td><td>{{ $laporan->layanan->lokasiTk3->kode ?? '-' }} - {{ $laporan->layanan->lokasiTk3->nama ?? '-' }}</td></tr>
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
                        @if((string)$gangguan->kondisi === (string)config('constants.kondisi_gangguan_peralatan.beroperasi'))
                          <span class="badge bg-success">BEROPERASI</span>
                        @else
                          <span class="badge bg-danger">GANGGUAN</span>
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
                    <td><center>{{ \Carbon\Carbon::parse($gangguanNonPeralatan->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                    <td><center>{{ strtoupper($gangguanNonPeralatan->deskripsi ?? '-') }}</center></td>
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
              @php
                // Tentukan apakah ada penggantian
                $adaPenggantian = isset($penggantian) && $penggantian->count() > 0;
                $adaPerbaikan = isset($perbaikan) && $perbaikan->count() > 0;
                $adaSemuaTindakLanjut = isset($semuaTindakLanjut) && $semuaTindakLanjut->count() > 0;
              @endphp

              @if($laporan->jenis == 1)
                {{-- LOGIC: Untuk gangguan peralatan, kelompokkan per peralatan --}}
                @if($adaPenggantian || $adaPerbaikan)
                  @php
                    // Kelompokkan data per peralatan
                    $tindakLanjutPerPeralatan = collect();
                    
                    // Tambahkan data penggantian
                    if($adaPenggantian) {
                      foreach($penggantian as $item) {
                        $peralatanLamaId = $item->peralatan_lama_id;
                        if (!$tindakLanjutPerPeralatan->has($peralatanLamaId)) {
                          $tindakLanjutPerPeralatan[$peralatanLamaId] = [
                            'peralatan' => $item->peralatanLama,
                            'tindakan' => collect()
                          ];
                        }
                        $tindakLanjutPerPeralatan[$peralatanLamaId]['tindakan']->push([
                          'jenis' => 'PENGGANTIAN',
                          'peralatan_lama' => $item->peralatanLama->nama ?? '-',
                          'peralatan_baru' => $item->peralatanBaru->nama ?? '-',
                          'waktu' => $item->tindaklanjut->waktu ?? now(),
                          'deskripsi' => $item->tindaklanjut->deskripsi ?? '-',
                          'kondisi' => $item->tindaklanjut->kondisi
                        ]);
                      }
                    }
                    
                    // Tambahkan data perbaikan
                    if($adaPerbaikan) {
                      foreach($perbaikan as $item) {
                        $peralatanId = $item->peralatan_id;
                        if (!$tindakLanjutPerPeralatan->has($peralatanId)) {
                          $tindakLanjutPerPeralatan[$peralatanId] = [
                            'peralatan' => $item->peralatan,
                            'tindakan' => collect()
                          ];
                        }
                        $tindakLanjutPerPeralatan[$peralatanId]['tindakan']->push([
                          'jenis' => 'PERBAIKAN',
                          'peralatan_lama' => $item->peralatan->nama ?? '-',
                          'peralatan_baru' => '-',
                          'waktu' => $item->waktu ?? now(),
                          'deskripsi' => $item->deskripsi ?? '-',
                          'kondisi' => $item->kondisi
                        ]);
                      }
                    }
                  @endphp

                  @foreach($tindakLanjutPerPeralatan as $peralatanId => $data)
                  <div class="mb-4">
                      <h6>
                        <span class="badge bg-primary">
                          {{ strtoupper($data['peralatan']->kode ?? '-') }} - {{ strtoupper($data['peralatan']->nama ?? '-') }}
                        </span>
                      </h6>
                    
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
                          @foreach($data['tindakan'] as $index => $tindakan)
                          <tr>
                            <td><center>{{ $index + 1 }}</center></td>
                            <td><center>{{ $tindakan['jenis'] }}</center></td>
                            <td><center>{{ strtoupper($tindakan['peralatan_lama']) }}</center></td>
                            <td><center>{{ strtoupper($tindakan['peralatan_baru']) }}</center></td>
                            <td><center>{{ \Carbon\Carbon::parse($tindakan['waktu'])->format('d-m-Y H:i') }}</center></td>
                            <td><center>{{ strtoupper($tindakan['deskripsi']) }}</center></td>
                            <td>
                              <center>
                                @if($tindakan['kondisi'] == 1 || $tindakan['kondisi'] == '1')
                                  <span class="badge bg-success">BEROPERASI</span>
                                @else
                                  <span class="badge bg-danger">GANGGUAN</span>
                                @endif
                              </center>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @endforeach
                @else
                  <p class="text-center">Tidak ada data tindak lanjut yang tersedia.</p>
                @endif

              @else
                {{-- LOGIC: Untuk gangguan non-peralatan, gunakan tabel sederhana --}}
                @if($adaSemuaTindakLanjut)
                  <table class="table table-bordered table-striped table-sm">
                    <thead>
                      <tr>
                        <th><center>No</center></th>
                        <th><center>Jenis</center></th>
                        <th><center>Waktu</center></th>
                        <th><center>Deskripsi</center></th>
                        <th><center>Kondisi</center></th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($semuaTindakLanjut as $i => $item)
                      <tr>
                        <td><center>{{ $i + 1 }}</center></td>
                        <td><center>PERBAIKAN</center></td>
                        <td><center>{{ \Carbon\Carbon::parse($item->waktu ?? now())->format('d-m-Y H:i') }}</center></td>
                        <td><center>{{ strtoupper($item->deskripsi ?? '-') }}</center></td>
                        <td>
                          <center>
                            @if($item->kondisi == 1 || $item->kondisi == '1')
                              <span class="badge bg-success">BEROPERASI</span>
                            @else
                              <span class="badge bg-danger">GANGGUAN</span>
                            @endif
                          </center>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                @else
                  <p class="text-center">Tidak ada data tindak lanjut yang tersedia.</p>
                @endif
              @endif
            </div>
            <div class="card-footer">
              @if(isset($penggantian) && $penggantian->count() > 0)
                <a href="{{ route('logbook.laporan.edit.step4', $laporan->id) }}" class="btn btn-success btn-sm">
                  <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
                </a>
              @else
                <a href="{{ route('logbook.laporan.edit.step3', $laporan->id) }}" class="btn btn-success btn-sm">
                  <i class="fas fa-angle-left"></i>&nbsp;&nbsp;Kembali
                </a>
              @endif
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

@section('tail')
<script type="text/javascript">
$(function(){
    @if (session()->has('notif'))
        @if (session()->get('notif') == 'edit_sukses')
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Sukses!',
                body: 'Laporan telah berhasil diperbarui',
                autohide: true,
                delay: 3000
            })
        @elseif(session()->get('notif') == 'edit_gagal')
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error!',
                body: 'Gagal memperbarui laporan',
                autohide: true,
                delay: 3000
            })
        @endif
    @endif
});
</script>
@endsection