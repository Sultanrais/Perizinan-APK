<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadDokumenModal" tabindex="-1" role="dialog" aria-labelledby="uploadDokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDokumenModalLabel">Upload Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="perizinan_id" value="{{ $perizinan->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis Dokumen</label>
                        <select name="jenis_dokumen" class="form-control" required>
                            <option value="">Pilih Jenis Dokumen</option>
                            <option value="ktp">KTP</option>
                            <option value="npwp">NPWP</option>
                            <option value="surat_permohonan">Surat Permohonan</option>
                            <option value="dokumen_pendukung">Dokumen Pendukung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" id="customFile" required accept=".pdf,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="customFile">Pilih file</label>
                        </div>
                        <small class="form-text text-muted">
                            Format yang didukung: PDF, JPG, JPEG, PNG (Maks. 10MB)
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan dokumen (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Daftar Dokumen -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dokumen</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadDokumenModal">
                <i class="fas fa-upload"></i> Upload Dokumen
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Dokumen</th>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Versi</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinan->dokumen as $index => $dokumen)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ucfirst($dokumen->jenis_dokumen) }}</td>
                        <td>{{ $dokumen->nama_dokumen }}</td>
                        <td>{{ $dokumen->getFormattedSizeAttribute() }}</td>
                        <td>v{{ $dokumen->version }}</td>
                        <td>{{ $dokumen->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group">
                                @if($dokumen->isImage())
                                    <button type="button" class="btn btn-info btn-sm" onclick="previewImage('{{ $dokumen->getFileUrlAttribute() }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                                @if($dokumen->isPDF())
                                    <a href="{{ route('dokumen.preview', $dokumen->id) }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                <a href="{{ route('dokumen.download', $dokumen->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteDokumen({{ $dokumen->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada dokumen</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Image -->
<div class="modal fade" id="previewImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(url) {
    $('#previewImage').attr('src', url);
    $('#previewImageModal').modal('show');
}

function deleteDokumen(id) {
    if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
        $.ajax({
            url: '/dokumen/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat menghapus dokumen');
            }
        });
    }
}

// Update label file input
$(document).on('change', '.custom-file-input', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>
@endpush
