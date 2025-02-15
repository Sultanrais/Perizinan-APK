@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Laporan Bulanan</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <button onclick="exportData('monthly')" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Statistik Perizinan Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $data['month'])->isoFormat('MMMM Y') }}</h3>
                        <div class="card-tools">
                            <form action="{{ route('reports.monthly') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="month" name="month" class="form-control" value="{{ $data['month'] }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $data['total']['all'] }}</h3>
                                        <p>Total Perizinan</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $data['total']['pending'] }}</h3>
                                        <p>Pending</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $data['total']['disetujui'] }}</h3>
                                        <p>Disetujui</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $data['total']['ditolak'] }}</h3>
                                        <p>Ditolak</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pending</th>
                                    <th>Disetujui</th>
                                    <th>Ditolak</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['daily_data'] as $date => $counts)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }}</td>
                                    <td>{{ $counts['pending'] }}</td>
                                    <td>{{ $counts['disetujui'] }}</td>
                                    <td>{{ $counts['ditolak'] }}</td>
                                    <td>{{ array_sum($counts) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function exportData(type) {
    window.location.href = `{{ route('reports.export') }}?type=${type}&month={{ $data['month'] }}`;
}
</script>
@endpush
@endsection
