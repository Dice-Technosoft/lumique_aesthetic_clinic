@extends('layouts.admin')

@section('title', 'CRM Lead Management')
@section('breadcrumb_parent', 'Clinic CRM')
@section('breadcrumb_current', 'Leads & Pipeline')
@section('page_title', 'Patient Leads & Pipeline Management')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div class="filter-pills-group">
            <a href="{{ route('admin.leads', ['search' => $search]) }}" class="filter-pill {{ !$status ? 'active' : '' }}">All Leads</a>
            <a href="{{ route('admin.leads', ['status' => 'new', 'search' => $search]) }}" class="filter-pill {{ $status === 'new' ? 'active' : '' }}">New</a>
            <a href="{{ route('admin.leads', ['status' => 'follow_up', 'search' => $search]) }}" class="filter-pill {{ $status === 'follow_up' ? 'active' : '' }}">Follow-up Due</a>
            <a href="{{ route('admin.leads', ['status' => 'converted', 'search' => $search]) }}" class="filter-pill {{ $status === 'converted' ? 'active' : '' }}">Converted</a>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.leads') }}" method="GET" class="admin-search-wrapper">
                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="leadSearchInput" name="search" value="{{ $search }}" placeholder="Search patient name, email, phone..." class="admin-search-input" oninput="filterLeadsLive(this.value)">
                @if($search)
                    <a href="{{ route('admin.leads', ['status' => $status]) }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openLeadModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 22%;">Patient Name</th>
                    <th style="width: 22%;">Contact</th>
                    <th style="width: 20%;">Procedure Interest</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 12%;">Follow-ups</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr id="lead_row_{{ $lead->id }}" class="lead-data-row" data-search="{{ strtolower($lead->name . ' ' . $lead->email . ' ' . $lead->phone . ' ' . $lead->service_name) }}">
                    <td style="word-break: break-word;">
                        <strong>{{ $lead->name }}</strong>
                        @if($lead->estimated_value)
                        <div class="small gold-text" style="font-weight: 600;">Est. ₹{{ number_format($lead->estimated_value, 2) }}</div>
                        @endif
                    </td>
                    <td style="word-break: break-word;">
                        <div><a href="tel:{{ $lead->phone }}" style="color: var(--color-charcoal); text-decoration: none; font-weight: 500;">{{ $lead->phone }}</a></div>
                        <small class="text-muted">{{ $lead->email }}</small>
                    </td>
                    <td style="word-break: break-word;">
                        <span class="badge badge-gold">{{ $lead->service_name ?: 'General Consultation' }}</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $lead->status }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                    </td>
                    <td>
                        <div class="small">
                            <strong>Notes:</strong> {{ $lead->notesList->count() }}<br>
                            <strong>Follow-ups:</strong> {{ $lead->followups->count() }}
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group">
                            <!-- View / Show Details -->
                            <button type="button" class="action-icon-btn btn-view" data-tooltip="View Details" aria-label="View Details" onclick='openViewLeadModal(@json($lead))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>

                            <!-- Edit / Update -->
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Patient Lead" aria-label="Edit Lead" onclick='openLeadModal(@json($lead))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>

                            <!-- Delete -->
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Patient Lead" aria-label="Delete Lead" onclick="deleteLead({{ $lead->id }}, '{{ addslashes($lead->name) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">No patient leads found in database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $leads->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Lead -->
<div class="modal-overlay" id="leadModal">
    <div class="modal-card" style="max-width: 600px;">
        <button class="modal-close" onclick="closeLeadModal()">&times;</button>
        <div class="modal-header">
            <h3 id="leadModalTitle">Add Patient Lead</h3>
            <p class="text-muted" style="font-size: 0.82rem;">Create or update clinical lead details in database</p>
        </div>
        <form onsubmit="handleLeadSubmit(event)" id="leadForm">
            <input type="hidden" id="lead_id" name="id">

            <div class="form-group">
                <label for="lead_name">Patient Name *</label>
                <input type="text" id="lead_name" name="name" required class="form-control" placeholder="e.g. Priya Sharma">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="lead_email">Email Address *</label>
                    <input type="email" id="lead_email" name="email" required class="form-control" placeholder="priya@example.com">
                </div>
                <div class="form-group">
                    <label for="lead_phone">Phone Number *</label>
                    <input type="text" id="lead_phone" name="phone" required class="form-control" placeholder="+91 98200 12345">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="lead_service_name">Procedure Interest</label>
                    <input type="text" id="lead_service_name" name="service_name" class="form-control" placeholder="e.g. HydraFacial MD Elite">
                </div>
                <div class="form-group">
                    <label for="lead_status">Pipeline Status</label>
                    <select id="lead_status" name="status" class="form-control">
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="consultation_scheduled">Consultation Scheduled</option>
                        <option value="follow_up">Follow Up Due</option>
                        <option value="converted">Converted</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="lead_estimated_value">Estimated Treatment Value (₹)</label>
                <input type="number" id="lead_estimated_value" name="estimated_value" step="0.01" class="form-control" placeholder="e.g. 15000">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeLeadModal()">Cancel</button>
                <button type="submit" id="saveLeadBtn" class="btn btn-gold btn-sm">Save Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Lead Details -->
<div class="modal-overlay" id="viewLeadModal">
    <div class="modal-card" style="max-width: 650px;">
        <button class="modal-close" onclick="closeViewLeadModal()">&times;</button>
        <div class="modal-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div>
                    <h3 id="viewLeadName" style="margin: 0;">Patient Details</h3>
                    <small class="text-muted" id="viewLeadSub">Clinical Lead Record</small>
                </div>
                <span id="viewLeadStatusBadge" class="status-badge">New</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: var(--color-bg-light); padding: 0.85rem 1rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Phone</small>
                <div id="viewLeadPhone" style="font-weight: 600; margin-top: 2px;">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.85rem 1rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Email</small>
                <div id="viewLeadEmail" style="font-weight: 600; margin-top: 2px;">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.85rem 1rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Procedure Interest</small>
                <div id="viewLeadService" style="font-weight: 600; margin-top: 2px; color: var(--color-crimson);">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.85rem 1rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Estimated Value</small>
                <div id="viewLeadValue" style="font-weight: 600; margin-top: 2px; color: var(--color-gold-bright);">-</div>
            </div>
        </div>

        <!-- Consultation Notes & Follow-up Actions -->
        <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
            <button type="button" class="btn btn-outline-gold btn-sm" style="flex: 1;" onclick="triggerAddNoteFromView()">+ Add Note</button>
            <button type="button" class="btn btn-gold btn-sm" style="flex: 1;" onclick="triggerFollowUpFromView()">Schedule Follow-up</button>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeViewLeadModal()">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Add Note -->
<div class="modal-overlay" id="leadNoteModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeLeadNoteModal()">&times;</button>
        <div class="modal-header">
            <h3>Add Consultation Note</h3>
            <p id="noteModalLeadName" class="text-muted"></p>
        </div>
        <form onsubmit="handleNoteSubmit(event)">
            <input type="hidden" id="note_lead_id">
            <div class="form-group">
                <label for="note_text">Note Details *</label>
                <textarea id="note_text" rows="3" required class="form-control" placeholder="Patient concerns, skin history, recommended treatments..."></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeLeadNoteModal()">Cancel</button>
                <button type="submit" id="saveNoteBtn" class="btn btn-gold btn-sm">Save Note</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Schedule Follow-up -->
<div class="modal-overlay" id="followUpModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeFollowUpModal()">&times;</button>
        <div class="modal-header">
            <h3>Schedule Patient Follow-up</h3>
            <p id="fuModalLeadName" class="text-muted"></p>
        </div>
        <form onsubmit="handleFollowUpSubmit(event)">
            <input type="hidden" id="fu_lead_id">
            <div class="form-row">
                <div class="form-group">
                    <label for="fu_date">Follow-up Date *</label>
                    <input type="date" id="fu_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="fu_time">Time (Optional)</label>
                    <input type="time" id="fu_time" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="fu_note">Follow-up Objective</label>
                <textarea id="fu_note" rows="2" class="form-control" placeholder="e.g. Discuss treatment plan & consultation booking"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeFollowUpModal()">Cancel</button>
                <button type="submit" id="saveFuBtn" class="btn btn-gold btn-sm">Schedule Follow-up</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterLeadsLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.lead-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    let currentViewingLead = null;

    function openLeadModal(lead = null) {
        const form = document.getElementById('leadForm');
        form.reset();
        document.getElementById('lead_id').value = '';

        if (lead) {
            document.getElementById('leadModalTitle').innerText = 'Edit Patient Lead';
            document.getElementById('lead_id').value = lead.id;
            document.getElementById('lead_name').value = lead.name || '';
            document.getElementById('lead_email').value = lead.email || '';
            document.getElementById('lead_phone').value = lead.phone || '';
            document.getElementById('lead_service_name').value = lead.service_name || '';
            document.getElementById('lead_status').value = lead.status || 'new';
            document.getElementById('lead_estimated_value').value = lead.estimated_value || '';
        } else {
            document.getElementById('leadModalTitle').innerText = 'Add Patient Lead';
            document.getElementById('lead_status').value = 'new';
        }

        document.getElementById('leadModal').classList.add('open');
    }

    function closeLeadModal() {
        document.getElementById('leadModal').classList.remove('open');
    }

    async function handleLeadSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveLeadBtn');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const id = document.getElementById('lead_id').value;
        const payload = {
            name: document.getElementById('lead_name').value,
            email: document.getElementById('lead_email').value,
            phone: document.getElementById('lead_phone').value,
            service_name: document.getElementById('lead_service_name').value,
            status: document.getElementById('lead_status').value,
            estimated_value: document.getElementById('lead_estimated_value').value || null,
        };

        const url = id ? `/api/v1/admin/leads/${id}` : '/api/v1/admin/leads';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeLeadModal();
                showToast(data.message || 'Lead saved successfully in database!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                showToast(data.message || 'Error saving lead', 'error');
            }
        } catch(err) {
            showToast('Network error saving lead', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Lead';
        }
    }

    function openViewLeadModal(lead) {
        currentViewingLead = lead;
        document.getElementById('viewLeadName').innerText = lead.name;
        document.getElementById('viewLeadSub').innerText = 'Lead ID #' + lead.id + ' • Registered on ' + (lead.created_at ? new Date(lead.created_at).toLocaleDateString() : 'Website');
        document.getElementById('viewLeadPhone').innerHTML = `<a href="tel:${lead.phone}" style="color: var(--color-charcoal); text-decoration: none;">${lead.phone}</a>`;
        document.getElementById('viewLeadEmail').innerHTML = `<a href="mailto:${lead.email}" style="color: var(--color-charcoal); text-decoration: none;">${lead.email}</a>`;
        document.getElementById('viewLeadService').innerText = lead.service_name || 'General Consultation';
        document.getElementById('viewLeadValue').innerText = lead.estimated_value ? '₹' + Number(lead.estimated_value).toLocaleString('en-IN') : 'Not Specified';

        const badge = document.getElementById('viewLeadStatusBadge');
        badge.className = 'status-badge status-' + lead.status;
        badge.innerText = (lead.status || 'new').replace('_', ' ').toUpperCase();

        document.getElementById('viewLeadModal').classList.add('open');
    }

    function closeViewLeadModal() {
        document.getElementById('viewLeadModal').classList.remove('open');
    }

    function triggerAddNoteFromView() {
        if (!currentViewingLead) return;
        closeViewLeadModal();
        openLeadNoteModal(currentViewingLead.id, currentViewingLead.name);
    }

    function triggerFollowUpFromView() {
        if (!currentViewingLead) return;
        closeViewLeadModal();
        openFollowUpModal(currentViewingLead.id, currentViewingLead.name);
    }

    function deleteLead(id, name) {
        confirmDeleteModal('Delete Patient Lead', name, async () => {
            try {
                const res = await fetch(`/api/v1/admin/leads/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`lead_row_${id}`);
                    if (row) row.remove();
                    showToast(data.message || 'Lead deleted successfully!', 'success');
                } else {
                    showToast('Failed to delete lead', 'error');
                }
            } catch(err) {
                showToast('Network error deleting lead', 'error');
            }
        });
    }

    function openLeadNoteModal(id, name) {
        document.getElementById('note_lead_id').value = id;
        document.getElementById('note_text').value = '';
        document.getElementById('noteModalLeadName').innerText = 'Patient: ' + name;
        document.getElementById('leadNoteModal').classList.add('open');
    }
    function closeLeadNoteModal() {
        document.getElementById('leadNoteModal').classList.remove('open');
    }

    function openFollowUpModal(id, name) {
        document.getElementById('fu_lead_id').value = id;
        document.getElementById('fu_date').value = '';
        document.getElementById('fu_time').value = '';
        document.getElementById('fu_note').value = '';
        document.getElementById('fuModalLeadName').innerText = 'Patient: ' + name;
        document.getElementById('followUpModal').classList.add('open');
    }
    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.remove('open');
    }

    async function handleNoteSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveNoteBtn');
        btn.disabled = true;
        btn.innerText = 'Saving...';
        const id = document.getElementById('note_lead_id').value;
        const note = document.getElementById('note_text').value;

        try {
            const res = await fetch(`/api/v1/admin/leads/${id}/notes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ note: note })
            });
            if (res.ok) {
                closeLeadNoteModal();
                showToast('Note added successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Error adding note', 'error');
            }
        } catch(err) {
            showToast('Network error saving note', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Note';
        }
    }

    async function handleFollowUpSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveFuBtn');
        btn.disabled = true;
        btn.innerText = 'Scheduling...';
        const id = document.getElementById('fu_lead_id').value;
        const payload = {
            follow_up_date: document.getElementById('fu_date').value,
            follow_up_time: document.getElementById('fu_time').value,
            note: document.getElementById('fu_note').value,
        };

        try {
            const res = await fetch(`/api/v1/admin/leads/${id}/follow-ups`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                closeFollowUpModal();
                showToast('Follow-up scheduled successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Error scheduling follow-up', 'error');
            }
        } catch(err) {
            showToast('Network error scheduling follow-up', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Schedule Follow-up';
        }
    }
</script>
@endsection
