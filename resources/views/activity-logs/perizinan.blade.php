@extends('layouts.app')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Log Aktivitas Perizinan</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('perizinan.index') }}">Perizinan</a></li>
            <li class="breadcrumb-item active">Log Aktivitas</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Info Box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Perizinan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">Nomor Izin</th>
                                <td>{{ $perizinan->nomor_izin }}</td>
                            </tr>
                            <tr>
                                <th>Nama Pemohon</th>
                                <td>{{ $perizinan->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @switch($perizinan->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @break
                                        @case('disetujui')
                                            <span class="badge badge-success">Disetujui</span>
                                            @break
                                        @case('ditolak')
                                            <span class="badge badge-danger">Ditolak</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $perizinan->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline Aktivitas</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($activityLogs as $log)
                    <div class="time-label">
                        <span class="bg-info">{{ $log->created_at->format('d M Y') }}</span>
                    </div>
                    <div>
                        @switch($log->activity_type)
                            @case('status_changed')
                                <i class="fas fa-sync bg-info"></i>
                                @break
                            @case('comment_added')
                                <i class="fas fa-comment bg-success"></i>
                                @break
                            @case('document_uploaded')
                                <i class="fas fa-file-upload bg-primary"></i>
                                @break
                            @default
                                <i class="fas fa-circle bg-secondary"></i>
                        @endswitch

                        <div class="timeline-item">
                            <span class="time">
                                <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}
                            </span>
                            <h3 class="timeline-header">
                                {{ ucfirst($log->activity_type) }}
                                <small class="text-muted">oleh {{ $log->performed_by }}</small>
                            </h3>
                            <div class="timeline-body">
                                {{ $log->description }}
                                @if($log->old_values || $log->new_values)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        @if($log->old_values)
                                            Nilai lama: {{ json_encode($log->old_values) }}
                                        @endif
                                        @if($log->new_values)
                                            <br>Nilai baru: {{ json_encode($log->new_values) }}
                                        @endif
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div>
                        <i class="fas fa-info bg-info"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">
                                Belum ada aktivitas yang tercatat.
                            </div>
                        </div>
                    </div>
                    @endforelse
                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
