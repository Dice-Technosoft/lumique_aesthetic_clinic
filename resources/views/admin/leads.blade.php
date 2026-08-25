@extends('layouts.admin')

@section('title', 'Appointments Management - Lumique Clinic Admin')
@section('breadcrumb_parent', 'Clinic CRM')
@section('breadcrumb_current', 'Appointments')
@section('page_title', 'Patient Appointments & Consultation Bookings')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div class="filter-pills-group">
            <a href="{{ route('admin.leads', ['search' => $search]) }}" class="filter-pill {{ !$status ? 'active' : '' }}">All Appointments</a>
            <a href="{{ route('admin.leads', ['status' => 'new', 'search' => $search]) }}" class="filter-pill {{ $status === 'new' ? 'active' : '' }}">New</a>
            <a href="{{ route('admin.leads', ['status' => 'consultation_scheduled', 'search' => $search]) }}" class="filter-pill {{ $status === 'consultation_scheduled' ? 'active' : '' }}">Scheduled</a>
            <a href="{{ route('admin.leads', ['status' => 'follow_up', 'search' => $search]) }}" class="filter-pill {{ $status === 'follow_up' ? 'active' : '' }}">Follow-up Due</a>
            <a href="{{ route('admin.leads', ['status' => 'converted', 'search' => $search]) }}" class="filter-pill {{ $status === 'converted' ? 'active' : '' }}">Converted / Completed</a>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.leads') }}" method="GET" class="admin-search-wrapper">
                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="leadSearchInput" name="search" value="{{ $search }}" placeholder="Search patient, phone, service..." class="admin-search-input" oninput="filterLeadsLive(this.value)">
                @if($search)
                    <a href="{{ route('admin.leads', ['status' => $status]) }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openLeadModal()">+ Book Appointment</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 20%;">Patient / Customer</th>
                    <th style="width: 18%;">Contact</th>
                    <th style="width: 18%;">Treatment / Procedure</th>
                    <th style="width: 15%;">Appointment Date</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 7%;">Notes</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr id="lead_row_{{ $lead->id }}" class="lead-data-row" data-search="{{ strtolower($lead->name . ' ' . $lead->email . ' ' . $lead->phone . ' ' . ($lead->service_name ?? '') . ' ' . ($lead->service->title ?? '')) }}">
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
                        <span class="badge badge-gold">{{ $lead->service_name ?: ($lead->service->title ?? 'General Consultation') }}</span>
                    </td>
                    <td>
                        @if($lead->preferred_date)
                            <div style="font-weight: 600; color: var(--color-crimson);">
                                {{ $lead->preferred_date->format('M d, Y') }}
                            </div>
                            @if($lead->preferred_time)
                                <small class="text-muted">{{ $lead->preferred_time }}</small>
                            @endif
                        @else
                            <span class="text-muted">Not Set</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $lead->status }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                    </td>
                    <td>
                        <div class="small text-muted">
                            {{ $lead->notesList->count() }} notes
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end; gap: 5px;">
                            <!-- View / Show Details -->
                            <button type="button" class="action-icon-btn btn-view" data-tooltip="View Details" aria-label="View Details" onclick='openViewLeadModal(@json($lead))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>

                            <!-- Edit Appointment -->
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Appointment" aria-label="Edit Appointment" onclick='openLeadModal(@json($lead))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>

                            <!-- Delete Appointment -->
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Appointment" aria-label="Delete Appointment" onclick="deleteLead({{ $lead->id }}, '{{ addslashes($lead->name) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">No patient appointments found in database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $leads->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Appointment -->
<div class="modal-overlay" id="leadModal">
    <div class="modal-card" style="max-width: 650px;">
        <button class="modal-close" onclick="closeLeadModal()">&times;</button>
        <div class="modal-header">
            <h3 id="leadModalTitle" style="display: flex; align-items: center; gap: 8px;">
                <span>📅</span>
                <span>Schedule / Edit Appointment</span>
            </h3>
            <p class="text-muted" style="font-size: 0.82rem;">Create or update clinical appointment details & notify patient</p>
        </div>
        <form onsubmit="handleLeadSubmit(event)" id="leadForm" autocomplete="off">
            <input type="hidden" id="lead_id" name="id">

            <!-- Patient Name with Realtime Autocomplete Suggestion -->
            <div class="form-group" style="position: relative; margin-bottom: 1rem;">
                <label for="lead_name">Patient / Customer Name *</label>
                <input type="text" 
                       id="lead_name" 
                       name="name" 
                       required 
                       class="form-control" 
                       placeholder="Start typing patient name for auto-suggestions..." 
                       oninput="handlePatientAutocomplete(this.value)"
                       autocomplete="off">
                
                <!-- Autocomplete Dropdown Menu -->
                <div id="patientSuggestionsDropdown" style="display: none; position: absolute; top: calc(100% + 2px); left: 0; right: 0; background: #ffffff; border: 1px solid var(--color-border); border-radius: 6px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 1050; max-height: 220px; overflow-y: auto;">
                    <!-- Dynamically populated suggestions -->
                </div>
                <small class="text-muted" style="font-size: 0.75rem; margin-top: 3px; display: block;">
                    💡 Type to auto-search previous patients or create a brand new patient.
                </small>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="lead_phone">Phone Number *</label>
                    <input type="text" id="lead_phone" name="phone" required class="form-control" placeholder="+91 98200 12345">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="lead_email">Email Address *</label>
                    <input type="email" id="lead_email" name="email" required class="form-control" placeholder="patient@example.com">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1.2;">
                    <label for="lead_service_id">Treatment / Procedure</label>
                    <select id="lead_service_id" name="service_id" class="form-control">
                        <option value="">-- General Dermatology Consultation --</option>
                        @isset($services)
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" data-title="{{ $svc->title }}">{{ $svc->title }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group" style="flex: 0.8;">
                    <label for="lead_status">Status</label>
                    <select id="lead_status" name="status" class="form-control">
                        <option value="new">New</option>
                        <option value="consultation_scheduled" selected>Scheduled</option>
                        <option value="contacted">Contacted</option>
                        <option value="follow_up">Follow Up Due</option>
                        <option value="converted">Converted / Completed</option>
                        <option value="lost">Cancelled / Lost</option>
                    </select>
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="lead_preferred_date">Appointment Date</label>
                    <input type="date" id="lead_preferred_date" name="preferred_date" class="form-control">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="lead_preferred_time">Time Slot</label>
                    <input type="text" id="lead_preferred_time" name="preferred_time" class="form-control" placeholder="e.g. 11:00 AM">
                </div>
                <div class="form-group" style="flex: 0.8;">
                    <label for="lead_priority">Priority</label>
                    <select id="lead_priority" name="priority" class="form-control">
                        <option value="high">High (VIP)</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="lead_estimated_value">Estimated Treatment Value (₹)</label>
                <input type="number" id="lead_estimated_value" name="estimated_value" step="0.01" class="form-control" placeholder="e.g. 15000">
            </div>

            <div class="form-group mb-3">
                <label for="lead_notes">Consultation Notes / Patient Intake</label>
                <textarea id="lead_notes" name="notes" rows="3" class="form-control" placeholder="Skin type, primary concerns, doctor instructions..."></textarea>
            </div>

            <div style="background: rgba(197, 160, 89, 0.08); border-left: 3px solid var(--color-gold); padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <small style="display: block; color: var(--color-charcoal); font-weight: 500;">
                    ✉️ <strong>Dual Email Sending:</strong> On booking/saving, confirmation emails are automatically dispatched to the patient and clinic admin.
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeLeadModal()">Cancel</button>
                <button type="submit" id="saveLeadBtn" class="btn btn-gold btn-sm">Save Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Appointment Details -->
<div class="modal-overlay" id="viewLeadModal">
    <div class="modal-card" style="max-width: 650px;">
        <button class="modal-close" onclick="closeViewLeadModal()">&times;</button>
        <div class="modal-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div>
                    <h3 id="viewLeadName" style="margin: 0;">Patient Appointment Details</h3>
                    <small class="text-muted" id="viewLeadSub">Clinical Appointment Record</small>
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
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Treatment / Procedure</small>
                <div id="viewLeadService" style="font-weight: 600; margin-top: 2px; color: var(--color-crimson);">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.85rem 1rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em;">Appointment Slot</small>
                <div id="viewLeadDate" style="font-weight: 600; margin-top: 2px; color: var(--color-gold-bright);">-</div>
            </div>
        </div>

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
                <textarea id="fu_note" rows="2" class="form-control" placeholder="e.g. Discuss treatment plan & post-care guidance"></textarea>
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
    let autocompleteTimeout = null;

    // --- Realtime Patient Name Autocomplete ---
    function handlePatientAutocomplete(query) {
        clearTimeout(autocompleteTimeout);
        const dropdown = document.getElementById('patientSuggestionsDropdown');
        
        if (!query || query.trim().length < 1) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        autocompleteTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`/api/v1/admin/patients/search?q=${encodeURIComponent(query.trim())}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.success && data.data && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(patient => {
                        const safeName = (patient.name || '').replace(/'/g, "\\'");
                        const safeEmail = (patient.email || '').replace(/'/g, "\\'");
                        const safePhone = (patient.phone || '').replace(/'/g, "\\'");
                        const safeService = (patient.service_name || '').replace(/'/g, "\\'");

                        html += `
                            <div style="padding: 10px 14px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.15s ease;"
                                 onmouseover="this.style.background='#fdf8f4'"
                                 onmouseout="this.style.background='#ffffff'"
                                 onclick="selectPatientSuggestion('${safeName}', '${safeEmail}', '${safePhone}', '${safeService}')">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="color: var(--color-charcoal); font-size: 0.9rem;">👤 ${patient.name}</strong>
                                    <span style="font-size: 0.75rem; color: var(--color-crimson); font-weight: 600;">${patient.phone}</span>
                                </div>
                                <div style="font-size: 0.78rem; color: var(--color-charcoal-muted); display: flex; justify-content: space-between; margin-top: 2px;">
                                    <span>✉️ ${patient.email || 'No email'}</span>
                                    <span>${patient.service_name ? '• ' + patient.service_name : ''}</span>
                                </div>
                            </div>
                        `;
                    });
                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                }
            } catch (err) {
                dropdown.style.display = 'none';
            }
        }, 220);
    }

    function selectPatientSuggestion(name, email, phone, serviceName) {
        document.getElementById('lead_name').value = name;
        document.getElementById('lead_email').value = email;
        document.getElementById('lead_phone').value = phone;

        // Auto-match service dropdown if exists
        if (serviceName) {
            const select = document.getElementById('lead_service_id');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].text.toLowerCase().includes(serviceName.toLowerCase())) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }

        const dropdown = document.getElementById('patientSuggestionsDropdown');
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
    }

    // Hide dropdown if clicked outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('patientSuggestionsDropdown');
        const input = document.getElementById('lead_name');
        if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
            dropdown.style.display = 'none';
        }
    });

    function openLeadModal(lead = null) {
        const form = document.getElementById('leadForm');
        form.reset();
        document.getElementById('lead_id').value = '';
        document.getElementById('patientSuggestionsDropdown').style.display = 'none';

        if (lead) {
            document.getElementById('leadModalTitle').innerHTML = '<span>✏️</span><span>Edit Appointment</span>';
            document.getElementById('lead_id').value = lead.id;
            document.getElementById('lead_name').value = lead.name || '';
            document.getElementById('lead_email').value = lead.email || '';
            document.getElementById('lead_phone').value = lead.phone || '';
            
            if (lead.service_id) {
                document.getElementById('lead_service_id').value = lead.service_id;
            } else {
                document.getElementById('lead_service_id').value = '';
            }

            let prefDate = '';
            if (lead.preferred_date) {
                prefDate = lead.preferred_date.substring(0, 10);
            }
            document.getElementById('lead_preferred_date').value = prefDate;
            document.getElementById('lead_preferred_time').value = lead.preferred_time || '';
            document.getElementById('lead_status').value = lead.status || 'consultation_scheduled';
            document.getElementById('lead_priority').value = lead.priority || 'medium';
            document.getElementById('lead_estimated_value').value = lead.estimated_value || '';
            document.getElementById('lead_notes').value = lead.notes || '';
        } else {
            document.getElementById('leadModalTitle').innerHTML = '<span>📅</span><span>Schedule New Appointment</span>';
            document.getElementById('lead_status').value = 'consultation_scheduled';
            document.getElementById('lead_preferred_date').value = new Date().toISOString().split('T')[0];
            document.getElementById('lead_preferred_time').value = '11:00 AM';
        }

        document.getElementById('leadModal').classList.add('open');
    }

    function closeLeadModal() {
        document.getElementById('leadModal').classList.remove('open');
        document.getElementById('patientSuggestionsDropdown').style.display = 'none';
    }

    async function handleLeadSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveLeadBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving & Notifying...';

        const id = document.getElementById('lead_id').value;
        const serviceSelect = document.getElementById('lead_service_id');
        const selectedOpt = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceId = serviceSelect.value ? parseInt(serviceSelect.value) : null;
        const serviceName = selectedOpt ? selectedOpt.getAttribute('data-title') : null;

        const payload = {
            name: document.getElementById('lead_name').value.trim(),
            email: document.getElementById('lead_email').value.trim(),
            phone: document.getElementById('lead_phone').value.trim(),
            service_id: serviceId,
            service_name: serviceName,
            preferred_date: document.getElementById('lead_preferred_date').value || null,
            preferred_time: document.getElementById('lead_preferred_time').value.trim() || null,
            status: document.getElementById('lead_status').value,
            priority: document.getElementById('lead_priority').value,
            estimated_value: document.getElementById('lead_estimated_value').value || null,
            notes: document.getElementById('lead_notes').value.trim() || null,
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
                showToast(data.message || 'Appointment saved and emails dispatched successfully!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                showToast(data.message || 'Error saving appointment', 'error');
            }
        } catch(err) {
            showToast('Network error saving appointment', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Appointment';
        }
    }

    function openViewLeadModal(lead) {
        currentViewingLead = lead;
        document.getElementById('viewLeadName').innerText = lead.name;
        document.getElementById('viewLeadSub').innerText = 'Appointment ID #' + lead.id + ' • ' + (lead.created_at ? new Date(lead.created_at).toLocaleDateString() : 'Website');
        document.getElementById('viewLeadPhone').innerHTML = `<a href="tel:${lead.phone}" style="color: var(--color-charcoal); text-decoration: none;">${lead.phone}</a>`;
        document.getElementById('viewLeadEmail').innerHTML = `<a href="mailto:${lead.email}" style="color: var(--color-charcoal); text-decoration: none;">${lead.email}</a>`;
        document.getElementById('viewLeadService').innerText = lead.service_name || (lead.service ? lead.service.title : 'General Consultation');
        
        let dateDisplay = 'Not Set';
        if (lead.preferred_date) {
            dateDisplay = new Date(lead.preferred_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            if (lead.preferred_time) dateDisplay += ' (' + lead.preferred_time + ')';
        }
        document.getElementById('viewLeadDate').innerText = dateDisplay;

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
        if (!confirm(`Are you sure you want to permanently delete appointment #${id} for "${name}"?`)) {
            return;
        }

        (async () => {
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
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        setTimeout(() => row.remove(), 300);
                    }
                    showToast(data.message || 'Appointment deleted successfully!', 'success');
                } else {
                    showToast('Failed to delete appointment', 'error');
                }
            } catch(err) {
                showToast('Network error deleting appointment', 'error');
            }
        })();
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
