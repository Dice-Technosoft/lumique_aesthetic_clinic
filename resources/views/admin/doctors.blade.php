@extends('layouts.admin')

@section('title', 'Specialists & Doctors - Lumique Clinic Admin')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Specialists & Doctors')
@section('page_title', 'Specialists & Medical Doctors')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Medical Specialists ({{ $doctors->total() }})</h3>
            <small class="text-muted">Manage doctor credentials, medical degrees, certifications, clinical experience, and homepage spotlight</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.doctors') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="doctorSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search doctor, degree, role..." class="admin-search-input" oninput="filterDoctorsLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.doctors') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewDoctorModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 23%;">Specialist & Photo</th>
                    <th style="width: 21%;">Role & Department</th>
                    <th style="width: 24%;">Degrees & Certifications</th>
                    <th style="width: 10%; text-align: center;">Experience</th>
                    <th style="width: 10%; text-align: center;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doc)
                <tr id="doc_row_{{ $doc->id }}" class="doctor-data-row" data-search="{{ strtolower($doc->name . ' ' . $doc->designation . ' ' . $doc->qualification . ' ' . $doc->department) }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="table-img-thumb" style="border-radius: 50%; overflow: hidden; width: 44px; height: 44px; flex-shrink: 0; border: 2px solid var(--color-gold);">
                                @if($doc->photo)
                                    <img src="{{ $doc->photo }}" alt="{{ $doc->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; background: rgba(139, 21, 56, 0.1); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                                        {{ strtoupper(substr($doc->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div style="overflow: hidden;">
                                <strong style="display: block; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--color-charcoal);">{{ $doc->name }}</strong>
                                @if($doc->is_lead)
                                    <span class="badge badge-gold" style="font-size: 0.65rem; padding: 1px 6px;">★ Lead Specialist</span>
                                @else
                                    <small class="text-muted" style="font-size: 0.75rem;">Medical Faculty</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong style="display: block; font-size: 0.85rem; color: var(--color-burgundy);">{{ $doc->designation }}</strong>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $doc->department ?? 'Dermatology & Aesthetics' }}</small>
                    </td>
                    <td>
                        <div style="font-size: 0.8rem; color: var(--color-charcoal); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $doc->qualification }}">
                            {{ $doc->qualification }}
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: 600; color: var(--color-gold); font-size: 0.85rem;">{{ $doc->experience_years ? $doc->experience_years . '+ Yrs' : 'Experienced' }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $doc->status ? 'published' : 'draft' }}">
                            {{ $doc->status ? 'Active' : 'Draft' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end;">
                            <button class="action-icon-btn btn-edit" data-tooltip="Edit Specialist" onclick='openEditDoctorModal(@json($doc))' aria-label="Edit">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button class="action-icon-btn btn-delete" data-tooltip="Delete Specialist" onclick="deleteDoctor({{ $doc->id }}, '{{ addslashes($doc->name) }}')" aria-label="Delete">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-doctors-row">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No doctor specialists found in database.
                    </td>
                </tr>
                @endforelse
                <tr id="no-live-matches-row" style="display: none;">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No doctors matching "<span id="liveSearchQuery"></span>".
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Luxury Styled Pagination -->
    <div class="admin-pagination-row">
        {{ $doctors->links() }}
    </div>
</div>

<!-- Add/Edit Doctor Specialist Modal -->
<div class="modal-overlay" id="doctorModal">
    <div class="modal-card" style="max-width: 680px;">
        <button type="button" class="modal-close" onclick="closeDoctorModal()">&times;</button>
        <div class="modal-header">
            <h3 id="doctorModalTitle">Add Specialist Profile</h3>
            <p class="text-muted" style="font-size: 0.85rem;">Manage doctor credentials, degrees, certifications, and clinical biography</p>
        </div>
        <form id="doctorForm" onsubmit="handleDoctorSubmit(event)" enctype="multipart/form-data">
            <input type="hidden" id="doc_id" name="id">

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="doc_name">Doctor Full Name & Medical Titles *</label>
                    <input type="text" id="doc_name" name="name" required class="form-control" placeholder="e.g. Dr. Alisha Vance, MD, DVD">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="doc_designation">Role / Designation *</label>
                    <input type="text" id="doc_designation" name="designation" required class="form-control" placeholder="e.g. Lead Dermatologist & Medical Director">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="doc_qualification">Degrees, Certifications & Fellowships *</label>
                <input type="text" id="doc_qualification" name="qualification" required class="form-control" placeholder="e.g. MBBS, MD (Dermatology, Venereology & Leprosy), Fellowship in Aesthetic Medicine (FACD)">
                <small class="text-muted">Displayed prominently in the doctor credentials card on the homepage and about page</small>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="doc_department">Department / Specialization</label>
                    <input type="text" id="doc_department" name="department" class="form-control" placeholder="e.g. Clinical Dermatology & Laser Aesthetics">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="doc_experience_years">Years of Clinical Experience</label>
                    <input type="number" id="doc_experience_years" name="experience_years" class="form-control" placeholder="e.g. 12" min="0" max="60">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="doc_short_bio">Short Introduction / Summary</label>
                <textarea id="doc_short_bio" name="short_bio" rows="2" class="form-control" placeholder="Brief summary highlighted next to the doctor portrait..."></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="doc_full_bio">Detailed Clinical Biography</label>
                <textarea id="doc_full_bio" name="full_bio" rows="3" class="form-control" placeholder="Detailed career history, clinical philosophy, fellowship achievements..."></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="doc_photo_file">Doctor Portrait Photo</label>
                <input type="file" id="doc_photo_file" name="photo_file" class="form-control" accept="image/*">
                <small class="text-muted">Recommended 3:4 portrait ratio with high resolution</small>
            </div>

            <div class="form-row" style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: var(--color-bg-light); border-radius: 6px;">
                <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" id="doc_is_lead" name="is_lead" value="1">
                    <span style="font-weight: 600; color: var(--color-crimson);">★ Set as Lead Specialist (Homepage Spotlight)</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" id="doc_status" name="status" value="1" checked>
                    <span>Active on Website</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeDoctorModal()">Cancel</button>
                <button type="submit" class="btn btn-gold btn-sm" id="saveDoctorBtn">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Realtime Client Live Search
    function filterDoctorsLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.doctor-data-row');
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

    function openNewDoctorModal() {
        document.getElementById('doctorModalTitle').textContent = 'Add Specialist Profile';
        document.getElementById('doctorForm').reset();
        document.getElementById('doc_id').value = '';
        document.getElementById('doc_status').checked = true;
        document.getElementById('doc_is_lead').checked = false;
        document.getElementById('saveDoctorBtn').textContent = 'Save';
        document.getElementById('doctorModal').classList.add('open');
    }

    function openEditDoctorModal(doc) {
        document.getElementById('doctorModalTitle').textContent = 'Edit Specialist: ' + doc.name;
        document.getElementById('doc_id').value = doc.id;
        document.getElementById('doc_name').value = doc.name || '';
        document.getElementById('doc_designation').value = doc.designation || '';
        document.getElementById('doc_qualification').value = doc.qualification || '';
        document.getElementById('doc_department').value = doc.department || '';
        document.getElementById('doc_experience_years').value = doc.experience_years || 12;
        document.getElementById('doc_short_bio').value = doc.short_bio || '';
        document.getElementById('doc_full_bio').value = doc.full_bio || '';
        document.getElementById('doc_is_lead').checked = doc.is_lead ? true : false;
        document.getElementById('doc_status').checked = doc.status ? true : false;
        document.getElementById('saveDoctorBtn').textContent = 'Update';
        document.getElementById('doctorModal').classList.add('open');
    }

    function closeDoctorModal() {
        document.getElementById('doctorModal').classList.remove('open');
    }

    async function handleDoctorSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('doc_id').value;
        const btn = document.getElementById('saveDoctorBtn');
        btn.disabled = true;
        btn.textContent = id ? 'Updating...' : 'Saving...';

        const formData = new FormData(document.getElementById('doctorForm'));
        formData.set('status', document.getElementById('doc_status').checked ? '1' : '0');
        formData.set('is_lead', document.getElementById('doc_is_lead').checked ? '1' : '0');

        const url = id ? `/api/v1/admin/doctors/${id}` : '/api/v1/admin/doctors';
        if (id) {
            formData.append('_method', 'PUT');
        }

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
                closeDoctorModal();
                showToast(id ? 'Doctor profile updated successfully!' : 'Doctor specialist added successfully!', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Error saving doctor details', 'error');
            }
        } catch(err) {
            console.error(err);
            showToast('Network error saving doctor details', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = id ? 'Update' : 'Save';
        }
    }

    function deleteDoctor(id, name) {
        confirmDeleteModal('Delete Doctor Profile', name, async () => {
            try {
                const res = await fetch(`/api/v1/admin/doctors/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`doc_row_${id}`);
                    if (row) row.remove();
                    showToast(`Specialist "${name}" deleted successfully!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete doctor', 'error');
                }
            } catch(err) {
                showToast('Network error deleting doctor', 'error');
            }
        });
    }
</script>
@endsection
