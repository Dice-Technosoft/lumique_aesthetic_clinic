@extends('layouts.admin')

@section('title', 'Videos & Media Management')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Videos & Media')
@section('page_title', 'Clinical Videos & Procedural Demonstrations')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>Clinical Video Library ({{ $videos->total() }})</h3>
            <small class="text-muted">Manage doctor explanations, treatment walkthroughs, and educational videos</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.videos') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="videoSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search videos, category..." class="admin-search-input" oninput="filterVideosLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.videos') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewVideoModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 44%;">Video & Thumbnail</th>
                    <th style="width: 16%;">Category</th>
                    <th style="width: 15%;">YouTube ID</th>
                    <th style="width: 12%; text-align: center;">Status</th>
                    <th style="width: 13%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($videos as $vid)
                @php
                    $vYtId = $vid->youtube_video_id;
                    if (!$vYtId && $vid->youtube_url) {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid->youtube_url, $m);
                        $vYtId = $m[1] ?? 'dQw4w9WgXcQ';
                    }
                    $vidThumb = (!empty($vid->thumbnail) && !str_contains($vid->thumbnail, 'pexels')) ? $vid->thumbnail : "https://img.youtube.com/vi/{$vYtId}/hqdefault.jpg";
                @endphp
                <tr id="video_row_{{ $vid->id }}" class="video-data-row" data-search="{{ strtolower($vid->title . ' ' . $vid->category . ' ' . $vYtId) }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div style="width: 76px; height: 48px; border-radius: 6px; overflow: hidden; flex-shrink: 0; position: relative; border: 1px solid rgba(197, 160, 89, 0.3); background: #000;">
                                <img src="{{ $vidThumb }}" alt="{{ $vid->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.18); display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 18px; height: 18px; border-radius: 50%; background: var(--color-crimson); display: flex; align-items: center; justify-content: center; color: #fff;">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    </div>
                                </div>
                            </div>
                            <div style="overflow: hidden;">
                                <strong style="display: block; font-size: 0.9rem; color: var(--color-charcoal); line-height: 1.35; margin-bottom: 2px;">{{ $vid->title }}</strong>
                                <a href="{{ $vid->youtube_url }}" target="_blank" style="color: var(--color-crimson); font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <span style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $vid->youtube_url }}</span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-neutral">{{ ucfirst(str_replace('-', ' ', $vid->category ?? 'General')) }}</span></td>
                    <td><code>{{ $vYtId ?: 'Embedded' }}</code></td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $vid->status === 'published' ? 'published' : 'draft' }}">{{ ucfirst($vid->status) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end;">
                            <a href="{{ $vid->youtube_url }}" target="_blank" class="action-icon-btn btn-view" data-tooltip="Watch on YouTube" aria-label="Watch Video">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </a>
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Video" aria-label="Edit Video" onclick='openEditVideoModal(@json($vid))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Video" aria-label="Delete Video" onclick="deleteVideo({{ $vid->id }}, '{{ addslashes($vid->title) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No clinical videos found in database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $videos->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Video -->
<div class="modal-overlay" id="videoModal">
    <div class="modal-card" style="max-width: 600px;">
        <button class="modal-close" onclick="closeVideoModal()">&times;</button>
        <div class="modal-header">
            <h3 id="videoModalTitle">Add Clinical Video</h3>
            <p id="videoModalSub" class="text-muted" style="font-size: 0.85rem;">Save video URL and clinical details to database</p>
        </div>
        <form onsubmit="handleVideoSubmit(event)" id="videoForm" enctype="multipart/form-data">
            <input type="hidden" id="vid_id" name="id">

            <div class="form-group">
                <label for="vid_title">Video Title *</label>
                <input type="text" id="vid_title" name="title" required class="form-control" placeholder="e.g. Medical HydraFacial MD® Procedure Walkthrough">
            </div>

            <div class="form-group">
                <label for="vid_url">YouTube Video URL *</label>
                <input type="url" id="vid_url" name="youtube_url" required class="form-control" placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX" oninput="extractYoutubePreview(this.value)">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vid_category">Category</label>
                    <select id="vid_category" name="category" class="form-control">
                        <option value="skin-treatments">Skin Treatments</option>
                        <option value="laser-procedures">Laser Procedures</option>
                        <option value="hair-restoration">Hair Restoration</option>
                        <option value="patient-stories">Patient Stories</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vid_status">Status</label>
                    <select id="vid_status" name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="vid_thumbnail_file">Custom Thumbnail (Optional)</label>
                <div style="display: flex; gap: 10px; align-items: center; background: var(--color-ivory); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--color-border); margin-bottom: 5px;">
                    <img id="vid_thumb_preview" src="/images/logo.jpeg" alt="Thumbnail Preview" style="width: 76px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                    <div style="flex: 1;">
                        <input type="file" id="vid_thumbnail_file" name="thumbnail_file" accept="image/*" class="form-control form-control-sm" onchange="previewVidThumb(this)">
                    </div>
                </div>
                <input type="hidden" id="vid_thumbnail" name="thumbnail">
            </div>

            <div class="form-group">
                <label for="vid_description">Video Description</label>
                <textarea id="vid_description" name="description" rows="2" class="form-control" placeholder="Brief summary of the clinical procedure shown in video..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeVideoModal()">Cancel</button>
                <button type="submit" id="saveVidBtn" class="btn btn-gold btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterVideosLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.video-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    function extractYoutubePreview(url) {
        const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i);
        if (match && match[1]) {
            const ytThumbUrl = `https://img.youtube.com/vi/${match[1]}/hqdefault.jpg`;
            document.getElementById('vid_thumb_preview').src = ytThumbUrl;
            // If user hasn't chosen custom file, auto set thumbnail
            const fileInput = document.getElementById('vid_thumbnail_file');
            if (!fileInput.files || fileInput.files.length === 0) {
                document.getElementById('vid_thumbnail').value = ytThumbUrl;
            }
        }
    }

    function previewVidThumb(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('vid_thumb_preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openNewVideoModal() {
        document.getElementById('videoForm').reset();
        document.getElementById('vid_id').value = '';
        document.getElementById('vid_thumb_preview').src = '/images/logo.jpeg';
        document.getElementById('videoModalTitle').innerText = 'Add Clinical Video';
        document.getElementById('videoModalSub').innerText = 'Save video URL and clinical details to database';
        document.getElementById('saveVidBtn').innerText = 'Save';
        document.getElementById('videoModal').classList.add('open');
    }

    function openEditVideoModal(vid) {
        const vYtId = vid.youtube_video_id || (vid.youtube_url ? (vid.youtube_url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i) || [])[1] : null);
        const ytThumb = (vid.thumbnail && !vid.thumbnail.includes('pexels')) ? vid.thumbnail : (vYtId ? `https://img.youtube.com/vi/${vYtId}/hqdefault.jpg` : '/images/logo.jpeg');

        document.getElementById('vid_id').value = vid.id;
        document.getElementById('vid_title').value = vid.title || '';
        document.getElementById('vid_url').value = vid.youtube_url || '';
        document.getElementById('vid_category').value = vid.category || 'skin-treatments';
        document.getElementById('vid_status').value = vid.status || 'published';
        document.getElementById('vid_thumbnail').value = vid.thumbnail || '';
        document.getElementById('vid_thumb_preview').src = ytThumb;
        document.getElementById('vid_description').value = vid.description || '';

        document.getElementById('videoModalTitle').innerText = 'Edit Video: ' + vid.title;
        document.getElementById('videoModalSub').innerText = 'Update video details in database';
        document.getElementById('saveVidBtn').innerText = 'Update';
        document.getElementById('videoModal').classList.add('open');
    }

    function closeVideoModal() {
        document.getElementById('videoModal').classList.remove('open');
    }

    async function handleVideoSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveVidBtn');
        const id = document.getElementById('vid_id').value;
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const formData = new FormData(document.getElementById('videoForm'));
        if (id) {
            formData.append('_method', 'PUT');
        }
        const url = id ? `/api/v1/admin/videos/${id}` : '/api/v1/admin/videos';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeVideoModal();
                showToast(id ? 'Video updated in database!' : 'Video saved to database!', 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showToast(data.message || 'Failed to save video', 'error');
            }
        } catch(err) {
            showToast('Network error saving video', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Video';
        }
    }

    function deleteVideo(id, title) {
        confirmDeleteModal('Delete Video', title, async () => {
            try {
                const res = await fetch(`/api/v1/admin/videos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`video_row_${id}`);
                    if (row) row.remove();
                    showToast(`"${title}" deleted from database!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete video', 'error');
                }
            } catch(err) {
                showToast('Network error deleting video', 'error');
            }
        });
    }
</script>
@endsection
