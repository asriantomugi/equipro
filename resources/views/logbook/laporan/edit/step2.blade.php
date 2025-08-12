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
                            <li class="step-item completed"><a href="#">Pilih Layanan</a></li>
                            <li class="step-item active"><a href="#">Input Gangguan</a></li>
                            <li class="step-item"><a href="#">Tindaklanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('laporan.edit.step2.update', $laporan->id) }}" id="step2-form" novalidate>
            @csrf
            <input type="hidden" name="laporan_id"  value="{{ $laporan->id }}">
            <input type="hidden" name="layanan_id"  value="{{ $laporan->layanan_id }}">

            {{-- CARD 1: DATA LAYANAN --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">DATA LAYANAN</h3></div>
                <div class="card-body">
                    @php
                        $fasilitasNama  = $layanan->fasilitas->nama ?? '-';
                        $lokasiTkt1Nama = $layanan->lokasiTk1->nama ?? '-';
                        $lokasiTkt2Nama = $layanan->lokasiTk2->nama ?? '-';
                        $lokasiTkt3Nama = $layanan->lokasiTk3->nama ?? '-';
                    @endphp

                    @foreach([
                        'KODE'            => $layanan->kode,
                        'NAMA'            => $layanan->nama,
                        'FASILITAS'       => $fasilitasNama,
                        'LOKASI TINGKAT 1'=> $lokasiTkt1Nama,
                        'LOKASI TINGKAT 2'=> $lokasiTkt2Nama,
                        'LOKASI TINGKAT 3'=> $lokasiTkt3Nama,
                        'STATUS'          => $layanan->status ? 'Aktif' : 'Tidak Aktif',
                        'KONDISI'         => $layanan->kondisi ==
                                             config('constants.kondisi_layanan.Serviceable')
                                             ? 'Serviceable' : 'Unserviceable',
                    ] as $label => $value)
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">{{ $label }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $value }}" readonly>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group row">
                        <label for="jenis_laporan" class="col-sm-3 col-form-label">
                            JENIS LAPORAN <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            {{-- Jenis laporan tidak bisa diubah saat edit --}}
                            <input type="text" class="form-control" 
                                   value="{{ $selectedJenisLaporan == 'gangguan_peralatan' ? 'Gangguan Peralatan' : 'Gangguan Non Peralatan' }}" 
                                   readonly>
                            <input type="hidden" name="jenis_laporan" value="{{ $selectedJenisLaporan }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Jenis laporan tidak dapat diubah saat edit
                            </small>
                        </div>
                    </div>

                </div>
            </div>

            {{-- CARD 2: FORM GANGGUAN --}}
            <div class="card mt-2" id="card-input-gangguan">
                <div class="card-header"><h3 class="card-title">INPUT GANGGUAN</h3></div>
                <div class="card-body" id="form-gangguan-container">
                    {{-- Diisi JavaScript --}}
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="card mt-3">
                <div class="card-footer">
                    <a href="{{ url('/logbook/laporan/daftar') }}" 
                      class="btn btn-default">Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        Lanjut &nbsp;&nbsp;<i class="fas fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </form>

    </div>
</section>
@endsection

{{-- =====================  STYLE  ===================== --}}
@push('styles')
<style>
/* cegah dropdown terpotong */
#card-input-gangguan,
#card-input-gangguan .card,
#card-input-gangguan .card-body {
    overflow: visible;
}

/* hilangkan pseudo‑asterisk bawaan browser / bootstrap */
input[type="datetime-local"]::after,
input.form-control:required::after,
input[type="datetime-local"]:required::after {
    content: none !important;
    display: none !important;
    color: transparent !important;
}

/* styling untuk readonly field */
.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
/* ---------- DATA DARI BACK‑END ---------- */
const kondisiGangguan   = @json(config('constants.kondisi_gangguan_peralatan'));
const peralatan         = @json($layanan->daftarPeralatanLayanan ?? []);
const jenisLaporan      = @json($selectedJenisLaporan);

// PERBAIKAN: Gunakan data waktu yang sudah diformat oleh controller
const existingData = {
    waktu_gangguan: @json($waktuGangguan ?? ''), // Ini sudah dalam format Y-m-d\TH:i dari controller
    deskripsi_gangguan: @json($gangguanNonPeralatan->deskripsi ?? ''),
    gangguan_peralatan: @json($gangguanPeralatan ?? [])
};

console.log('Debug existing data:', existingData); // Untuk debugging

/* ---------- UTIL ---------- */
function markInvalid(el,msg){
  el.classList.add('is-invalid');
  let fe = el.parentNode.querySelector('.invalid-feedback.dynamic');
  if(!fe){
    fe=document.createElement('div');
    fe.className='invalid-feedback dynamic';
    el.parentNode.appendChild(fe);
  }
  fe.innerText=msg;
}

// PERBAIKAN: Tidak perlu function formatDateTimeLocal lagi karena controller sudah memformat
// Tapi tetap buat untuk fallback jika ada kasus edge
function formatDateTimeLocal(dateString) {
    if (!dateString) return '';
    
    // Jika sudah dalam format datetime-local (Y-m-d\TH:i)
    if (dateString.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
        return dateString; // Sudah dalam format yang benar
    }
    
    // Jika sudah dalam format datetime-local tapi dengan detik (Y-m-d\TH:i:s)
    if (dateString.includes('T') && dateString.length > 16) {
        return dateString.substring(0, 16); // Ambil hanya Y-m-d\TH:i
    }
    
    // Jika dalam format database (Y-m-d H:i:s)
    if (dateString.includes(' ')) {
        return dateString.replace(' ', 'T').substring(0, 16);
    }
    
    // Fallback: coba parse sebagai Date
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    } catch (e) {
        console.error('Error formatting date:', e);
        return '';
    }
}

