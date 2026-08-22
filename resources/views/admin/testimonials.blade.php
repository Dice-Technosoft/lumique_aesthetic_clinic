@extends('layouts.admin')

@section('title', 'Patient Stories & Reviews - Lumique Clinic Admin')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Patient Stories')
@section('page_title', 'Patient Stories & Client Reviews')

@section('content')
@php
    $colorPalette = [
        ['bg' => '#8B1538', 'text' => '#ffffff'], // Crimson/Burgundy
        ['bg' => '#1A446C', 'text' => '#ffffff'], // Deep Sapphire Navy
        ['bg' => '#1B634B', 'text' => '#ffffff'], // Emerald Forest
        ['bg' => '#B8860B', 'text' => '#ffffff'], // Warm Dark Gold
        ['bg' => '#5E2B7A', 'text' => '#ffffff'], // Royal Amethyst
        ['bg' => '#A04D2D', 'text' => '#ffffff'], // Terracotta Rust
        ['bg' => '#0F6B75', 'text' => '#ffffff'], // Deep Marine Teal
        ['bg' => '#A82442', 'text' => '#ffffff'], // Radiant Ruby
        ['bg' => '#3E4B5E', 'text' => '#ffffff'], // Slate Steel
    ];
@endphp

<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Patient Testimonials ({{ $testimonials->total() }})</h3>
            <small class="text-muted">Manage verified client reviews, star ratings, and real patient feedback</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.testimonials') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="testimonialSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search patient, treatment, city..." class="admin-search-input" oninput="filterTestimonialsLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.testimonials') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewTestimonialModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 22%;">Patient</th>
                    <th style="width: 22%;">Treatment & Location</th>
                    <th style="width: 12%;">Rating</th>
                    <th style="width: 22%;">Review Content</th>
                    <th style="width: 10%; text-align: center;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $item)
                @php
                    $words = preg_split('/\s+/', trim($item->name));
                    $firstL = substr($words[0] ?? 'P', 0, 1);
                    $lastL = isset($words[1]) ? substr($words[count($words) - 1], 0, 1) : '';
                    $initials = strtoupper($firstL . $lastL);
                    if (empty($initials)) { $initials = 'PT'; }
                    
                    $colorIdx = abs(crc32($item->name)) % count($colorPalette);
                    $avatar = $colorPalette[$colorIdx];
                @endphp
                <tr id="testi_row_{{ $item->id }}" class="testimonial-data-row" data-search="{{ strtolower($item->name . ' ' . $item->treatment_taken . ' ' . $item->designation . ' ' . $item->content) }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">
                                {{ $initials }}
                            </div>
                            <div style="overflow: hidden;">
                                <strong style="display: block; font-size: 0.9rem; color: var(--color-charcoal); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->name }}</strong>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $item->source ?? 'Verified Patient' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong style="display: block; font-size: 0.85rem; color: var(--color-burgundy);">{{ $item->treatment_taken ?? 'Clinical Consultation' }}</strong>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $item->designation ?? 'Mumbai, India' }}</small>
                    </td>
                    <td>
                        <div style="color: var(--color-gold); font-size: 0.95rem; line-height: 1; letter-spacing: 1px;">
                            @for($i=0; $i<$item->rating; $i++) ★ @endfor
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $item->rating }} / 5 Stars</small>
                    </td>
                    <td>
                        <p style="font-size: 0.8125rem; line-height: 1.45; color: var(--color-charcoal); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            "{{ $item->content }}"
                        </p>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $item->status ? 'published' : 'draft' }}">
                            {{ $item->status ? 'Active' : 'Draft' }}
                        </span>
                        @if($item->is_featured)
                            <div style="font-size: 0.7rem; color: var(--color-gold); margin-top: 3px; font-weight: 600;">★ Featured</div>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end;">
                            <button class="action-icon-btn btn-edit" data-tooltip="Edit Review" onclick='openEditTestimonialModal(@json($item))' aria-label="Edit">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button class="action-icon-btn btn-delete" data-tooltip="Delete Review" onclick="deleteTestimonial({{ $item->id }}, '{{ addslashes($item->name) }}')" aria-label="Delete">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-testimonials-row">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No patient stories found in database.
                    </td>
                </tr>
                @endforelse
                <tr id="no-live-matches-row" style="display: none;">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No patient stories matching "<span id="liveSearchQuery"></span>".
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Luxury Styled Pagination -->
    <div class="admin-pagination-row">
        {{ $testimonials->links() }}
    </div>
</div>

