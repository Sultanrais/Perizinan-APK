@extends('layouts.app')

@section('title', 'Laporan Perizinan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Laporan Perizinan</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Filter Laporan</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('laporan.perizinan') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoriList as $kategori)
                                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                            {{ $kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('laporan.perizinan') }}" class="btn btn-secondary">
                                <i class="fas fa-sync me-2"></i>Reset
                            </a>
                            <div class="float-end">
                                <button type="button" class="btn btn-primary" onclick="exportData()">
                                    <i class="fas fa-file-csv me-2"></i>Export CSV
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Data Perizinan</h3>
                        </div>
                        <div class="col text-end">
                            <a href="{{ route('perizinan.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Tambah Perizinan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Perizinan</th>
                                <th>Pemohon</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perizinan as $index => $izin)
                            <tr>
                                <td>{{ $perizinan->firstItem() + $index }}</td>
                                <td>{{ $izin->nomor }}</td>
                                <td>{{ $izin->user->name }}</td>
                                <td>{{ $izin->kategori }}</td>
                                <td>
                                    <span class="badge badge-dot mr-4">
                                        @if($izin->status == 'PENDING')
                                        <i class="bg-warning"></i>
                                        <span class="status">Pending</span>
                                        @elseif($izin->status == 'APPROVED')
                                        <i class="bg-success"></i>
                                        <span class="status">Disetujui</span>
                                        @elseif($izin->status == 'REJECTED')
                                        <i class="bg-danger"></i>
                                        <span class="status">Ditolak</span>
                                        @endif
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
                <div class="card-footer py-3">
                    <div class="d-flex justify-content-end">
                        {{ $perizinan->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge-dot {
    font-size: 0.875rem;
    padding-left: 0;
    padding-right: 0;
    text-transform: none;
    background: transparent;
}
.badge-dot i {
    display: inline-block;
    vertical-align: middle;
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
    margin-right: 0.5rem;
}
.table thead th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    padding: 0.75rem;
    background-color: #f6f9fc;
}
</style>
@endpush

@push('scripts')
<script>
function exportData() {
    // Get form data
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const status = document.querySelector('select[name="status"]').value;
    const kategori = document.querySelector('select[name="kategori"]').value;

    // Create URL with query parameters
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate,
        status: status,
        kategori: kategori,
        type: 'perizinan'
    });

    // Redirect to export URL
    window.location.href = `{{ route('laporan.export') }}?${params.toString()}`;
}
</script>
@endpush
