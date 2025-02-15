@extends('layouts.app')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Notifikasi</h1>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Notifikasi</h3>
                <div class="card-tools">
                    <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group">
                    @forelse($notifications as $notification)
                    <div class="list-group-item {{ $notification->is_read ? '' : 'list-group-item-light' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $notification->title }}</h5>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                    @if($notification->is_read)
                                        <span class="text-success">
                                            <i class="fas fa-check"></i> Dibaca {{ $notification->read_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div class="btn-group">
                                @if(!$notification->is_read)
                                <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item">
                        <p class="mb-0 text-center text-muted">Tidak ada notifikasi</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update badge count setiap 30 detik
    setInterval(function() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            });
    }, 30000);
});
</script>
@endpush
