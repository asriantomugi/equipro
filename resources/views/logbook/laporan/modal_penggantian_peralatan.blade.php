<table id="tabelPeralatanTersedia" class="table table-bordered table-striped">
    <thead>
        <tr class="table-condensed">
            <th style="width: 10px"><center>NO.</center></th>
            <th><center>KODE</center></th>
            <th><center>NAMA</center></th>
            <th><center>MERK</center></th>
            <th><center>TIPE</center></th>
            <th><center>MODEL</center></th>
            <th><center>SN</center></th>
            <th><center>KONDISI</center></th>
            <th style="width: 100px"><center>AKSI</center></th>
        </tr>
    </thead>
    <tbody>
        @forelse($peralatan as $alat)
            <tr class="table-condensed">
                <td><center>{{ $loop->iteration }}</center></td>
                <td><center>{{ strtoupper($alat->kode ?? '-') }}</center></td>
                <td><center>{{ strtoupper($alat->nama ?? '-') }}</center></td>
                <td><center>{{ strtoupper($alat->merk ?? '-') }}</center></td>
                <td><center>{{ strtoupper($alat->tipe ?? '-') }}</center></td>
                <td><center>{{ strtoupper($alat->model ?? '-') }}</center></td>
                <td><center>{{ strtoupper($alat->serial_number ?? '-') }}</center></td>
        @if($alat->kondisi == config('constants.kondisi_peralatan.normal'))
            <td><center><span class="badge bg-success">NORMAL</span></center></td>
        @elseif($alat->kondisi == config('constants.kondisi_peralatan.normal_sebagian'))
            <td><center><span class="badge bg-warning">NORMAL SEBAGIAN</span></center></td>
        @else
            <td><center><span class="badge bg-danger">RUSAK</span></center></td>
        @endif
                <td>
                    <center>
                        <button type="button" class="btn btn-sm btn-success btn-pilih-peralatan"
                            data-detail='@json($alat)'>
                            Pilih
                        </button>
                    </center>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted">Data tidak ditemukan</td>
            </tr>
        @endforelse
    </tbody>
</table>
