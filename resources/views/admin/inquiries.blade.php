@extends('layouts.admin')

@section('title', 'Contact Inquiries - Lumique Clinic Admin')
@section('breadcrumb_parent', 'Clinic CRM')
@section('breadcrumb_current', 'Contact Inquiries')
@section('page_title', 'Website Contact Inquiries')

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
                    <th style="width: 20%;">Patient Name</th>
                    <th style="width: 20%;">Contact Info</th>
                    <th style="width: 17%;">Procedure Interest</th>
                    <th style="width: 13%;">Inquiry Date</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 19%; text-align: right; min-width: 175px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                <tr id="inquiry_row_{{ $inq->id }}" class="inquiry-data-row" data-search="{{ strtolower($inq->name . ' ' . $inq->email . ' ' . $inq->phone . ' ' . ($inq->service->title ?? '') . ' ' . ($inq->service_name ?? '')) }}">
                    <td>
                        <strong class="inq-patient-name">{{ $inq->name }}</strong>
                        @if($inq->message)
                        <div class="small text-muted inq-patient-msg" style="max-width: 230px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $inq->message }}">
                            "{{ $inq->message }}"
                        </div>
                        @endif
                    </td>
                    <td>
                        <div><a href="tel:{{ $inq->phone }}" class="inq-patient-phone">{{ $inq->phone }}</a></div>
                        <small><a href="mailto:{{ $inq->email }}" class="text-muted inq-patient-email">{{ $inq->email }}</a></small>
                    </td>
                    <td>
                        <span class="badge {{ $inq->status === 'converted' ? 'badge-gold' : 'badge-neutral' }} inq-service-badge">
                            {{ $inq->service_name ?: ($inq->service->title ?? 'General Inquiry') }}
                        </span>
                    </td>
                    <td>
                        <span class="inq-requested-date">{{ $inq->created_at ? $inq->created_at->format('M d, Y') : '-' }}</span>
                        <br><small class="text-muted">{{ $inq->created_at ? $inq->created_at->format('h:i A') : '' }}</small>
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
                        <div class="table-actions-group" style="justify-content: flex-end; flex-wrap: nowrap; gap: 6px;">
                            <!-- Convert to Appointment -->
                            <button type="button" 
                                    class="action-icon-btn btn-view" 
                                    data-tooltip="Convert to Appointment" 
                                    aria-label="Convert to Appointment"
                                    style="color: var(--color-crimson); border-color: rgba(139, 21, 56, 0.3);"
                                    data-id="{{ $inq->id }}"
                                    data-name="{{ $inq->name }}"
                                    data-email="{{ $inq->email }}"
                                    data-phone="{{ $inq->phone }}"
                                    data-service-id="{{ $inq->service_id }}"
                                    data-date="{{ $inq->preferred_date ? $inq->preferred_date->format('Y-m-d') : '' }}"
                                    data-time="{{ $inq->preferred_time }}"
                                    data-message="{{ $inq->message }}"
                                    data-status="{{ $inq->status }}"
                                    onclick="openConvertModalFromButton(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                            </button>

                            <!-- WhatsApp Patient -->
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inq->phone) }}?text=Hello%20{{ urlencode($inq->name) }},%20this%20is%20Lumique%20Aesthetic%20Clinic%20Mumbai%20regarding%20your%20consultation%20inquiry." 
                               target="_blank" 
                               class="action-icon-btn btn-view" 
                               data-tooltip="Chat on WhatsApp"
                               aria-label="WhatsApp Patient">
                               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </a>

                            <!-- Edit Inquiry -->
                            <button type="button" 
                                    class="action-icon-btn btn-edit" 
                                    data-tooltip="Edit Inquiry" 
                                    aria-label="Edit Inquiry" 
                                    data-id="{{ $inq->id }}"
                                    data-name="{{ $inq->name }}"
                                    data-email="{{ $inq->email }}"
                                    data-phone="{{ $inq->phone }}"
                                    data-service-id="{{ $inq->service_id }}"
                                    data-status="{{ $inq->status }}"
                                    data-message="{{ $inq->message }}"
                                    onclick="openEditInquiryModalFromButton(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>

                            <!-- Delete Inquiry -->
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
                    <td colspan="6" class="text-center py-5">No contact inquiries found matching criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $inquiries->links() }}
    </div>
</div>