<!-- Add/Edit Testimonial Modal -->
<div class="modal-overlay" id="testimonialModal">
    <div class="modal-card" style="max-width: 580px;">
        <button type="button" class="modal-close" onclick="closeTestimonialModal()">&times;</button>
        <div class="modal-header">
            <h3 id="testimonialModalTitle">Add Patient Story</h3>
            <p class="text-muted" style="font-size: 0.85rem;">Publish genuine patient experiences, ratings, and feedback</p>
        </div>
        <form id="testimonialForm" onsubmit="handleTestimonialSubmit(event)">
            <input type="hidden" id="testi_id" name="id">

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="testi_name">Patient Name *</label>
                    <input type="text" id="testi_name" name="name" required class="form-control" placeholder="e.g. Priya Nair">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="testi_designation">Designation / Location</label>
                    <input type="text" id="testi_designation" name="designation" class="form-control" placeholder="e.g. Fashion Stylist, Bandra">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="testi_treatment">Treatment Taken *</label>
                    <input type="text" id="testi_treatment" name="treatment_taken" required class="form-control" placeholder="e.g. HydraFacial MD & Carbon Laser">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="testi_rating">Rating (Stars) *</label>
                    <select id="testi_rating" name="rating" required class="form-control">
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="testi_content">Patient Review / Feedback *</label>
                <textarea id="testi_content" name="content" rows="4" required class="form-control" placeholder="Detailed testimonial from the patient regarding their treatment experience..."></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="testi_source">Source Platform (Optional)</label>
                <input type="text" id="testi_source" name="source" class="form-control" placeholder="e.g. Google Review (Verified Patient)">
            </div>

            <div class="form-row" style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: var(--color-bg-light); border-radius: 6px;">
                <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" id="testi_status" name="status" value="1" checked>
                    <span>Active on Website</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" id="testi_is_featured" name="is_featured" value="1">
                    <span>Feature on Homepage</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeTestimonialModal()">Cancel</button>
                <button type="submit" class="btn btn-gold btn-sm" id="saveTestimonialBtn">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Realtime Client Live Search
    function filterTestimonialsLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.testimonial-data-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            if (!query || rowData.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noMatchesRow = document.getElementById('no-live-matches-row');
        if (noMatchesRow) {
            if (visibleCount === 0 && rows.length > 0) {
                document.getElementById('liveSearchQuery').textContent = query;
                noMatchesRow.style.display = '';
            } else {
                noMatchesRow.style.display = 'none';
            }
        }
    }

    function openNewTestimonialModal() {
        document.getElementById('testimonialModalTitle').textContent = 'Add Patient Story';
        document.getElementById('testimonialForm').reset();
        document.getElementById('testi_id').value = '';
        document.getElementById('testi_status').checked = true;
        document.getElementById('testi_is_featured').checked = false;
        document.getElementById('testi_rating').value = '5';
        document.getElementById('testi_source').value = 'Google Review (Verified Patient)';
        document.getElementById('saveTestimonialBtn').textContent = 'Save';
        document.getElementById('testimonialModal').classList.add('open');
    }

    function openEditTestimonialModal(item) {
        document.getElementById('testimonialModalTitle').textContent = 'Edit Patient Story: ' + item.name;
        document.getElementById('testi_id').value = item.id;
        document.getElementById('testi_name').value = item.name || '';
        document.getElementById('testi_designation').value = item.designation || '';
        document.getElementById('testi_treatment').value = item.treatment_taken || '';
        document.getElementById('testi_rating').value = item.rating || '5';
        document.getElementById('testi_content').value = item.content || '';
        document.getElementById('testi_source').value = item.source || '';
        document.getElementById('testi_status').checked = item.status ? true : false;
        document.getElementById('testi_is_featured').checked = item.is_featured ? true : false;
        document.getElementById('saveTestimonialBtn').textContent = 'Update';
        document.getElementById('testimonialModal').classList.add('open');
    }

    function closeTestimonialModal() {
        document.getElementById('testimonialModal').classList.remove('open');
    }

    async function handleTestimonialSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('testi_id').value;
        const btn = document.getElementById('saveTestimonialBtn');
        btn.disabled = true;
        btn.textContent = id ? 'Updating...' : 'Saving...';

        const payload = {
            name: document.getElementById('testi_name').value,
            designation: document.getElementById('testi_designation').value,
            treatment_taken: document.getElementById('testi_treatment').value,
            rating: parseInt(document.getElementById('testi_rating').value, 10),
            content: document.getElementById('testi_content').value,
            source: document.getElementById('testi_source').value,
            status: document.getElementById('testi_status').checked ? 1 : 0,
            is_featured: document.getElementById('testi_is_featured').checked ? 1 : 0,
        };

        const url = id ? `/api/v1/admin/testimonials/${id}` : '/api/v1/admin/testimonials';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeTestimonialModal();
                showToast(id ? 'Patient review updated successfully!' : 'Patient story added successfully!', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Error saving review', 'error');
            }
        } catch(err) {
            console.error(err);
            showToast('Network error saving review', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = id ? 'Update' : 'Save';
        }
    }

    function deleteTestimonial(id, name) {
        confirmDeleteModal('Delete Patient Story', name, async () => {
            try {
                const res = await fetch(`/api/v1/admin/testimonials/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`testi_row_${id}`);
                    if (row) row.remove();
                    showToast(`Review from "${name}" deleted successfully!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete review', 'error');
                }
            } catch(err) {
                showToast('Network error deleting review', 'error');
            }
        });
    }
</script>
@endsection
