<!-- Comments List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Komentar</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Comment Form -->
        <form id="commentForm" class="mb-4">
            @csrf
            <div class="form-group">
                <textarea name="content" class="form-control" rows="3" placeholder="Tulis komentar..."></textarea>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="isPrivate" name="is_private" value="1">
                    <label class="custom-control-label" for="isPrivate">Komentar Internal</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
        </form>

        <!-- Comments List -->
        <div id="commentsList">
            <div class="btn-group mb-3">
                <button type="button" class="btn btn-default" data-filter="all">Semua</button>
                <button type="button" class="btn btn-default" data-filter="public">Publik</button>
                <button type="button" class="btn btn-default" data-filter="internal">Internal</button>
            </div>

            <div class="timeline" id="commentsTimeline">
                <!-- Comments will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perizinanId = '{{ $perizinan->id }}';
    let currentFilter = 'all';

    function loadComments(type = 'all') {
        fetch(`/perizinan/${perizinanId}/comments?type=${type}`)
            .then(response => response.json())
            .then(comments => {
                const timeline = document.getElementById('commentsTimeline');
                timeline.innerHTML = '';

                comments.forEach(comment => {
                    const timelineItem = document.createElement('div');
                    timelineItem.className = 'timeline-item';
                    timelineItem.innerHTML = `
                        <span class="time">
                            <i class="fas fa-clock"></i> ${new Date(comment.created_at).toLocaleString()}
                        </span>
                        <h3 class="timeline-header">
                            ${comment.commented_by}
                            ${comment.is_private ? '<span class="badge badge-warning">Internal</span>' : ''}
                        </h3>
                        <div class="timeline-body">
                            ${comment.content}
                        </div>
                        <div class="timeline-footer">
                            ${comment.can_edit ? `
                                <button class="btn btn-primary btn-sm edit-comment" data-id="${comment.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            ` : ''}
                            ${comment.can_delete ? `
                                <form class="d-inline" method="POST" action="/comments/${comment.id}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            ` : ''}
                        </div>
                    `;
                    timeline.appendChild(timelineItem);
                });
            });
    }

    // Handle comment form submission
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('comment_type', document.getElementById('isPrivate').checked ? 'internal' : 'public');

        fetch(`/perizinan/${perizinanId}/comments`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.reset();
                loadComments(currentFilter);
            }
        });
    });

    // Handle filter buttons
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            loadComments(currentFilter);
        });
    });

    // Initial load
    loadComments();
});
</script>
@endpush
