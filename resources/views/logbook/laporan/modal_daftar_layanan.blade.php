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
    @foreach($layanan as $item)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ strtoupper($item->kode) }}</td>
      <td>{{ strtoupper($item->nama) }}</td>
      <td>{{ strtoupper($item->fasilitas->nama ?? '-') }}</td>
      <td>{{ strtoupper($item->LokasiTk1->nama ?? '-') }}</td>
      <td>{{ strtoupper($item->LokasiTk2->nama ?? '-') }}</td>
      <td>{{ strtoupper($item->LokasiTk3->nama ?? '-') }}</td>
      <td>
        <a href="{{ route('tambah.step2', ['layanan_id' => $item->id]) }}" class="btn btn-primary btn-sm">Pilih</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
