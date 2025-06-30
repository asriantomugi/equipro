<div class="modal fade" id="modalPilihPeralatanGanti" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Daftar Peralatan Tersedia</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">

        <form id="filter-ganti-peralatan-form">
          @csrf
          <div class="row">
            <div class="col-lg-3">
              <label>Jenis Alat</label>
              <select name="jenis" class="form-control">
                <option value="">- ALL -</option>
                @foreach($jenis as $item)
                  <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-3">
              <label>Kondisi</label>
              <select name="kondisi" class="form-control">
                <option value="">- ALL -</option>
                <option value="1">NORMAL</option>
                <option value="0">RUSAK</option>
              </select>
            </div>
            <div class="col-lg-3">
              <label>Kepemilikan</label>
              <select name="sewa" class="form-control">
                <option value="">- ALL -</option>
                <option value="1">SEWA</option>
                <option value="0">ASET</option>
              </select>
            </div>
            <div class="col-lg-3">
              <label>Perusahaan</label>
              <select name="perusahaan" class="form-control">
                <option value="">- ALL -</option>
                @foreach($perusahaan as $item)
                  <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <br>
          <div class="row">
            <div class="col-lg-12">
              <button type="submit" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-filter"></i>&nbsp;&nbsp;Filter Data
              </button>
            </div>
          </div>
        </form>

        <br>
        <div id="tabel-peralatan-ganti" class="mt-3">
          <!-- Data akan ditampilkan via AJAX -->
        </div>
      </div>
    </div>
  </div>
</div>
