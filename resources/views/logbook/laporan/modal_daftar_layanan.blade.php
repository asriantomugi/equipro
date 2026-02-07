<table id="layananTable" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th><center>NO</center></th>
      <th><center>KODE</center></th>
      <th><center>NAMA LAYANAN</center></th>
      <th><center>FASILITAS</center></th>
      <th><center>LOKASI TK 1</center></th>
      <th><center>LOKASI TK 2</center></th>
      <th><center>LOKASI TK 3</center></th>
      <th><center></center></th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @forelse($layanan as $item)
    <tr>
      <td><center>{{ $no++ }}</center></td>
      <td><center>{{ $item->kode ?? '-' }}</center></td>
      <td><center>{{ $item->nama ?? '-' }}</center></td>
      <td><center>{{ $item->fasilitas->nama ?? '-' }}</center></td>
      <td><center>{{ $item->lokasiTk1->nama ?? '-' }}</center></td>
      <td><center>{{ $item->lokasiTk2->nama ?? '-' }}</center></td>
      <td><center>{{ $item->lokasiTk3->nama ?? '-' }}</center></td>
      <td><center>
        <div class="d-flex">
        <button class="btn btn-secondary btn-sm" 
                onclick="detail('{{ $item->id }}')"
                title="Detail">
                <i class="fas fa-angle-double-right"></i>
        </button>
        <a href="{{ route('logbook.laporan.tambah.step2.form', $item->id) }}"
           class="btn btn-sm btn-primary">Pilih
        </a>
      </div>
      </center></td>
    </tr>
    @empty
    <tr>
      <td colspan="8" class="text-center">
        <em>Tidak ada layanan tersedia</em>
      </td>
    </tr>
    @endforelse
  </tbody>
</table>