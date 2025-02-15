@extends('layouts.app')

@section('title', 'Data Perizinan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Perizinan</h6>
            @if(auth()->user()->hasRole('pemohon'))
            <a href="{{ route('perizinan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajukan Perizinan
            </a>
            @endif
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="perizinanTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Usaha</th>
                            <th>Jenis Usaha</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perizinan as $index => $izin)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $izin->nama_usaha }}</td>
                            <td>{{ $izin->jenis_usaha }}</td>
                            <td>{{ $izin->kategori }}</td>
                            <td>
                                <span class="badge badge-{{ $izin->status == 'PENDING' ? 'warning' : ($izin->status == 'APPROVED' ? 'success' : 'danger') }}">
                                    {{ $izin->status }}
                                </span>
                            </td>
                            <td>{{ $izin->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('perizinan.show', $izin->id) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('perizinan.destroy', $izin->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                <div class="d-flex justify-content-end">
                    {{ $perizinan->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge {
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 4px;
}
.badge-warning {
    background-color: #f6c23e;
    color: #fff;
}
.badge-success {
    background-color: #1cc88a;
    color: #fff;
}
.badge-danger {
    background-color: #e74a3b;
    color: #fff;
}
</style>
@endpush
