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
            <button class="btn btn-gold btn-sm" onclick="openLeadModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 22%;">Patient / Customer</th>
                    <th style="width: 20%;">Contact</th>
                    <th style="width: 20%;">Treatment / Procedure</th>
                    <th style="width: 15%;">Appointment Slot</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 11%; text-align: right; min-width: 110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                @php
                    $activeFu = $lead->followups->where('status', '!=', 'completed')->sortByDesc('follow_up_date')->first() ?? $lead->followups->sortByDesc('created_at')->first();
                @endphp
                <tr id="lead_row_{{ $lead->id }}" class="lead-data-row" data-search="{{ strtolower($lead->name . ' ' . $lead->email . ' ' . $lead->phone . ' ' . ($lead->service_name ?? '') . ' ' . ($lead->service->title ?? '')) }}">
                    <td style="word-break: break-word;">
                        <strong>{{ $lead->name }}</strong>
                        @if($lead->estimated_value)
                        <div class="small gold-text" style="font-weight: 600;">Est. ₹{{ number_format($lead->estimated_value, 2) }}</div>
                        @endif
                        @if($lead->notesList->count() > 0)
                        <div class="small text-muted" style="font-size: 0.75rem; margin-top: 2px;">
                            💬 {{ $lead->notesList->count() }} note{{ $lead->notesList->count() > 1 ? 's' : '' }}
                        </div>
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
                        <select onchange="updateAppointmentStatus({{ $lead->id }}, this.value)" class="status-select status-{{ $lead->status }}">
                            <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="consultation_scheduled" {{ $lead->status === 'consultation_scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="follow_up" {{ $lead->status === 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                            <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted / Done</option>
                            <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Cancelled / Lost</option>
                        </select>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end; gap: 5px;">
                            <!-- View Details -->
                            <button type="button" 
                                    class="action-icon-btn btn-view" 
                                    data-tooltip="View Details" 
                                    aria-label="View Details" 
                                    data-id="{{ $lead->id }}"
                                    data-name="{{ $lead->name }}"
                                    data-email="{{ $lead->email }}"
                                    data-phone="{{ $lead->phone }}"
                                    data-service="{{ $lead->service_name ?: ($lead->service->title ?? 'General Consultation') }}"
                                    data-date="{{ $lead->preferred_date ? $lead->preferred_date->format('M d, Y') : 'Not Set' }}"
                                    data-time="{{ $lead->preferred_time ?? '' }}"
                                    data-value="{{ $lead->estimated_value ?? '' }}"
                                    data-status="{{ $lead->status ?? 'new' }}"
                                    data-created="{{ $lead->created_at ? $lead->created_at->format('M d, Y') : 'Website' }}"
                                    data-fu-date="{{ $activeFu && $activeFu->follow_up_date ? $activeFu->follow_up_date->format('M d, Y') : '' }}"
                                    data-fu-rawdate="{{ $activeFu && $activeFu->follow_up_date ? $activeFu->follow_up_date->format('Y-m-d') : '' }}"
                                    data-fu-time="{{ $activeFu ? $activeFu->follow_up_time : '' }}"
                                    data-fu-note="{{ $activeFu ? $activeFu->note : '' }}"
                                    data-fu-status="{{ $activeFu ? $activeFu->status : '' }}"
                                    onclick="openViewLeadModalFromButton(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>

                            <!-- Edit Appointment -->
                            <button type="button" 
                                    class="action-icon-btn btn-edit" 
                                    data-tooltip="Edit Appointment" 
                                    aria-label="Edit Appointment" 
                                    data-id="{{ $lead->id }}"
                                    data-name="{{ $lead->name }}"
                                    data-email="{{ $lead->email }}"
                                    data-phone="{{ $lead->phone }}"
                                    data-service-id="{{ $lead->service_id }}"
                                    data-date="{{ $lead->preferred_date ? $lead->preferred_date->format('Y-m-d') : '' }}"
                                    data-time="{{ $lead->preferred_time }}"
                                    data-status="{{ $lead->status }}"
                                    data-value="{{ $lead->estimated_value }}"
                                    data-notes="{{ $lead->notes }}"
                                    onclick="openLeadModalFromButton(this)">
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
                    <td colspan="6" class="text-center py-5">No patient appointments found in database.</td>
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
    <div class="modal-card" style="max-width: 620px; max-height: 88vh; overflow-y: auto; padding: 1.75rem;">
        <button class="modal-close" onclick="closeLeadModal()">&times;</button>
        <div class="modal-header" style="margin-bottom: 1.25rem;">
            <h3 id="leadModalTitle" style="display: flex; align-items: center; gap: 8px; margin: 0 0 4px 0;">
                <span>📅</span>
                <span>Add Appointment</span>
            </h3>
            <p class="text-muted" style="font-size: 0.82rem; margin: 0;">Create or update clinical appointment details & notify patient</p>
        </div>
        <form onsubmit="handleLeadSubmit(event)" id="leadForm" autocomplete="off">
            <input type="hidden" id="lead_id" name="id">

            <!-- Patient Name with Realtime Autocomplete Suggestion -->
            <div class="form-group" style="position: relative; margin-bottom: 0.75rem;">
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
                <small class="text-muted" style="font-size: 0.75rem; margin-top: 2px; display: block;">
                    💡 Type to auto-search previous appointments or create a brand new patient.
                </small>
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="lead_phone">Phone Number *</label>
                    <input type="text" id="lead_phone" name="phone" required class="form-control" placeholder="+91 98200 12345">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="lead_email">Email Address *</label>
                    <input type="email" id="lead_email" name="email" required class="form-control" placeholder="patient@example.com">
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
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

            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: flex-end;">
                <div class="form-group" style="flex: 1.1;">
                    <label for="lead_preferred_date">Appointment Date</label>
                    <input type="date" id="lead_preferred_date" name="preferred_date" class="form-control">
                </div>
                <div class="form-group" style="flex: 1.4;">
                    <label>Appointment Time Slot</label>
                    <div style="display: flex; gap: 4px; align-items: center;">
                        <!-- Hour 1-12 -->
                        <select id="lead_time_hour" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 500;">
                            @for($h = 1; $h <= 12; $h++)
                                <option value="{{ sprintf('%02d', $h) }}" {{ $h == 11 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                            @endfor
                        </select>
                        <span style="font-weight: bold; color: var(--color-charcoal-muted);">:</span>
                        <!-- Minute 00-55 -->
                        <select id="lead_time_min" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 500;">
                            @for($m = 0; $m < 60; $m += 5)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $m == 0 ? 'selected' : '' }}>{{ sprintf('%02d', $m) }}</option>
                            @endfor
                        </select>
                        <!-- AM / PM -->
                        <select id="lead_time_ampm" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 600; min-width: 65px;">
                            <option value="AM" selected>AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group mb-2">
                <label for="lead_estimated_value">Estimated Treatment Value (₹)</label>
                <input type="number" id="lead_estimated_value" name="estimated_value" step="0.01" class="form-control" placeholder="e.g. 15000">
            </div>

            <div class="form-group mb-3">
                <label for="lead_notes">Consultation Notes / Patient Intake</label>
                <textarea id="lead_notes" name="notes" rows="2" class="form-control" placeholder="Skin type, primary concerns, doctor instructions..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeLeadModal()">Cancel</button>
                <button type="submit" id="saveLeadBtn" class="btn btn-gold btn-sm">Save Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Appointment Details -->
<div class="modal-overlay" id="viewLeadModal">
    <div class="modal-card" style="max-width: 620px; max-height: 88vh; overflow-y: auto; padding: 1.75rem;">
        <button class="modal-close" onclick="closeViewLeadModal()">&times;</button>
        <div class="modal-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 0.85rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div>
                    <h3 id="viewLeadName" style="margin: 0;">Patient Appointment Details</h3>
                    <small class="text-muted" id="viewLeadSub">Clinical Appointment Record</small>
                </div>
                <span id="viewLeadStatusBadge" class="status-badge">New</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div style="background: var(--color-bg-light); padding: 0.75rem 0.9rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;">Phone</small>
                <div id="viewLeadPhone" style="font-weight: 600; margin-top: 2px;">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.75rem 0.9rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;">Email</small>
                <div id="viewLeadEmail" style="font-weight: 600; margin-top: 2px;">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.75rem 0.9rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;">Treatment / Procedure</small>
                <div id="viewLeadService" style="font-weight: 600; margin-top: 2px; color: var(--color-crimson);">-</div>
            </div>
            <div style="background: var(--color-bg-light); padding: 0.75rem 0.9rem; border-radius: 8px;">
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;">Appointment Slot</small>
                <div id="viewLeadDate" style="font-weight: 600; margin-top: 2px; color: var(--color-gold-bright);">-</div>
            </div>
        </div>

        <!-- Dynamic Follow-Up Card Container -->
        <div id="viewLeadFollowupContainer" style="display: none; margin-bottom: 1.25rem;"></div>

        <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem;">
            <button type="button" class="btn btn-outline-gold btn-sm" style="flex: 1;" onclick="triggerAddNoteFromView()">+ Add Note</button>
            <button type="button" class="btn btn-gold btn-sm" id="viewLeadFuBtn" style="flex: 1;" onclick="triggerFollowUpFromView()">Schedule Follow-up</button>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeViewLeadModal()">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Add Note -->
<div class="modal-overlay" id="leadNoteModal">
    <div class="modal-card" style="max-width: 550px; padding: 1.75rem;">
        <button class="modal-close" onclick="closeLeadNoteModal()">&times;</button>
        <div class="modal-header" style="margin-bottom: 1rem;">
            <h3 style="margin: 0 0 4px 0;">Add Consultation Note</h3>
            <p id="noteModalLeadName" class="text-muted" style="font-size: 0.82rem; margin: 0;"></p>
        </div>
        <form onsubmit="handleNoteSubmit(event)">
            <input type="hidden" id="note_lead_id">
            <div class="form-group mb-3">
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
    <div class="modal-card" style="max-width: 550px; padding: 1.75rem;">
        <button class="modal-close" onclick="closeFollowUpModal()">&times;</button>
        <div class="modal-header" style="margin-bottom: 1rem;">
            <h3 id="followUpModalTitle" style="margin: 0 0 4px 0;">Schedule Patient Follow-up</h3>
            <p id="fuModalLeadName" class="text-muted" style="font-size: 0.82rem; margin: 0;"></p>
        </div>
        <form onsubmit="handleFollowUpSubmit(event)">
            <input type="hidden" id="fu_lead_id">
            <div class="form-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: flex-end;">
                <div class="form-group" style="flex: 1;">
                    <label for="fu_date">Follow-up Date *</label>
                    <input type="date" id="fu_date" required class="form-control">
                </div>
                <div class="form-group" style="flex: 1.3;">
                    <label>Follow-up Time</label>
                    <div style="display: flex; gap: 4px; align-items: center;">
                        <select id="fu_time_hour" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 500;">
                            @for($h = 1; $h <= 12; $h++)
                                <option value="{{ sprintf('%02d', $h) }}" {{ $h == 11 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                            @endfor
                        </select>
                        <span style="font-weight: bold; color: var(--color-charcoal-muted);">:</span>
                        <select id="fu_time_min" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 500;">
                            @for($m = 0; $m < 60; $m += 5)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $m == 0 ? 'selected' : '' }}>{{ sprintf('%02d', $m) }}</option>
                            @endfor
                        </select>
                        <select id="fu_time_ampm" class="form-control" style="padding: 0.45rem 0.4rem; font-weight: 600; min-width: 65px;">
                            <option value="AM" selected>AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="fu_note">Follow-up Objective / Note</label>
                <textarea id="fu_note" rows="2" class="form-control" placeholder="e.g. Check skin recovery & schedule session 2"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeFollowUpModal()">Cancel</button>
                <button type="submit" id="saveFuBtn" class="btn btn-gold btn-sm">Schedule & Send Alert</button>
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

    // Dynamic Live Status Change
    async function updateAppointmentStatus(id, status) {
        try {
            const res = await fetch(`/api/v1/admin/leads/${id}/status`, {
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
                const row = document.getElementById(`lead_row_${id}`);
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

    // Helper: Parse Time String (e.g., "11:30 AM" or "05:00 PM")
    function parseTimeToSelectors(timeStr, hourElId, minElId, ampmElId) {
        if (!timeStr) {
            document.getElementById(hourElId).value = '11';
            document.getElementById(minElId).value = '00';
            document.getElementById(ampmElId).value = 'AM';
            return;
        }

        const match = timeStr.match(/(\d{1,2})[:.](\d{2})\s*(AM|PM)?/i);
        if (match) {
            let h = parseInt(match[1]);
            let m = match[2];
            let ampm = match[3] ? match[3].toUpperCase() : 'AM';
            
            if (h > 12) {
                h -= 12;
                ampm = 'PM';
            }
            if (h === 0) h = 12;

            document.getElementById(hourElId).value = (h < 10 ? '0' + h : '' + h);
            
            let minInt = parseInt(m);
            minInt = Math.round(minInt / 5) * 5;
            if (minInt >= 60) minInt = 55;
            let minFormatted = minInt < 10 ? '0' + minInt : '' + minInt;
            
            document.getElementById(minElId).value = minFormatted;
            document.getElementById(ampmElId).value = ampm;
        } else {
            document.getElementById(hourElId).value = '11';
            document.getElementById(minElId).value = '00';
            document.getElementById(ampmElId).value = 'AM';
        }
    }

    let currentViewingLead = null;
    let autocompleteTimeout = null;

    // --- Realtime Patient Name Autocomplete (Only Appointment records) ---
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

    function openLeadModal() {
        const form = document.getElementById('leadForm');
        form.reset();
        document.getElementById('lead_id').value = '';
        document.getElementById('patientSuggestionsDropdown').style.display = 'none';

        document.getElementById('leadModalTitle').innerHTML = '<span>📅</span><span>Add Appointment</span>';
        document.getElementById('lead_status').value = 'consultation_scheduled';
        document.getElementById('lead_preferred_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('lead_time_hour').value = '11';
        document.getElementById('lead_time_min').value = '00';
        document.getElementById('lead_time_ampm').value = 'AM';

        const modal = document.getElementById('leadModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function openLeadModalFromButton(btn) {
        const form = document.getElementById('leadForm');
        form.reset();
        document.getElementById('patientSuggestionsDropdown').style.display = 'none';

        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || '';
        const email = btn.getAttribute('data-email') || '';
        const phone = btn.getAttribute('data-phone') || '';
        const serviceId = btn.getAttribute('data-service-id') || '';
        const date = btn.getAttribute('data-date') || '';
        const time = btn.getAttribute('data-time') || '';
        const status = btn.getAttribute('data-status') || 'consultation_scheduled';
        const value = btn.getAttribute('data-value') || '';
        const notes = btn.getAttribute('data-notes') || '';

        document.getElementById('leadModalTitle').innerHTML = '<span>✏️</span><span>Edit Appointment</span>';
        document.getElementById('lead_id').value = id;
        document.getElementById('lead_name').value = name;
        document.getElementById('lead_email').value = email;
        document.getElementById('lead_phone').value = phone;
        
        if (serviceId) {
            document.getElementById('lead_service_id').value = serviceId;
        } else {
            document.getElementById('lead_service_id').value = '';
        }

        document.getElementById('lead_preferred_date').value = date;
        parseTimeToSelectors(time, 'lead_time_hour', 'lead_time_min', 'lead_time_ampm');

        document.getElementById('lead_status').value = status;
        document.getElementById('lead_estimated_value').value = value;
        document.getElementById('lead_notes').value = notes;

        const modal = document.getElementById('leadModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function closeLeadModal() {
        const modal = document.getElementById('leadModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
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

        const h = document.getElementById('lead_time_hour').value;
        const m = document.getElementById('lead_time_min').value;
        const ap = document.getElementById('lead_time_ampm').value;
        const formattedTime = `${h}:${m} ${ap}`;

        const payload = {
            name: document.getElementById('lead_name').value.trim(),
            email: document.getElementById('lead_email').value.trim(),
            phone: document.getElementById('lead_phone').value.trim(),
            service_id: serviceId,
            service_name: serviceName,
            preferred_date: document.getElementById('lead_preferred_date').value || null,
            preferred_time: formattedTime,
            status: document.getElementById('lead_status').value,
            priority: 'medium',
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
                showToast(data.message || 'Appointment saved successfully!', 'success');
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

    function openViewLeadModalFromButton(btn) {
        currentViewingLead = {
            id: btn.getAttribute('data-id'),
            name: btn.getAttribute('data-name'),
            email: btn.getAttribute('data-email'),
            phone: btn.getAttribute('data-phone'),
            service: btn.getAttribute('data-service'),
            date: btn.getAttribute('data-date'),
            time: btn.getAttribute('data-time'),
            value: btn.getAttribute('data-value'),
            status: btn.getAttribute('data-status'),
            created: btn.getAttribute('data-created'),
            fuDate: btn.getAttribute('data-fu-date'),
            fuRawDate: btn.getAttribute('data-fu-rawdate'),
            fuTime: btn.getAttribute('data-fu-time'),
            fuNote: btn.getAttribute('data-fu-note'),
            fuStatus: btn.getAttribute('data-fu-status')
        };

        document.getElementById('viewLeadName').innerText = currentViewingLead.name;
        document.getElementById('viewLeadSub').innerText = 'Appointment ID #' + currentViewingLead.id + ' • ' + currentViewingLead.created;
        document.getElementById('viewLeadPhone').innerHTML = `<a href="tel:${currentViewingLead.phone}" style="color: var(--color-charcoal); text-decoration: none;">${currentViewingLead.phone}</a>`;
        document.getElementById('viewLeadEmail').innerHTML = `<a href="mailto:${currentViewingLead.email}" style="color: var(--color-charcoal); text-decoration: none;">${currentViewingLead.email}</a>`;
        document.getElementById('viewLeadService').innerText = currentViewingLead.service;
        
        let dateDisplay = currentViewingLead.date;
        if (currentViewingLead.time) dateDisplay += ' (' + currentViewingLead.time + ')';
        document.getElementById('viewLeadDate').innerText = dateDisplay;

        const badge = document.getElementById('viewLeadStatusBadge');
        badge.className = 'status-badge status-' + currentViewingLead.status;
        badge.innerText = (currentViewingLead.status || 'new').replace('_', ' ').toUpperCase();

        // Handle Active Follow-up Display
        const fuContainer = document.getElementById('viewLeadFollowupContainer');
        const fuBtn = document.getElementById('viewLeadFuBtn');

        if (currentViewingLead.fuDate) {
            fuContainer.innerHTML = `
                <div style="background: rgba(139, 21, 56, 0.05); border: 1px solid rgba(139, 21, 56, 0.2); padding: 0.85rem 1rem; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: var(--color-crimson); font-size: 0.88rem;">📅 Scheduled Follow-Up</strong>
                        <span class="status-badge status-${currentViewingLead.fuStatus || 'follow_up'}">${(currentViewingLead.fuStatus || 'Pending').toUpperCase()}</span>
                    </div>
                    <div style="font-weight: 600; color: var(--color-charcoal); margin-top: 4px;">
                        ${currentViewingLead.fuDate} ${currentViewingLead.fuTime ? 'at ' + currentViewingLead.fuTime : ''}
                    </div>
                    ${currentViewingLead.fuNote ? `<div style="font-size: 0.82rem; color: var(--color-charcoal-muted); margin-top: 3px; font-style: italic;">📝 "${currentViewingLead.fuNote}"</div>` : ''}
                </div>
            `;
            fuContainer.style.display = 'block';
            fuBtn.innerText = 'Reschedule Follow-up';
        } else {
            fuContainer.innerHTML = '';
            fuContainer.style.display = 'none';
            fuBtn.innerText = 'Schedule Follow-up';
        }

        const modal = document.getElementById('viewLeadModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function closeViewLeadModal() {
        const modal = document.getElementById('viewLeadModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
    }

    function triggerAddNoteFromView() {
        if (!currentViewingLead) return;
        closeViewLeadModal();
        openLeadNoteModal(currentViewingLead.id, currentViewingLead.name);
    }

    function triggerFollowUpFromView() {
        if (!currentViewingLead) return;
        closeViewLeadModal();
        openFollowUpModal(currentViewingLead.id, currentViewingLead.name, currentViewingLead.fuRawDate, currentViewingLead.fuTime, currentViewingLead.fuNote);
    }

    function deleteLead(id, name) {
        confirmDeleteModal('Delete Appointment', name, async () => {
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
        });
    }

    function openLeadNoteModal(id, name) {
        document.getElementById('note_lead_id').value = id;
        document.getElementById('note_text').value = '';
        document.getElementById('noteModalLeadName').innerText = 'Patient: ' + name;
        const modal = document.getElementById('leadNoteModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }
    function closeLeadNoteModal() {
        const modal = document.getElementById('leadNoteModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
    }

    function openFollowUpModal(id, name, preDate = '', preTime = '', preNote = '') {
        document.getElementById('fu_lead_id').value = id;
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fu_date').value = preDate || today;
        parseTimeToSelectors(preTime, 'fu_time_hour', 'fu_time_min', 'fu_time_ampm');
        document.getElementById('fu_note').value = preNote || '';
        document.getElementById('fuModalLeadName').innerText = 'Patient: ' + name;

        if (preDate) {
            document.getElementById('followUpModalTitle').innerText = 'Reschedule Patient Follow-up';
        } else {
            document.getElementById('followUpModalTitle').innerText = 'Schedule Patient Follow-up';
        }

        const modal = document.getElementById('followUpModal');
        modal.classList.add('open');
        modal.classList.add('active');
    }

    function closeFollowUpModal() {
        const modal = document.getElementById('followUpModal');
        modal.classList.remove('open');
        modal.classList.remove('active');
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
        btn.innerText = 'Scheduling & Notifying Admin...';
        const id = document.getElementById('fu_lead_id').value;

        const h = document.getElementById('fu_time_hour').value;
        const m = document.getElementById('fu_time_min').value;
        const ap = document.getElementById('fu_time_ampm').value;
        const formattedTime = `${h}:${m} ${ap}`;

        const payload = {
            follow_up_date: document.getElementById('fu_date').value,
            follow_up_time: formattedTime,
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
                showToast('Follow-up scheduled successfully & email alert sent to admin!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Error scheduling follow-up', 'error');
            }
        } catch(err) {
            showToast('Network error scheduling follow-up', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Schedule & Send Alert';
        }
    }
</script>
@endsection
