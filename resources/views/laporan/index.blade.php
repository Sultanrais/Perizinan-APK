@extends('layouts.app')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Laporan Perizinan</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Statistik -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending</span>
                        <span class="info-box-number">{{ $totalPending }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Disetujui</span>
                        <span class="info-box-number">{{ $totalDisetujui }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ditolak</span>
                        <span class="info-box-number">{{ $totalDitolak }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Perizinan -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Perizinan</h3>
                <div class="card-tools">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nomor Izin</th>
                                <th>Nama Pemohon</th>
                                <th>NIK</th>
                                <th>Jenis Usaha</th>
                                <th>Status</th>
                                <th>Tanggal Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perizinans as $index => $perizinan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $perizinan->nomor_izin }}</td>
                                <td>{{ $perizinan->nama_pemohon }}</td>
                                <td>{{ $perizinan->nik }}</td>
                                <td>{{ $perizinan->jenis_usaha }}</td>
                                <td>
                                    @if($perizinan->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($perizinan->status == 'disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $perizinan->tanggal_pengajuan ? \Carbon\Carbon::parse($perizinan->tanggal_pengajuan)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style type="text/css" media="print">
    @page {
        size: landscape;
    }
    .no-print {
        display: none !important;
    }
    .main-sidebar,
    .main-header,
    .main-footer {
        display: none !important;
    }
    .content-wrapper {
        margin-left: 0 !important;
        padding-top: 0 !important;
    }
</style>
@endpush