/* ---------- BUILD FORM DINAMIS ---------- */
function buildGangguanForm(jenis){
  const card=document.getElementById('card-input-gangguan');
  const cont=document.getElementById('form-gangguan-container');
  
  // PERBAIKAN: Langsung gunakan waktu dari controller, hanya format ulang jika diperlukan
  let formattedWaktu = existingData.waktu_gangguan;
  
  // Validasi tambahan untuk memastikan format benar
  if (formattedWaktu && !formattedWaktu.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)) {
      formattedWaktu = formatDateTimeLocal(formattedWaktu);
  }
  
  console.log('Waktu yang akan ditampilkan:', formattedWaktu); // Debug
  
  let html=`<div class="form-group row mb-2">
     <label class="col-sm-3 col-form-label">Waktu Gangguan<span class="text-danger">*</span></label>
     <div class="col-sm-9">
       <input type="datetime-local" name="waktu_gangguan" class="form-control" 
              value="${formattedWaktu || ''}" 
              data-required="true">
     </div>
   </div>`;

  if(jenis==='gangguan_peralatan'){
     if(peralatan.length===0){
        html+=`<p class="text-danger">Tidak ada daftar peralatan untuk layanan ini.</p>`;
     } else {
        peralatan.forEach((p,i)=>{
           const nama = p.peralatan?.nama ?? p.nama ?? '-';
           const peralatanId = p.peralatan?.id ?? p.peralatan_id ?? p.id ?? '';
           
           // Cari data existing untuk peralatan ini
           const existingGangguan = existingData.gangguan_peralatan.find(g => {
               return g.peralatan_id == peralatanId || 
                      g.peralatan_id == p.peralatan_id ||
                      g.peralatan_id == p.id;
           });
           
           const kondisiValue = existingGangguan?.kondisi !== undefined ? String(existingGangguan.kondisi) : '';
           const deskripsiValue = existingGangguan?.deskripsi ?? '';
           
           console.log(`Peralatan ${i+1} (${nama}):`, {
               peralatanId,
               existingGangguan,
               kondisiValue,
               deskripsiValue
           }); // Debug
           
           html+=`
            <hr>
            <div class="mb-2">
                <strong>Peralatan ${i+1}: <span class="badge bg-primary">${nama}</span></strong>
            </div>
            <input type="hidden" name="gangguan[${i}][id]" value="${peralatanId}">
            <input type="hidden" name="gangguan[${i}][existing_id]" value="${existingGangguan?.id || ''}">
            <div class="form-group row mb-2">
              <label class="col-sm-3 col-form-label">Kondisi Gangguan<span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <select name="gangguan[${i}][kondisi]" class="form-control" data-required="true">
                  <option value="">- Pilih -</option>`;
                  
           // Build options untuk kondisi
           Object.entries(kondisiGangguan).forEach(([key, value]) => {
               const isSelected = kondisiValue !== '' && String(kondisiValue) === String(value) ? 'selected' : '';
               const labelText = key.replace('_', ' ').toUpperCase();
               
               html += `<option value="${value}" ${isSelected}>${labelText}</option>`;
           });
           
           html += `</select>
              </div>
            </div>
            <div class="form-group row mb-2">
              <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
              <div class="col-sm-9">
                <textarea name="gangguan[${i}][deskripsi]" class="form-control" rows="3" placeholder="Masukkan deskripsi gangguan...">${deskripsiValue}</textarea>
              </div>
            </div>`;
        });
     }
  } else if(jenis === 'gangguan_non_peralatan'){
     html+=`
      <div class="form-group row mb-2">
        <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
        <div class="col-sm-9">
          <textarea name="deskripsi_gangguan" class="form-control" rows="3" placeholder="Masukkan deskripsi gangguan non peralatan...">${existingData.deskripsi_gangguan || ''}</textarea>
        </div>
      </div>`;
  }
  
  cont.innerHTML=html;
}

/* ---------- DOM READY ---------- */
document.addEventListener('DOMContentLoaded',()=>{
  const form=document.getElementById('step2-form');

  // Build form dengan data existing
  buildGangguanForm(jenisLaporan);

  form.addEventListener('submit',e=>{
    e.preventDefault();

    // reset status lama
    form.querySelectorAll('.is-invalid,.is-valid')
        .forEach(el=>el.classList.remove('is-invalid','is-valid'));
    form.querySelectorAll('.invalid-feedback.dynamic').forEach(el=>el.remove());

    let hasErr=false;

    // validasi semua field wajib
    form.querySelectorAll('[data-required="true"]').forEach(el=>{
       if(el.value === '' || el.value === null || el.value === undefined){
          const label=el.closest('.form-group')?.querySelector('label')
                     ?.innerText.replace('*','').trim()||'Field ini';
          markInvalid(el,`${label} wajib diisi`);
          hasErr=true;
       }else{
          el.classList.add('is-valid');
       }
    });

     if(!hasErr) form.submit();
  });
});
</script>
@endpush