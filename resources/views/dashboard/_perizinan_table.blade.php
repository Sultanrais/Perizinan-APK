<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor</th>
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
            <td>{{ $index + 1 }}</td>
            <td>{{ $izin->nomor }}</td>
            <td>{{ $izin->user->name }}</td>
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
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
