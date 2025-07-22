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
        <div class="card"><div class="card-body py-2">
          <ul class="step d-flex flex-nowrap">
            <li class="step-item completed"><a href="{{ route('tambah.step1') }}">Pilih Layanan</a></li>
            <li class="step-item completed"><a href="{{ route('tambah.step2.back',['laporan_id'=>$laporan->id]) }}">Input Gangguan</a></li>
            <li class="step-item active"><a href="#">Tindaklanjut</a></li>
            <li class="step-item"><a href="#">Review</a></li>
          </ul>
        </div></div>
      </div>
    </div>

    {{-- Form --}}
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header"><h3 class="card-title">FORM TINDAK LANJUT</h3></div>
          <div class="card-body">

          @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul></div>
          @endif

            <form action="{{ route('tambah.simpanStep3Back') }}" method="POST" id="formStep3">
              @csrf
              <input type="hidden" name="laporan_id"  value="{{ $laporan->id }}">
              <input type="hidden" name="layanan_id"  value="{{ $laporan->layanan_id }}">
              <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis == 1 ? 1 : 0 }}">

              {{-- ================= GANGGUAN PERALATAN ================= --}}
              @if ($laporan->jenis == 1)
                @php $shown = 0; @endphp
                @foreach ($layanan->daftarPeralatanLayanan as $index => $dpl)
                  @continue(!in_array($dpl->peralatan->id,$peralatanGangguanIds))
                  @php $shown++; @endphp
                  @if ($shown > 1)<hr>@endif

                  @php $tl = $tindaklanjutPeralatan[$dpl->peralatan->id][0] ?? null; @endphp
                  <div class="mb-3">
                    <strong>Peralatan {{ $shown }}:
                      <span class="badge bg-primary">{{ $dpl->peralatan->nama }}</span>
                    </strong>
                  </div>

                  {{-- Jenis --}}
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Jenis <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                      <select name="tindaklanjut[{{ $dpl->peralatan->id }}][jenis]"
                              class="form-control tindak-jenis"
                              data-nama="{{ $dpl->peralatan->nama }}">
                        <option value="">- Pilih -</option>
                        @foreach ($jenisTindakLanjut as $label=>$value)
                          <option value="{{ $value?1:0 }}"
                              {{ $tl && $tl->jenis_tindaklanjut==($value?1:0)?'selected':'' }}>
                              {{ ucfirst($label) }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  {{-- Waktu --}}
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                      <input type="datetime-local"
                             name="tindaklanjut[{{ $dpl->peralatan->id }}][waktu]"
                             class="form-control tindak-waktu"
                             data-nama="{{ $dpl->peralatan->nama }}"
                             value="{{ $tl ? \Carbon\Carbon::parse($tl->waktu)->format('Y-m-d\TH:i') : '' }}">
                    </div>
                  </div>

                  {{-- Deskripsi --}}
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                    <div class="col-sm-9">
                      <textarea name="tindaklanjut[{{ $dpl->peralatan->id }}][deskripsi]"
                                class="form-control">{{ $tl->deskripsi ?? '' }}</textarea>
                    </div>
                  </div>

                  {{-- Kondisi --}}
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                      <select name="tindaklanjut[{{ $dpl->peralatan->id }}][kondisi]"
                              class="form-control tindak-kondisi"
                              data-nama="{{ $dpl->peralatan->nama }}">
                        <option value="">- Pilih -</option>
                        @foreach ($kondisiTindaklanjut as $label=>$value)
                          <option value="{{ $value?1:0 }}"
                              {{ $tl && $tl->kondisi==($value?1:0)?'selected':'' }}>
                              {{ ucfirst($label) }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                @endforeach

              {{-- ================= NON‑PERALATAN ================= --}}
              @else
                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Waktu <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="waktu" class="form-control" value="{{ $tindaklanjutNonPeralatan ? \Carbon\Carbon::parse($tindaklanjutNonPeralatan->waktu)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9">
                                        <textarea name="deskripsi" class="form-control">{{ $tindaklanjutNonPeralatan->deskripsi ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kondisi <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="kondisi" class="form-control">
                                            <option value="">- Pilih -</option>
                                            @foreach ($kondisiTindaklanjut as $label => $value)
                                                <option value="{{ $value ? 1 : 0 }}" {{ $tindaklanjutNonPeralatan && $tindaklanjutNonPeralatan->kondisi == ($value ? 1 : 0) ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
              @endif

              <hr>

              {{-- Kondisi Layanan --}}
              <div class="form-group row mt-4 d-none" id="group-kondisi-layanan">
                <label class="col-sm-3 col-form-label">Update Kondisi Layanan <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <select name="kondisi_setelah" class="form-control">
                    <option value="">- Pilih -</option>
                    @foreach ($kondisiSetelah as $label=>$value)
                      <option value="{{ $value?1:0 }}"
                        {{ (int)old('kondisi_setelah',$laporan->kondisi_setelah)==($value?1:0)?'selected':'' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="card-footer">
                <a href="{{ route('tambah.step2.back',['laporan_id'=>$laporan->id]) }}"
                   class="btn btn-success btn-sm">
                   <i class="fas fa-angle-left"></i>&nbsp;Kembali
                </a>
                <button type="submit" class="btn btn-success btn-sm float-right" id="btn-submit">
                   Lanjut&nbsp;<i class="fas fa-angle-right"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
function markInvalid(el,msg){
  el.classList.remove('is-valid'); el.classList.add('is-invalid');
  if(!el.parentNode.querySelector('.invalid-feedback.dynamic')){
     const d=document.createElement('div');
     d.className='invalid-feedback dynamic'; d.innerHTML=msg;
     el.parentNode.appendChild(d);
  }
}
function markValid(el){
  el.classList.remove('is-invalid'); el.classList.add('is-valid');
  const fb=el.parentNode.querySelector('.invalid-feedback.dynamic'); if(fb) fb.remove();
}

/* tampilkan dropdown kondisi_layanan bila semua kondisi = 1 */
function toggleKondisiLayanan(){
  const grp=document.getElementById('group-kondisi-layanan');
  const btn=document.getElementById('btn-submit');
  const selects=[...document.querySelectorAll('.tindak-kondisi')].filter(s=>s.value!=='');
  const allOperasi=selects.length>0 && selects.every(s=>s.value==='1');
  if(allOperasi){ grp.classList.remove('d-none'); }
  else{
    grp.classList.add('d-none');
    grp.querySelector('select').value='';
  }
  btn.disabled = !(allOperasi && grp.querySelector('select').value!=='');
}

document.addEventListener('DOMContentLoaded',()=>{
  const form=document.getElementById('formStep3');
  const btn=document.getElementById('btn-submit');

  document.querySelectorAll('.tindak-kondisi').forEach(sel=>{
    sel.addEventListener('change',toggleKondisiLayanan);
  });
  document.getElementById('group-kondisi-layanan')
          .querySelector('select')
          .addEventListener('change',toggleKondisiLayanan);
  toggleKondisiLayanan();

  form.addEventListener('submit',e=>{
    let valid=true;
    form.querySelectorAll('.is-invalid').forEach(el=>el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback.dynamic').forEach(el=>el.remove());

    @if ($laporan->jenis == 1)
      document.querySelectorAll('.tindak-jenis').forEach(el=>{
          if(el.value===''){ markInvalid(el,'Jenis wajib'); valid=false;} else markValid(el);
      });
      document.querySelectorAll('.tindak-waktu').forEach(el=>{
          if(el.value===''){ markInvalid(el,'Waktu wajib'); valid=false;} else markValid(el);
      });
      document.querySelectorAll('.tindak-kondisi').forEach(el=>{
          if(el.value===''){ markInvalid(el,'Kondisi wajib'); valid=false;} else markValid(el);
      });
    @else
      const waktu = form.querySelector('[name="waktu"]');
            const kondisi = form.querySelector('[name="kondisi"]');

            if (!waktu.value) {
                markInvalid(waktu, 'Waktu wajib diisi');
                valid = false;
            } else markValid(waktu);

            if (!kondisi.value) {
                markInvalid(kondisi, 'Kondisi wajib dipilih');
                valid = false;
            } else markValid(kondisi);
    @endif

    const grp=document.getElementById('group-kondisi-layanan');
    const ks=grp.querySelector('select');
    if(!grp.classList.contains('d-none')){
        if(ks.value===''){ markInvalid(ks,'Kondisi layanan wajib'); valid=false; }
        else markValid(ks);
    }

    if(!valid || btn.disabled){ e.preventDefault(); }
  });
});
</script>
@endpush
