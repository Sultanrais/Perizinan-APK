@extends('layouts.app')

@section('content-header')
<h1>
    Edit Perizinan
</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Perizinan</h3>
    </div>
    <form method="POST" action="{{ route('perizinan.update', $perizinan) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Nomor Izin</label>
                <input type="text" class="form-control" value="{{ $perizinan->nomor_izin }}" disabled>
            </div>

            <div class="form-group">
                <label for="nama_pemohon">Nama Pemohon</label>
                <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" 
                    id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon', $perizinan->nama_pemohon) }}" required>
                @error('nama_pemohon')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" value="{{ $perizinan->nik }}" disabled>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                    id="alamat" name="alamat" rows="3" required>{{ old('alamat', $perizinan->alamat) }}</textarea>
                @error('alamat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_usaha">Jenis Usaha</label>
                <select class="form-control @error('jenis_usaha') is-invalid @enderror" id="jenis_usaha" name="jenis_usaha" required>
                    @foreach($jenisUsaha as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis_usaha', $perizinan->jenis_usaha) == $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" class="form-control @error('kategori') is-invalid @enderror" 
                    id="kategori" name="kategori" value="{{ old('kategori', $perizinan->kategori) }}" required>
                @error('kategori')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_usaha">Nama Usaha</label>
                <input type="text" class="form-control @error('nama_usaha') is-invalid @enderror" 
                    id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $perizinan->nama_usaha) }}" required>
                @error('nama_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat_usaha">Alamat Usaha</label>
                <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" 
                    id="alamat_usaha" name="alamat_usaha" rows="3" required>{{ old('alamat_usaha', $perizinan->alamat_usaha) }}</textarea>
                @error('alamat_usaha')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="PENDING" {{ $perizinan->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="APPROVED" {{ $perizinan->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                    <option value="REJECTED" {{ $perizinan->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                    id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $perizinan->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('perizinan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
