@extends('layouts.app')

@section('title', 'Laporan Aktivitas')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Laporan Aktivitas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Filter Aktivitas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('laporan.aktivitas') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tipe Aktivitas</label>
                                <select name="type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>Login</option>
                                    <option value="status_changed" {{ request('type') == 'status_changed' ? 'selected' : '' }}>Perubahan Status</option>
                                    <option value="created" {{ request('type') == 'created' ? 'selected' : '' }}>Pembuatan Data</option>
                                    <option value="updated" {{ request('type') == 'updated' ? 'selected' : '' }}>Update Data</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Semua User</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
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
                            <a href="{{ route('laporan.aktivitas') }}" class="btn btn-secondary">
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
                    <h3 class="mb-0">Data Aktivitas</h3>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Tipe</th>
                                <th>Aktivitas</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $index => $activity)
                            <tr>
                                <td>{{ $activities->firstItem() + $index }}</td>
                                <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $activity->user->name }}</td>
                                <td>
                                    <span class="badge badge-dot mr-4">
                                        @if($activity->type == 'login')
                                        <i class="bg-info"></i>
                                        <span class="status">Login</span>
                                        @elseif($activity->type == 'status_changed')
                                        <i class="bg-primary"></i>
                                        <span class="status">Perubahan Status</span>
                                        @elseif($activity->type == 'created')
                                        <i class="bg-success"></i>
                                        <span class="status">Pembuatan Data</span>
                                        @elseif($activity->type == 'updated')
                                        <i class="bg-warning"></i>
                                        <span class="status">Update Data</span>
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $activity->title }}</td>
                                <td>{{ $activity->message }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $activities->links() }}
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
    const type = document.querySelector('select[name="type"]').value;
    const userId = document.querySelector('select[name="user_id"]').value;

    // Create URL with query parameters
    const params = new URLSearchParams({
        start_date: startDate,
        end_date: endDate,
        type: type,
        user_id: userId,
        type: 'aktivitas'
    });

    // Redirect to export URL
    window.location.href = `{{ route('laporan.export') }}?${params.toString()}`;
}
</script>
@endpush
