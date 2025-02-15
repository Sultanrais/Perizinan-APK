@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('breadcrumb')
<li class="breadcrumb-item active">Profil Pengguna</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile Card -->
            <div class="card card-profile">
                <div class="card-header text-center border-0 pt-0 pt-lg-2 pb-4 pb-lg-3">
                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-sm btn-info mb-0 d-none d-lg-block">Pesan</a>
                        <a href="#" class="btn btn-sm btn-info mb-0 d-block d-lg-none">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-dark float-right mb-0 d-none d-lg-block">Edit Profil</a>
                        <a href="#" class="btn btn-sm btn-dark float-right mb-0 d-block d-lg-none">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col">
                            <div class="d-flex justify-content-center">
                                <div class="profile-image-wrapper">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile" class="rounded-circle">
                                    @else
                                        <div class="profile-initial rounded-circle">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <label for="avatar" class="profile-image-edit" data-bs-toggle="tooltip" title="Ubah foto profil">
                                        <i class="fas fa-camera"></i>
                                        <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h5>{{ $user->name }}</h5>
                        <div class="h6 font-weight-300">
                            <i class="fas fa-envelope mr-2"></i>{{ $user->email }}
                        </div>
                        <div class="h6 mt-2">
                            <i class="fas fa-user-shield mr-2"></i>{{ $user->roles->pluck('name')->implode(', ') }}
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col">
                            <div class="card-profile-stats d-flex justify-content-center">
                                <div class="text-center mx-3">
                                    <span class="heading">{{ $user->perizinan->count() }}</span>
                                    <span class="description">Perizinan</span>
                                </div>
                                <div class="text-center mx-3">
                                    <span class="heading">{{ $user->perizinan->where('status', 'APPROVED')->count() }}</span>
                                    <span class="description">Disetujui</span>
                                </div>
                                <div class="text-center mx-3">
                                    <span class="heading">{{ $user->perizinan->where('status', 'PENDING')->count() }}</span>
                                    <span class="description">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Keamanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Verifikasi 2 Langkah</h6>
                            <small class="text-muted">Tambahkan lapisan keamanan ekstra</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="two_factor" 
                                   {{ $user->two_factor_enabled ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Notifikasi Login</h6>
                            <small class="text-muted">Dapatkan email saat ada login baru</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="login_notification"
                                   {{ $user->login_notification ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <!-- Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informasi Profil
                        <button class="btn btn-sm btn-primary float-end" onclick="enableEdit()">
                            <i class="fas fa-edit me-2"></i>Edit
                        </button>
                    </h5>
                </div>
                <div class="card-body">
                    <form id="profileForm" action="{{ route('profile.update') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="{{ $user->name }}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="{{ $user->email }}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="{{ $user->phone }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="birth_date" 
                                           value="{{ $user->birth_date }}" disabled>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Alamat</label>
                                    <textarea class="form-control" name="address" rows="3" 
                                              disabled>{{ $user->address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4" style="display: none;" id="formButtons">
                            <button type="button" class="btn btn-secondary me-2" onclick="cancelEdit()">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terakhir</h5>
                </div>
                <div class="card-body p-0">
                    <div class="timeline timeline-one-side">
                        @forelse($user->activityLogs()->latest()->take(5)->get() as $log)
                            <div class="timeline-block">
                                <span class="timeline-step bg-{{ $log->getActivityColor() }}">
                                    <i class="fas {{ $log->getActivityIcon() }}"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $log->description }}</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="mb-0">Belum ada aktivitas</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-profile .profile-image-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin-top: -60px;
    }
    .card-profile .profile-image-wrapper img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .card-profile .profile-initial {
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        background: #5e72e4;
        color: #fff;
        border: 3px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .profile-image-edit {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 36px;
        height: 36px;
        background: #5e72e4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .profile-image-edit:hover {
        background: #324cdd;
    }
    .card-profile-stats {
        padding: 1rem 0;
    }
    .card-profile-stats > div {
        margin-right: 1rem;
        padding: 0.875rem;
    }
    .card-profile-stats > div:last-child {
        margin-right: 0;
    }
    .card-profile-stats .heading {
        font-size: 1.1rem;
        font-weight: bold;
        display: block;
    }
    .card-profile-stats .description {
        font-size: 0.875rem;
        color: #8898aa;
    }
    .timeline {
        margin: 0;
        padding: 1.5rem;
        border-radius: .375rem;
        position: relative;
    }
    .timeline.timeline-one-side:before {
        left: 1rem;
    }
    .timeline:before {
        content: "";
        position: absolute;
        top: 0;
        left: 1rem;
        height: 100%;
        border-right: 2px solid #e9ecef;
    }
    .timeline-block {
        margin: 2rem 0;
        display: flex;
        align-items: flex-start;
    }
    .timeline-step {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        position: relative;
        z-index: 1;
    }
    .timeline-step i {
        color: #fff;
    }
    .timeline-content {
        flex: 1;
        position: relative;
        padding-left: 0.5rem;
    }
    .form-switch .form-check-input {
        width: 2.5em;
    }
    .form-switch .form-check-input:checked {
        background-color: #2dce89;
        border-color: #2dce89;
    }
</style>
@endpush

@push('scripts')
<script>
function enableEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea');
    const buttons = document.getElementById('formButtons');
    
    inputs.forEach(input => {
        input.disabled = false;
    });
    
    buttons.style.display = 'block';
}

function cancelEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea');
    const buttons = document.getElementById('formButtons');
    
    inputs.forEach(input => {
        input.disabled = true;
        input.value = input.defaultValue;
    });
    
    buttons.style.display = 'none';
}

// Avatar upload preview
document.getElementById('avatar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.profile-image-wrapper img') || 
                       document.createElement('img');
            img.src = e.target.result;
            img.classList.add('rounded-circle');
            
            const initial = document.querySelector('.profile-initial');
            if (initial) initial.remove();
            
            document.querySelector('.profile-image-wrapper').appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Toggle switches
document.getElementById('two_factor').addEventListener('change', function(e) {
    // Handle two factor authentication toggle
    fetch('{{ route("profile.toggle-2fa") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            enabled: e.target.checked
        })
    });
});

document.getElementById('login_notification').addEventListener('change', function(e) {
    // Handle login notification toggle
    fetch('{{ route("profile.toggle-notification") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            enabled: e.target.checked
        })
    });
});

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
@endpush
