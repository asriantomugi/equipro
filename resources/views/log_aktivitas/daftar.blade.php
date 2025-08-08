@extends('log_aktivitas.main')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <style>
    /* ✅ FIX tampilan select dropdown DataTables */
    .dataTables_length select {
      appearance: auto !important;
      -webkit-appearance: auto !important;
      -moz-appearance: auto !important;
      padding-right: 10px;
      background: none !important;
      font-family: inherit !important;
    }

    /* ✅ Hindari icon font mempengaruhi select */
    select.form-control,
    select.custom-select {
      font-family: 'Arial', sans-serif !important;
    }
  </style>
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">

    <!-- CARD -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">DAFTAR LOG AKTIVITAS</h3>
      </div>

      <div class="card-body">
        <table id="tabelLogAktivitas" class="table table-bordered table-striped">
          <thead>
            <tr class="table-condensed">
              <th style="width: 10px"><center>NO.</center></th>
              <th><center>WAKTU</center></th>
              <th><center>USER</center></th>
              <th><center>AKTIVITAS</center></th>
              <th><center>IP ADDRESS</center></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($daftar as $index => $log)
              <tr class="table-condensed">
                <td><center>{{ $index + 1 }}</center></td>
                <td><center>{{ $log->created_at->format('d-m-Y H:i:s') }}</center></td>
                <td><center>{{ strtoupper(optional($log->user)->name ?? '-') }}</center></td>
                <td><center>{{ strtoupper($log->aktivitas) }}</center></td>
                <td><center>{{ $log->ip_address ?? '-' }}</center></td>
              </tr>
            @empty
              <tr>
                <td colspan="5"><center>Belum ada log aktivitas.</center></td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
@endsection

@section('tail')
<script type="text/javascript">
  $(document).ready(function() {
    $('#tabelLogAktivitas').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
      },
      ordering: true,
      responsive: true,
      autoWidth: false,
      pageLength: 10,

      // ✅ Tambah class form-control biar dropdownnya konsisten
      initComplete: function () {
        $('.dataTables_length select').addClass('form-control form-control-sm');
      }
    });
  });
</script>
@endsection
