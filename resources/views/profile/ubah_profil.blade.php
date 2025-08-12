
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ubah Profil</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Ubah Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i> Data Profil Pengguna
                            </h3>
                        </div>
                        
                        <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name', $user->name) }}" required
                                                       placeholder="Masukkan nama lengkap">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email', $user->email) }}" required
                                                       placeholder="Masukkan alamat email">
                                                @error('email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jabatan">Jabatan</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                                                       id="jabatan" name="jabatan" value="{{ old('jabatan', $detail_user->jabatan ?? '') }}"
                                                       placeholder="Masukkan jabatan">
                                                @error('jabatan')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telepon">Telepon</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                                                       id="telepon" name="telepon" value="{{ old('telepon', $detail_user->telepon ?? '') }}" 
                                                       maxlength="15" placeholder="Masukkan nomor telepon">
                                                @error('telepon')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Maksimal 15 karakter</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                </div>
                                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                                          id="alamat" name="alamat" rows="4" 
                                                          placeholder="Masukkan alamat lengkap">{{ old('alamat', $detail_user->alamat ?? '') }}</textarea>
                                                @error('alamat')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Maksimal 500 karakter</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <span class="text-muted"><small><span class="text-danger">*</span> Field wajib diisi</small></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Informasi Profil
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nama</span>
                                    <span class="info-box-number">{{ $user->name }}</span>
                                </div>
                            </div>

                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="far fa-envelope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Email</span>
                                    <span class="info-box-number text-truncate">{{ $user->email }}</span>
                                </div>
                            </div>

                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-user-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Role</span>
                                    <span class="info-box-number">{{ session()->get('role_name') }}</span>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="text-muted">
                                <p><small><strong>Terakhir Diupdate:</strong><br>
                                {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Belum pernah diupdate' }}</small></p>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('profile.password-form') }}" class="btn btn-warning btn-sm btn-block">
                                    <i class="fas fa-key"></i> Ubah Password
                                </a>
                                <a href="{{ route('profile.activity-log') }}" class="btn btn-info btn-sm btn-block">
                                    <i class="fas fa-history"></i> Lihat Log Aktivitas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- /.content -->
</div>

<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation
    $('#profileForm').on('submit', function(e) {
        let name = $('#name').val().trim();
        let email = $('#email').val().trim();
        
        if (!name) {
            e.preventDefault();
            alert('Nama lengkap wajib diisi!');
            $('#name').focus();
            return false;
        }
        
        if (!email) {
            e.preventDefault();
            alert('Email wajib diisi!');
            $('#email').focus();
            return false;
        }

        // Validate email format
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            $('#email').focus();
            return false;
        }

        // Show loading state
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
    });

    // Validate phone number input (only numbers)
    $('#telepon').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Character counter for alamat
    $('#alamat').on('input', function() {
        let maxLength = 500;
        let currentLength = $(this).val().length;
        let remaining = maxLength - currentLength;
        
        if (remaining < 0) {
            $(this).val($(this).val().substring(0, maxLength));
            remaining = 0;
        }
        
        $(this).next('.form-text').html(`Maksimal 500 karakter (tersisa: ${500 - $(this).val().length})`);
    });
});

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua perubahan akan hilang.')) {
        $('#profileForm')[0].reset();
    }
}
</script>
@endsection