<!-- Modal: Convert Inquiry to Customer Appointment -->
<div class="modal-overlay" id="convertModal">
    <div class="modal-card" style="max-width: 620px; max-height: 88vh; overflow-y: auto; padding: 1.75rem;">
        <button type="button" class="modal-close" onclick="closeConvertModal()">&times;</button>
        <div class="modal-header" style="margin-bottom: 1.25rem;">
            <h3 style="display: flex; align-items: center; gap: 8px; margin: 0 0 4px 0;">
                <span>📅</span>
                <span>Convert to Confirmed Appointment</span>
            </h3>
            <p class="text-muted" style="font-size: 0.82rem; margin: 0;">Move this inquiry into the Appointments CRM and trigger confirmation emails</p>
        </div>
        <form id="convertForm" onsubmit="handleConvertSubmit(event)">
            <input type="hidden" id="convert_inquiry_id">
            
            <div class="form-group mb-2">
                <label for="convert_name">Patient / Customer Name *</label>
                <input type="text" id="convert_name" class="form-control" required placeholder="e.g. Meera Joshi">
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="convert_phone">Phone Number *</label>
                    <input type="text" id="convert_phone" class="form-control" required placeholder="+91 98201 44552">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="convert_email">Email Address *</label>
                    <input type="email" id="convert_email" class="form-control" required placeholder="meera.joshi@example.com">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div class="form-group" style="flex: 1.2;">
                    <label for="convert_service">Treatment / Procedure</label>
                    <select id="convert_service" class="form-control" onchange="handleConvertServiceChange(this)">
                        <option value="" data-price="">-- General Dermatology Consultation --</option>
                        @isset($services)
                            @foreach($services as $svc)
                                @php
                                    $cleanPrice = preg_replace('/[^0-9.]/', '', $svc->price_starting_at ?? '');
                                @endphp
                                <option value="{{ $svc->id }}" data-title="{{ $svc->title }}" data-price="{{ $cleanPrice }}">{{ $svc->title }} @if($svc->price_starting_at)({{ $svc->price_starting_at }})@endif</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group" style="flex: 0.8;">
                    <label for="convert_estimated_value">Est. Value (₹)</label>
                    <input type="number" id="convert_estimated_value" step="0.01" class="form-control" placeholder="e.g. 15000">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: flex-end;">
                <div class="form-group" style="flex: 1.1;">
                    <label for="convert_date">Appointment Date *</label>
                    <input type="date" id="convert_date" class="form-control" required>
                </div>
                <div class="form-group" style="flex: 1.1;">
                    <label for="convert_preferred_time">Appointment Time Slot *</label>
                    <input type="time" id="convert_preferred_time" class="form-control" required>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="convert_notes">Appointment Notes / Patient Concerns</label>
                <textarea id="convert_notes" rows="2" class="form-control" placeholder="Skin type, primary concerns, doctor instructions..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeConvertModal()">Cancel</button>
                <button type="submit" class="btn btn-gold btn-sm" id="convertBtn">Convert & Book Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Inquiry -->
