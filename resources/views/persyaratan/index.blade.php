@extends('layouts.app')

@section('content-header')
<h1>Persyaratan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Persyaratan</h3>
        @if(auth()->user()->role === 'admin')
        <div class="card-tools">
            <a href="{{ route('persyaratan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Persyaratan
            </a>
        </div>
        @endif
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Wajib</th>
                        <th>Tipe File</th>
                        <th>Ukuran Maks.</th>
                        @if(auth()->user()->role === 'admin')
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($persyaratans as $persyaratan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $persyaratan->nama }}</td>
                        <td>{{ $persyaratan->deskripsi }}</td>
                        <td>{!! $persyaratan->wajib ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-warning">Tidak</span>' !!}</td>
                        <td>{{ $persyaratan->tipe_file }}</td>
                        <td>{{ number_format($persyaratan->max_size / 1024, 1) }} MB</td>
                        @if(auth()->user()->role === 'admin')
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('persyaratan.edit', $persyaratan) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('persyaratan.destroy', $persyaratan) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus persyaratan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" class="text-center">
                            Tidak ada data persyaratan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
