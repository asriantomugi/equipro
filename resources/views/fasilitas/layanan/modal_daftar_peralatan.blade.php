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
        <th style="width: 100px"></th>
        </tr>
    </thead>
    <tbody>
    @forelse($daftar_tersedia as $satu)
        <tr class="table-condensed">
            <td></td>
            <td><center>{{ strtoupper($satu->kode) }}</center></td>
            <td><center>{{ strtoupper($satu->nama) }}</center></td>
            <td><center>{{ strtoupper($satu->merk) }}</center></td>
            <td><center>{{ strtoupper($satu->tipe) }}</center></td>
            <td><center>{{ strtoupper($satu->model) }}</center></td>
            <td><center>{{ strtoupper($satu->serial_number) }}</center></td>

        @if($satu->kondisi == config('constants.kondisi_peralatan.normal'))
            <td><center><span class="badge bg-success">NORMAL</span></center></td>
        @else
            <td><center><span class="badge bg-danger">RUSAK</span></center></td>
        @endif
            <td>
                <center>
                    <button class="btn btn-sm btn-success btn-tambah-peralatan" data-id="{{ $satu->id }}">Tambah</button>
                </center>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center">Tidak ada data.</td>
        </tr>
    @endforelse                  
    </tbody>
</table>