<div class="modal-overlay" id="inquiryModal">
    <div class="modal-card" style="max-width: 620px; max-height: 88vh; overflow-y: auto; padding: 1.75rem;">
        <button type="button" class="modal-close" onclick="closeInquiryModal()">&times;</button>
        <div class="modal-header" style="margin-bottom: 1.25rem;">
            <h3 id="inquiryModalTitle" style="margin: 0 0 4px 0;">Edit Contact Inquiry</h3>
            <p class="text-muted" style="font-size: 0.82rem; margin: 0;">Update patient details, requested procedure, status, or message notes</p>
        </div>
        <form id="inquiryForm" onsubmit="saveInquiry(event)">
            <input type="hidden" id="edit_inquiry_id">
            
            <div class="form-group mb-2">
                <label for="edit_inq_name">Patient Full Name *</label>
                <input type="text" id="edit_inq_name" class="form-control" required placeholder="e.g. Meera Joshi">
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_phone">Phone Number *</label>
                    <input type="text" id="edit_inq_phone" class="form-control" required placeholder="+91 98201 44552">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="edit_inq_email">Email Address *</label>
                    <input type="email" id="edit_inq_email" class="form-control" required placeholder="meera.joshi@example.com">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div class="form-group" style="flex: 1.2;">
                    <label for="edit_inq_service">Procedure / Service Interest</label>
                    <select id="edit_inq_service" class="form-control">
                        <option value="">-- General Consultation --</option>
                        @isset($services)
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" data-title="{{ $svc->title }}">{{ $svc->title }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group" style="flex: 0.8;">
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

            <div class="form-group mb-3">
                <label for="edit_inq_message">Patient Message / Consultation Notes</label>
                <textarea id="edit_inq_message" rows="3" class="form-control" placeholder="Patient concerns, aesthetic goals, or intake notes..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-border);">
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

    // Helper: Time Formatters for input[type="time"]
    function formatTimeForInput(timeStr) {
        if (!timeStr) return '11:00';
        if (/^\d{2}:\d{2}$/.test(timeStr)) return timeStr;
        const match = timeStr.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
        if (match) {
            let h = parseInt(match[1]);
            const m = match[2];
            const ampm = match[3] ? match[3].toUpperCase() : '';
            if (ampm === 'PM' && h < 12) h += 12;
            if (ampm === 'AM' && h === 12) h = 0;
            return (h < 10 ? '0' + h : '' + h) + ':' + m;
        }
        return '11:00';
    }

    function formatTimeTo12Hour(time24) {
        if (!time24) return '';
        const match = time24.match(/(\d{1,2}):(\d{2})/);
        if (match) {
            let h = parseInt(match[1]);
            const m = match[2];
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            if (h === 0) h = 12;
            return (h < 10 ? '0' + h : '' + h) + ':' + m + ' ' + ampm;
        }
        return time24;
    }

    // --- Convert Inquiry to Appointment ---
    function openConvertModalFromButton(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || '';
        const email = btn.getAttribute('data-email') || '';
        const phone = btn.getAttribute('data-phone') || '';
        const serviceId = btn.getAttribute('data-service-id') || '';
        const date = btn.getAttribute('data-date') || '';
        const time = btn.getAttribute('data-time') || '';
        const message = btn.getAttribute('data-message') || '';

        document.getElementById('convert_inquiry_id').value = id;
        document.getElementById('convert_name').value = name;
        document.getElementById('convert_email').value = email;
        document.getElementById('convert_phone').value = phone;
        
        const serviceSelect = document.getElementById('convert_service');
        if (serviceId) {
            serviceSelect.value = serviceId;
        } else {
            serviceSelect.value = '';
        }

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('convert_date').value = date ? date.substring(0, 10) : today;
        document.getElementById('convert_preferred_time').value = formatTimeForInput(time);
        
        document.getElementById('convert_notes').value = message;
        document.getElementById('convert_estimated_value').value = '';
        handleConvertServiceChange(document.getElementById('convert_service'));

        const modal = document.getElementById('convertModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function handleConvertServiceChange(selectEl) {
        if (!selectEl) return;
        const opt = selectEl.options[selectEl.selectedIndex];
        const price = opt ? opt.getAttribute('data-price') : '';
        if (price) {
            document.getElementById('convert_estimated_value').value = price;
        }
    }

    function closeConvertModal() {
        const modal = document.getElementById('convertModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
    }

    async function handleConvertSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('convert_inquiry_id').value;
        const btn = document.getElementById('convertBtn');
        const originalText = btn.innerHTML;

        const serviceSelect = document.getElementById('convert_service');
        const selectedOpt = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceId = serviceSelect.value ? parseInt(serviceSelect.value) : null;
        const serviceName = selectedOpt ? selectedOpt.getAttribute('data-title') : null;

        const timeValue = document.getElementById('convert_preferred_time').value;
        const formattedTime = formatTimeTo12Hour(timeValue);

        const payload = {
            name: document.getElementById('convert_name').value.trim(),
            email: document.getElementById('convert_email').value.trim(),
            phone: document.getElementById('convert_phone').value.trim(),
            service_id: serviceId,
            service_name: serviceName,
            preferred_date: document.getElementById('convert_date').value,
            preferred_time: formattedTime,
            priority: 'medium',
            estimated_value: document.getElementById('convert_estimated_value').value || null,
            notes: document.getElementById('convert_notes').value.trim() || null,
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Converting & Moving...';

        try {
            const res = await fetch(`/api/v1/admin/inquiries/${id}/convert-to-appointment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showToast(data.message || 'Converted to Appointment & moved to Appointments module!', 'success');
                closeConvertModal();

                // Instantly animate removal from inquiries table
                const row = document.getElementById(`inquiry_row_${id}`);
                if (row) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => row.remove(), 400);
                }

                setTimeout(() => {
                    window.location.href = "{{ route('admin.leads') }}";
                }, 750);
            } else {
                showToast(data.message || 'Failed to convert inquiry', 'error');
            }
        } catch (err) {
            showToast('Error converting inquiry to appointment', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // --- Edit Inquiry ---
    function openEditInquiryModalFromButton(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || '';
        const email = btn.getAttribute('data-email') || '';
        const phone = btn.getAttribute('data-phone') || '';
        const serviceId = btn.getAttribute('data-service-id') || '';
        const status = btn.getAttribute('data-status') || 'new';
        const message = btn.getAttribute('data-message') || '';

        document.getElementById('edit_inquiry_id').value = id;
        document.getElementById('edit_inq_name').value = name;
        document.getElementById('edit_inq_email').value = email;
        document.getElementById('edit_inq_phone').value = phone;
        
        const serviceSelect = document.getElementById('edit_inq_service');
        if (serviceId) {
            serviceSelect.value = serviceId;
        } else {
            serviceSelect.value = '';
        }

        document.getElementById('edit_inq_status').value = status;
        document.getElementById('edit_inq_message').value = message;

        const modal = document.getElementById('inquiryModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function closeInquiryModal() {
        const modal = document.getElementById('inquiryModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
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

    function deleteInquiry(id, name) {
        confirmDeleteModal('Delete Inquiry', name, async () => {
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
        });
    }
</script>
@endsection
