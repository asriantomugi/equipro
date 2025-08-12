<table id="layananTable" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>NO.</th>
      <th>KODE</th>
      <th>NAMA LAYANAN</th>
      <th>FASILITAS</th>
      <th>LOKASI T.1</th>
      <th>LOKASI T.2</th>
      <th>LOKASI T.3</th>
      <th>ACTION</th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @forelse($layanan as $item)
    <tr>
      <td>{{ $no++ }}</td>
      <td>{{ $item->kode ?? '-' }}</td>
      <td>{{ $item->nama }}</td>
      <td>{{ $item->fasilitas->nama ?? '-' }}</td>
      <td>{{ $item->LokasiTk1->nama ?? '-' }}</td>
      <td>{{ $item->LokasiTk2->nama ?? '-' }}</td>
      <td>{{ $item->LokasiTk3->nama ?? '-' }}</td>
      <td>
        <a href="{{ url('/logbook/laporan/tambah/step2?layanan_id=' . $item->id) }}" 
           class="btn btn-sm btn-primary">
          <i class="fas fa-plus"></i> Pilih
        </a>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="8" class="text-center">
        <em>Tidak ada layanan tersedia. Semua layanan sedang dalam proses laporan.</em>
      </td>
    </tr>
    @endforelse
  </tbody>
</table>