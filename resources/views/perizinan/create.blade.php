@extends('layouts.app')

@section('title', isset($perizinan) ? 'Edit Perizinan' : 'Ajukan Perizinan Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('perizinan.index') }}">Daftar Perizinan</a></li>
<li class="breadcrumb-item active">{{ isset($perizinan) ? 'Edit Perizinan' : 'Ajukan Perizinan' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <form action="{{ isset($perizinan) ? route('perizinan.update', $perizinan->id) : route('perizinan.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  id="perizinanForm"
                  class="needs-validation"
                  novalidate>
                @csrf
                @if(isset($perizinan))
                    @method('PUT')
                @endif

                <!-- Progress Bar -->
                <div class="card fade-in mb-4">
                    <div class="card-body">
                        <div class="progress-wizard">
                            <div class="progress-wizard-bar"></div>
                            <div class="progress-step active" data-step="1">
                                <div class="progress-title">Data Pemohon</div>
                            </div>
                            <div class="progress-step" data-step="2">
                                <div class="progress-title">Data Usaha</div>
                            </div>
                            <div class="progress-step" data-step="3">
                                <div class="progress-title">Upload Dokumen</div>
                            </div>
                            <div class="progress-step" data-step="4">
                                <div class="progress-title">Konfirmasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Steps -->
                <div class="card fade-in">
                    <div class="card-body">
                        <!-- Step 1: Data Pemohon -->
                        <div class="form-step active" data-step="1">
                            <h5 class="mb-4"><i class="fas fa-user me-2"></i>Data Pemohon</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="nama_pemohon" 
                                               class="form-control @error('nama_pemohon') is-invalid @enderror" 
                                               value="{{ old('nama_pemohon', $perizinan->nama_pemohon ?? '') }}"
                                               required>
                                        @error('nama_pemohon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="nik" 
                                               class="form-control @error('nik') is-invalid @enderror" 
                                               value="{{ old('nik', $perizinan->nik ?? '') }}"
                                               pattern="[0-9]{16}"
                                               required>
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Masukkan 16 digit NIK</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="alamat" 
                                                  class="form-control @error('alamat') is-invalid @enderror" 
                                                  rows="3"
                                                  required>{{ old('alamat', $perizinan->alamat ?? '') }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Data Usaha -->
                        <div class="form-step" data-step="2">
                            <h5 class="mb-4"><i class="fas fa-store me-2"></i>Data Usaha</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Usaha <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="nama_usaha" 
                                               class="form-control @error('nama_usaha') is-invalid @enderror"
                                               value="{{ old('nama_usaha', $perizinan->nama_usaha ?? '') }}"
                                               required>
                                        @error('nama_usaha')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Jenis Usaha <span class="text-danger">*</span></label>
                                        <select name="jenis_usaha" 
                                                class="form-select @error('jenis_usaha') is-invalid @enderror"
                                                required>
                                            <option value="">Pilih Jenis Usaha</option>
                                            @foreach($jenisUsaha as $jenis)
                                                <option value="{{ $jenis }}" 
                                                    {{ old('jenis_usaha', $perizinan->jenis_usaha ?? '') == $jenis ? 'selected' : '' }}>
                                                    {{ $jenis }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jenis_usaha')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Alamat Usaha <span class="text-danger">*</span></label>
                                        <textarea name="alamat_usaha" 
                                                  class="form-control @error('alamat_usaha') is-invalid @enderror"
                                                  rows="3"
                                                  required>{{ old('alamat_usaha', $perizinan->alamat_usaha ?? '') }}</textarea>
                                        @error('alamat_usaha')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Kategori Perizinan <span class="text-danger">*</span></label>
                                        <select name="kategori" 
                                                class="form-select @error('kategori') is-invalid @enderror"
                                                required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="SIUP" {{ old('kategori', $perizinan->kategori ?? '') == 'SIUP' ? 'selected' : '' }}>SIUP</option>
                                            <option value="TDP" {{ old('kategori', $perizinan->kategori ?? '') == 'TDP' ? 'selected' : '' }}>TDP</option>
                                            <option value="SITU" {{ old('kategori', $perizinan->kategori ?? '') == 'SITU' ? 'selected' : '' }}>SITU</option>
                                            <option value="IMB" {{ old('kategori', $perizinan->kategori ?? '') == 'IMB' ? 'selected' : '' }}>IMB</option>
                                            <option value="HO" {{ old('kategori', $perizinan->kategori ?? '') == 'HO' ? 'selected' : '' }}>HO</option>
                                        </select>
                                        @error('kategori')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Upload Dokumen -->
                        <div class="form-step" data-step="3">
                            <h5 class="mb-4"><i class="fas fa-file-upload me-2"></i>Upload Dokumen</h5>
                            <div class="row g-4">
                                @foreach($requiredDokumen as $jenis => $keterangan)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ $keterangan }} <span class="text-danger">*</span></label>
                                        <input type="file" 
                                               name="dokumen[{{ $jenis }}]" 
                                               class="form-control @error('dokumen.'.$jenis) is-invalid @enderror"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               required>
                                        @error('dokumen.'.$jenis)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Format: PDF, JPG, PNG (max. 2MB)</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 4: Konfirmasi -->
                        <div class="form-step" data-step="4">
                            <h5 class="mb-4"><i class="fas fa-check-circle me-2"></i>Konfirmasi Pengajuan</h5>
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Perhatian!</h6>
                                <p class="mb-0">Pastikan semua data yang Anda masukkan sudah benar sebelum mengirim pengajuan. 
                                   Setelah pengajuan dikirim, Anda tidak dapat mengubah data kecuali diminta oleh petugas.</p>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="konfirmasi" 
                                       id="konfirmasi"
                                       required>
                                <label class="form-check-label" for="konfirmasi">
                                    Saya menyatakan bahwa data yang saya masukkan adalah benar dan dapat dipertanggungjawabkan
                                </label>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" 
                                    class="btn btn-secondary" 
                                    id="prevBtn" 
                                    style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                            </button>
                            
                            <button type="button" 
                                    class="btn btn-primary" 
                                    id="nextBtn">
                                Selanjutnya<i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            <button type="submit" 
                                    class="btn btn-success" 
                                    id="submitBtn" 
                                    style="display: none;">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-wizard {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 30px;
}

.progress-wizard-bar {
    background-color: #e9ecef;
    height: 3px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    z-index: 0;
}

.progress-step {
    background: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
}

.progress-step.active {
    border-color: #0d6efd;
    color: #0d6efd;
}

.progress-step.complete {
    border-color: #198754;
    background-color: #198754;
    color: white;
}

.progress-title {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 10px;
    white-space: nowrap;
    font-size: 0.875rem;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
}

.fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/perizinan-form.js') }}"></script>
@endpush
