@extends('layouts.admin')

@section('title', 'Inquiries Management')
@section('breadcrumb_parent', 'Clinic CRM')
@section('breadcrumb_current', 'Inquiries & Bookings')
@section('page_title', 'Inquiry & Booking Submissions')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row">
        <div class="filter-pills-group">
            <a href="{{ route('admin.inquiries') }}" class="filter-pill {{ !$status ? 'active' : '' }}">All Inquiries</a>
            <a href="{{ route('admin.inquiries', ['status' => 'new']) }}" class="filter-pill {{ $status === 'new' ? 'active' : '' }}">New Unread</a>
            <a href="{{ route('admin.inquiries', ['status' => 'contacted']) }}" class="filter-pill {{ $status === 'contacted' ? 'active' : '' }}">Contacted</a>
            <a href="{{ route('admin.inquiries', ['status' => 'converted']) }}" class="filter-pill {{ $status === 'converted' ? 'active' : '' }}">Converted</a>
        </div>

        <form action="{{ route('admin.inquiries') }}" method="GET" class="admin-search-wrapper">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <span class="search-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" id="inquirySearchInput" name="search" value="{{ $search }}" placeholder="Search by name, email, or phone..." class="admin-search-input" oninput="filterInquiriesLive(this.value)">
            @if($search)
                <a href="{{ route('admin.inquiries', ['status' => $status]) }}" class="search-clear-link" title="Clear search">&times;</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 21%;">Patient Name</th>
                    <th style="width: 19%;">Contact Info</th>
                    <th style="width: 15%;">Type / Service</th>
                    <th style="width: 12%;">Date Requested</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 16%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                <tr id="inquiry_row_{{ $inq->id }}" class="inquiry-data-row" data-search="{{ strtolower($inq->name . ' ' . $inq->email . ' ' . $inq->phone . ' ' . ($inq->service->title ?? '') . ' ' . ($inq->service_name ?? '')) }}">
                    <td>#{{ $inq->id }}</td>
                    <td>
                        <strong class="inq-patient-name">{{ $inq->name }}</strong>
                        @if($inq->message)
                        <div class="small text-muted inq-patient-msg" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $inq->message }}">
                            "{{ $inq->message }}"
                        </div>
                        @endif
                    </td>
                    <td>
                        <div><a href="tel:{{ $inq->phone }}" class="inq-patient-phone">{{ $inq->phone }}</a></div>
                        <small><a href="mailto:{{ $inq->email }}" class="text-muted inq-patient-email">{{ $inq->email }}</a></small>
                    </td>
                    <td>
                        <span class="badge {{ $inq->type === 'appointment' ? 'badge-gold' : 'badge-neutral' }} inq-service-badge">
                            {{ $inq->service_name ?: ($inq->service->title ?? ucfirst($inq->type)) }}
                        </span>
                    </td>
                    <td>
                        <span class="inq-requested-date">{{ $inq->preferred_date ? $inq->preferred_date->format('M d, Y') : 'Immediate' }}</span>
                        @if($inq->preferred_time)<br><small class="text-muted inq-requested-time">{{ $inq->preferred_time }}</small>@endif
                    </td>
                    <td>
                        <select onchange="updateInquiryStatus({{ $inq->id }}, this.value)" class="status-select status-{{ $inq->status }}">
                            <option value="new" {{ $inq->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ $inq->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="in_progress" {{ $inq->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="converted" {{ $inq->status === 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="closed" {{ $inq->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="spam" {{ $inq->status === 'spam' ? 'selected' : '' }}>Spam</option>
                        </select>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end; flex-wrap: nowrap; gap: 5px;">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inq->phone) }}?text=Hello%20{{ urlencode($inq->name) }},%20this%20is%20Lumique%20Aesthetic%20Clinic%20Mumbai%20regarding%20your%20consultation%20inquiry." 
                               target="_blank" 
                               class="action-icon-btn btn-view" 
                               data-tooltip="Chat on WhatsApp"
                               aria-label="WhatsApp Patient">
                               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </a>
                            <a href="tel:{{ $inq->phone }}" 
                               class="action-icon-btn" 
                               style="color: var(--color-gold-bright);"
                               data-tooltip="Call Patient Phone"
                               aria-label="Call Patient">
                               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </a>
                            <button type="button" 
                                    class="action-icon-btn btn-edit" 
                                    data-tooltip="Edit Inquiry" 
                                    aria-label="Edit Inquiry" 
                                    onclick='openEditInquiryModal(@json($inq))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" 
                                    class="action-icon-btn btn-delete" 
                                    data-tooltip="Delete Inquiry" 
                                    aria-label="Delete Inquiry" 
                                    onclick="deleteInquiry({{ $inq->id }}, '{{ addslashes($inq->name) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-inquiries-row">
                    <td colspan="7" class="text-center py-5">No inquiries found matching criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $inquiries->links() }}
    </div>
</div>

<!-- Edit Inquiry Modal -->
<div class="modal-overlay" id="inquiryModal">
    <div class="modal-card" style="max-width: 650px;">
        <button type="button" class="modal-close" onclick="closeInquiryModal()">&times;</button>
        <div class="modal-header">
            <h3 id="inquiryModalTitle">Edit Inquiry & Booking</h3>
            <p class="text-muted" style="font-size: 0.85rem;">Update patient details, booking date, status, or message notes</p>
        </div>
        <form id="inquiryForm" onsubmit="saveInquiry(event)">
            <input type="hidden" id="edit_inquiry_id">
            
            <div class="form-group mb-3">
                <label for="edit_inq_name">Patient Full Name *</label>
                <input type="text" id="edit_inq_name" class="form-control" required placeholder="e.g. Meera Joshi">
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_phone">Phone Number *</label>
                    <input type="text" id="edit_inq_phone" class="form-control" required placeholder="+91 98201 44552">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_email">Email Address *</label>
                    <input type="email" id="edit_inq_email" class="form-control" required placeholder="meera.joshi@example.com">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_service">Treatment / Service</label>
                    <select id="edit_inq_service" class="form-control">
                        <option value="">-- General Consultation --</option>
                        @isset($services)
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" data-title="{{ $svc->title }}">{{ $svc->title }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_status">Status *</label>
                    <select id="edit_inq_status" class="form-control">
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="converted">Converted</option>
                        <option value="closed">Closed</option>
                        <option value="spam">Spam</option>
                    </select>
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_date">Preferred Date</label>
                    <input type="date" id="edit_inq_date" class="form-control">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_time">Preferred Time Slot</label>
                    <input type="text" id="edit_inq_time" class="form-control" placeholder="e.g. 11:00 AM or Morning">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_priority">Priority</label>
                    <select id="edit_inq_priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="edit_inq_message">Patient Message / Consultation Notes</label>
                <textarea id="edit_inq_message" rows="3" class="form-control" placeholder="Patient concerns, aesthetic goals, or intake notes..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeInquiryModal()">Cancel</button>
                <button type="submit" class="btn btn-gold btn-sm" id="saveInquiryBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterInquiriesLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.inquiry-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    async function updateInquiryStatus(id, status) {
        try {
            const res = await fetch(`/api/v1/admin/inquiries/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                showToast('Status updated to: ' + status.replace('_', ' '), 'success');
                const row = document.getElementById(`inquiry_row_${id}`);
                if (row) {
                    const select = row.querySelector('.status-select');
                    if (select) {
                        select.className = `status-select status-${status}`;
                    }
                }
            } else {
                showToast(data.message || 'Failed to update status', 'error');
            }
        } catch(e) {
            showToast('Error updating status', 'error');
        }
    }

    function openEditInquiryModal(inq) {
        document.getElementById('edit_inquiry_id').value = inq.id;
        document.getElementById('edit_inq_name').value = inq.name || '';
        document.getElementById('edit_inq_email').value = inq.email || '';
        document.getElementById('edit_inq_phone').value = inq.phone || '';
        
        // Service
        const serviceSelect = document.getElementById('edit_inq_service');
        if (inq.service_id) {
            serviceSelect.value = inq.service_id;
        } else {
            serviceSelect.value = '';
        }

        // Status & Priority
        document.getElementById('edit_inq_status').value = inq.status || 'new';
        document.getElementById('edit_inq_priority').value = inq.priority || 'medium';

        // Dates & Time
        let formattedDate = '';
        if (inq.preferred_date) {
            formattedDate = inq.preferred_date.substring(0, 10);
        }
        document.getElementById('edit_inq_date').value = formattedDate;
        document.getElementById('edit_inq_time').value = inq.preferred_time || '';
        document.getElementById('edit_inq_message').value = inq.message || '';

        document.getElementById('inquiryModal').classList.add('active');
    }

    function closeInquiryModal() {
        document.getElementById('inquiryModal').classList.remove('active');
    }

    async function saveInquiry(e) {
        e.preventDefault();
        const id = document.getElementById('edit_inquiry_id').value;
        const btn = document.getElementById('saveInquiryBtn');
        const originalText = btn.innerHTML;

        const serviceSelect = document.getElementById('edit_inq_service');
        const selectedOpt = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceId = serviceSelect.value ? parseInt(serviceSelect.value) : null;
        const serviceName = selectedOpt ? selectedOpt.getAttribute('data-title') : null;

        const payload = {
            name: document.getElementById('edit_inq_name').value.trim(),
            email: document.getElementById('edit_inq_email').value.trim(),
            phone: document.getElementById('edit_inq_phone').value.trim(),
            service_id: serviceId,
            service_name: serviceName,
            status: document.getElementById('edit_inq_status').value,
            priority: document.getElementById('edit_inq_priority').value,
            preferred_date: document.getElementById('edit_inq_date').value || null,
            preferred_time: document.getElementById('edit_inq_time').value.trim() || null,
            message: document.getElementById('edit_inq_message').value.trim() || null,
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        try {
            const res = await fetch(`/api/v1/admin/inquiries/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showToast('Inquiry updated successfully', 'success');
                closeInquiryModal();
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Validation error updating inquiry', 'error');
            }
        } catch (err) {
            showToast('Failed to save inquiry changes', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function deleteInquiry(id, name) {
        if (!confirm(`Are you sure you want to permanently delete inquiry #${id} for "${name}"?`)) {
            return;
        }

        try {
            const res = await fetch(`/api/v1/admin/inquiries/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showToast('Inquiry deleted successfully', 'success');
                const row = document.getElementById(`inquiry_row_${id}`);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => row.remove(), 300);
                }
            } else {
                showToast(data.message || 'Failed to delete inquiry', 'error');
            }
        } catch (err) {
            showToast('Error deleting inquiry', 'error');
        }
    }
</script>
@endsection
