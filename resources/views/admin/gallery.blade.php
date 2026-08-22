@extends('layouts.admin')

@section('title', 'Results Gallery Management')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Results Gallery')
@section('page_title', 'Clinical Before & After Results Gallery')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>Before & After Case Studies ({{ $items->total() }})</h3>
            <small class="text-muted">Manage clinical transformation photos, treatment categories, and patient results</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.gallery') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="gallerySearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search cases, treatment..." class="admin-search-input" oninput="filterGalleryLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.gallery') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewGalleryModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 140px; min-width: 130px; max-width: 150px;">Transformation</th>
                    <th style="width: 46%;">Case Title / Treatment</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr id="gallery_row_{{ $item->id }}" class="gallery-data-row" data-search="{{ strtolower($item->title . ' ' . $item->category . ' ' . $item->treatment_name) }}">
                    <td style="width: 140px; padding-right: 0.5rem;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <div style="text-align: center;">
                                <img src="{{ $item->image_before ?: ($item->image ?: '/images/logo.jpeg') }}" alt="Before" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover; border: 1px solid var(--color-border); display: block;">
                                <span style="font-size: 0.65rem; color: var(--color-charcoal-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Before</span>
                            </div>
                            <span style="color: var(--color-gold); font-size: 0.9rem; font-weight: bold;">➝</span>
                            <div style="text-align: center;">
                                <img src="{{ $item->image_after ?: ($item->image ?: '/images/logo.jpeg') }}" alt="After" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover; border: 1.5px solid var(--color-gold); display: block;">
                                <span style="font-size: 0.65rem; color: var(--color-gold); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">After</span>
                            </div>
                        </div>
                    </td>
                    <td style="word-break: break-word; padding-left: 0.5rem;">
                        <strong style="color: var(--color-charcoal); font-size: 0.9rem;">{{ $item->title }}</strong><br>
                        <span style="color: var(--color-crimson); font-size: 0.75rem; font-weight: 500;">{{ $item->treatment_name ?: 'Clinical Case' }}</span>
                    </td>
                    <td><span class="badge badge-gold">{{ $categories->firstWhere('slug', $item->category)->name ?? ucfirst(str_replace('-', ' ', $item->category ?: 'General')) }}</span></td>
                    <td>
                        <span class="status-badge status-{{ $item->status ? 'published' : 'draft' }}">{{ $item->status ? 'Active' : 'Hidden' }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group">
                            <a href="{{ route('gallery.index') }}" target="_blank" class="action-icon-btn btn-view" data-tooltip="Live Gallery Preview" aria-label="View Gallery">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Result Item" aria-label="Edit Item" onclick='openEditGalleryModal(@json($item))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Result Item" aria-label="Delete Item" onclick="deleteGalleryItem({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">No case study results found in database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $items->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Gallery Item -->
<div class="modal-overlay" id="galleryModal">
    <div class="modal-card" style="max-width: 650px;">
        <button class="modal-close" onclick="closeGalleryModal()">&times;</button>
        <div class="modal-header">
            <h3 id="galleryModalTitle">Add Before & After Result</h3>
            <p id="galleryModalSub" class="text-muted" style="font-size: 0.85rem;">Upload patient transformation images to database</p>
        </div>
        <form onsubmit="handleGallerySubmit(event)" id="galleryForm" enctype="multipart/form-data">
            <input type="hidden" id="gal_id" name="id">

            <div class="form-group">
                <label for="gal_title">Case Study Title *</label>
                <input type="text" id="gal_title" name="title" required class="form-control" placeholder="e.g. 4-Session Picosecond Laser Tattoo Clearance">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gal_category">Category *</label>
                    <select id="gal_category" name="category" class="form-control">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="gal_treatment">Treatment Name</label>
                    <input type="text" id="gal_treatment" name="treatment_name" class="form-control" placeholder="e.g. Picosecond Laser (4 Sessions)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="before_file">Before Image</label>
                    <div style="display: flex; gap: 10px; align-items: center; background: var(--color-ivory); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--color-border); margin-bottom: 5px;">
                        <img id="before_preview" src="/images/logo.jpeg" alt="Before" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <div style="flex: 1;">
                            <input type="file" id="before_file" name="before_file" accept="image/*" class="form-control form-control-sm" onchange="previewGalImage(this, 'before_preview')">
                        </div>
                    </div>
                    <input type="hidden" id="gal_image_before" name="image_before">
                </div>

                <div class="form-group">
                    <label for="after_file">After Image</label>
                    <div style="display: flex; gap: 10px; align-items: center; background: var(--color-ivory); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--color-border); margin-bottom: 5px;">
                        <img id="after_preview" src="/images/logo.jpeg" alt="After" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <div style="flex: 1;">
                            <input type="file" id="after_file" name="after_file" accept="image/*" class="form-control form-control-sm" onchange="previewGalImage(this, 'after_preview')">
                        </div>
                    </div>
                    <input type="hidden" id="gal_image_after" name="image_after">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gal_alt_text">Alt Description</label>
                    <input type="text" id="gal_alt_text" name="alt_text" class="form-control" placeholder="Descriptive text for accessibility">
                </div>
                <div class="form-group">
                    <label for="gal_status">Display Status</label>
                    <select id="gal_status" name="status" class="form-control">
                        <option value="1">Active / Visible</option>
                        <option value="0">Hidden / Draft</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeGalleryModal()">Cancel</button>
                <button type="submit" id="saveGalBtn" class="btn btn-gold btn-sm">Save Result</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterGalleryLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.gallery-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    function previewGalImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openNewGalleryModal() {
        document.getElementById('galleryForm').reset();
        document.getElementById('gal_id').value = '';
        document.getElementById('before_preview').src = '/images/logo.jpeg';
        document.getElementById('after_preview').src = '/images/logo.jpeg';
        document.getElementById('galleryModalTitle').innerText = 'Add Before & After Result';
        document.getElementById('galleryModalSub').innerText = 'Upload patient transformation images to database';
        document.getElementById('galleryModal').classList.add('open');
    }

    function openEditGalleryModal(item) {
        document.getElementById('gal_id').value = item.id;
        document.getElementById('gal_title').value = item.title || '';
        document.getElementById('gal_category').value = item.category || 'skin-rejuvenation';
        document.getElementById('gal_treatment').value = item.treatment_name || '';
        document.getElementById('gal_alt_text').value = item.alt_text || '';
        document.getElementById('gal_status').value = item.status ? '1' : '0';
        document.getElementById('gal_image_before').value = item.image_before || '';
        document.getElementById('gal_image_after').value = item.image_after || '';
        document.getElementById('before_preview').src = item.image_before || (item.image || '/images/logo.jpeg');
        document.getElementById('after_preview').src = item.image_after || (item.image || '/images/logo.jpeg');

        document.getElementById('galleryModalTitle').innerText = 'Edit Result: ' + item.title;
        document.getElementById('galleryModalSub').innerText = 'Update transformation images and details in database';
        document.getElementById('galleryModal').classList.add('open');
    }

    function closeGalleryModal() {
        document.getElementById('galleryModal').classList.remove('open');
    }

    async function handleGallerySubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveGalBtn');
        const id = document.getElementById('gal_id').value;
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const formData = new FormData(document.getElementById('galleryForm'));
        if (id) {
            formData.append('_method', 'PUT');
        }
        const url = id ? `/api/v1/admin/gallery/${id}` : '/api/v1/admin/gallery';

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
                closeGalleryModal();
                showToast(id ? 'Result updated in database!' : 'Result saved to database!', 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showToast(data.message || 'Failed to save gallery item', 'error');
            }
        } catch(err) {
            showToast('Network error saving gallery item', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Result';
        }
    }

    function deleteGalleryItem(id, title) {
        confirmDeleteModal('Delete Gallery Item', title, async () => {
            try {
                const res = await fetch(`/api/v1/admin/gallery/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`gallery_row_${id}`);
                    if (row) row.remove();
                    showToast(`"${title}" deleted from database!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete item', 'error');
                }
            } catch(err) {
                showToast('Network error deleting item', 'error');
            }
        });
    }
</script>
@endsection
