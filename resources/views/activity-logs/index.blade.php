@extends('layouts.app')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Log Aktivitas</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Log Aktivitas</h3>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipe Aktivitas</label>
                            <select name="activity_type" class="form-control">
                                <option value="">Semua Tipe</option>
                                <option value="status_changed" {{ request('activity_type') == 'status_changed' ? 'selected' : '' }}>Perubahan Status</option>
                                <option value="comment_added" {{ request('activity_type') == 'comment_added' ? 'selected' : '' }}>Komentar Ditambahkan</option>
                                <option value="document_uploaded" {{ request('activity_type') == 'document_uploaded' ? 'selected' : '' }}>Dokumen Diupload</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button type="button" class="btn btn-success" id="exportBtn">
                                    <i class="fas fa-file-excel"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Log Aktivitas</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nomor Izin</th>
                                <th>Tipe Aktivitas</th>
                                <th>Deskripsi</th>
                                <th>Dilakukan Oleh</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('perizinan.show', $log->perizinan_id) }}">
                                        {{ $log->perizinan->nomor_izin }}
                                    </a>
                                </td>
                                <td>
                                    @switch($log->activity_type)
                                        @case('status_changed')
                                            <span class="badge badge-info">Perubahan Status</span>
                                            @break
                                        @case('comment_added')
                                            <span class="badge badge-success">Komentar</span>
                                            @break
                                        @case('document_uploaded')
                                            <span class="badge badge-primary">Upload Dokumen</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $log->activity_type }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->user ? $log->user->name : $log->performed_by }}</td>
                                <td>{{ $log->ip_address }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data log aktivitas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($activityLogs->hasPages())
            <div class="card-footer">
                {{ $activityLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle export
    document.getElementById('exportBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('filterForm'));
        const queryString = new URLSearchParams(formData).toString();
        
        fetch(`{{ route('activity-logs.export') }}?${queryString}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Convert data to CSV or handle export
                    const csvContent = convertToCSV(data.data);
                    downloadCSV(csvContent, 'activity_logs.csv');
                }
            });
    });

    function convertToCSV(data) {
        const headers = Object.keys(data[0]);
        const rows = data.map(row => headers.map(header => row[header]));
        return [headers, ...rows].map(row => row.join(',')).join('\n');
    }

    function downloadCSV(content, filename) {
        const blob = new Blob([content], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.click();
    }
});
</script>
@endpush
