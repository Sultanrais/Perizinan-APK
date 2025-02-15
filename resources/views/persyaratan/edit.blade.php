@extends('layouts.app')

@section('content-header')
<h1>Edit Persyaratan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Persyaratan</h3>
    </div>
    <form action="{{ route('persyaratan.update', $persyaratan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="nama">Nama Persyaratan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                       id="nama" name="nama" value="{{ old('nama', $persyaratan->nama) }}" required>
                @error('nama')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                          id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $persyaratan->deskripsi) }}</textarea>
                @error('deskripsi')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="wajib" name="wajib" value="1" 
                           {{ old('wajib', $persyaratan->wajib) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="wajib">Wajib</label>
                </div>
                @error('wajib')
                <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipe_file">Tipe File yang Diizinkan <span class="text-danger">*</span></label>
                <select class="form-control @error('tipe_file') is-invalid @enderror" 
                        id="tipe_file" name="tipe_file" required>
                    <option value="">Pilih Tipe File</option>
                    <option value="pdf" {{ old('tipe_file', $persyaratan->tipe_file) === 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="image" {{ old('tipe_file', $persyaratan->tipe_file) === 'image' ? 'selected' : '' }}>Gambar</option>
                    <option value="pdf,image" {{ old('tipe_file', $persyaratan->tipe_file) === 'pdf,image' ? 'selected' : '' }}>PDF & Gambar</option>
                </select>
                @error('tipe_file')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="max_size">Ukuran Maksimal (KB) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('max_size') is-invalid @enderror" 
                       id="max_size" name="max_size" value="{{ old('max_size', $persyaratan->max_size) }}" 
                       min="1" max="10240" required>
                <small class="form-text text-muted">1 MB = 1024 KB. Maksimal 10 MB (10240 KB)</small>
                @error('max_size')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('persyaratan.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection
