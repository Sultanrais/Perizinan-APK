@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistik Kartu -->
    <div class="row fade-in">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Perizinan</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalPerizinan }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">Total keseluruhan</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Pending</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalPending }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">Menunggu persetujuan</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Disetujui</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalApproved }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">Perizinan disetujui</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Ditolak</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalRejected }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">Perizinan ditolak</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Perizinan -->
        <div class="col-xl-8 mb-4">
            <div class="card slide-in">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Statistik Perizinan</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="perizinanChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik per Kategori -->
        <div class="col-xl-4 mb-4">
            <div class="card slide-in">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Kategori Perizinan</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($kategoriDistribution as $stat)
                    <div class="progress-wrapper">
                        <div class="progress-info">
                            <div class="progress-label">
                                <span>{{ $stat->kategori }}</span>
                            </div>
                            <div class="progress-percentage">
                                <span>{{ $stat->total }}</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 aria-valuenow="{{ ($stat->total / $totalPerizinan) * 100 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100" 
                                 style="width: {{ ($stat->total / $totalPerizinan) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center mb-0">Belum ada data kategori</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Perizinan Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card slide-in">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Perizinan Terbaru</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('perizinan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Nomor</th>
                                <th scope="col">Pemohon</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Status</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestPerizinan as $perizinan)
                            <tr>
                                <td>{{ $perizinan->nomor }}</td>
                                <td>{{ $perizinan->user->name }}</td>
                                <td>{{ $perizinan->kategori }}</td>
                                <td>
                                    <span class="badge badge-dot mr-4">
                                        @if($perizinan->status == 'PENDING')
                                        <i class="bg-warning"></i>
                                        <span class="status">Pending</span>
                                        @elseif($perizinan->status == 'APPROVED')
                                        <i class="bg-success"></i>
                                        <span class="status">Disetujui</span>
                                        @elseif($perizinan->status == 'REJECTED')
                                        <i class="bg-danger"></i>
                                        <span class="status">Ditolak</span>
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $perizinan->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('perizinan.show', $perizinan->id) }}" 
                                       class="btn btn-sm btn-primary">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data perizinan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-stats .icon {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-stats .icon i {
    font-size: 1.5rem;
}
.progress-wrapper {
    margin-bottom: 1.5rem;
}
.progress-wrapper:last-child {
    margin-bottom: 0;
}
.progress-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}
.progress {
    height: 8px;
    margin-bottom: 1rem;
    overflow: hidden;
    border-radius: 0.25rem;
    background-color: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Line Chart
    var ctx = document.getElementById('perizinanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Perizinan',
                data: @json($chartData),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Pie Chart
    var ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [
                    {{ $statusDistribution['Pending'] }},
                    {{ $statusDistribution['Disetujui'] }},
                    {{ $statusDistribution['Ditolak'] }}
                ],
                backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b'],
                hoverBackgroundColor: ['#f4b619', '#17a673', '#e02d1b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
