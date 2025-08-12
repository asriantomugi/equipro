@extends('logbook.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">

        {{-- STEP NAV --}}
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-2">
                        <ul class="step d-flex flex-nowrap">
                            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
                            <li class="step-item active"><a href="#">Input Gangguan</a></li>
                            <li class="step-item"><a href="#">Tindaklanjut</a></li>
                            <li class="step-item"><a href="#">Review</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('tambah.step2.back.simpan') }}" id="step2-form" novalidate>
            @csrf
            <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
            <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

            {{-- CARD 1 – DATA LAYANAN --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">DATA LAYANAN</h3></div>
                <div class="card-body">
                    @php
                        $fasilitasNama  = $layanan->fasilitas->nama ?? '-';
                        $lok1 = $layanan->LokasiTk1->nama ?? '-';
                        $lok2 = $layanan->LokasiTk2->nama ?? '-';
                        $lok3 = $layanan->LokasiTk3->nama ?? '-';
                    @endphp
                    @foreach([
                        'KODE' => $layanan->kode,
                        'NAMA' => $layanan->nama,
                        'FASILITAS' => $fasilitasNama,
                        'LOKASI TINGKAT 1' => $lok1,
                        'LOKASI TINGKAT 2' => $lok2,
                        'LOKASI TINGKAT 3' => $lok3,
                        'STATUS' => $layanan->status ? 'Aktif' : 'Tidak Aktif',
                        'KONDISI' => $layanan->kondisi == config('constants.kondisi_layanan.Serviceable')
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
                        <label class="col-sm-3 col-form-label">JENIS LAPORAN <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="jenis_laporan" id="jenis_laporan" class="form-control" data-required="true">
                                <option value="">- Pilih -</option>
                                @foreach($jenisLaporan as $key => $val)
                                    <option value="{{ $key }}" {{ old('jenis_laporan', $selectedJenisLaporan) == $key ? 'selected' : '' }}>
                                        {{ Str::title(str_replace('_',' ',$key)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2 – INPUT GANGGUAN (dinamis) --}}
            <div class="card d-none mt-2" id="card-input-gangguan">
                <div class="card-header"><h3 class="card-title">INPUT GANGGUAN</h3></div>
                <div class="card-body" id="form-gangguan-container"></div>
            </div>

            {{-- FOOTER --}}
            <div class="card mt-3">
                <div class="card-footer">
                    <a href="{{ route('tambah.step1') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-angle-left"></i>&nbsp;Kembali
                    </a>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        Lanjut&nbsp;<i class="fas fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

{{-- =====================  SCRIPT  ===================== --}}
@push('scripts')
<script>
/* ---------- DATA DARI BACK‑END ---------- */
const kondisiGangguan   = @json(config('constants.kondisi_gangguan_peralatan'));
const peralatan         = @json($layanan->daftarPeralatanLayanan);
const jenisLaporanAwal  = @json(old('jenis_laporan', $selectedJenisLaporan ?? ''));
const waktuGangguanAwal = @json($waktuGangguan ?? ''); // Data dari controller
const gangguanPeralatan = @json($gangguanPeralatan ?? []);
const gangguanNon       = @json($gangguanNonPeralatan ?? null);

// Debug untuk melihat data yang diterima
console.log('Debug data received:');
console.log('- waktuGangguanAwal:', waktuGangguanAwal);
console.log('- jenisLaporanAwal:', jenisLaporanAwal);
console.log('- gangguanPeralatan:', gangguanPeralatan);
console.log('- gangguanNon:', gangguanNon);

/* ---------- UTIL ---------- */
function markInvalid(el, msg){
  el.classList.add('is-invalid');
  let fe = el.parentNode.querySelector('.invalid-feedback.dynamic');
  if(!fe){
    fe = document.createElement('div');
    fe.className = 'invalid-feedback dynamic';
    el.parentNode.appendChild(fe);
  }
  fe.innerText = msg;
}

/* ---------- BUILD FORM DINAMIS--------*/
function buildGangguanForm(jenis){
  const card=document.getElementById('card-input-gangguan');
  const cont=document.getElementById('form-gangguan-container');
  card.classList.remove('d-none');
  
  // Format waktu gangguan dengan lebih hati-hati
  let waktuValue = '';
  if (waktuGangguanAwal) {
    waktuValue = waktuGangguanAwal;
    
    // Jika masih dalam format database (Y-m-d H:i:s), convert ke datetime-local format
    if (waktuValue.includes(' ') && !waktuValue.includes('T')) {
      waktuValue = waktuValue.replace(' ', 'T').substring(0, 16);
    }
    
    console.log('Waktu value after processing:', waktuValue);
  }
  
  let html=`
   <div class="form-group row mb-2">
     <label class="col-sm-3 col-form-label">Waktu Gangguan<span class="text-danger">*</span></label>
     <div class="col-sm-9">
       <input type="datetime-local" name="waktu_gangguan" class="form-control" data-required="true"
              value="${waktuValue}">
     </div>
   </div>`;

  if(jenis==='gangguan_peralatan'){
     peralatan.forEach((p,i)=>{
         // Cari gangguan yang sesuai dengan peralatan ini
         const g = gangguanPeralatan.find(gp => gp.peralatan_id == p.peralatan?.id) ?? {};
         const nama = p.peralatan?.nama ?? '-';
         const id   = p.peralatan?.id;
         
         console.log(`Peralatan ${i+1}:`, {nama, id, gangguan: g});
         
         html+=`
         <hr>
         <div class="mb-2"><strong>Peralatan ${i+1}: <span class="badge bg-primary">${nama}</span></strong></div>
         <input type="hidden" name="gangguan[${i}][id]" value="${id}">
         <div class="form-group row mb-2">
           <label class="col-sm-3 col-form-label">Kondisi Gangguan<span class="text-danger">*</span></label>
           <div class="col-sm-9">
             <select name="gangguan[${i}][kondisi]" class="form-control" data-required="true">
               <option value="">- Pilih -</option>
               ${Object.entries(kondisiGangguan).map(([k,v])=>{
                   const sel = (g.kondisi == v) ? 'selected' : '';
                   return `<option value="${v}" ${sel}>${k.replace('_',' ').toUpperCase()}</option>`;
               }).join('')}
             </select>
           </div>
         </div>
         <div class="form-group row mb-2">
           <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
           <div class="col-sm-9">
             <textarea name="gangguan[${i}][deskripsi]" class="form-control" rows="3">${g.deskripsi ?? ''}</textarea>
           </div>
         </div>`;
     });
  }else if(jenis === 'gangguan_non_peralatan'){
     html+=`
       <div class="form-group row mb-2">
         <label class="col-sm-3 col-form-label">Deskripsi Gangguan</label>
         <div class="col-sm-9">
           <textarea name="deskripsi_gangguan" class="form-control" rows="3">${gangguanNon?.deskripsi ?? ''}</textarea>
         </div>
       </div>`;
  }else{
     card.classList.add('d-none');
  }
  cont.innerHTML=html;
}

/* ---------- DOM READY ---------- */
document.addEventListener('DOMContentLoaded', ()=>{
  const form   = document.getElementById('step2-form');
  const select = document.getElementById('jenis_laporan');

  // Build form jika ada jenis laporan awal
  if(jenisLaporanAwal) {
    console.log('Building form with jenis:', jenisLaporanAwal);
    buildGangguanForm(jenisLaporanAwal);
  }
  
  select.addEventListener('change', e => buildGangguanForm(e.target.value));

  form.addEventListener('submit', e => {
    e.preventDefault();

    /* bersihkan status lama */
    form.querySelectorAll('.is-invalid, .is-valid')
        .forEach(el => el.classList.remove('is-invalid','is-valid'));
    form.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());

    let hasError = false;

    /* validasi jenis laporan */
    if(!select.value){
      markInvalid(select,'Jenis laporan wajib dipilih');
      hasError = true;
    } else {
      select.classList.add('is-valid');
    }

    /* validasi semua field data-required */
    form.querySelectorAll('[data-required="true"]').forEach(el=>{
      if(!el.value){
        const label = el.closest('.form-group')?.querySelector('label')?.innerText.replace('*','').trim()
                     || 'Field ini';
        markInvalid(el, `${label} wajib diisi`);
        hasError = true;
      } else {
        el.classList.add('is-valid');
      }
    });

    if(!hasError) form.submit();
  });
});
</script>
@endpush