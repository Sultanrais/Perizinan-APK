@extends('layouts.app')

@section('title', 'Detail Perizinan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('perizinan.index') }}">Daftar Perizinan</a></li>
<li class="breadcrumb-item active">Detail Perizinan</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Status Card -->
            <div class="card fade-in mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-0">Detail Perizinan #{{ $perizinan->id }}</h3>
                            <p class="text-sm mb-0">Diajukan pada {{ $perizinan->created_at->format('d F Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block me-3">
                                <span class="text-sm d-block mb-1">Status Perizinan</span>
                                @if($perizinan->status == 'PENDING')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($perizinan->status == 'APPROVED')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </div>
                            <div class="btn-group">
                                @if(auth()->user()->hasRole('admin'))
                                    @if($perizinan->status == 'PENDING')
                                        <form action="{{ route('perizinan.approve', $perizinan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui perizinan ini?')">
                                                <i class="fas fa-check me-2"></i>Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('perizinan.reject', $perizinan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak perizinan ini?')">
                                                <i class="fas fa-times me-2"></i>Tolak
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('perizinan.edit', $perizinan->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                    <form action="{{ route('perizinan.destroy', $perizinan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus perizinan ini? Semua data dan dokumen terkait akan dihapus secara permanen.')">
                                            <i class="fas fa-trash me-2"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Informasi Pemohon -->
                <div class="col-md-6 mb-4">
                    <div class="card slide-in">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pemohon</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200" class="text-muted">Nama Lengkap</td>
                                    <td>: {{ $perizinan->nama_pemohon }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK</td>
                                    <td>: {{ $perizinan->nik }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat</td>
                                    <td>: {{ $perizinan->alamat }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informasi Usaha -->
                <div class="col-md-6 mb-4">
                    <div class="card slide-in">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-store me-2"></i>Informasi Usaha</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200" class="text-muted">Jenis Usaha</td>
                                    <td>: {{ $perizinan->jenis_usaha }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Usaha</td>
                                    <td>: {{ $perizinan->nama_usaha }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat Usaha</td>
                                    <td>: {{ $perizinan->alamat_usaha }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="card fade-in mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($perizinan->dokumen as $dokumen)
                        <div class="col-md-3 mb-4">
                            <div class="document-card p-3 border rounded text-center">
                                @if(in_array(strtolower($dokumen->ekstensi), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ asset('storage/' . $dokumen->path) }}" class="img-fluid mb-3 rounded" alt="{{ $dokumen->jenis_dokumen }}">
                                @else
                                    <i class="fas fa-file-pdf text-danger fa-3x mb-3"></i>
                                @endif
                                <h6 class="text-uppercase mb-3">{{ ucwords(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ asset('storage/' . $dokumen->path) }}" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">
                                        <i class="fas fa-eye me-1"></i>Lihat
                                    </a>
                                    <a href="{{ route('perizinan.download', ['perizinan' => $perizinan->id, 'dokumen' => $dokumen->id]) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Riwayat Status -->
            <div class="card fade-in">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($perizinan->trackings as $track)
                        <div class="timeline-item">
                            <div class="timeline-marker 
                                @if($track->status == 'PENDING') bg-warning
                                @elseif($track->status == 'APPROVED') bg-success
                                @else bg-danger @endif">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">{{ $track->keterangan }}</h6>
                                <small class="text-muted">
                                    {{ $track->created_at->format('d F Y H:i') }} oleh {{ $track->user->name }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .document-card {
        height: 100%;
        background: #fff;
        transition: all 0.3s ease;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .document-card img {
        max-height: 120px;
        object-fit: cover;
        border-radius: 4px;
    }
    .document-card .fa-file-pdf {
        color: #dc3545;
    }
    .document-card h6 {
        color: #495057;
        font-weight: 600;
    }
    .document-card .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
    .document-card .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .document-card .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: #fff;
    }
    .document-card .btn i {
        font-size: 0.875rem;
    }
    .timeline {
        position: relative;
        padding: 1rem 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 1rem;
        height: 100%;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        padding-left: 3rem;
        padding-bottom: 1.5rem;
    }
    .timeline-marker {
        position: absolute;
        left: 0.65rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    .timeline-content {
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-radius: 0.375rem;
    }
    .badge {
        padding: 0.5em 1em;
        font-size: 0.875rem;
        text-transform: uppercase;
    }
    .btn-group .btn {
        margin: 0 0.25rem;
    }
</style>
@endpush
